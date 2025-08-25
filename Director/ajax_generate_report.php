<?php
include('../connection/connection.php'); // DB connection
require __DIR__ . '/../vendor/autoload.php'; // Composer autoload

use Mpdf\Mpdf;

// --- Get parameters ---
$sid           = $_GET['sid'] ?? null;
$section_id    = $_GET['section_id'] ?? null;
$academic_year = $_GET['academic_year'] ?? null;
$mode          = $_GET['mode'] ?? 'download'; 
$semesterSel   = $_GET['semester'] ?? 'all';

if (!$sid || !$section_id || !$academic_year) {
    die('Missing parameters.');
}

$sid           = (int)$sid;
$section_id    = (int)$section_id;
$academic_year = mysqli_real_escape_string($conn, $academic_year);

// --- Fetch student info ---
$studentQuery = "
    SELECT s.first_name, s.father_name, s.grand_father_name, s.dob, s.gender, s.student_id,
           s.region AS region_id, s.zone AS zone_id, s.woreda AS woreda_id, s.kebele,
           r.name AS region_name, z.name AS zone_name, w.name AS woreda_name
    FROM students s
    LEFT JOIN regions r ON s.region = r.id
    LEFT JOIN zones z ON s.zone = z.id
    LEFT JOIN woredas w ON s.woreda = w.id
    WHERE s.sid = '$sid'
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
    $section_id = (int)$section_id; $semester = (int)$semester; $sid = (int)$sid;
    $academic_year = mysqli_real_escape_string($conn, $academic_year);

    // Total students assigned to this section/year
    $totalRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM assign_student WHERE section_id='$section_id' AND academic_year='$academic_year'");
    $totalRow = $totalRes ? mysqli_fetch_assoc($totalRes) : null;
    $total = (int)($totalRow['total'] ?? 0);

    // Dense rank by average mark for this semester
    $query = "SELECT student_id, AVG(result) AS avg_mark
              FROM marks
              WHERE section_id='$section_id'
                AND academic_year='$academic_year'
                AND semester='$semester'
              GROUP BY student_id
              ORDER BY avg_mark DESC";
    $res = mysqli_query($conn, $query);
    if (!$res) return ['-', $total];
    $rank = 0; $lastAvg = null; $studentRank = '-';
    while ($row = mysqli_fetch_assoc($res)) {
        $avg = (float)$row['avg_mark'];
        if ($lastAvg === null || $avg < $lastAvg - 1e-9) { // new lower score => next rank
            $rank = $rank === 0 ? 1 : $rank + 1;
            $lastAvg = $avg;
        }
        if ((int)$row['student_id'] === $sid) {
            $studentRank = $rank;
            break;
        }
    }
    return [$studentRank, $total];
}

// --- Semester totals & absence ---
$sem1Sum = $sem1Avg = $sem2Sum = $sem2Avg = 0;
$sem1AbsentTotal = $sem2AbsentTotal = 0;
$sem1Rank = $sem2Rank = '-';
$sem1Total = $sem2Total = 0;
if ($semesterSel === 'all' || (string)$semesterSel === '1') {
    list($sem1Sum, $sem1Avg) = calcTotalsBySemester($allSubjects, $marksMap, 1);
    $sem1AbsentTotal = getTotalAbsence($conn, $sid, $section_id, $academic_year, 1);
    list($sem1Rank, $sem1Total) = getClassRank($conn, $section_id, $academic_year, 1, $sid);
}
if ($semesterSel === 'all' || (string)$semesterSel === '2') {
    list($sem2Sum, $sem2Avg) = calcTotalsBySemester($allSubjects, $marksMap, 2);
    $sem2AbsentTotal = getTotalAbsence($conn, $sid, $section_id, $academic_year, 2);
    list($sem2Rank, $sem2Total) = getClassRank($conn, $section_id, $academic_year, 2, $sid);
}
$promotionStatus = ($sem2Avg >= 50) ? 'Promoted' : 'Not Promoted';

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
// Resolve class/grade label
$secRes = mysqli_query($conn, "SELECT section_name, class_type FROM sections WHERE cid = '$section_id' LIMIT 1");
$sec = $secRes ? mysqli_fetch_assoc($secRes) : null;
$sectionLabel = $sec ? ($sec['section_name'].' ('.$sec['class_type'].')') : $section_id;
// Extract numeric grade from section name (e.g., 11A -> 11)
$gradeNum = null;
if ($sec && preg_match('/(\d{1,2})/', $sec['section_name'], $m)) {
    $gradeNum = (int)$m[1];
}
// Compute promoted grade number
$promotedGradeNum = $gradeNum ? $gradeNum + 1 : null;

// Resolve logo paths (used as large header and small center icon)
$logoAbs = realpath(__DIR__ . '/../assets/img/logo.png');
$logoAbs = $logoAbs ? str_replace('\\', '/', $logoAbs) : '';
$logoLarge = $logoAbs ? "<img src='$logoAbs' alt='Logo' style='width:90px;height:auto;display:block;margin:0 auto 6px;'>" : "";
$logoSmall = $logoAbs ? "<img src='$logoAbs' alt='Logo Icon' style='width:36px;height:auto;display:block;margin:6px auto;'>" : "";

$html1 = "
<div style='border:1px solid #000; padding:20px; width:700px; font-family: Arial, Helvetica, sans-serif;'>
    
    <!-- Title Section -->
    <div style='text-align:center; margin-bottom:20px;'>
        " . $logoLarge . "
        <h3 style='margin:0;'>Sidama National Regional Government</h3>
        <h3 style='margin:0;'>ሲዳማ ክልል አስተዳደር </h3>
        <h4 style='margin:0;'>Bilate Zuriya Woreda</h4>
        <h4 style='margin:0;'>ቢላቴ ዙሪያ ወረዳ </h4>
        <h4 style='margin:0;'>Balela Secondary School</h4>
        <h4 style='margin:0;'>ባለላ ከፍተኛ ት/ቤት</h4>
        " . $logoSmall . "
        <h2 style='margin-top:6px;'>የተማሪ ደብዳቤ</h2>
        <h3 style='margin:0;'>STUDENT REPORT CARD</h3>
        <h4 style='margin:0;'>STUDENT REPORT CARD (Sidaamu Afoo)</h4>
    </div>

    <!-- Student Info Section -->
    <div style='margin-top:20px; line-height:2; font-size:15px;'>
        <p>
            <b>የተማሪ ስም (Name of Student):</b> 
            <span style='display:inline-block; border-bottom:1px solid #000; min-width:400px;'>
                $studentFullName
            </span>
        </p>
        
        <p>
            <b>ፆታ (Sex):</b> 
            <span style='display:inline-block; border-bottom:1px solid #000; min-width:100px;'>$student[gender]</span>
            
            &nbsp;&nbsp;&nbsp; 
            
            <b>እድሜ (Age):</b> 
            <span style='display:inline-block; border-bottom:1px solid #000; min-width:100px;'>$age</span>
        </p>
        
        <p>
            <b>Region:</b>
            <span style='display:inline-block; border-bottom:1px solid #000; min-width:160px;'>{$student['region_name']}</span>
            &nbsp;&nbsp;&nbsp;
            <b>Zone:</b>
            <span style='display:inline-block; border-bottom:1px solid #000; min-width:160px;'>{$student['zone_name']}</span>
        </p>
        <p>
            <b>Woreda:</b>
            <span style='display:inline-block; border-bottom:1px solid #000; min-width:160px;'>{$student['woreda_name']}</span>
            &nbsp;&nbsp;&nbsp;
            <b>ቀበሌ (Kebele):</b>
            <span style='display:inline-block; border-bottom:1px solid #000; min-width:120px;'>{$student['kebele']}</span>
        </p>
        
        <p>
            <b>ክፍል (Class / Grade):</b>
            <span style='display:inline-block; border-bottom:1px solid #000; min-width:160px; text-decoration:underline;'>"
            . ($gradeNum ? $gradeNum : $sectionLabel) .
            "</span>
        </p>
        
        <p>
            <b>Promoted to Grade:</b>
            <span style='display:inline-block; border-bottom:1px solid #000; min-width:120px;'>"
            . ($promotionStatus === 'Promoted' && $promotedGradeNum ? $promotedGradeNum : 'Not Promoted') .
            "</span>
        </p>
        <p>
            <b>Academic Year / የትምህርት አመት:</b>
            <span style='display:inline-block; border-bottom:1px solid #000; min-width:160px;'><b>$academic_year</b></span>
        </p>
    </div>

    <!-- Director Section -->
    <div style='margin-top:50px;'>
        <p><b>የዳይሬክተር ስም (Director's Name):</b>______________________</p>
        <p><b>Signature / ፊርማ:</b> ______________________</p>
    </div>

</div>
";


// Add base CSS to prefer Ethiopic-capable fonts
$mpdf->WriteHTML("<style>body{font-family:'Abyssinica SIL','DejaVu Sans',sans-serif;}</style>");
$mpdf->WriteHTML($html1);
// Add second page only when needed
if ($semesterSel === 'all' || $semesterSel === '1' || $semesterSel === '2') {
    $mpdf->AddPage();
}

// --- Page 2: Marks Table ---
$rowsHtml = ''; $yearlyAvgSum = 0; $yearlyAvgCount = 0;
// Map common English subject names to Amharic; fallback to original
$amharicMap = array(
    'Mathematics' => 'ሒሳብ',
    'Math' => 'ሒሳብ',
    'Physics' => 'ፊዚክስ',
    'Chemistry' => 'ኬሚስትሪ',
    'Biology' => 'ባዮሎጂ',
    'English' => 'እንግሊዝኛ',
    'Amharic' => 'አማርኛ',
    'Civics' => 'ሲቪክስ',
    'History' => 'ታሪክ',
    'Geography' => 'ጂኦግራፊ',
    'ICT' => 'አይሲቲ',
    'Physical Education' => 'አካል ብቃት',
    'Economics' => 'ኢኮኖሚክስ'
);
foreach ($allSubjects as $suid => $subName) {
    $subAm = $amharicMap[$subName] ?? $subName;
    $subEn = $subName;
    $mark1 = $marksMap[$suid][1] ?? '-';
    $mark2 = $marksMap[$suid][2] ?? '-';
    // Determine what to show based on selection
    if ($semesterSel === '1') {
        $s1 = $mark1;
        $s2 = '';
        $avgCell = '';
    } elseif ($semesterSel === '2') {
        $s1 = '';
        $s2 = $mark2;
        $avgCell = '';
    } else { // all
        $s1 = $mark1;
        $s2 = $mark2;
        $avg = ($mark1 !== '-' && $mark2 !== '-') ? round(($mark1 + $mark2) / 2, 2) : ($mark1 !== '-' ? $mark1 : ($mark2 !== '-' ? $mark2 : '-'));
        if ($avg !== '-' && $avg !== '') { $yearlyAvgSum += $avg; $yearlyAvgCount++; }
        $avgCell = $avg;
    }
    $rowsHtml .= "<tr>
        <td><div>$subAm</div><div style='font-size:11px;color:#555;'>$subEn</div></td>
        <td align='center'>$s1</td>
        <td align='center'>$s2</td>
        <td align='center'>$avgCell</td>
    </tr>";
}
$yearAvg = $yearlyAvgCount ? round($yearlyAvgSum/$yearlyAvgCount,2) : 0;

// Build marks table according to semester selection
if ($semesterSel === '1') {
    $html2 = "
    <h3>Marks Table / የነጥብ ሰንጠረዥ - 1ኛ መ/ዓ/ት</h3>
    <table border='1' cellpadding='5'>
    <tr><th>Subject / ትምህርት</th><th>1st Sem / 1ኛ መ/ዓ/ት</th></tr>
    ";
    $html2 .= str_replace([
      "<td align='center'>$s2</td>",
      "<td align='center'>$avg</td>"
    ], '', $rowsHtml);
    $html2 .= "
    <tr><td>Absence / የቀረበት ቀን</td><td align='center'>$sem1AbsentTotal</td></tr>
    <tr><td>Total / ጠቅላላ ነጥብ</td><td align='center'>$sem1Sum</td></tr>
    <tr><td>Average / አማካኝ</td><td align='center'>$sem1Avg</td></tr>
    <tr><td>Rank / ደረጃ</td><td align='center'>" . ($sem1Rank === '-' ? '-' : ($sem1Rank . '/' . $sem1Total)) . "</td></tr>
    </table>";
} elseif ($semesterSel === '2') {
    $html2 = "
    <h3>Marks Table / የነጥብ ሰንጠረዥ - 2ኛ መ/ዓ/ት</h3>
    <table border='1' cellpadding='5'>
    <tr><th>Subject / ትምህርት</th><th>2nd Sem / 2ኛ መ/ዓ/ት</th></tr>
    ";
    // Build rows for only 2nd semester values
    $rows2 = '';
    foreach ($allSubjects as $suid => $subName) {
        $s2 = $marksMap[$suid][2] ?? '-';
        $subAm = $amharicMap[$subName] ?? $subName;
        $subEn = $subName;
        $rows2 .= "<tr><td><div>$subAm</div><div style='font-size:11px;color:#555;'>$subEn</div></td><td align='center'>$s2</td></tr>";
    }
    $html2 .= $rows2;
    $html2 .= "
    <tr><td>Absence / የቀረበት ቀን</td><td align='center'>$sem2AbsentTotal</td></tr>
    <tr><td>Total / ጠቅላላ ነጥብ</td><td align='center'>$sem2Sum</td></tr>
    <tr><td>Average / አማካኝ</td><td align='center'>$sem2Avg</td></tr>
    <tr><td>Rank / ደረጃ</td><td align='center'>" . ($sem2Rank === '-' ? '-' : ($sem2Rank . '/' . $sem2Total)) . "</td></tr>
    </table>";
} else { // all
    // Build a unified table with 4 columns; leave non-selected semester/avg blank
    $html2 = "
    <h3>Marks Table / የነጥብ ሰንጠረዥ</h3>
    <table border='1' cellpadding='5'>
    <tr><th>Subject / ትምህርት</th><th>1st Sem / 1ኛ መ/ዓ/ት</th><th>2nd Sem / 2ኛ መ/ዓ/ት</th><th>Average / አማካኝ</th></tr>
    $rowsHtml
    ";
    // Summary rows with conditional blanks
    if ($semesterSel === '1') {
        $html2 .= "
        <tr><td>Absence / የቀረበት ቀን</td><td align='center'>$sem1AbsentTotal</td><td align='center'></td><td></td></tr>
        <tr><td>Total / ጠቅላላ ነጥብ</td><td align='center'>$sem1Sum</td><td align='center'></td><td></td></tr>
        <tr><td>Average / አማካኝ</td><td align='center'>$sem1Avg</td><td align='center'></td><td></td></tr>
        <tr><td>Rank / ደረጃ</td><td align='center'>" . ($sem1Rank === '-' ? '-' : ($sem1Rank . '/' . $sem1Total)) . "</td><td align='center'></td><td></td></tr>
        ";
    } elseif ($semesterSel === '2') {
        $html2 .= "
        <tr><td>Absence / የቀረበት ቀን</td><td align='center'></td><td align='center'>$sem2AbsentTotal</td><td></td></tr>
        <tr><td>Total / ጠቅላላ ነጥብ</td><td align='center'></td><td align='center'>$sem2Sum</td><td></td></tr>
        <tr><td>Average / አማካኝ</td><td align='center'></td><td align='center'>$sem2Avg</td><td></td></tr>
        <tr><td>Rank / ደረጃ</td><td align='center'></td><td align='center'>" . ($sem2Rank === '-' ? '-' : ($sem2Rank . '/' . $sem2Total)) . "</td><td></td></tr>
        ";
    } else {
        $html2 .= "
        <tr><td>Absence / የቀረበት ቀን</td><td align='center'>$sem1AbsentTotal</td><td align='center'>$sem2AbsentTotal</td><td>-</td></tr>
        <tr><td>Total / ጠቅላላ ነጥብ</td><td align='center'>$sem1Sum</td><td align='center'>$sem2Sum</td><td>-</td></tr>
        <tr><td>Average / አማካኝ</td><td align='center'>$sem1Avg</td><td align='center'>$sem2Avg</td><td>$yearAvg</td></tr>
        <tr><td>Rank / ደረጃ</td><td align='center'>" . ($sem1Rank === '-' ? '-' : ($sem1Rank . '/' . $sem1Total)) . "</td><td align='center'>" . ($sem2Rank === '-' ? '-' : ($sem2Rank . '/' . $sem2Total)) . "</td><td>-</td></tr>
        ";
    }
    $html2 .= "</table>";
}

$mpdf->WriteHTML($html2);

// --- Footer ---
$mpdf->SetFooter('Balela Secondary School / ባለላ የላይንኪ ዲሪሚ ሮሲ ሚኒ ሮሳኖቴ ጉማ');

// --- Output PDF ---
$filename = 'ReportCard_'.$student['student_id'].'.pdf';
// Clean any prior output buffers to prevent corrupt PDFs/blank pages
if (ob_get_length()) { @ob_end_clean(); }
if ($mode === 'download') {
    $mpdf->Output($filename, 'D'); // force download
} else { // inline or preview
    // Force inline rendering for iframe in modal
    $mpdf->Output($filename, 'I');
}
