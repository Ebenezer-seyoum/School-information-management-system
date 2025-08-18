<?php
include('../connection/connection.php'); 

if (isset($_GET['section_id'], $_GET['academic_year'], $_GET['semester'])) {
    $section_id = $_GET['section_id'];
    $academic_year = mysqli_real_escape_string($conn, $_GET['academic_year']);
    $semester = (int)$_GET['semester']; // 1 or 2

    $query = "SELECT s.sid, s.student_id, s.first_name, s.father_name, s.mother_name, s.gender, s.dob
              FROM assign_student a
              JOIN students s ON a.student_id = s.sid
              WHERE a.section_id='$section_id' AND a.academic_year='$academic_year'
              ORDER BY s.first_name ASC";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        ?>
        <!-- Search Bar -->
        <div class="mb-3">
            <input type="text" id="studentSearch" class="form-control" placeholder="Search student by name or ID...">
        </div>

        <!-- Student Table -->
        <table class="table table-bordered table-striped" id="studentTable">
            <thead class="table-primary">
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['student_id']) . '</td>
                        <td>' . htmlspecialchars($row['first_name'] . ' ' . $row['father_name']) . '</td>
                        <td>
                          <a href="ajax_generate_report.php?sid=' . urlencode($row['sid']) . 
                             '&section_id=' . $section_id . 
                             '&academic_year=' . urlencode($academic_year) . 
                             '&semester=' . $semester . '" 
                             class="btn btn-sm btn-primary">Generate</a>
                        </td>
                      </tr>';
            }
            ?>
            </tbody>
        </table>

        <!-- Search Script -->
        <script>
            document.getElementById('studentSearch').addEventListener('keyup', function() {
                let filter = this.value.toLowerCase();
                let rows = document.querySelectorAll('#studentTable tbody tr');
                rows.forEach(row => {
                    let text = row.innerText.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        </script>
        <?php
    } else {
        echo '<div class="alert alert-warning">No students assigned for this academic year.</div>';
    }
} else {
    // Display missing parameters message
    echo '<div class="alert alert-danger">Missing required parameters: section_id, academic_year, or semester.</div>';
}
?>
