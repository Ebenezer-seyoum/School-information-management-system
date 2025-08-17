<?php
session_start();
include('../connection/connection.php');

$student_sid = $_SESSION['sid'] ?? '';
$section_id = (int)($_POST['section_id'] ?? 0);
$academic_year = mysqli_real_escape_string($conn, $_POST['academic_year'] ?? '');
$result = [];

if($student_sid && $section_id && $academic_year){
    // Check if student is assigned to this class
    $check = mysqli_query($conn, "
        SELECT 1 
        FROM assign_student ast
         JOIN sections c ON ast.section_id = c.cid
         JOIN students s ON ast.student_id = s.sid
        WHERE s.student_id = '$student_sid'
          AND c.cid = $section_id
          AND ast.academic_year = '$academic_year'
        LIMIT 1
    ") or die(mysqli_error($conn));

    if(mysqli_num_rows($check) > 0){
        // Fetch subjects and teacher
        $q = mysqli_query($conn, "
            SELECT sub.subject_name,
                   CONCAT(u.first_name,' ',u.father_name) AS teacher_name
            FROM curriculum_subjects cs
            JOIN subjects sub ON cs.subject_id = sub.suid
            LEFT JOIN assign_teacher at ON at.subject_id = sub.suid 
                                       AND at.section_id = $section_id
                                       AND at.academic_year = '$academic_year'
            LEFT JOIN users u ON at.teacher_id = u.uid
            WHERE cs.class_id = $section_id
            ORDER BY sub.subject_name ASC
        ") or die(mysqli_error($conn));

        while($row = mysqli_fetch_assoc($q)){
            $result[] = [
                'subject_name' => $row['subject_name'],
                'teacher_name' => $row['teacher_name'] ?? 'N/A'
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($result);
