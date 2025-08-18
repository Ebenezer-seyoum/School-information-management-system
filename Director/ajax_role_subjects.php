<?php
include('../connection/connection.php');

$year = $_GET['year'] ?? '';
$class_id = (int)($_GET['class_id'] ?? 0);

if(!$year || !$class_id){ 
    echo json_encode([]); 
    exit; 
}

// Escape year to prevent SQL injection
$year_safe = mysqli_real_escape_string($conn, $year);

$sql = "
    SELECT 
        sub.suid, 
        sub.subject_name, 
        sub.abbreviation_name, 
        CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.father_name,'')) AS teacher_name
    FROM curriculum_subjects cs
    JOIN subjects sub 
        ON cs.subject_id = sub.suid
    LEFT JOIN assign_teacher at 
        ON at.subject_id = sub.suid 
        AND at.section_id = cs.class_id 
        AND at.academic_year = '$year_safe'
    LEFT JOIN users u 
        ON at.teacher_id = u.uid
    WHERE cs.class_id = $class_id
    ORDER BY sub.subject_name
";

$res = mysqli_query($conn, $sql);

$subjects = [];
while($row = mysqli_fetch_assoc($res)){
    // If no teacher assigned, set as null
    if(trim($row['teacher_name']) === ''){
        $row['teacher_name'] = null;
    }
    $subjects[] = $row;
}

header('Content-Type: application/json');
echo json_encode($subjects);
