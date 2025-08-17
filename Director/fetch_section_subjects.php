<?php
include('../connection/connection.php');

$section_id = (int)($_POST['section_id'] ?? 0);

$result = [];
if($section_id){
    // Fetch subjects for this section from curriculum_subjects
    $subjects = mysqli_query($conn, "
        SELECT s.suid, s.subject_name, s.abbreviation_name
        FROM curriculum_subjects cs
        JOIN subjects s ON cs.subject_id = s.suid
        WHERE cs.class_id = $section_id
        ORDER BY s.subject_name ASC
    ");

    while($sub = mysqli_fetch_assoc($subjects)){
        $result[] = [
            'suid' => $sub['suid'],
            'subject_name' => $sub['subject_name'],
            'abbreviation_name' => $sub['abbreviation_name']
        ];
    }
}

// Return JSON
echo json_encode($result);
