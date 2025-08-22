<?php
include('../connection/connection.php'); // DB connection
require __DIR__ . '/../vendor/autoload.php'; // Composer autoload

use Mpdf\Mpdf;

// --- Get parameters ---
$sid           = $_GET['sid'] ?? null;
$section_id    = $_GET['section_id'] ?? null;
$academic_year = $_GET['academic_year'] ?? null;
$mode          = $_GET['mode'] ?? 'download'; 

if (!$sid || !$section_id || !$academic_year) {
    die('Missing parameters.');
}

$sid           = (int)$sid;
$section_id    = (int)$section_id;
$academic_year = mysqli_real_escape_string($conn, $academic_year);

// --- Fetch student info ---
$studentQuery = "
    SELECT first_name, father_name, grand_father_name, dob, gender, student_id, woreda, kebele
    FROM students
    WHERE sid = '$sid'
    LIMIT 1
";
$studentRes = mysqli_query($conn, $studentQuery);
if (!$studentRes || mysqli_num_rows($studentRes) == 0) die('Student not found.');
$student = mysqli_fetch_assoc($studentRes);

// --- Calculate age ---
$dob   = new DateTime($student['dob']);
$today = new DateTime();
$age   = $today->diff($dob)->y;

// --- Fetch subjects for the class ---
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

// --- Utility Functions ---
function calcTotalsBySemester($allSubjects, $marksMap, $semester) {
    $sum = $cnt = 0;
    foreach ($allSubjects as $suid => $name) {
        if (isset($marksMap[$suid][$semester])) {
            $sum += $marksMap[$suid][$semester];
            $cnt++;
        }
    }
    $avg = $cnt ? round($sum / $cnt, 2) : 0;
    return [$sum, $avg, $cnt];
}

function getTotalAbsence($conn, $sid, $section_id, $academic_year, $semester) {
    $sid = (int)$sid; $section_id = (int)$section_id; $semester = (int)$semester;
    $academic_year = mysqli_real_escape_string($conn, $academic_year);

    $q = "SELECT COUNT(*) AS total_absent
          FROM attendance
          WHERE student_id='$sid'
            AND section_id='$section_id'
            AND academic_year='$academic_year'
            AND semester='$semester'
            AND status='Absent'";
    $res = mysqli_query($conn, $q);
    if (!$res) return 0;
    $row = mysqli_fetch_assoc($res);
    return (int)($row['total_absent'] ?? 0);
}

function getClassRank($conn, $section_id, $academic_year, $semester, $sid) {
    $query = "SELECT student_id, AVG(result) AS avg_mark
              FROM marks
              WHERE section_id='$section_id'
                AND academic_year='$academic_year'
                AND semester='$semester'
              GROUP BY student_id
              ORDER BY avg_mark DESC";
    $res = mysqli_query($conn, $query);
    $rank = 1;
    while ($row = mysqli_fetch_assoc($res)) {
        if ((int)$row['student_id'] === (int)$sid) return $rank;
        $rank++;
    }
    return '-';
}

// --- Semester totals & absence ---
list($sem1Sum, $sem1Avg) = calcTotalsBySemester($allSubjects, $marksMap, 1);
list($sem2Sum, $sem2Avg) = calcTotalsBySemester($allSubjects, $marksMap, 2);
$sem1AbsentTotal = getTotalAbsence($conn, $sid, $section_id, $academic_year, 1);
$sem2AbsentTotal = getTotalAbsence($conn, $sid, $section_id, $academic_year, 2);
$promotionStatus = ($sem2Avg >= 50) ? 'Promoted' : 'Not Promoted';
$sem1Rank = getClassRank($conn, $section_id, $academic_year, 1, $sid);
$sem2Rank = getClassRank($conn, $section_id, $academic_year, 2, $sid);

// --- Generate PDF with mPDF ---
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font' => 'dejavusans', // base; autoLang will switch to Ethiopic font
    'tempDir' => __DIR__ . '/../tmp'
]);

// Improve complex script rendering and automatic font switching for Amharic/Ethiopic
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;

$mpdf->SetTitle('Report Card - ' . $student['first_name']);

// --- Page 1: Student Info ---
$studentFullName = $student['first_name'].' '.$student['father_name'].' '.$student['grand_father_name'];

$html1 = "
<div style='text-align:center;'>
    <h3>ሲዳማ ክልል አስተዳደር | Sidama National Regional Government</h3>
    <h4>ቢላቴ ዙሪያ ወረዳ | Bilate Zuriya Woreda</h4>
    <h4>ባለላ የላይንኪ ዲሪሚ ሮሲ ሚኒ ሮሳኖቴ ጉማ | Balela Secondary School</h4>
    <h2 style='margin-top:10px;'>STUDENT REPORT CARD / የተማሪ ደብዳቤ</h2>
    <p>Academic Year / የትምህርት አመት: <b>$academic_year</b></p>
</div>

<div style='margin-top:20px;'>
    <h3>Student Information / የተማሪ መረጃ</h3>
    <p><b>Name / ስም:</b> — $studentFullName</p>
    <p><b>Gender / ፆታ:</b> — {$student['gender']}</p>
    <p><b>Age / እድሜ:</b> — $age ዓመት</p>
    <p><b>Address / አድራሻ:</b> — {$student['woreda']}, {$student['kebele']}</p>
    <p><b>Class / ክፍል:</b> — $section_id</p>
    <p><b>Promotion Status / የማሻሻያ ሁኔታ:</b> — $promotionStatus</p>
</div>

<div style='margin-top:40px;'>
    <p><b>Director Name / የዳይሬክተር ስም: ______________________ </b></p>
    <p style='margin-top:60px;'>Signature / ፊርማ: ______________________</p>
</div>
";

// Add base CSS to prefer Ethiopic-capable fonts
$mpdf->WriteHTML("<style>body{font-family:'Abyssinica SIL','DejaVu Sans',sans-serif;}</style>");
$mpdf->WriteHTML($html1);
$mpdf->AddPage();

// --- Page 2: Marks Table ---
$rowsHtml = ''; $yearlyAvgSum = 0; $yearlyAvgCount = 0;
foreach ($allSubjects as $suid => $subName) {
    $s1 = $marksMap[$suid][1] ?? '-';
    $s2 = $marksMap[$suid][2] ?? '-';
    $avg = ($s1 !== '-' && $s2 !== '-') ? round(($s1+$s2)/2,2) : ($s1 !== '-' ? $s1 : ($s2 !== '-' ? $s2 : '-'));
    if ($avg !== '-') { $yearlyAvgSum += $avg; $yearlyAvgCount++; }
    $rowsHtml .= "<tr>
        <td>$subName</td>
        <td align='center'>$s1</td>
        <td align='center'>$s2</td>
        <td align='center'>$avg</td>
    </tr>";
}
$yearAvg = $yearlyAvgCount ? round($yearlyAvgSum/$yearlyAvgCount,2) : 0;

$html2 = "
<h3>Marks Table / የነጥብ ሰንጠረዥ</h3>
<table border='1' cellpadding='5'>
<tr><th>Subject / ትምህርት</th><th>1st Sem / 1ኛ መ/ዓ/ት</th><th>2nd Sem / 2ኛ መ/ዓ/ት</th><th>Average / አማካኝ</th></tr>
$rowsHtml
<tr><td>Absence / የቀረበት ቀን</td><td align='center'>$sem1AbsentTotal</td><td align='center'>$sem2AbsentTotal</td><td>-</td></tr>
<tr><td>Total / ጠቅላላ ነጥብ</td><td align='center'>$sem1Sum</td><td align='center'>$sem2Sum</td><td>-</td></tr>
<tr><td>Average / አማካኝ</td><td align='center'>$sem1Avg</td><td align='center'>$sem2Avg</td><td>$yearAvg</td></tr>
<tr><td>Rank / ደረጃ</td><td align='center'>$sem1Rank</td><td align='center'>$sem2Rank</td><td>-</td></tr>
</table>
";

$mpdf->WriteHTML($html2);

// --- Footer ---
$mpdf->SetFooter('Balela Secondary School / ባለላ የላይንኪ ዲሪሚ ሮሲ ሚኒ ሮሳኖቴ ጉማ');

// --- Output PDF ---
$filename = 'ReportCard_'.$student['student_id'].'.pdf';
// Clean any prior output buffers to prevent corrupt PDFs/blank pages
if (ob_get_length()) { @ob_end_clean(); }
if ($mode === 'download') {
    $mpdf->Output($filename, 'D'); // force download
} else {
    $mpdf->Output($filename, 'I'); // inline preview
}
