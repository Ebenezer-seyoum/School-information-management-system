<?php
include('directorHeader.php');

// --- Check login ---
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);
if (!isset($_SESSION["uid"]) || $roleName != "Director") {
    echo "You are not authorized to view this page.";
    exit;
}

// --- Helpers ---
function fetchAcademicYears($conn) {
  $res = mysqli_query($conn, "SELECT DISTINCT academic_year FROM assign_instructor ORDER BY academic_year DESC");
  $years = [];
  while ($r = mysqli_fetch_assoc($res)) $years[] = $r['academic_year'];
  return $years;
}

// --- Main ---
$years = fetchAcademicYears($conn);
?>
<!-- page header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Update attendance</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Attendance Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Update attendance</a></li>
      </ul>
  </div>
<!-- end page header -->

    <!-- Filter First: Section + Academic Year -->
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:800px; width:100%;">
          <div class="text-center mb-3">
            <h5 class="fw-bold">Select Section and Academic Year</h5>
            <p class="text-muted mb-0">Choose a section and year to update attendance.</p>
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
                <?php foreach ($years as $y): ?>
                  <option value="<?= htmlspecialchars($y) ?>"><?= htmlspecialchars($y) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2 d-grid">
              <button type="button" id="openUpdateBtn" class="btn btn-primary btn-md">view sections</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header w-100">
        <div class="d-flex align-items-center w-100 gap-2">
          <h5 class="modal-title mb-0" id="studentsModalLabel">Update Attendance</h5>
          <div class="ms-auto" style="min-width: 280px;">
            <div class="input-group">
              <span class="input-group-text bg-primary text-white"><i class="fas fa-search"></i></span>
              <input type="text" id="searchInput" class="form-control" placeholder="Search student...">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <!-- Date Range Filter -->
        <div class="d-flex justify-content-end mb-3 gap-2">
          <label>From: <input type="date" id="dateFrom" class="form-control d-inline-block w-auto"></label>
          <label>To: <input type="date" id="dateTo" class="form-control d-inline-block w-auto"></label>
          <button class="btn btn-sm btn-primary" id="filterDateBtn">Apply</button>
        </div>

        <div id="studentsTable"><div class="text-center p-3">Loading...</div></div>
      </div>

    </div>
  </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const studentsModalEl = document.getElementById("studentsModal");
  const studentsModal = new bootstrap.Modal(studentsModalEl);
  const studentsTable = document.getElementById("studentsTable");
  let currentClassId = null;
  let currentSemester = 1;

  // Filter-first open
  document.getElementById('openUpdateBtn').addEventListener('click', function(){
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
    fetch('get_class_id.php',{
      method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`section_id=${encodeURIComponent(sectionId)}&academic_year=${encodeURIComponent(year)}`
    }).then(r=>r.json()).then(data=>{
      if(!data || !data.success){
        Swal.fire('Error', (data&&data.message)?data.message:'Combination not found','error');
        return;
      }
      currentClassId = data.class_id;
      document.getElementById("studentsModalLabel").innerText = `Update Attendance • ${sectionName} - ${classType} (${year})`;
      loadAttendanceEditor(currentClassId, currentSemester);
      studentsModal.show();
    }).catch(()=> Swal.fire('Error','Failed to resolve class','error'));
  });

  function loadAttendanceEditor(classId, semester, fromDate='', toDate='', search=''){
    studentsTable.innerHTML = "<div class='text-center p-3'>Loading...</div>";
    const url = new URL("fetch_update_attendance.php", window.location.href);
    url.searchParams.set("class_id", classId);
    url.searchParams.set("semester", semester);
    if(fromDate) url.searchParams.set("from", fromDate);
    if(toDate) url.searchParams.set("to", toDate);
    if(search) url.searchParams.set("search", search);

    fetch(url)
      .then(res => res.text())
      .then(html => { studentsTable.innerHTML = html; })
      .catch(() => { studentsTable.innerHTML = "<div class='text-danger'>Failed to load attendance data.</div>"; });
  }

  // Save attendance
  studentsTable.addEventListener('click', function(e){
    if (e.target && e.target.id === 'saveAttendanceBtn') {
      const form = document.getElementById('attendanceForm');
      if (!form) return;

      Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to update attendance records.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!'
      }).then((result) => {
        if (!result.isConfirmed) return;

        const btn = e.target;
        btn.disabled = true;
        const originalText = btn.innerText;
        btn.innerText = 'Updating...';

        const formData = new FormData(form);

        fetch('update_attendance_list.php', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(data => {
          if (data && data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Updated!',
              text: data.message || 'Attendance updated successfully.',
              timer: 1500,
              showConfirmButton: false
            });
            // reload after update
            setTimeout(() => {
              loadAttendanceEditor(currentClassId, currentSemester,
                document.getElementById("dateFrom").value,
                document.getElementById("dateTo").value,
                document.getElementById("searchInput").value);
            }, 1600);
          } else {
            Swal.fire({ icon: 'error', title: 'Update Failed', text: (data && data.message) ? data.message : 'Error updating attendance.' });
          }
        })
        .catch(err => {
          Swal.fire({ icon: 'error', title: 'Network / JSON Error', text: 'Could not connect to server or invalid response. ' + err });
        })
        .finally(() => {
          btn.disabled = false;
          btn.innerText = originalText;
        });
      });
    }
  });

  // Semester change
  studentsTable.addEventListener('change', function(e){
    if (e.target && e.target.id === 'semesterSelect') {
      currentSemester = e.target.value || 1;
      loadAttendanceEditor(currentClassId, currentSemester,
        document.getElementById("dateFrom").value,
        document.getElementById("dateTo").value,
        document.getElementById("searchInput").value);
    }
  });

  // Date filter
  document.getElementById("filterDateBtn").addEventListener("click", function(){
    const fromDate = document.getElementById("dateFrom").value;
    const toDate = document.getElementById("dateTo").value;
    loadAttendanceEditor(currentClassId, currentSemester, fromDate, toDate, document.getElementById("searchInput").value);
  });

  // Search filter
  document.getElementById("searchInput").addEventListener("input", function(){
    loadAttendanceEditor(currentClassId, currentSemester,
      document.getElementById("dateFrom").value,
      document.getElementById("dateTo").value,
      this.value);
  });

  // Cleanup modal
  studentsModalEl.addEventListener('hidden.bs.modal', function () {
    studentsTable.innerHTML = '';
    document.getElementById("searchInput").value = "";
    document.getElementById("dateFrom").value = "";
    document.getElementById("dateTo").value = "";
  });
});
</script>

<?php include('../Admin/footer.php'); ?>
