<?php
include('../connection/connection.php'); 

if (isset($_GET['section_id'], $_GET['academic_year'])) {
    $section_id = $_GET['section_id'];
    $academic_year = mysqli_real_escape_string($conn, $_GET['academic_year']);

    $query = "SELECT s.sid, s.student_id, s.first_name, s.father_name, s.mother_name, s.gender, s.dob
              FROM assign_student a
              JOIN students s ON a.student_id = s.sid
              WHERE a.section_id='$section_id' AND a.academic_year='$academic_year'
              ORDER BY s.first_name ASC";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo '<table class="table table-bordered">';
        echo '<thead><tr>
                <th>ID</th>
                <th>Name</th>
                <th>Action</th>
              </tr></thead><tbody>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>
                    <td>' . $row['student_id'] . '</td>
                    <td>' . $row['first_name'] . ' ' . $row['father_name'] . '</td>
                    <td>
                      <a href="fetch_studentDetail.php?sid=' . htmlspecialchars($row['sid']) . '" class="btn btn-sm btn-primary">View Details</a>
                    </td>
                  </tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<div class="text-danger">No students assigned for this academic year.</div>';
    }
}
?>
