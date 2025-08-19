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
    $res = mysqli_query($conn, "SELECT DISTINCT academic_year 
                                FROM assign_instructor
                                ORDER BY academic_year DESC");
    $years = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $years[] = $r['academic_year'];
    }
    return $years;
}
function fetchAllClasses($conn, $year) {
    $yearEsc = mysqli_real_escape_string($conn, $year);
    $sql = "SELECT at.hid, at.section_id, at.academic_year,
                   s.section_name, s.class_type, i.first_name, i.father_name
            FROM assign_instructor at
            LEFT JOIN sections s ON at.section_id = s.cid
            LEFT JOIN users i ON at.instructor_id = i.uid
            WHERE at.academic_year = '$yearEsc'
            ORDER BY s.section_name ASC";
    $res = mysqli_query($conn, $sql);
    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
    return $rows;
}

// --- Main ---
$years = fetchAcademicYears($conn);
$selectedYear = $_GET['academic_year'] ?? ($years[0] ?? null);
$classes = $selectedYear ? fetchAllClasses($conn, $selectedYear) : [];
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">All Classes (Director View)</h3>
    </div>

    <form method="GET" class="mb-3">
      <label for="academic_year">Select Academic Year:</label>
      <select name="academic_year" id="academic_year" class="form-control w-auto d-inline-block">
        <?php foreach ($years as $year): ?>
          <option value="<?= htmlspecialchars($year) ?>" <?= ($year == $selectedYear) ? 'selected' : '' ?>>
            <?= htmlspecialchars($year) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Show Classes</button>
    </form>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-hover text-center align-middle">
          <thead class="table-secondary">
            <tr>
              <th>#</th>
              <th>Class</th>
              <th>Class Type</th>
              <th>Instructor</th>
              <th>Academic Year</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($classes) > 0): $no=1; ?>
              <?php foreach ($classes as $c): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($c['section_name']) ?></td>
                  <td><?= htmlspecialchars($c['class_type']) ?></td>
                  <td><?= htmlspecialchars($c['first_name'].' '.$c['father_name']) ?></td>
                  <td><?= htmlspecialchars($c['academic_year']) ?></td>
                  <td>
                    <button type="button"
                            class="btn btn-warning btn-sm view-students"
                            data-class-id="<?= (int)$c['hid'] ?>"
                            data-class-name="<?= htmlspecialchars($c['section_name'].' - '.$c['class_type']) ?>"
                            data-year="<?= htmlspecialchars($c['academic_year']) ?>">
                      Update Attendance
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-danger">No classes for this year.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentsModalLabel">Update Attendance</h5>
        <!-- ✅ Only ONE search bar here -->
        <input type="text" id="searchInput" class="form-control ms-3" placeholder="Search student..." style="width: 250px;">
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

  // Open modal -> fetch attendance
  document.querySelectorAll(".view-students").forEach(btn => {
    btn.addEventListener("click", function () {
      currentClassId = this.dataset.classId;
      const className = this.dataset.className;
      const year = this.dataset.year;

      document.getElementById("studentsModalLabel").innerText =
        "Update Attendance • " + className + " (" + year + ")";

      loadAttendanceEditor(currentClassId, currentSemester);
      studentsModal.show();
    });
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
