<?php
session_start();
include('../connection/connection.php');
include('../connection/function.php');

$student_id = (int)($_GET['student_id'] ?? 0);
$year = mysqli_real_escape_string($conn, $_GET['year'] ?? '');
$semester = (int)($_GET['semester'] ?? 1);

if(!$student_id || !$year){
    echo json_encode([]);
    exit;
}

// Corrected query
$sql = "SELECT sub.subject_name, t.first_name AS teacher_name, m.result
        FROM marks m
        JOIN subjects sub ON sub.suid = m.subject_id
        JOIN users t ON t.uid = m.teacher_id
        WHERE m.student_id = $student_id
          AND m.academic_year = '$year'
          AND m.semester = $semester
        ORDER BY sub.subject_name";

$result = mysqli_query($conn, $sql);
$data = [];
while($row = mysqli_fetch_assoc($result)){
    $data[] = [
        'subject_name' => $row['subject_name'],
        'teacher_name' => $row['teacher_name'],
        'result' => $row['result']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
?>
