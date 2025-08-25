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
    $teachers_array[$t['uid']] = htmlspecialchars($t['full_name'], ENT_QUOTES, 'UTF-8');
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
        $assigned_teacher_name = null;
        if ($assigned_teacher) {
            // Map to name from preloaded teachers array
            if (isset($teachers_array[$assigned_teacher])) {
                $assigned_teacher_name = $teachers_array[$assigned_teacher];
            } else {
                // Fallback query (should rarely happen)
                $tq = mysqli_query($conn, "SELECT CONCAT(first_name,' ',father_name) AS full_name FROM users WHERE uid=".(int)$assigned_teacher." LIMIT 1");
                $tr = mysqli_fetch_assoc($tq);
                $assigned_teacher_name = $tr ? htmlspecialchars($tr['full_name']) : null;
            }
        }

        $result[] = [
            'suid' => (int)$sub['suid'],
            'subject_name' => htmlspecialchars($sub['subject_name'], ENT_QUOTES, 'UTF-8'),
            'assigned_teacher' => $assigned_teacher,
            'assigned_teacher_name' => $assigned_teacher_name,
            'teachers' => $teachers_array
        ];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($result);
