<?php
include('directorHeader.php');
?>
<?php
$success = $allErr = "";
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);

if (isset($_SESSION["uid"]) && ($roleName == "Director")) {
?>
<style>
  .profile-img { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; }
  .modal-body { max-height: 400px; overflow-y: auto; }
</style>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">View Classes</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Class</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">View Class</a></li>
      </ul>
    </div>

    <div class="main-content">
      <section class="section">
        <!-- Section + Academic Year -->
        <div class="row mb-4">
          <div class="col-12 d-flex justify-content-center">
            <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:800px; width:100%;">
              <div class="text-center mb-3">
                <h5 class="fw-bold">Select Section and Academic Year</h5>
                <p class="text-muted mb-0">Choose a section and year to view its students.</p>
              </div>
              <div class="row g-3 align-items-end">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Section</label>
                  <select id="sectionSelect" class="form-select form-select-lg">
                    <option value="">-- Select Section --</option>
                    <?php
                    $sections = mysqli_query($conn,"SELECT * FROM sections ORDER BY class_type, section_name ASC");
                    $grouped_sections = [];
                    while($sec=mysqli_fetch_assoc($sections)) $grouped_sections[$sec['class_type']][] = $sec;
                    foreach($grouped_sections as $type => $secs): ?>
                      <optgroup label="<?= htmlspecialchars($type) ?>">
                        <?php foreach($secs as $s): ?>
                          <option value="<?= $s['cid'] ?>"><?= htmlspecialchars($s['section_name']) ?></option>
                        <?php endforeach; ?>
                      </optgroup>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-semibold">Academic Year</label>
                  <select id="academicYear" class="form-select form-select-lg">
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
                <div class="col-md-2 d-grid">
                  <button type="button" id="showClassBtn" class="btn btn-primary btn-md">View Class</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Class card -->
        <div class="row" id="classResultRow" style="display:none;">
          <div class="col-12 col-lg-8 mx-auto">
            <div class="card">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="mb-1" id="classTitle">Section: -</h5>
                  <div class="text-muted" id="classMeta">Year: -</div>
                </div>
                <div>
                  <button class="btn btn-info" id="openStudentsBtn">View Students</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<!-- Students List Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentsModalLabel">Students in Class</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="studentsList">Loading students...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Student Detail Modal -->
<div class="modal fade" id="studentDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Student Details</h5>
        <div class="ms-auto">
          <button class="btn btn-sm btn-light me-2" id="editStudentBtn">Edit</button>
          <button class="btn btn-sm btn-dark" id="printStudentBtn">Print</button>
        </div>
        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="studentDetailContent">
        Select a student to view details.
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const sectionSelect = document.getElementById('sectionSelect');
  const yearSelect = document.getElementById('academicYear');
  const resultRow = document.getElementById('classResultRow');
  const classTitle = document.getElementById('classTitle');
  const classMeta = document.getElementById('classMeta');
  const openBtn = document.getElementById('openStudentsBtn');

  let currentSection = null;
  let currentSectionName = '';
  let currentYear = '';
  let currentStudentId = null;

  // Show class card
  document.getElementById('showClassBtn').addEventListener('click', function(){
    if(!sectionSelect.value || !yearSelect.value){
      alert('Please select section and academic year');
      return;
    }
    currentSection = sectionSelect.value;
    currentYear = yearSelect.value;
    currentSectionName = sectionSelect.options[sectionSelect.selectedIndex].text;
    classTitle.textContent = `Section: ${currentSectionName}`;
    classMeta.textContent = `Year: ${currentYear}`;
    resultRow.style.display = '';
  });

  // Open students list modal
  openBtn.addEventListener('click', function(){
    if(!currentSection || !currentYear) return;
    document.getElementById('studentsModalLabel').textContent = 
      `Students in Class: ${currentSectionName} | ${currentYear}`;
    document.getElementById('studentsList').innerHTML = 'Loading...';
    new bootstrap.Modal(document.getElementById('studentsModal')).show();

    fetch(`fetch_students.php?section_id=${currentSection}&academic_year=${currentYear}`)
      .then(r=>r.text())
      .then(html=>{
        document.getElementById('studentsList').innerHTML = html;

        // Bind View Detail buttons inside fetched list
        document.querySelectorAll('.viewDetailBtn').forEach(btn=>{
          btn.addEventListener('click', function(){
            currentStudentId = this.getAttribute('data-sid');
            loadStudentDetail(currentStudentId);
          });
        });
      })
      .catch(()=>{ document.getElementById('studentsList').innerHTML = 'Error fetching students'; });
  });

  // Load student details into modal
  function loadStudentDetail(sid){
    const content = document.getElementById('studentDetailContent');
    content.innerHTML = 'Loading...';
    new bootstrap.Modal(document.getElementById('studentDetailModal')).show();

    fetch('fetch_studentDetail.php?sid=' + sid)
      .then(r=>r.text())
      .then(html=>{ content.innerHTML = html; })
      .catch(()=>{ content.innerHTML = 'Error loading details'; });
  }

  // Edit button
  document.getElementById('editStudentBtn').addEventListener('click', function(){
    if(currentStudentId){
      window.location.href = 'edit_student.php?sid=' + currentStudentId;
    }
  });

  // Print button
  document.getElementById('printStudentBtn').addEventListener('click', function(){
    const content = document.getElementById('studentDetailContent').innerHTML;
    const win = window.open('', '', 'width=800,height=600');
    win.document.write('<html><head><title>Print Student</title></head><body>' + content + '</body></html>');
    win.document.close();
    win.print();
  });

});
</script>

<?php
} else {
  echo "You are not authorized to view this page.";
}
include('../Admin/footer.php');
?>
