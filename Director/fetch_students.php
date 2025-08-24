<?php
include('../connection/connection.php');

if (isset($_GET['section_id'], $_GET['academic_year'])) {
    $section_id = intval($_GET['section_id']);
    $academic_year = mysqli_real_escape_string($conn, $_GET['academic_year']);

    $query = "SELECT s.sid, s.student_id, s.first_name, s.father_name ,s.gender
              FROM assign_student a
              JOIN students s ON a.student_id = s.sid
              WHERE a.section_id='$section_id' AND a.academic_year='$academic_year'
              ORDER BY s.first_name ASC";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
?>
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <?php $rowNo = isset($rowNo) ? $rowNo + 1 : 1; ?>
                    <td><?= $rowNo; ?></td>
                    <td><?= htmlspecialchars($row['student_id']); ?></td>
                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['father_name']); ?></td>
                    <td><?= htmlspecialchars($row['gender']); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <script>
        document.querySelectorAll('.viewDetailBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                let sid = this.getAttribute('data-sid');
                let target = document.getElementById('studentDetailContent');
                target.innerHTML = '<div class="text-center p-3">Loading...</div>';
                let modal = new bootstrap.Modal(document.getElementById('studentModal'));
                modal.show();

                fetch('fetch_studentDetail.php?sid=' + sid)
                  .then(r => r.text())
                  .then(html => { target.innerHTML = html; })
                  .catch(() => { target.innerHTML = '<div class="text-danger">Error loading student details.</div>'; });
            });
        });
        </script>
<?php
    } else {
        echo '<div class="alert alert-warning">No students found.</div>';
    }
}
?>
