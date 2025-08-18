<?php
include('../connection/connection.php'); // adjust path

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
if($class_id <= 0){ echo "<div class='text-danger'>Invalid class ID</div>"; exit; }

$res = mysqli_query($conn, "SELECT u.sid, CONCAT(u.first_name, ' ', u.father_name, ' ', u.grand_father_name) AS full_name, u.gender
                            FROM assign_student ast
                            LEFT JOIN students u ON ast.student_id = u.sid
                            WHERE ast.section_id = (
                                SELECT section_id FROM assign_instructor WHERE hid = $class_id LIMIT 1
                            )
                            ORDER BY u.first_name ASC");

if(mysqli_num_rows($res) > 0){
    echo '<table class="table table-hover text-center align-middle">
            <thead class="table-secondary">
              <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Gender</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>';
    $no=1;
    while($s = mysqli_fetch_assoc($res)){
        echo "<tr>
                <td>".$no++."</td>
                <td>".htmlspecialchars($s['full_name'])."</td>
                <td>".htmlspecialchars($s['gender'])."</td>
                <td><a href='student_profile.php?sid=".$s['sid']."' class='btn btn-info btn-sm'>View Profile</a></td>
              </tr>";
    }
    echo '</tbody></table>';
}else{
    echo "<div class='text-danger'>No students assigned to this class.</div>";
}
?>
