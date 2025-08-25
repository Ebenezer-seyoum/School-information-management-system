<?php
include('../connection/connection.php');
session_start();

$student_id   = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$academic_year = isset($_GET['academic_year']) ? mysqli_real_escape_string($conn, $_GET['academic_year']) : '';

if ($student_id <= 0 || !$academic_year){
  echo "<div class='text-danger'>Invalid parameters.</div>";
  exit;
}

// Fetch subjects for this student's section and year
$sqlSec = "SELECT section_id FROM assign_student WHERE student_id=$student_id AND academic_year='$academic_year' LIMIT 1";
$secRes = mysqli_query($conn, $sqlSec);
$rowSec = mysqli_fetch_assoc($secRes);
if (!$rowSec){
  echo "<div class='text-danger'>Student not found for the selected year.</div>";
  exit;
}
$section_id = (int)$rowSec['section_id'];

$subjects = mysqli_query($conn, "
  SELECT s.suid, s.subject_name 
  FROM curriculum_subjects cs 
  JOIN subjects s ON cs.subject_id = s.suid 
  WHERE cs.class_id = $section_id
  ORDER BY s.subject_name ASC
");

// Build marks: semester 1 and 2
$marksBySubject = [];
while($sub = mysqli_fetch_assoc($subjects)){
  $sid = (int)$sub['suid'];
  $marksBySubject[$sid] = [
    'name' => htmlspecialchars($sub['subject_name'], ENT_QUOTES, 'UTF-8'),
    's1' => '',
    's2' => ''
  ];
}

$sqlMarks = "SELECT subject_id, semester, result FROM marks WHERE student_id=$student_id AND section_id=$section_id AND academic_year='$academic_year'";
$resMarks = mysqli_query($conn, $sqlMarks);
while($m = mysqli_fetch_assoc($resMarks)){
  $sid = (int)$m['subject_id'];
  $sem = (int)$m['semester'];
  $val = is_numeric($m['result']) ? (0 + $m['result']) : htmlspecialchars($m['result']);
  if(isset($marksBySubject[$sid])){
    if ($sem === 1) $marksBySubject[$sid]['s1'] = $val;
    if ($sem === 2) $marksBySubject[$sid]['s2'] = $val;
  }
}

// Render table
echo "<div class='table-responsive'>
<table class='table table-bordered align-middle text-center'>
  <thead class='table-secondary'>
    <tr>
      <th>#</th>
      <th>Subject</th>
      <th>Semester 1</th>
      <th>Semester 2</th>
    </tr>
  </thead>
  <tbody>";
$no=1;
foreach($marksBySubject as $info){
  $s1 = ($info['s1'] !== '') ? $info['s1'] : '-';
  $s2 = ($info['s2'] !== '') ? $info['s2'] : '-';
  echo "<tr>
    <td>".$no++."</td>
    <td>".$info['name']."</td>
    <td>".$s1."</td>
    <td>".$s2."</td>
  </tr>";
}

echo "  </tbody>
</table>
</div>";
