<?php
session_start();
include('../connection/connection.php');
include('../connection/function.php');

$uid = $_SESSION['uid'] ?? 0;
$profile = getUserByID($uid);
if(getRoleNameById($profile['user_type'])!=='Director'){ 
    echo '<p class="text-danger">Not authorized</p>'; exit; 
}

$section_id  = (int)($_GET['section_id'] ?? 0);
$subject_id  = (int)($_GET['subject_id'] ?? 0);
$academic_year = mysqli_real_escape_string($conn,$_GET['year'] ?? '');
$semester = (int)($_GET['semester'] ?? 1);

if(!$section_id || !$subject_id || !$academic_year){
    echo '<p class="text-danger">Invalid request</p>'; exit;
}

// Get assigned teacher for this subject & section
$teacher_res = mysqli_query($conn,"SELECT teacher_id FROM assign_teacher 
    WHERE section_id=$section_id AND subject_id=$subject_id 
      AND academic_year='$academic_year' LIMIT 1");
$teacher_name = '';
if(mysqli_num_rows($teacher_res)>0){
    $teacher_id = mysqli_fetch_assoc($teacher_res)['teacher_id'];
    $tdata = getUserByID($teacher_id);
    $teacher_name = $tdata['first_name'].' '.$tdata['father_name'];
}

// Fetch students with existing marks
$sql = "SELECT st.sid, st.student_id, st.first_name, st.father_name, st.grand_father_name,
               COALESCE(m.result,'') AS marks
        FROM assign_student ast
        JOIN students st ON ast.student_id = st.sid
        LEFT JOIN marks m ON m.student_id = st.sid
            AND m.section_id = ast.section_id
            AND m.subject_id = $subject_id
            AND m.academic_year = '$academic_year'
            AND m.semester = $semester
        WHERE ast.section_id = $section_id
          AND ast.academic_year = '$academic_year'
        ORDER BY st.first_name ASC, st.father_name ASC";

$res = mysqli_query($conn,$sql);
if(!$res){ echo '<p class="text-danger">DB Error: '.mysqli_error($conn).'</p>'; exit; }

if(mysqli_num_rows($res)==0){
    echo '<p class="text-danger">No students found.</p>'; exit;
}

// Display table
echo '<table class="table table-bordered table-hover text-center align-middle">';
echo '<thead class="table-secondary">';
echo '<tr><th>#</th><th>Student ID</th><th>Full Name</th><th>Mark</th><th>Assigned Teacher</th></tr>';
echo '</thead><tbody>';

$no = 1;
while($row = mysqli_fetch_assoc($res)){
    $full_name = $row['first_name'].' '.$row['father_name'].' '.$row['grand_father_name'];
    $mark_value = htmlspecialchars($row['marks']);
    echo '<tr>';
    echo '<td>'.$no++.'</td>';
    echo '<td>'.$row['student_id'].'</td>';
    echo '<td>'.$full_name.'</td>';
    echo '<td><input type="number" min="0" max="100" class="form-control text-center mark-input" name="mark['.$row['sid'].']" value="'.$mark_value.'"></td>';
    echo '<td>'.$teacher_name.'</td>';
    echo '</tr>';
}

echo '</tbody></table>';
?>
