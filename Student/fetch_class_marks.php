<?php
session_start();
include('../connection/connection.php');

$student_id = $_SESSION['student_id'] ?? null;
$section_id = (int)($_POST['section_id'] ?? 0);
$academic_year = mysqli_real_escape_string($conn, $_POST['academic_year'] ?? '');
$semester = (int)($_POST['semester'] ?? 0);

if (!$student_id || !$section_id || !$academic_year || !$semester){
    echo json_encode([]);
    exit;
}

// Get student sid via JOIN
$check = mysqli_query($conn, "
    SELECT s.sid
    FROM assign_student ast
    JOIN students s ON ast.student_id = s.sid
    WHERE s.student_id='$student_id'
      AND ast.section_id=$section_id
      AND ast.academic_year='$academic_year'
      AND ast.semester=$semester
    LIMIT 1
") or die(mysqli_error($conn));

if (mysqli_num_rows($check) == 0){
    echo json_encode([['subject_name'=>'Student not assigned','marks'=>'N/A']]);
    exit;
}

$row = mysqli_fetch_assoc($check);
$student_sid = $row['sid'];

// Fetch subjects + marks
$q = mysqli_query($conn, "
    SELECT sub.subject_name,
           (SELECT result 
            FROM marks m
            WHERE m.subject_id=sub.suid
              AND m.student_id=$student_sid
              AND m.section_id=$section_id
              AND m.academic_year='$academic_year'
              AND m.semester=$semester
            LIMIT 1) AS marks
    FROM curriculum_subjects cs
    INNER JOIN subjects sub ON cs.subject_id=sub.suid
    WHERE cs.class_id=$section_id
    ORDER BY sub.subject_name ASC
") or die(mysqli_error($conn));

$result = [];
while($row = mysqli_fetch_assoc($q)){
    $result[] = [
        'subject_name'=>$row['subject_name'],
        'marks'=>$row['marks'] !== null ? $row['marks'] : 'N/A'
    ];
}

header('Content-Type: application/json');
echo json_encode($result);
