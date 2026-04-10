<?php
include('../connection/connection.php');

header('Content-Type: application/json');

// Accept one or multiple teacher IDs (comma-separated)
$teacher_id_input = isset($_POST['teacher_id']) ? trim($_POST['teacher_id']) : '';
$academic_year = isset($_POST['academic_year']) ? mysqli_real_escape_string($conn, $_POST['academic_year']) : '';

if ($teacher_id_input === '' || $academic_year === '') {
    echo json_encode(['error' => 'Invalid teacher ID or academic year']);
    exit;
}

// Normalize teacher IDs to integers
$teacher_ids = array_filter(array_map(function ($id) {
    return (int)trim($id);
}, explode(',', $teacher_id_input)), function ($id) {
    return $id > 0;
});

if (empty($teacher_ids)) {
    echo json_encode([]);
    exit;
}

$teacher_ids_str = implode(',', $teacher_ids);

// Query assignments for given teachers and academic year
$query = "
    SELECT 
        at.teacher_id AS teacher_id,
        s.subject_name,
        sec.section_name,
        sec.class_type
    FROM assign_teacher at
    JOIN subjects s ON at.subject_id = s.suid
    JOIN sections sec ON at.section_id = sec.cid
    WHERE at.teacher_id IN ($teacher_ids_str)
      AND at.academic_year = '$academic_year'
    ORDER BY at.teacher_id, sec.class_type, sec.section_name, s.subject_name
";

$result = mysqli_query($conn, $query);
if (!$result) {
    echo json_encode(['error' => 'Database query failed']);
    exit;
}

$assignments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $assignments[] = [
        'teacher_id'   => (int)$row['teacher_id'],
        'subject_name' => htmlspecialchars($row['subject_name'], ENT_QUOTES, 'UTF-8'),
        'section_name' => htmlspecialchars($row['section_name'], ENT_QUOTES, 'UTF-8'),
        'class_type'   => htmlspecialchars($row['class_type'], ENT_QUOTES, 'UTF-8')
    ];
}

echo json_encode($assignments);
exit;
