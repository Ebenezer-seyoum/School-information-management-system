<?php
include('teacherHeader.php'); // adjust path as needed
// --- Check login ---
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);
if (!isset($_SESSION["uid"]) || $roleName != "Teacher") {
    echo "You are not authorized to view this page.";
    exit;
}

// --- Helpers ---
function fetchAcademicYears($conn, $teacher_id){
    $res = mysqli_query($conn, "SELECT DISTINCT academic_year 
                                FROM assign_instructor 
                                WHERE instructor_id = $teacher_id
                                ORDER BY academic_year DESC");
    $years = [];
    while($r = mysqli_fetch_assoc($res)){ $years[] = $r['academic_year']; }
    return $years;
}
function fetchAssignedClasses($conn, $teacher_id, $year){
    $res = mysqli_query($conn, "SELECT at.hid, at.section_id, at.academic_year, s.section_name, s.class_type
                                FROM assign_instructor at
                                LEFT JOIN sections s ON at.section_id = s.cid
                                WHERE at.instructor_id = $teacher_id AND at.academic_year = '$year'
                                ORDER BY s.section_name ASC");
    $tmp = [];
    while($r = mysqli_fetch_assoc($res)){ $tmp[] = $r; }
    return $tmp;
}

// --- Main Logic ---
$years = fetchAcademicYears($conn, $_SESSION["uid"]);
$selectedYear = $_GET['academic_year'] ?? ($years[0] ?? null);
$classes = $selectedYear ? fetchAssignedClasses($conn, $_SESSION["uid"], $selectedYear) : [];
?>

<!-- page header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Mark Attendance</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Attendance</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#"> Mark Attendance</a></li>
      </ul>
  </div>
<!-- end page header -->
    <!-- Select Academic Year -->
    <form method="GET" class="mb-3">
        <label for="academic_year">Select Academic Year:</label>
        <select name="academic_year" id="academic_year" class="form-control w-auto d-inline-block">
            <?php foreach($years as $year): ?>
                <option value="<?= $year ?>" <?= ($year == $selectedYear) ? 'selected' : '' ?>><?= $year ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Show Classes</button>
    </form>

    <div class="card">
      <div class="card-body">
        <?php if(count($classes) > 0): ?>
          <div class="row g-3">
            <?php foreach($classes as $c): ?>
              <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm">
                  <div class="card-body d-flex flex-column">
                    <h6 class="mb-1"><?= htmlspecialchars($c['section_name']) ?></h6>
                    <div class="text-muted small mb-2"><?= htmlspecialchars($c['class_type']) ?></div>
                    <div class="small text-secondary mb-3">Year: <?= htmlspecialchars($c['academic_year']) ?></div>
                    <div class="mt-auto">
                      <button type="button"
                              class="btn btn-primary btn-sm w-100 view-students"
                              data-class-id="<?= $c['hid'] ?>"
                              data-class-name="<?= htmlspecialchars($c['section_name'] . ' - ' . $c['class_type']) ?>"
                              data-year="<?= $c['academic_year'] ?>">
                          View Students
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center text-danger py-3">No classes assigned for this year.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"><!-- wider -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Students in Class</h5>
        <!-- X button removed on purpose -->
      </div>
      <div class="modal-body">
        <div id="studentsTable">Loading...</div>
      </div>
    </div>
  </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Keep reference to current class id so we can reload semester changes
let currentClassId = null;

// Open modal and load attendance sheet
document.querySelectorAll(".view-students").forEach(btn => {
  btn.addEventListener("click", function() {
    currentClassId = this.dataset.classId;
    const className = this.dataset.className;
    const year = this.dataset.year;

    document.querySelector("#studentsModal .modal-title").innerText =
      "Attendance • " + className + " (" + year + ")";

    loadAttendanceSheet(currentClassId, 1); // default semester 1

    // show modal
    const modal = new bootstrap.Modal(document.getElementById("studentsModal"));
    modal.show();
  });
});

// Load attendance sheet HTML into modal (no scripts inside the HTML)
function loadAttendanceSheet(classId, semester){
  const target = document.getElementById('studentsTable');
  target.innerHTML = "Loading...";
  fetch("fetch_students_for_attendance.php?class_id=" + classId + "&semester=" + semester)
    .then(res => res.text())
    .then(html => { target.innerHTML = html; })
    .catch(() => { target.innerHTML = "<div class='text-danger'>Failed to load.</div>"; });
}

// --- Event delegation for elements INSIDE the loaded HTML ---

// Search filter
document.getElementById('studentsTable').addEventListener('input', function(e){
  if (e.target && e.target.id === 'searchInput') {
    const filter = e.target.value.toLowerCase();
    document.querySelectorAll('#studentTable tr').forEach(row => {
      const name = row.cells[1]?.innerText.toLowerCase() || '';
      row.style.display = name.includes(filter) ? '' : 'none';
    });
  }
});

// Semester change -> reload HTML
document.getElementById('studentsTable').addEventListener('change', function(e){
  if (e.target && e.target.id === 'semesterSelect') {
    const sem = e.target.value;
    if (currentClassId) loadAttendanceSheet(currentClassId, sem);
  }
});

// Save Attendance (SweetAlert confirm + AJAX) + auto-close modal on success
document.getElementById('studentsTable').addEventListener('click', function(e){
  if (e.target && e.target.id === 'saveAttendanceBtn') {
    const form = document.getElementById('attendanceForm');
    if (!form) return;

    const formData = new FormData(form);

    Swal.fire({
      title: "Are you sure?",
      text: "Do you want to save this attendance?",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Yes, Save it!",
      cancelButtonText: "No, Cancel"
    }).then((result) => {
      if (result.isConfirmed) {
        // Disable button to avoid double submit
        e.target.disabled = true;
        e.target.innerText = 'Saving...';

        fetch("save_attendance.php", { method: "POST", body: formData })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              Swal.fire("Saved!", data.message, "success").then(() => {
                // Close the modal after success
                const modalEl = document.getElementById("studentsModal");
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
              });
            } else {
              Swal.fire("Error", data.message, "error");
            }
          })
          .catch(() => Swal.fire("Error", "Something went wrong!", "error"))
          .finally(() => {
            e.target.disabled = false;
            e.target.innerText = 'Save Attendance';
          });
      }
    });
  }

  // Bottom "Close" button inside loaded HTML -> close modal
  if (e.target && e.target.id === 'closeAttendanceBtn') {
    const modalEl = document.getElementById("studentsModal");
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modal.hide();
  }
});

// Optional: clear content when modal is fully hidden
document.getElementById('studentsModal').addEventListener('hidden.bs.modal', function () {
  document.getElementById('studentsTable').innerHTML = '';
  currentClassId = null;
});
</script>

<?php include('../Admin/footer.php'); ?>
