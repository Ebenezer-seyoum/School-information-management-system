<?php
include('directorHeader.php');
?>
<?php
$success = $allErr = "";
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);

if (isset($_SESSION["uid"]) && ($roleName == "Director")) {
?>
<!-- CSS for profile image -->
<style>
  .profile-img { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; }
  .modal-body { max-height: 400px; overflow-y: auto; }
  .form-inline { display: flex; gap: 10px; align-items: center; }
</style>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Generate Report Card</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Student</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Generate Report card</a></li>
      </ul>
    </div>

    <div class="main-content">
      <section class="section">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">All Classes</h4>
                <form method="GET" class="d-flex w-100">
                  <div class="search-box w-100">
                    <div class="input-group">
                      <span class="input-group-text bg-primary text-white"><i class="fas fa-search"></i></span>
                      <input type="text" name="search" id="userSearch" class="form-control search-input" placeholder="Search by ID, Name, or Role...">
                      <button class="btn btn-primary" type="button" aria-label="Search">Search</button>
                    </div>
                  </div>
                </form>
              </div>

              <div class="card-body table-responsive">
                <table class="table table-hover align-middle text-center" id="classTable">
                  <thead class="table-secondary">
                    <tr>
                      <th>#</th>
                      <th>Section Name</th>
                      <th>Class Type</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $classes = getAllSections();
                    $no = 1;
                    if (!empty($classes)) {
                      foreach ($classes as $cls) {
                        ?>
                        <tr>
                          <td><?= $no ?></td>
                          <td><?= $cls['section_name'] ?></td>
                          <td><?= $cls['class_type'] ?></td>
                          <td>
                            <button class="btn btn-info btn-sm view-students-btn" 
                              data-section-id="<?= $cls['cid'] ?>" 
                              data-section-name="<?= $cls['section_name'] ?>">
                              View Students
                            </button>
                          </td>
                        </tr>
                        <?php
                        $no++;
                      }
                    } else {
                      echo '<tr><td colspan="4" class="text-center text-danger">No classes found.</td></tr>';
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentsModalLabel">Students in Class</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-inline mb-3">
          <div>
            <label for="academicYearModal" class="form-label">Academic Year</label>
            <select id="academicYearModal" class="form-control">
              <option value="">-- Select Academic Year --</option>
              <?php
              $yrs = mysqli_query($conn, "SELECT DISTINCT academic_year FROM assign_student ORDER BY academic_year DESC");
              while($r = mysqli_fetch_assoc($yrs)){
                $ay = htmlspecialchars($r['academic_year']);
                echo "<option value='{$ay}'>{$ay}</option>";
              }
              ?>
            </select>
          </div>
          <div>
            <label for="semesterModal" class="form-label">Semester</label>
            <select id="semesterModal" class="form-control">
              <option value="">Select Semester</option>
              <option value="1">Semester 1</option>
              <option value="2">Semester 2</option>
            </select>
          </div>
        </div>
        <div id="studentsList">Please select academic year and semester...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Preview PDF Modal -->
<div class="modal fade" id="previewPdfModal" tabindex="-1" aria-labelledby="previewPdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewPdfModalLabel">Report Card Preview</h5>
        <div class="d-flex gap-2">
          <a id="previewOpenNewTab" href="#" target="_blank" class="btn btn-sm btn-outline-primary">Open in new tab</a>
          <a id="previewDownload" href="#" target="_blank" class="btn btn-sm btn-outline-success">Download</a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0" style="height: 80vh;">
        <iframe id="reportPdfIframe" src="" style="width:100%; height:100%; border:0;"></iframe>
      </div>
    </div>
  </div>
  </div>

<script>
// Event listener for "View Students" button
document.querySelectorAll('.view-students-btn').forEach(button => {
  button.addEventListener('click', function() {
    const sectionId = this.dataset.sectionId;
    const sectionName = this.dataset.sectionName;
    const modalTitle = document.getElementById('studentsModalLabel');
    const studentsList = document.getElementById('studentsList');

    // Show modal
    var studentsModal = new bootstrap.Modal(document.getElementById('studentsModal'));
    studentsModal.show();

    // Reset inputs
    const academicYearInput = document.getElementById('academicYearModal');
    const semesterInput = document.getElementById('semesterModal');
    academicYearInput.value = '';
    semesterInput.value = '';
    modalTitle.textContent = `Students in Class: ${sectionName}`;
    studentsList.innerHTML = 'Please select academic year and semester...';

    // Fetch function
    function fetchStudents() {
      const year = academicYearInput.value.trim();
      const semester = semesterInput.value;
      if(year && semester) {
        modalTitle.textContent = `Students in Class: ${sectionName} | ${year} | Semester ${semester}`;
        fetch(`ajax_fetch_students_results.php?section_id=${sectionId}&academic_year=${year}&semester=${semester}`)
          .then(res => res.text())
          .then(data => {
            studentsList.innerHTML = data;
          })
          .catch(err => { studentsList.innerHTML = 'Error fetching students'; });
      } else {
        modalTitle.textContent = `Students in Class: ${sectionName}`;
        studentsList.innerHTML = 'Please select academic year and semester...';
      }
    }

    academicYearInput.oninput = fetchStudents;
    semesterInput.onchange = fetchStudents;

  });
});

// Delegated handlers inside the modal content so injected scripts are not required
(function(){
  const container = document.getElementById('studentsList');
  if (!container) return;

  // Handle Show Report button clicks (preview in dedicated modal)
  container.addEventListener('click', function(e){
    const btn = e.target.closest && e.target.closest('.preview-btn');
    if (!btn) return;
    const url = btn.dataset.previewUrl;
    if (!url) return;

    // Setup links
    const openBtn = document.getElementById('previewOpenNewTab');
    const downloadBtn = document.getElementById('previewDownload');
    if (openBtn) openBtn.href = url;
    if (downloadBtn) {
      const dlUrl = url.includes('mode=preview') ? url.replace('mode=preview','mode=download') : (url + (url.includes('?') ? '&' : '?') + 'mode=download');
      downloadBtn.href = dlUrl;
    }

    // Set iframe and open modal
    const iframe = document.getElementById('reportPdfIframe');
    if (iframe) iframe.src = url;
    const modal = new bootstrap.Modal(document.getElementById('previewPdfModal'));
    modal.show();
  });

  // Handle search inside injected table
  container.addEventListener('input', function(e){
    if (e.target && e.target.id === 'studentSearch') {
      const filter = e.target.value.toLowerCase();
      const rows = container.querySelectorAll('#studentTable tbody tr');
      rows.forEach(function(row){
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    }
  });
})();
</script>

<?php
} else {
  echo "You are not authorized to view this page.";
}
include('../Admin/footer.php');
?>
