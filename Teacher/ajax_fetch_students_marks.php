<?php
session_start();
include('../connection/connection.php');
include('../connection/function.php');

$uid = $_SESSION['uid'] ?? 0;
$profile = getUserByID($uid);
if(getRoleNameById($profile['user_type'])!=='Teacher'){ echo "Not authorized"; exit; }

$atid = (int)($_GET['atid'] ?? 0);
$section = (int)($_GET['section_id'] ?? 0);
$subject = (int)($_GET['subject_id'] ?? 0);
$year = mysqli_real_escape_string($conn,$_GET['year'] ?? '');
$semester = (int)($_GET['semester'] ?? 1);

if(!$atid || !$section || !$subject || !$year){ echo "Missing parameters"; exit; }

// verify assignment
$res=mysqli_query($conn,"SELECT 1 FROM assign_teacher WHERE atid=$atid AND teacher_id=$uid AND section_id=$section AND subject_id=$subject AND academic_year='$year' LIMIT 1");
if(mysqli_num_rows($res)==0){ echo "Not assigned"; exit; }

// fetch students
$students=mysqli_query($conn,"SELECT s.sid,s.first_name,s.father_name,s.student_id , s.grand_father_name
    FROM assign_student ast
    JOIN students s ON ast.student_id=s.sid
    WHERE ast.section_id=$section AND ast.academic_year='$year'
    ORDER BY s.first_name ASC");

if(mysqli_num_rows($students)==0){ echo "<div class='alert alert-warning'>No students found.</div>"; exit; }

$subjectAbbr = mysqli_fetch_assoc(mysqli_query($conn,"SELECT abbreviation_name AS subject_abbr FROM subjects WHERE suid=$subject LIMIT 1"))['subject_abbr'] ?? '';

echo '<table class="table table-bordered text-center align-middle"><thead><tr><th>#</th><th>Student ID</th><th>Full Name</th><th>Mark</th></tr></thead><tbody>';

$no=1;
while($s=mysqli_fetch_assoc($students)){
    $sid = (int)$s['sid'];
    $full = $s['first_name'].' '.$s['father_name'] .' '.$s['grand_father_name'];

    $markData=mysqli_fetch_assoc(mysqli_query($conn,"SELECT result,mark_status FROM marks
        WHERE student_id=$sid AND section_id=$section AND subject_id=$subject AND academic_year='$year' AND semester=$semester LIMIT 1"));

    $existing = $markData['result'] ?? '';
    $status = (int)($markData['mark_status'] ?? 0);
    $readonly = ($existing !== '' && $status==2) ? 'readonly' : '';
    $saveDisabled = ($existing !== '' && $status==2) ? 'disabled' : '';

    echo "<tr>
        <td>$no</td>
        <td>{$s['student_id']}</td>
        <td class='text-start'>$full</td>
        <td>
            <input type='hidden' name='student_id[]' value='$sid'>
            <input type='number' id='markInput$sid' name='mark[]' class='form-control text-center' min='0' max='100' value='$existing' $readonly>
        </td>
    </tr>";
    $no++;
}
echo '</tbody></table>';
