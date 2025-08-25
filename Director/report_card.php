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
        <div class="container d-flex justify-content-center align-items-center" style="min-height:300px;">
          <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:600px; width:100%;">
            <div class="text-center mb-4">
              <h4 class="fw-bold">Select Section to Generate Report Card</h4>
              <p class="text-muted">Choose a section from the dropdown below</p>
            </div>
            <div class="row g-3 align-items-center">
              <div class="col-12">
                <label for="sectionSelect" class="form-label fw-semibold">Section</label>
                <select id="sectionSelect" class="form-select form-select-lg">
                  <option value="">-- Select Section --</option>
                  <?php
                    $classes = getAllSections();
                    $grouped = [];
                    if (!empty($classes)) {
                      foreach ($classes as $cls) {
                        $grouped[$cls['class_type']][] = $cls;
                      }
                      foreach ($grouped as $type => $secs) {
                        echo '<optgroup label="'.htmlspecialchars($type).'">';
                        foreach ($secs as $s) {
                          $id = (int)$s['cid'];
                          $label = htmlspecialchars($s['section_name'].' ('.$s['class_type'].')');
                          $nameOnly = htmlspecialchars($s['section_name']);
                          echo "<option value='{$id}' data-name='{$nameOnly}'>{$label}</option>";
                        }
                        echo '</optgroup>';
                      }
                    }
                  ?>
                </select>
              </div>
              <div class="col-12 d-grid mt-3">
                <button id="showStudentsBtn" type="button" class="btn btn-primary btn-lg">Show Students</button>
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
// Filter-first: pick a section, then open modal to pick year/semester and view students
document.getElementById('showStudentsBtn').addEventListener('click', function(){
  const sel = document.getElementById('sectionSelect');
  const sectionId = sel.value;
  const sectionName = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].dataset.name : '';
  const modalTitle = document.getElementById('studentsModalLabel');
  const studentsList = document.getElementById('studentsList');

  if(!sectionId){
    alert('Please select a section first.');
    return;
  }

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

  function fetchStudents(){
    const year = academicYearInput.value.trim();
    const semester = semesterInput.value;
    if(year && semester){
      modalTitle.textContent = `Students in Class: ${sectionName} | ${year} | Semester ${semester}`;
      fetch(`ajax_fetch_students_results.php?section_id=${sectionId}&academic_year=${encodeURIComponent(year)}&semester=${semester}`)
        .then(res=>res.text())
        .then(html=>{ studentsList.innerHTML = html; })
        .catch(()=>{ studentsList.innerHTML = 'Error fetching students'; });
    } else {
      modalTitle.textContent = `Students in Class: ${sectionName}`;
      studentsList.innerHTML = 'Please select academic year and semester...';
    }
  }

  academicYearInput.oninput = fetchStudents;
  semesterInput.onchange = fetchStudents;
});

// Delegated handler: open preview modal for report
(function(){
  const container = document.getElementById('studentsList');
  if(!container) return;
  container.addEventListener('click', function(e){
    const btn = e.target.closest && e.target.closest('.preview-btn');
    if(!btn) return;
    const url = btn.dataset.previewUrl;
    if(!url) return;
    const openBtn = document.getElementById('previewOpenNewTab');
    const downloadBtn = document.getElementById('previewDownload');
    if (openBtn) openBtn.href = url;
    if (downloadBtn) {
      const dlUrl = url.includes('mode=preview') ? url.replace('mode=preview','mode=download') : (url + (url.includes('?') ? '&' : '?') + 'mode=download');
      downloadBtn.href = dlUrl;
    }
    const iframe = document.getElementById('reportPdfIframe');
    if (iframe) iframe.src = url;
    new bootstrap.Modal(document.getElementById('previewPdfModal')).show();
  });
})();
</script>

<?php
} else {
  echo "You are not authorized to view this page.";
}
include('../Admin/footer.php');
?>
