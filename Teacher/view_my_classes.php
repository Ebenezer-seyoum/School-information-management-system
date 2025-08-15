<?php
include('teacherHeader.php'); // adjust path as needed

// Get teacher profile
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);

if(!isset($_SESSION["uid"]) || $roleName != "Teacher"){
    echo "You are not authorized to view this page.";
    exit;
}

// --- Helpers ---

// Fetch academic years assigned to this teacher
function fetchAcademicYears($conn, $teacher_id){
    $res = mysqli_query($conn, "SELECT DISTINCT academic_year 
                                FROM assign_teacher 
                                WHERE teacher_id = $teacher_id
                                ORDER BY academic_year DESC");
    $years = [];
    while($r = mysqli_fetch_assoc($res)){
        $years[] = $r['academic_year'];
    }
    return $years;
}

// Fetch assigned classes for a specific year
function fetchAssignedClasses($conn, $teacher_id, $year){
    $res = mysqli_query($conn, "SELECT at.atid, at.section_id, at.academic_year, s.section_name, s.class_type, sub.subject_name
                                FROM assign_teacher at
                                LEFT JOIN sections s ON at.section_id = s.cid
                                LEFT JOIN subjects sub ON at.subject_id = sub.suid
                                WHERE at.teacher_id = $teacher_id AND at.academic_year = '$year'
                                ORDER BY s.section_name ASC");
    $tmp = [];
    while($r = mysqli_fetch_assoc($res)){
        $tmp[] = $r;
    }
    return $tmp;
}

// Fetch students for a specific class
function fetchStudentsByClass($conn, $class_id) {
    if(!$class_id) return [];
    $res = mysqli_query($conn, "SELECT u.sid, u.first_name, u.father_name, u.gender
                                FROM assign_student ast
                                LEFT JOIN students u ON ast.student_id = u.sid
                                WHERE ast.section_id = $class_id
                                ORDER BY u.first_name ASC");
    $students = [];
    while($r = mysqli_fetch_assoc($res)){
        $students[] = $r;
    }
    return $students;
}

// --- Main Logic ---
$years = fetchAcademicYears($conn, $_SESSION["uid"]);

// Determine selected year
$selectedYear = $_GET['academic_year'] ?? ($years[0] ?? null);

// Fetch classes for selected year
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
              <th>Subject</th>
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
                  <td><?= htmlspecialchars($c['subject_name']) ?></td>
                  <td><?= htmlspecialchars($c['academic_year']) ?></td>
                  <td>
                    <a href="view_Allstudents.php?class_id=<?= $c['atid'] ?>" class="btn btn-primary btn-sm">View Students</a>
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

<?php include('../Admin/footer.php'); ?>
