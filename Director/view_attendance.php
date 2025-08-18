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
// Fetch all academic years (no instructor filter)
function fetchAcademicYears($conn){
    $res = mysqli_query($conn, "SELECT DISTINCT academic_year 
                                FROM assign_instructor
                                ORDER BY academic_year DESC");
    $years = [];
    while($r = mysqli_fetch_assoc($res)){ $years[] = $r['academic_year']; }
    return $years;
}

// Fetch all classes for selected academic year
function fetchAllClasses($conn, $year){
    $res = mysqli_query($conn, "SELECT at.hid, at.section_id, at.academic_year,
                                       s.section_name, s.class_type, i.first_name, i.father_name
                                FROM assign_instructor at
                                LEFT JOIN sections s ON at.section_id = s.cid
                                LEFT JOIN users i ON at.instructor_id = i.uid
                                WHERE at.academic_year = '$year'
                                ORDER BY s.section_name ASC");
    $tmp = [];
    while($r = mysqli_fetch_assoc($res)){ $tmp[] = $r; }
    return $tmp;
}

// --- Main Logic ---
$years = fetchAcademicYears($conn);
$selectedYear = $_GET['academic_year'] ?? ($years[0] ?? null);
$classes = $selectedYear ? fetchAllClasses($conn, $selectedYear) : [];
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">All Classes (Director View)</h3>
    </div>

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
            <?php if(count($classes) > 0): $no=1; ?>
              <?php foreach($classes as $c): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($c['section_name']) ?></td>
                  <td><?= htmlspecialchars($c['class_type']) ?></td>
                  <td><?= htmlspecialchars($c['first_name'].' '.$c['father_name']) ?></td>
                  <td><?= htmlspecialchars($c['academic_year']) ?></td>
                  <td>
                    <button type="button"
                            class="btn btn-primary btn-sm view-students"
                            data-class-id="<?= $c['hid'] ?>"
                            data-class-name="<?= htmlspecialchars($c['section_name'].' - '.$c['class_type']) ?>"
                            data-year="<?= $c['academic_year'] ?>">
                        View Attendance
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
<div class="modal fade" id="studentsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Class Attendance</h5>
      </div>
      <div class="modal-body">
        <div id="studentsTable">Loading...</div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentClassId = null;

document.querySelectorAll(".view-students").forEach(btn => {
  btn.addEventListener("click", function() {
    currentClassId = this.dataset.classId;
    const className = this.dataset.className;
    const year = this.dataset.year;

    document.querySelector("#studentsModal .modal-title").innerText =
      "Attendance • " + className + " (" + year + ")";

    loadAttendanceSheet(currentClassId, 1);

    const modal = new bootstrap.Modal(document.getElementById("studentsModal"));
    modal.show();
  });
});

function loadAttendanceSheet(classId, semester){
  const target = document.getElementById('studentsTable');
  target.innerHTML = "Loading...";
  fetch("fetch_view_attendance.php?class_id=" + classId + "&semester=" + semester)
    .then(res => res.text())
    .then(html => { target.innerHTML = html; })
    .catch(() => { target.innerHTML = "<div class='text-danger'>Failed to load.</div>"; });
}

// bind close button inside fetched content
      const closeBtn = document.getElementById("closeAttendanceBtn");
      if (closeBtn) {
        closeBtn.addEventListener("click", () => {
          studentsModal.hide();
        });
      }
    })
    .catch(() => { target.innerHTML = "<div class='text-danger'>Failed to load.</div>"; });
// search + semester handlers remain same as your instructor page
</script>

<?php include('../Admin/footer.php'); ?>
