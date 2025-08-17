<?php
include('../connection/connection.php');

$section_id = (int)($_POST['section_id'] ?? 0);
$academic_year = mysqli_real_escape_string($conn, $_POST['academic_year'] ?? '');

$result = [];

if($section_id && $academic_year){
    // Fetch subjects for this section from curriculum_subjects
    $subjects_q = mysqli_query($conn, "
        SELECT s.suid, s.subject_name
        FROM curriculum_subjects cs
        JOIN subjects s ON cs.subject_id = s.suid
        WHERE cs.class_id = $section_id
        ORDER BY s.subject_name ASC
    ");

    // Fetch all teachers
    $teachers_q = mysqli_query($conn, "SELECT uid, CONCAT(first_name,' ',father_name) AS full_name FROM users WHERE user_type=1 ORDER BY first_name ASC");
    $teachers_array = [];
    while($t = mysqli_fetch_assoc($teachers_q)){
        $teachers_array[$t['uid']] = htmlspecialchars($t['full_name']);
    }

    while($sub = mysqli_fetch_assoc($subjects_q)){
        // Check if teacher is already assigned for this section + year
        $check_q = mysqli_query($conn, "
            SELECT teacher_id 
            FROM assign_teacher 
            WHERE section_id = $section_id AND subject_id = {$sub['suid']} AND academic_year = '$academic_year'
            LIMIT 1
        ");
        $assigned_teacher = mysqli_fetch_assoc($check_q)['teacher_id'] ?? null;

        $result[] = [
            'suid' => $sub['suid'],
            'subject_name' => $sub['subject_name'],
            'assigned_teacher' => $assigned_teacher,
            'teachers' => $teachers_array
        ];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($result);
