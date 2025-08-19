<?php
include('teacherHeader.php'); 

// --- Check login ---
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);
if (!isset($_SESSION["uid"]) || $roleName != "Teacher") {
    echo "You are not authorized to view this page.";
    exit;
}

// --- Helpers ---
// Fetch all academic years
function fetchAcademicYears($conn){
    $res = mysqli_query($conn, "SELECT DISTINCT academic_year 
                                FROM assign_teacher
                                ORDER BY academic_year DESC");
    $years = [];
    while($r = mysqli_fetch_assoc($res)){ $years[] = $r['academic_year']; }
    return $years;
}

// Fetch all classes for selected year
function fetchAllClasses($conn, $year){
    $res = mysqli_query($conn, "SELECT DISTINCT at.atid, at.section_id, at.academic_year,
                                       s.section_name, s.class_type, i.first_name, i.father_name
                                FROM assign_teacher at
                                LEFT JOIN sections s ON at.section_id = s.cid
                                LEFT JOIN users i ON at.teacher_id = i.uid
                                WHERE at.academic_year = '$year'
                                ORDER BY s.section_name ASC limit 1");
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
                  <td><?= htmlspecialchars($c['academic_year']) ?></td>
                  <td>
                    <button type="button"
                            class="btn btn-primary btn-sm view-students"
                            data-class-id="<?= $c['atid'] ?>"
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

<script>
let currentClassId = null;
let studentsModal = null;

// open modal
document.querySelectorAll(".view-students").forEach(btn => {
  btn.addEventListener("click", function() {
    currentClassId = this.dataset.classId;
    const className = this.dataset.className;
    const year = this.dataset.year;

    document.querySelector("#studentsModal .modal-title").innerText =
      "Attendance • " + className + " (" + year + ")";

    loadAttendanceSheet(currentClassId, 0, "weekly");

    studentsModal = new bootstrap.Modal(document.getElementById("studentsModal"));
    studentsModal.show();
  });
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
