<?php
include('../connection/connection.php');

$year       = $_GET['year'] ?? '';
$class_id   = (int)($_GET['class_id'] ?? 0);
$subject_id = (int)($_GET['subject_id'] ?? 0);
$semester   = (int)($_GET['semester'] ?? 1);

if (!$year || !$class_id) {
    echo json_encode([]);
    exit;
}

if ($subject_id === 0) {
    // Students only — no marks yet
    $sql = "SELECT s.sid AS id, s.student_id, CONCAT(s.first_name,' ',s.father_name) AS student_name,
                   0 AS mark_id,
                   1 AS mark_status, 
                   NULL AS result  -- no mark yet
            FROM assign_student ast
            JOIN students s ON ast.student_id = s.sid
            WHERE ast.section_id = $class_id
              AND ast.academic_year = '$year'
            ORDER BY s.first_name, s.father_name";
} else {
    // Students with marks if exist
    $sql = "SELECT s.sid AS id, s.student_id, CONCAT(s.first_name,' ',s.father_name) AS student_name, 
                   COALESCE(m.mid, 0) AS mark_id,
                   COALESCE(m.mark_status, 1) AS mark_status,
                   COALESCE(m.result, 0) AS result
            FROM assign_student ast
            JOIN students s ON ast.student_id = s.sid
            LEFT JOIN marks m ON m.student_id = s.sid 
                               AND m.section_id = ast.section_id
                               AND m.subject_id = $subject_id
                               AND m.academic_year = '$year'
                               AND m.semester = $semester
            WHERE ast.section_id = $class_id
              AND ast.academic_year = '$year'
            ORDER BY s.first_name, s.father_name";
}

$res = mysqli_query($conn, $sql);
$students = [];
while ($row = mysqli_fetch_assoc($res)) {
    $students[] = $row;
}

echo json_encode($students);
