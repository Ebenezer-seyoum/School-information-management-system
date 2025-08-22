<?php
include('../connection/connection.php'); 

if (isset($_GET['section_id'], $_GET['academic_year'], $_GET['semester'])) {
    $section_id = $_GET['section_id'];
    $academic_year = mysqli_real_escape_string($conn, $_GET['academic_year']);
    $semester = (int)$_GET['semester']; // 1 or 2

    $query = "SELECT s.sid, s.student_id, s.first_name, s.father_name, s.mother_name, s.gender, s.dob
              FROM assign_student a
              JOIN students s ON a.student_id = s.sid
              WHERE a.section_id='".mysqli_real_escape_string($conn,$section_id)."' AND a.academic_year='".$academic_year."'
              ORDER BY s.first_name ASC";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        ?>
        <div class="mb-3">
            <input type="text" id="studentSearch" class="form-control" placeholder="Search student by name or ID...">
        </div>

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
            $previewUrl = 'ajax_generate_report.php?' . http_build_query(array(
                'sid' => (int)$row['sid'],
                'section_id' => (int)$section_id,
                'academic_year' => $academic_year,
                'semester' => $semester,
                'mode' => 'preview'
            ));
            $downloadUrl = 'ajax_generate_report.php?' . http_build_query(array(
                'sid' => (int)$row['sid'],
                'section_id' => (int)$section_id,
                'academic_year' => $academic_year,
                'semester' => $semester,
                'mode' => 'download'
            ));
            echo '<tr>
                    <td>' . htmlspecialchars($row['student_id'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars($row['first_name'] . ' ' . $row['father_name'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary preview-btn" 
                                data-preview-url="' . htmlspecialchars($previewUrl, ENT_QUOTES, 'UTF-8') . '">
                                Show Report
                        </button>
                        <a href="' . htmlspecialchars($downloadUrl, ENT_QUOTES, 'UTF-8') . '" 
                           class="btn btn-sm btn-success download-btn" target="_blank">
                           Download
                        </a>
                    </td>
                  </tr>';
        }
        ?>
            </tbody>
        </table>
        <?php
    } else {
        echo '<div class="alert alert-warning">No students assigned for this academic year.</div>';
    }
} else {
    echo '<div class="alert alert-danger">Missing required parameters: section_id, academic_year, or semester.</div>';
}
?>
