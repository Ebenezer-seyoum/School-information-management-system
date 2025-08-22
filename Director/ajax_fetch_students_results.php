<?php
include('../connection/connection.php'); 

if (isset($_GET['section_id'], $_GET['academic_year'], $_GET['semester'])) {
    $section_id = (int)$_GET['section_id'];
    $academic_year = mysqli_real_escape_string($conn, $_GET['academic_year']);
    $semester = (int)$_GET['semester']; // 1 or 2

    // Fetch students assigned to this section and academic year
    $query = "
        SELECT s.sid, s.student_id, s.first_name, s.father_name, s.mother_name, s.gender, s.dob
        FROM assign_student a
        JOIN students s ON a.student_id = s.sid
        WHERE a.section_id='$section_id' AND a.academic_year='$academic_year'
        ORDER BY s.first_name ASC
    ";

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
            // Preview URL for modal
            $previewUrl = 'ajax_generate_report.php?' . http_build_query(array(
                'sid' => (int)$row['sid'],
                'section_id' => $section_id,
                'academic_year' => $academic_year,
                'semester' => $semester,
                'mode' => 'preview'
            ));
            // Download URL
            $downloadUrl = 'ajax_generate_report.php?' . http_build_query(array(
                'sid' => (int)$row['sid'],
                'section_id' => $section_id,
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

        <!-- PDF Preview Modal -->
        <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl" style="max-width:90%;">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="pdfPreviewLabel">Student Report Card Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <iframe id="pdfIframe" src="" frameborder="0" style="width:100%;height:80vh;"></iframe>
              </div>
              <div class="modal-footer">
                <a href="#" id="downloadBtn" class="btn btn-success" target="_blank">Download PDF</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Student search filter
            const searchInput = document.getElementById('studentSearch');
            const table = document.getElementById('studentTable').getElementsByTagName('tbody')[0];

            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                Array.from(table.rows).forEach(row => {
                    const name = row.cells[1].textContent.toLowerCase();
                    const id = row.cells[0].textContent.toLowerCase();
                    row.style.display = (name.includes(filter) || id.includes(filter)) ? '' : 'none';
                });
            });

            // PDF Preview Modal
            const previewButtons = document.querySelectorAll('.preview-btn');
            const pdfIframe = document.getElementById('pdfIframe');
            const downloadBtn = document.getElementById('downloadBtn');

            previewButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const url = this.dataset.previewUrl;
                    pdfIframe.src = url; // Load PDF in iframe
                    downloadBtn.href = url.replace('mode=preview', 'mode=download'); // Set download link
                    new bootstrap.Modal(document.getElementById('pdfPreviewModal')).show();
                });
            });
        });
        </script>

        <?php
    } else {
        echo '<div class="alert alert-warning">No students assigned for this academic year.</div>';
    }
} else {
    echo '<div class="alert alert-danger">Missing required parameters: section_id, academic_year, or semester.</div>';
}
?>
