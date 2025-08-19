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

// --- TCPDF Setup ---
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->setFontSubsetting(true);

// --- Colors ---
$primaryColor = [0, 51, 102]; // Dark Blue
$accentColor = [204, 153, 0]; // Gold
$textColor = [0, 0, 0]; // Black
$headerBgColor = [230, 240, 255]; // Light Blue

// --- Fonts ---
$mainFont = 'helvetica'; // Professional and clean
$amharicFont = 'dejavusans'; // Supports Amharic characters

$studentFullName = $student['first_name'] . ' ' . $student['father_name'] . ' ' . $student['mother_name'];

// ---------------- PAGE 1 ----------------
$pdf->AddPage();

// Background Image (subtle watermark effect)
$pdf->Image('../images/card-2.png', 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0, false, false, true);

// School Logo and Header
$pdf->Image('../images/logo.png', 85, 10, 40, 0, '', '', '', false, 300);
$pdf->SetFont($mainFont, 'B', 16);
$pdf->SetTextColorArray($primaryColor);
$pdf->SetXY(15, 30);
$pdf->Cell(180, 10, 'SCHOOL NAME REPORT CARD', 0, 1, 'C');
$pdf->SetFont($mainFont, '', 10);
$pdf->SetTextColorArray($textColor);
$pdf->SetXY(15, 38);
$pdf->Cell(180, 6, 'Academic Year: ' . $academic_year, 0, 1, 'C');

// Student Information Box
$pdf->SetFont($mainFont, 'B', 12);
$pdf->SetXY(15, 50);
$pdf->SetFillColorArray($headerBgColor);
$pdf->Cell(180, 8, 'Student Information', 1, 1, 'C', 1);

$pdf->SetFont($mainFont, '', 11);
$pdf->SetXY(20, 60);
$pdf->Cell(50, 8, 'Name:', 0, 0, 'L');
$pdf->SetFont($mainFont, 'B', 11);
$pdf->Cell(120, 8, $studentFullName, 0, 1, 'L');

$pdf->SetFont($mainFont, '', 11);
$pdf->SetXY(20, 68);
$pdf->Cell(50, 8, 'Gender:', 0, 0, 'L');
$pdf->Cell(120, 8, $student['gender'], 0, 1, 'L');

$pdf->SetXY(20, 76);
$pdf->Cell(50, 8, 'Age:', 0, 0, 'L');
$pdf->Cell(120, 8, $age . ' years', 0, 1, 'L');

$pdf->SetXY(20, 84);
$pdf->Cell(50, 8, 'Address:', 0, 0, 'L');
$pdf->Cell(120, 8, $student['woreda'] . ', ' . $student['kebele'], 0, 1, 'L');

$pdf->SetXY(20, 92);
$pdf->Cell(50, 8, 'Class:', 0, 0, 'L');
$pdf->Cell(120, 8, $section_id, 0, 1, 'L');

$pdf->SetXY(20, 100);
$pdf->Cell(50, 8, 'Promotion Status:', 0, 0, 'L');
$pdf->SetFont($mainFont, 'B', 11);
$pdf->SetTextColorArray($promotionStatus === 'Promoted' ? [0, 128, 0] : [255, 0, 0]);
$pdf->Cell(120, 8, $promotionStatus, 0, 1, 'L');
$pdf->SetTextColorArray($textColor);

// Footer Note
$pdf->SetFont($mainFont, 'I', 9);
$pdf->SetXY(15, 260);
$pdf->Cell(180, 6, 'Generated by School Management System', 0, 1, 'C');

// ---------------- PAGE 2 ----------------
$pdf->AddPage();

// Marks Table
$rowsHtml = '';
$yearlyAvgSum = 0;
$yearlyAvgCount = 0;

foreach ($allSubjects as $suid => $subName) {
    $s1 = $marksMap[$suid][1] ?? '-';
    $s2 = $marksMap[$suid][2] ?? '-';
    $avg = ($s1 !== '-' && $s2 !== '-') ? round(($s1 + $s2) / 2, 2) : ($s1 !== '-' ? $s1 : ($s2 !== '-' ? $s2 : '-'));
    if ($avg !== '-') {
        $yearlyAvgSum += $avg;
        $yearlyAvgCount++;
    }

    $bgColor = ($yearlyAvgCount % 2 == 0) ? 'background-color:#f5f7fa;' : '';
    $rowsHtml .= "
    <tr style='$bgColor'>
      <td>$subName</td>
      <td align='center'>$s1</td>
      <td align='center'>$s2</td>
      <td align='center'>$avg</td>
    </tr>
    ";
}

$yearAvg = $yearlyAvgCount ? round($yearlyAvgSum / $yearlyAvgCount, 2) : 0;

$html2 = '
<style>
  table { width: 100%; border-collapse: collapse; font-size: 11px; }
  th { background-color: #e6f0ff; color: #003366; font-weight: bold; padding: 8px; }
  td { padding: 8px; border: 1px solid #003366; }
  h4 { color: #003366; text-align: center; font-size: 14px; margin-bottom: 10px; }
</style>
<h4>የነጥብ ሰንጠረዥ / Marks Table</h4>
<table border="1">
  <tr>
    <th>Subject / ትምህርት</th>
    <th>1ኛ መ/ዓ/ት / 1st Semester</th>
    <th>2ኛ መ/ዓ/ት / 2nd Semester</th>
    <th>Average / አማካኝ ዉጤት</th>
  </tr>
  ' . $rowsHtml . '
  <tr style="background-color:#f5f7fa;">
    <td>Absence / የቀረበት ቀን</td>
    <td align="center">' . $sem1AbsentTotal . '</td>
    <td align="center">' . $sem2AbsentTotal . '</td>
    <td align="center">-</td>
  </tr>
  <tr>
    <td>Total / ጠቅላላ ነጥብ</td>
    <td align="center">' . $sem1Sum . '</td>
    <td align="center">' . $sem2Sum . '</td>
    <td align="center">-</td>
  </tr>
  <tr style="background-color:#f5f7fa;">
    <td>Average / አማካኝ</td>
    <td align="center">' . $sem1Avg . '</td>
    <td align="center">' . $sem2Avg . '</td>
    <td align="center">' . $yearAvg . '</td>
  </tr>
  <tr>
    <td>Rank / የክፍል ደረጃ</td>
    <td align="center">' . $sem1Rank . '</td>
    <td align="center">' . $sem2Rank . '</td>
    <td align="center">-</td>
  </tr>
</table>
';

// Write Marks Table
$pdf->SetFont($amharicFont, '', 11);
$pdf->writeHTML($html2, true, false, true, false, '');

// Footer Note
$pdf->SetFont($mainFont, 'I', 9);
$pdf->SetXY(15, 260);
$pdf->Cell(180, 6, 'Generated by School Management System', 0, 1, 'C');

// Output
$pdf->Output('ReportCard_' . $student['student_id'] . '.pdf', 'I');
?>