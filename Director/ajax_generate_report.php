<?php
// Ensure UTF-8
header('Content-Type: text/html; charset=utf-8');
include('../connection/connection.php');
require_once('../tcpdf/tcpdf.php');

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

// Calculate age
$dob   = new DateTime($student['dob']);
$today = new DateTime();
$age   = $today->diff($dob)->y;

// --- Fetch all subjects for the class ---
$allSubjects = [];
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
$marksMap = [];
$marksRes = mysqli_query($conn, "
    SELECT subject_id, semester, result
    FROM marks
    WHERE student_id = '$sid'
      AND section_id = '$section_id'
      AND academic_year = '$academic_year'
");
while ($m = mysqli_fetch_assoc($marksRes)) {
    $marksMap[(int)$m['subject_id']][(int)$m['semester']] = (float)$m['result'];
}

// --- Utilities ---
function calcTotalsBySemester($allSubjects, $marksMap, $semester) {
    $sum = 0; $cnt = 0;
    foreach ($allSubjects as $suid => $name) {
        if (isset($marksMap[$suid][$semester])) {
            $sum += $marksMap[$suid][$semester];
            $cnt++;
        }
    }
    $avg = $cnt ? round($sum / $cnt, 2) : 0;
    return [$sum, $avg, $cnt];
}

function getTotalAbsence($conn, $sid, $section_id, $academic_year, $semester){
    $sid = (int)$sid;
    $section_id = (int)$section_id;
    $semester = (int)$semester;
    $academic_year = mysqli_real_escape_string($conn, $academic_year);

    $q = "
        SELECT COUNT(*) AS total_absent
        FROM attendance
        WHERE student_id = '$sid'
          AND section_id  = '$section_id'
          AND academic_year = '$academic_year'
          AND semester = '$semester'
          AND status = 'Absent'
    ";
    $res = mysqli_query($conn, $q);
    if (!$res) return 0;
    $row = mysqli_fetch_assoc($res);
    return (int)($row['total_absent'] ?? 0);
}

// --- Semester totals ---
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
        if ((int)$row['student_id'] === (int)$sid) return $rank;
        $rank++;
    }
    return '-';
}

$sem1Rank = getClassRank($conn, $section_id, $academic_year, 1, $sid);
$sem2Rank = getClassRank($conn, $section_id, $academic_year, 2, $sid);

// --- TCPDF ---
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->setFontSubsetting(true);
$pdf->SetFont('', '', 12);

$studentFullName = $student['first_name'].' '.$student['father_name'].' '.$student['mother_name'];

// ---------------- PAGE 1 ----------------
$pdf->AddPage();
$pdf->Image('../images/card-2.png', 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
$pdf->Image('../images/logo.png', 90, 5, 30, 0, '', '', '', false, 300);

$pdf->SetFont('dejavusans', '', 12);
$pdf->SetXY(70, 55); $pdf->Cell(100, 8, $studentFullName, 0, 1, 'L');
$pdf->SetXY(40, 70); $pdf->Cell(30, 8, $student['gender'], 0, 1, 'L');
$pdf->SetXY(80, 70); $pdf->Cell(30, 8, $age, 0, 1, 'L');
$pdf->SetXY(70, 80); $pdf->Cell(100, 8, $student['woreda'].' '.$student['kebele'], 0, 1, 'L');
$pdf->SetXY(70, 95); $pdf->Cell(50, 8, $academic_year, 0, 1, 'L');
$pdf->SetXY(70, 105); $pdf->Cell(50, 8, $section_id, 0, 1, 'L');
$pdf->SetXY(70, 115); $pdf->Cell(50, 8, $promotionStatus, 0, 1, 'L');

// ---------------- PAGE 2 ----------------
$pdf->AddPage();
$rowsHtml = '';
$yearlyAvgSum = 0; $yearlyAvgCount = 0;

foreach ($allSubjects as $suid => $subName) {
    $s1 = $marksMap[$suid][1] ?? '-';
    $s2 = $marksMap[$suid][2] ?? '-';
    $avg = ($s1 !== '-' && $s2 !== '-') ? round(($s1+$s2)/2,2) : ($s1 !== '-' ? $s1 : ($s2 !== '-' ? $s2 : '-'));
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

$html2 = '
<h4 style="text-align:center;">የነጥብ ሰንጠረዥ / Marks Table</h4>
<table cellpadding="6" style="width:100%; border-collapse:collapse; font-size:12px;" border="1">
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

$pdf->writeHTML($html2, true, false, true, false, '');

// Output
$pdf->Output('ReportCard_'.$student['student_id'].'.pdf', 'I');
