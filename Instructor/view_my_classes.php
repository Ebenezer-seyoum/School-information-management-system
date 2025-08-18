<?php
include('instructorHeader.php'); // adjust path as needed

// --- Check login ---
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);
if(!isset($_SESSION["uid"]) || $roleName != "Instructor"){
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
    while($r = mysqli_fetch_assoc($res)){
        $years[] = $r['academic_year'];
    }
    return $years;
}

function fetchAssignedClasses($conn, $teacher_id, $year){
    $res = mysqli_query($conn, "SELECT at.hid, at.section_id, at.academic_year, s.section_name, s.class_type
                                FROM assign_instructor at
                                LEFT JOIN sections s ON at.section_id = s.cid
                                WHERE at.instructor_id = $teacher_id AND at.academic_year = '$year'
                                ORDER BY s.section_name ASC");
    $tmp = [];
    while($r = mysqli_fetch_assoc($res)){
        $tmp[] = $r;
    }
    return $tmp;
}

// --- Main Logic ---
$years = fetchAcademicYears($conn, $_SESSION["uid"]);
$selectedYear = $_GET['academic_year'] ?? ($years[0] ?? null);
$classes = $selectedYear ? fetchAssignedClasses($conn, $_SESSION["uid"], $selectedYear) : [];
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">My Classes</h3>
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
              <th>Academic Year</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if(count($classes) > 0): $no=1; ?>
              <?php foreach($classes as $c): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($c['section_name'] . ' - ' . $c['class_type']) ?></td>
                  <td><?= htmlspecialchars($c['academic_year']) ?></td>
                  <td>
                    <button type="button" 
                            class="btn btn-primary btn-sm view-students" 
                            data-class-id="<?= $c['hid'] ?>" 
                            data-class-name="<?= htmlspecialchars($c['section_name'] . ' - ' . $c['class_type']) ?>" 
                            data-year="<?= $c['academic_year'] ?>">
                        View Students
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-danger">No classes assigned for this year.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Students in Class</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="studentsTable">Loading...</div>
      </div>
    </div>
  </div>
</div>

<script>
// when click "View Students"
document.querySelectorAll(".view-students").forEach(btn => {
  btn.addEventListener("click", function() {
    let classId = this.dataset.classId;
    let className = this.dataset.className;
    let year = this.dataset.year;

    document.querySelector("#studentsModal .modal-title").innerText = 
      "Students in " + className + " (" + year + ")";
    document.querySelector("#studentsTable").innerHTML = "Loading...";

    // AJAX fetch students
    fetch("fetch_students.php?class_id=" + classId)
      .then(res => res.text())
      .then(html => {
        document.querySelector("#studentsTable").innerHTML = html;
      });

    // show modal
    let modal = new bootstrap.Modal(document.getElementById("studentsModal"));
    modal.show();
  });
});
</script>

<?php include('../Admin/footer.php'); ?>
