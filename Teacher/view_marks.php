<?php
include('teacherHeader.php'); // sets $conn and session

// --- AUTH ---
$uid = $_SESSION['uid'] ?? null;
if (!$uid) { echo "Not logged in."; exit; }
$profile = getUserByID($uid);
if (getRoleNameById($profile['user_type']) !== 'Teacher') {
    echo "Not authorized."; exit;
}

// --- Helpers ---
function fetchAssignedYears($conn,$teacher_id){
    $teacher_id = (int)$teacher_id;
    $res = mysqli_query($conn,"SELECT DISTINCT academic_year FROM assign_teacher WHERE teacher_id=$teacher_id ORDER BY academic_year DESC");
    $years = [];
    while($r=mysqli_fetch_assoc($res)) $years[]=$r['academic_year'];
    return $years;
}

function fetchAssignedClasses($conn,$teacher_id,$year){
    $teacher_id = (int)$teacher_id;
    $year = mysqli_real_escape_string($conn,$year);
    $sql = "SELECT at.atid, at.section_id, at.subject_id, at.academic_year,
                   s.section_name, s.class_type,
                   sub.abbreviation_name AS subject_abbr, sub.subject_name
            FROM assign_teacher at
            LEFT JOIN sections s ON at.section_id=s.cid
            LEFT JOIN subjects sub ON at.subject_id=sub.suid
            WHERE at.teacher_id=$teacher_id AND at.academic_year='$year'
            ORDER BY s.section_name ASC, sub.subject_name ASC";
    $res = mysqli_query($conn,$sql);
    $rows = [];
    while($r=mysqli_fetch_assoc($res)) $rows[]=$r;
    return $rows;
}

// --- Main ---
$years = fetchAssignedYears($conn,$uid);
$selectedYear = $_GET['academic_year'] ?? ($years[0]??null);
$classes = $selectedYear ? fetchAssignedClasses($conn,$uid,$selectedYear) : [];
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">View Marks</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Marks</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">View Marks</a></li>
      </ul>
    </div>

    <form method="GET" class="mb-3">
      <label>Academic Year:</label>
      <select name="academic_year" class="form-control w-auto d-inline-block">
        <?php foreach($years as $y): ?>
          <option value="<?= htmlspecialchars($y) ?>" <?= ($y==$selectedYear)?'selected':'' ?>><?= htmlspecialchars($y) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-primary btn-sm">Show Classes</button>
    </form>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-hover text-center align-middle">
          <thead class="table-secondary">
            <tr><th>#</th><th>Class</th><th>Subject</th><th>Year</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php if(count($classes)>0): $no=1; ?>
              <?php foreach($classes as $c): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($c['section_name'].' - '.$c['class_type']) ?></td>
                  <td><?= htmlspecialchars($c['subject_abbr'].' ('.$c['subject_name'].')') ?></td>
                  <td><?= htmlspecialchars($c['academic_year']) ?></td>
                  <td>
                    <button class="btn btn-info btn-sm view-students-btn"
                            data-atid="<?= (int)$c['atid'] ?>"
                            data-section="<?= (int)$c['section_id'] ?>"
                            data-subject="<?= (int)$c['subject_id'] ?>"
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

<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Class Students</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Semester:</label>
          <select id="semesterSelect" class="form-control w-auto d-inline-block">
            <option value="1">1st Semester</option>
            <option value="2">2nd Semester</option>
          </select>
          
        </div>
        <div class="mb-3">
          <input type="text" id="searchStudent" class="form-control" placeholder="Search Student by Name or SID">
        </div>
        <div id="studentsContainer">Loading...</div>

        <div id="studentDetails" class="mt-4" style="display:none;">
          <h5>Student Details</h5>
          <button id="closeDetails" class="btn btn-sm btn-warning ms-2">Close Details</button>
          <table class="table table-bordered">
            <thead class="table-light">
              <tr><th>Subject</th><th>Teacher</th><th>Result</th></tr>
            </thead>
            <tbody id="studentDetailsBody"></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  let current = {};

  const studentsContainer = document.getElementById('studentsContainer');
  const studentDetails = document.getElementById('studentDetails');
  const studentDetailsBody = document.getElementById('studentDetailsBody');
  const semesterSelect = document.getElementById('semesterSelect');
  const searchStudent = document.getElementById('searchStudent');
  const closeDetailsBtn = document.getElementById('closeDetails');

  function loadStudents(){
    const sem = semesterSelect.value;
    studentsContainer.innerHTML = 'Loading...';
    fetch(`ajax_fetch_students_marks.php?atid=${current.atid}&section_id=${current.section}&year=${current.year}&semester=${sem}`)
      .then(res => res.text())
      .then(html => {
        studentsContainer.innerHTML = html;
        studentDetails.style.display = 'none';
      })
      .catch(err => console.error('Error loading students:', err));
  }

  // Open modal
  document.querySelectorAll('.view-students-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      current.atid = this.dataset.atid;
      current.section = this.dataset.section;
      current.year = this.dataset.year;
      loadStudents();
      new bootstrap.Modal(document.getElementById('studentsModal')).show();
    });
  });

  // Reload students on semester change
  semesterSelect.addEventListener('change', loadStudents);

  // Search filter
  searchStudent.addEventListener('keyup', function(){
    const filter = this.value.toLowerCase();
    document.querySelectorAll('#studentsContainer tbody tr').forEach(tr => {
      tr.style.display = tr.textContent.toLowerCase().includes(filter) ? '' : 'none';
    });
  });

  // Close student details
  closeDetailsBtn.addEventListener('click', function(){
    studentDetails.style.display = 'none';
  });

  // Event delegation for dynamic Details button
  document.addEventListener('click', function(e){
    if(e.target.classList.contains('view-details-btn')){
      const sid = e.target.dataset.sid;
      const sem = semesterSelect.value;

      fetch(`ajax_fetch_student_results.php?student_id=${sid}&year=${current.year}&semester=${sem}`)
        .then(res => res.json())
        .then(data => {
          let rows = '';
          if(data.length > 0){
            data.forEach(d => {
              rows += `<tr>
                <td>${d.subject_name}</td>
                <td>${d.teacher_name}</td>
                <td>${d.result}</td>
              </tr>`;
            });
          } else {
            rows = `<tr><td colspan="3" class="text-center">No results found</td></tr>`;
          }
          studentDetailsBody.innerHTML = rows;
          studentDetails.style.display = 'block';
        })
        .catch(err => console.error('Error loading details:', err));
    }
  });

});
</script>



<?php include('../Admin/footer.php'); ?>
