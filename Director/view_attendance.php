<?php
include('directorHeader.php'); // use director header

// --- Check login ---
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);
if (!isset($_SESSION["uid"]) || $roleName != "Director") {
    echo "You are not authorized to view this page.";
    exit;
}

// --- Helpers ---
function fetchAcademicYears($conn){
  $res = mysqli_query($conn, "SELECT DISTINCT academic_year FROM assign_instructor ORDER BY academic_year DESC");
  $years = [];
  while($r = mysqli_fetch_assoc($res)) $years[] = $r['academic_year'];
  return $years;
}

$years = fetchAcademicYears($conn);
?>
<!-- page header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">View attendance</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Attendance Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">View attendance</a></li>
      </ul>
  </div>
<!-- end page header -->

    <!-- Filter First: Section + Academic Year -->
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:800px; width:100%;">
          <div class="text-center mb-3">
            <h5 class="fw-bold">Select Section and Academic Year</h5>
            <p class="text-muted mb-0">Choose a section and year to view attendance.</p>
          </div>
          <div class="row g-3 align-items-end">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Section</label>
              <select id="sectionSelect" class="form-select form-select-lg">
                <option value="">-- Select Section --</option>
                <?php
                $sections = mysqli_query($conn,"SELECT cid, section_name, class_type FROM sections ORDER BY class_type, section_name ASC");
                $grouped_sections = [];
                while($sec=mysqli_fetch_assoc($sections)) $grouped_sections[$sec['class_type']][] = $sec;
                foreach($grouped_sections as $type => $secs): ?>
                  <optgroup label="<?= htmlspecialchars($type) ?>">
                    <?php foreach($secs as $s): ?>
                      <option value="<?= (int)$s['cid'] ?>" data-type="<?= htmlspecialchars($s['class_type']) ?>"><?= htmlspecialchars($s['section_name']) ?></option>
                    <?php endforeach; ?>
                  </optgroup>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Academic Year</label>
              <select id="academicYear" class="form-select form-select-lg">
                <option value="">-- Select Academic Year --</option>
                <?php foreach($years as $y): ?>
                  <option value="<?= htmlspecialchars($y) ?>"><?= htmlspecialchars($y) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2 d-grid">
              <button type="button" id="viewAttendanceBtn" class="btn btn-primary btn-md">View sections</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Class Attendance</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="studentsTable">Loading...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
let currentClassId = null;
let studentsModal = null;

document.getElementById('viewAttendanceBtn').addEventListener('click', function(){
  const secSel = document.getElementById('sectionSelect');
  const yearSel = document.getElementById('academicYear');
  const sectionId = secSel.value;
  const year = yearSel.value;
  const sectionName = secSel.options[secSel.selectedIndex]?.text || '';
  const classType = secSel.options[secSel.selectedIndex]?.getAttribute('data-type') || '';
  if(!sectionId || !year){
    Swal.fire('Warning','Please select section and academic year','warning');
    return;
  }
  // resolve class id
  fetch('get_class_id.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`section_id=${encodeURIComponent(sectionId)}&academic_year=${encodeURIComponent(year)}`
  }).then(r=>r.json()).then(data=>{
    if(!data || !data.success){
      Swal.fire('Error', (data&&data.message)?data.message:'Combination not found', 'error');
      return;
    }
    currentClassId = data.class_id;
    document.querySelector('#studentsModal .modal-title').innerText = `Attendance • ${sectionName} - ${classType} (${year})`;
    loadAttendanceSheet(currentClassId, 0, 'weekly');
    studentsModal = new bootstrap.Modal(document.getElementById('studentsModal'));
    studentsModal.show();
  }).catch(()=> Swal.fire('Error','Failed to resolve class','error'));
});

// ajax loader
function loadAttendanceSheet(classId, semester=0, view="weekly", from="", to=""){
  const target = document.getElementById('studentsTable');
  target.innerHTML = "Loading...";

  const params = new URLSearchParams({
    class_id: classId,
    semester: semester,
    view: view,
    from: from,
    to: to
  });

  fetch("fetch_view_attendance.php?" + params.toString())
    .then(res => res.text())
    .then(html => {
      target.innerHTML = html;
      bindAttendanceEvents();
    })
    .catch(() => { target.innerHTML = "<div class='text-danger'>Failed to load.</div>"; });
}

// rebind events from ajax
function bindAttendanceEvents(){
  const semSel = document.getElementById("semesterSelect");
  if (semSel){
    semSel.addEventListener("change", function(){
      loadAttendanceSheet(currentClassId, this.value, document.getElementById("viewSelect").value);
    });
  }

  const viewSel = document.getElementById("viewSelect");
  if (viewSel){
    viewSel.addEventListener("change", function(){
      if (this.value === "range"){
        document.getElementById("rangeInputs").style.display = "block";
      } else {
        document.getElementById("rangeInputs").style.display = "none";
        loadAttendanceSheet(currentClassId, document.getElementById("semesterSelect").value, this.value);
      }
    });
  }

  const rangeBtn = document.getElementById("rangeBtn");
  if (rangeBtn){
    rangeBtn.addEventListener("click", function(){
      const from = document.getElementById("fromDate").value;
      const to = document.getElementById("toDate").value;
      loadAttendanceSheet(currentClassId, document.getElementById("semesterSelect").value, "range", from, to);
    });
  }

  const closeBtn = document.getElementById("closeAttendanceBtn");
  if (closeBtn){
    closeBtn.addEventListener("click", () => {
      if (studentsModal) studentsModal.hide();
    });
  }
}
</script>

<?php include('../Admin/footer.php'); ?>
