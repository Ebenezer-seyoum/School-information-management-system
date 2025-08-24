<?php
include('../connection/connection.php');

$section_id    = $_POST['section_id'] ?? null;
$academic_year = $_POST['academic_year'] ?? null;

if(!$section_id || !$academic_year){
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT s.sid, s.student_id, s.first_name, s.father_name, s.student_photo,
           sec.class_type
    FROM assign_student ast
    JOIN students s ON ast.student_id = s.sid
    JOIN sections sec ON ast.section_id = sec.cid
    WHERE ast.section_id = '$section_id' AND ast.academic_year = '$academic_year'
    ORDER BY s.first_name ASC
";

$result   = mysqli_query($conn, $sql);
$students = [];

while($row = mysqli_fetch_assoc($result)){
    $students[] = $row;
}

echo json_encode($students);
