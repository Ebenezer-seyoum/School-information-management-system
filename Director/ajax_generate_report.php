<?php
// IMPORTANT: Ensure this file is saved as UTF-8 (no BOM).
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

// Age
$dob   = new DateTime($student['dob']);
$today = new DateTime();
$age   = $today->diff($dob)->y;

// --- Fetch ALL subjects for this section/class (even if no mark yet) ---
$allSubjects = [];     // [suid => subject_name]
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

// --- Fetch marks for this student for both semesters ---
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
    $sum = 0;
    $cnt = 0;
    foreach ($allSubjects as $suid => $name) {
        if (isset($marksMap[$suid][$semester]) && $marksMap[$suid][$semester] !== '' && $marksMap[$suid][$semester] !== null) {
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

list($sem1Sum, $sem1Avg) = calcTotalsBySemester($allSubjects, $marksMap, 1);
list($sem2Sum, $sem2Avg) = calcTotalsBySemester($allSubjects, $marksMap, 2);

$sem1AbsentTotal = getTotalAbsence($conn, $sid, $section_id, $academic_year, 1);
$sem2AbsentTotal = getTotalAbsence($conn, $sid, $section_id, $academic_year, 2);

// Promotion decision based on Sem 2 average (change if your rule differs)
$promotionStatus = ($sem2Avg >= 50) ? 'Promoted' : 'Not Promoted';

// --- Class Rank per semester ---
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

// --- Init TCPDF ---
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
// metadata
$pdf->SetCreator('Balela School');
$pdf->SetAuthor('Balela Secondary School');
$pdf->SetTitle('Student Report Card');

// no default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// margins & flow
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);

// Use Unicode font that supports Amharic
// DejaVuSans is bundled with TCPDF and supports Ethiopic.
$pdf->setFontSubsetting(true);
$pdf->SetFont('dejavusans', '', 12);

// ------------- PAGE 1 (pure red bg) -------------
$pdf->AddPage();

// pure red background fill (whole page)
$pdf->SetFillColor(255, 0, 0);
$pdf->Rect(0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'F');

// content (NO white box backgrounds; keep everything transparent)
$studentFullName = htmlspecialchars($student['first_name'].' '.$student['father_name'].' '.$student['mother_name']);
$gender          = htmlspecialchars($student['gender']);
$address         = htmlspecialchars($student['woreda'].' - '.$student['kebele']);
$studIdStr       = htmlspecialchars($student['student_id']);

$html1 = '
<div>
  <h3 style="text-align:center; margin:4px 0;">ሲዳማ ሕጋዊ ክልል መንግስት / Sidama National Regional Government</h3>
  <h4 style="text-align:center; margin:4px 0;">ዞን / Zone: ሲዳማ / Sidama</h4>
  <h4 style="text-align:center; margin:4px 0;">ወረዳ / Woreda: ቢላቴ ዙሪያ / Bilate Zuriya</h4>
  <h4 style="text-align:center; margin:8px 0;">የተማሪ የሪፖርት ካርድ / Student Report Card - ባለስልጣን ትምህርት ቤት / Balela Secondary School</h4>

  <table cellpadding="6" style="font-size:12px; width:100%; border-collapse:collapse;">
    <tr>
      <td style="width:35%; border:1px solid #000;"><strong>የተማሪ ስም / Name of Student</strong></td>
      <td style="width:65%; border:1px solid #000;">'.$studentFullName.'</td>
    </tr>
    <tr>
      <td style="border:1px solid #000;"><strong>መለያ ቁጥር / Student ID</strong></td>
      <td style="border:1px solid #000;">'.$studIdStr.'</td>
    </tr>
    <tr>
      <td style="border:1px solid #000;"><strong>ፆታ / Sex</strong></td>
      <td style="border:1px solid #000;">'.$gender.'</td>
    </tr>
    <tr>
      <td style="border:1px solid #000;"><strong>እድሜ / Age</strong></td>
      <td style="border:1px solid #000;">'.$age.'</td>
    </tr>
    <tr>
      <td style="border:1px solid #000;"><strong>አድራሻ / Address</strong></td>
      <td style="border:1px solid #000;">'.$address.'</td>
    </tr>
    <tr>
      <td style="border:1px solid #000;"><strong>ት/ዓ / Academic Year</strong></td>
      <td style="border:1px solid #000;">'.htmlspecialchars($academic_year).'</td>
    </tr>
    <tr>
      <td style="border:1px solid #000;"><strong>ውጤት / Promotion</strong></td>
      <td style="border:1px solid #000;">'.$promotionStatus.'</td>
    </tr>
  </table>
</div>
';

$pdf->writeHTML($html1, true, false, true, false, '');


// ------------- PAGE 2 (pure red bg) -------------
$pdf->AddPage();

// pure red background fill (whole page)
$pdf->SetFillColor(255, 0, 0);
$pdf->Rect(0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'F');

// Marks table (NO header row; black borders; list all section subjects; two absence rows BEFORE totals)
$rowsHtml = '';
$yearlyAvgSum = 0;
$yearlyAvgCount = 0;
foreach ($allSubjects as $suid => $subName) {
  $has1 = isset($marksMap[$suid][1]) && $marksMap[$suid][1] !== '' && $marksMap[$suid][1] !== null;
  $has2 = isset($marksMap[$suid][2]) && $marksMap[$suid][2] !== '' && $marksMap[$suid][2] !== null;
  $sem1Val = $has1 ? (float)$marksMap[$suid][1] : '-';
  $sem2Val = $has2 ? (float)$marksMap[$suid][2] : '-';

  // per-subject average across available semesters
  $present = 0; $sum = 0;
  if ($has1) { $present++; $sum += (float)$marksMap[$suid][1]; }
  if ($has2) { $present++; $sum += (float)$marksMap[$suid][2]; }
  $rowAvg = $present > 0 ? round($sum / $present, 2) : '-';
  if ($present > 0) { $yearlyAvgSum += ($sum / $present); $yearlyAvgCount++; }

  $rowsHtml .= '
    <tr>
      <td style="border:1px solid #000; padding:6px;">'.htmlspecialchars($subName).'</td>
      <td style="border:1px solid #000; padding:6px; text-align:center;">'.$sem1Val.'</td>
      <td style="border:1px solid #000; padding:6px; text-align:center;">'.$sem2Val.'</td>
      <td style="border:1px solid #000; padding:6px; text-align:center;">'.$rowAvg.'</td>
    </tr>
  ';
}

$yearAvg = $yearlyAvgCount > 0 ? round($yearlyAvgSum / $yearlyAvgCount, 2) : 0;

// Single Absence row with both semesters
$absenceRow = '
  <tr>
    <td style="border:1px solid #000; padding:6px;"><strong>ጠቅላላ ጉድለት / Total Absence</strong></td>
    <td style="border:1px solid #000; padding:6px; text-align:center;">'.$sem1AbsentTotal.'</td>
    <td style="border:1px solid #000; padding:6px; text-align:center;">'.$sem2AbsentTotal.'</td>
    <td style="border:1px solid #000; padding:6px; text-align:center;">-</td>
  </tr>
';

// Totals / Averages / Ranks (4 columns)
$summaryRows = '
  <tr>
    <td style="border:1px solid #000; padding:6px;"><strong>ጠቅላላ ነጥብ / Total</strong></td>
    <td style="border:1px solid #000; padding:6px; text-align:center;">'.$sem1Sum.'</td>
    <td style="border:1px solid #000; padding:6px; text-align:center;">'.$sem2Sum.'</td>
    <td style="border:1px solid #000; padding:6px; text-align:center;">-</td>
  </tr>
  <tr>
    <td style="border:1px solid #000; padding:6px;"><strong>አማካኝ ነጥብ / Average</strong></td>
    <td style="border:1px solid #000; padding:6px; text-align:center;">'.$sem1Avg.'</td>
    <td style="border:1px solid #000; padding:6px; text-align:center;">'.$sem2Avg.'</td>
    <td style="border:1px solid #000; padding:6px; text-align:center;">'.$yearAvg.'</td>
  </tr>
  <tr>
    <td style="border:1px solid #000; padding:6px;"><strong>የክፍል ደረጃ / Class Rank</strong></td>
    <td style="border:1px solid #000; padding:6px; text-align:center;">'.$sem1Rank.'</td>
    <td style="border:1px solid #000; padding:6px; text-align:center;">'.$sem2Rank.'</td>
    <td style="border:1px solid #000; padding:6px; text-align:center;">-</td>
  </tr>
';

// Header row
$headerRow = '
  <tr>
    <th style="border:1px solid #000; padding:6px; text-align:left;">Subject</th>
    <th style="border:1px solid #000; padding:6px; text-align:center;">1st Semester</th>
    <th style="border:1px solid #000; padding:6px; text-align:center;">2nd Semester</th>
    <th style="border:1px solid #000; padding:6px; text-align:center;">Average</th>
  </tr>
';

$html2 = '
<div>
  <h4 style="text-align:center; margin:4px 0;">የነጥብ ሰንጠረዥ / Marks Table</h4>
  <table cellpadding="0" cellspacing="0" style="width:100%; border-collapse:collapse; font-size:12px;">
  '.$headerRow.'
  '.$rowsHtml.'
  '.$absenceRow.'
  '.$summaryRows.'
  </table>
</div>
';

$pdf->writeHTML($html2, true, false, true, false, '');

// --- Output PDF ---
$pdf->Output('ReportCard_'.$student['student_id'].'.pdf', 'I');
