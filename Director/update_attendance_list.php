<?php
include('../connection/connection.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success'=>false, 'message'=>'Invalid request']);
  exit;
}

$section_id    = isset($_POST['section_id']) ? (int)$_POST['section_id'] : 0;
$academic_year = isset($_POST['academic_year']) ? mysqli_real_escape_string($conn, $_POST['academic_year']) : '';
$semester      = isset($_POST['semester']) ? (int)$_POST['semester'] : 1;
$attendance    = isset($_POST['attendance']) ? $_POST['attendance'] : [];

if ($section_id<=0 || $academic_year==='' || empty($attendance)) {
  echo json_encode(['success'=>false, 'message'=>'Missing data']);
  exit;
}

$errors = 0;
foreach ($attendance as $studentId => $dates) {
  $studentId = (int)$studentId;
  foreach ($dates as $date => $status) {
    $date   = mysqli_real_escape_string($conn, $date);
    $status = mysqli_real_escape_string($conn, $status);

    if ($status === '') { // optional: skip blanks
      continue;
    }

    // upsert
    $exists = mysqli_query($conn, "SELECT id FROM attendance
                                   WHERE student_id=$studentId
                                     AND section_id=$section_id
                                     AND academic_year='$academic_year'
                                     AND semester=$semester
                                     AND attendance_date='$date'
                                   LIMIT 1");
    if ($exists && mysqli_num_rows($exists) > 0) {
      $q = "UPDATE attendance SET status='$status'
            WHERE student_id=$studentId
              AND section_id=$section_id
              AND academic_year='$academic_year'
              AND semester=$semester
              AND attendance_date='$date'";
    } else {
      $q = "INSERT INTO attendance (student_id, section_id, academic_year, semester, attendance_date, status)
            VALUES ($studentId, $section_id, '$academic_year', $semester, '$date', '$status')";
    }

    if (!mysqli_query($conn, $q)) { $errors++; }
  }
}

echo json_encode(['success' => $errors === 0, 'message' => $errors===0 ? 'OK' : 'Some records failed']);
