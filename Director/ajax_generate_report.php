<?php
// Ensure UTF-8
header('Content-Type: text/html; charset=utf-8');
include('../connection/connection.php');
require_once __DIR__ . '../vendor/autoload.php'; // mPDF autoload

// --- Get parameters ---
$sid           = $_GET['sid'] ?? null;
$section_id    = $_GET['section_id'] ?? null;
$academic_year = $_GET['academic_year'] ?? null;

if (!$sid || !$section_id || !$academic_year) {
    die('Missing parameters.');
}

$sid           = (int)$sid;
$section_id    = (int)$section_id;
$academic_year = mysqli_real_escape_string($conn, $academic_year);

// --- Fetch student info ---
$studentQuery = "
    SELECT first_name, father_name, mother_name, dob, gender, student_id, woreda, kebele
    FROM students
    WHERE sid = '$sid'
    LIMIT 1
";
$studentRes = mysqli_query($conn, $studentQuery);
if (!$studentRes || mysqli_num_rows($studentRes) == 0) {
    die('Student not found.');
}
$student = mysqli_fetch_assoc($studentRes);

// Age
$dob   = new DateTime($student['dob']);
$today = new DateTime();
$age   = $today->diff($dob)->y;

// --- Fetch ALL subjects ---
$allSubjects = []; // [suid => subject_name]
$subRes = mysqli_query($conn, "
    SELECT s.suid, s.subject_name
    FROM curriculum_subjects cs
    JOIN subjects s ON cs.subject_id = s.suid
    WHERE cs.class_id = '$section_id'
    ORDER BY s.subject_name ASC
");
while ($r = mysqli_fetch_assoc($subRes)) {
    $allSubjects[(int)$r['suid']] = $r['subject_name'];
}

// --- Fetch marks ---
$marksMap = []; // [subject_id][semester] = result
$marksRes = mysqli_query($conn, "
    SELECT subject_id, semester, result
    FROM marks
    WHERE student_id = '$sid'
      AND section_id = '$section_id'
      AND academic_year = '$academic_year'
");
while ($m = mysqli_fetch_assoc($marksRes)) {
    $subId = (int)$m['subject_id'];
    $sem   = (int)$m['semester'];
    $res   = (float)$m['result'];
    $marksMap[$subId][$sem] = $res;
}

// --- Utilities ---
function calcTotalsBySemester($allSubjects, $marksMap, $semester) {
    $sum = 0; $cnt = 0;
    foreach ($allSubjects as $suid => $name) {
        if (isset($marksMap[$suid][$semester])) {
            $sum += (float)$marksMap[$suid][$semester];
            $cnt++;
        }
    }
    $avg = $cnt ? round($sum / $cnt, 2) : 0;
    return [$sum, $avg, $cnt];
}

function getTotalAbsence($conn, $sid, $section_id, $academic_year, $semester){
    $q = "
        SELECT IFNULL(SUM(absent_days),0) AS total_absent
        FROM attendance
        WHERE student_id = '$sid'
          AND section_id  = '$section_id'
          AND academic_year = '$academic_year'
          AND semester = '$semester'
    ";
    $res = mysqli_query($conn, $q);
    $row = mysqli_fetch_assoc($res);
    return (int)($row['total_absent'] ?? 0);
}

// Totals
list($sem1Sum, $sem1Avg) = calcTotalsBySemester($allSubjects, $marksMap, 1);
list($sem2Sum, $sem2Avg) = calcTotalsBySemester($allSubjects, $marksMap, 2);

$sem1AbsentTotal = getTotalAbsence($conn, $sid, $section_id, $academic_year, 1);
$sem2AbsentTotal = getTotalAbsence($conn, $sid, $section_id, $academic_year, 2);

// Promotion
$promotionStatus = ($sem2Avg >= 50) ? 'Promoted' : 'Not Promoted';

// --- Class Rank ---
function getClassRank($conn, $section_id, $academic_year, $semester, $sid){
    $query = "
        SELECT student_id, AVG(result) AS avg_mark
        FROM marks
        WHERE section_id = '$section_id'
          AND academic_year = '$academic_year'
          AND semester = '$semester'
        GROUP BY student_id
        ORDER BY avg_mark DESC
    ";
    $res = mysqli_query($conn, $query);
    $rank = 1;
    while($row = mysqli_fetch_assoc($res)){
        if((int)$row['student_id'] === (int)$sid) return $rank;
        $rank++;
    }
    return '-';
}
$sem1Rank = getClassRank($conn, $section_id, $academic_year, 1, $sid);
$sem2Rank = getClassRank($conn, $section_id, $academic_year, 2, $sid);

// ------------------- mPDF -------------------
$mpdf = new \Mpdf\Mpdf([
    'default_font' => 'dejavusans',  // supports Amharic
    'format' => 'A4'
]);

$studentFullName = $student['first_name'].' '.$student['father_name'].' '.$student['mother_name'];

// ---------------- PAGE 1 ----------------
$page1 = '
<html>
<head>
<style>
body { font-family: dejavusans; }
.card-bg {
    position: absolute;
    top: 0; left: 0;
    width: 210mm;
    height: 297mm;
    background-image: url("../images/card-2.png");
    background-size: cover;
}
.student-info {
    position: absolute;
    left: 70mm;
    top: 55mm;
    font-size: 14px;
}
</style>
</head>
<body>
<div class="card-bg"></div>
<div class="student-info">
    <p><strong>Name:</strong> '.$studentFullName.'</p>
    <p><strong>Sex:</strong> '.$student['gender'].' &nbsp;&nbsp; <strong>Age:</strong> '.$age.'</p>
    <p><strong>Address:</strong> '.$student['woreda'].' '.$student['kebele'].'</p>
    <p><strong>Academic Year:</strong> '.$academic_year.'</p>
    <p><strong>Class:</strong> '.$section_id.'</p>
    <p><strong>Status:</strong> '.$promotionStatus.'</p>
</div>
</body>
</html>
';

$mpdf->WriteHTML($page1);
$mpdf->AddPage();

// ---------------- PAGE 2 (Marks Table) ----------------
$rowsHtml = '';
$yearlyAvgSum = 0; $yearlyAvgCount = 0;
foreach ($allSubjects as $suid => $subName) {
  $s1 = isset($marksMap[$suid][1]) ? $marksMap[$suid][1] : '-';
  $s2 = isset($marksMap[$suid][2]) ? $marksMap[$suid][2] : '-';
  $avg = ($s1 !== '-' && $s2 !== '-') ? round(($s1 + $s2)/2,2) : ($s1 !== '-' ? $s1 : ($s2 !== '-' ? $s2 : '-'));
  if ($avg !== '-') { $yearlyAvgSum += $avg; $yearlyAvgCount++; }

  $rowsHtml .= "
    <tr>
      <td>$subName</td>
      <td align='center'>$s1</td>
      <td align='center'>$s2</td>
      <td align='center'>$avg</td>
    </tr>
  ";
}
$yearAvg = $yearlyAvgCount ? round($yearlyAvgSum/$yearlyAvgCount,2) : 0;

$page2 = '
<h4 style="text-align:center;">የነጥብ ሰንጠረዥ / Marks Table</h4>
<table border="1" cellpadding="6" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:12px;">
  <tr>
    <th>Subject</th>
    <th>1ኛ መ/ዓ/ት / 1st Semester</th>
    <th>2ኛ መ/ዓ/ት / 2nd Semester</th>
    <th>Average / አማካኝ ዉጤት</th>
  </tr>
  '.$rowsHtml.'
  <tr>
    <td>Absence / የቀረበት ቀን</td>
    <td align="center">'.$sem1AbsentTotal.'</td>
    <td align="center">'.$sem2AbsentTotal.'</td>
    <td>-</td>
  </tr>
  <tr>
    <td>Total / ጠቅላላ ነጥብ</td>
    <td align="center">'.$sem1Sum.'</td>
    <td align="center">'.$sem2Sum.'</td>
    <td>-</td>
  </tr>
  <tr>
    <td>Average / አማካኝ</td>
    <td align="center">'.$sem1Avg.'</td>
    <td align="center">'.$sem2Avg.'</td>
    <td align="center">'.$yearAvg.'</td>
  </tr>
  <tr>
    <td>Rank / የክፍል ደረጃ</td>
    <td align="center">'.$sem1Rank.'</td>
    <td align="center">'.$sem2Rank.'</td>
    <td>-</td>
  </tr>
</table>
';

$mpdf->WriteHTML($page2);

// Output
$mpdf->Output('ReportCard_'.$student['student_id'].'.pdf', 'I');
