<?php
include('teacherHeader.php'); // adjust path

// --- Auth check ---
if (!isset($_SESSION["uid"])) {
    echo "You are not authorized to view this page.";
    exit;
}

$profile  = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);

if ($roleName !== "Teacher") {
    echo "You are not authorized to view this page.";
    exit;
}

// --- Helpers ---
// Fetch academic years assigned to this teacher
function fetchAcademicYears($conn, $teacher_id) {
    $res = mysqli_query($conn, "SELECT DISTINCT academic_year 
                                FROM assign_teacher 
                                WHERE teacher_id = $teacher_id
                                ORDER BY academic_year DESC");
    $years = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $years[] = $r['academic_year'];
    }
    return $years;
}

// Fetch assigned classes for a specific year
function fetchAssignedClasses($conn, $teacher_id, $year) {
    $res = mysqli_query($conn, "SELECT at.atid, at.section_id, at.academic_year, 
                                       s.section_name, s.class_type, sub.subject_name
                                FROM assign_teacher at
                                LEFT JOIN sections s ON at.section_id = s.cid
                                LEFT JOIN subjects sub ON at.subject_id = sub.suid
                                WHERE at.teacher_id = $teacher_id 
                                  AND at.academic_year = '$year'
                                ORDER BY s.section_name ASC");
    $tmp = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $tmp[] = $r;
    }
    return $tmp;
}

// --- Main Logic ---
$years        = fetchAcademicYears($conn, $_SESSION["uid"]);
$selectedYear = $_GET['academic_year'] ?? ($years[0] ?? null);
$classes      = $selectedYear ? fetchAssignedClasses($conn, $_SESSION["uid"], $selectedYear) : [];
?>
<!-- page header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Assigned Class</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Class Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Assigned Class</a></li>
      </ul>
  </div>
<!-- end page header -->

    <!-- Academic Year Filter -->
    <form method="GET" class="mb-3">
        <label for="academic_year">Select Academic Year:</label>
        <select name="academic_year" id="academic_year" class="form-control w-auto d-inline-block">
            <?php foreach ($years as $year): ?>
                <option value="<?= $year ?>" <?= ($year == $selectedYear) ? 'selected' : '' ?>>
                    <?= $year ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Show Classes</button>
    </form>

    <!-- Classes Table -->
    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-hover text-center align-middle" id="classTable">
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
            <?php if (count($classes) > 0): $no=1; ?>
              <?php foreach ($classes as $c): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($c['section_name'] . ' - ' . $c['class_type']) ?></td>
                  <td><?= htmlspecialchars($c['subject_name']) ?></td>
                  <td><?= htmlspecialchars($c['academic_year']) ?></td>
                  <td>
                    <button 
                      type="button"
                      class="btn btn-primary btn-sm view-students"
                      data-id="<?= $c['atid'] ?>"
                      data-class="<?= htmlspecialchars($c['section_name'].' - '.$c['class_type']) ?>"
                      data-year="<?= htmlspecialchars($c['academic_year']) ?>">
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

<!-- Modal for Students -->
<div class="modal fade" id="studentsModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Students in Class</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="studentsContent">
          <!-- AJAX loads table from view_Allstudents.php -->
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('../Admin/footer.php'); ?>

<script>
$(document).ready(function(){
  $('.view-students').on('click', function(){
    let classId   = $(this).data('id');
    let className = $(this).data('class');
    let year      = $(this).data('year');

    $('#studentsModal .modal-title').text(`Students in ${className} (${year})`);

    // Load student table dynamically
    $.get('view_Allstudents.php', { class_id: classId }, function(html){
      $('#studentsContent').html(html);

      // Initialize DataTable inside modal
      $('#studentsTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy','csv','excel','pdf','print'],
        pageLength: 10
      });

      $('#studentsModal').modal('show');
    });
  });
});

</script>
