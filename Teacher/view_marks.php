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
      <h3 class="fw-bold mb-3"> View Results</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Results</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">View Results</a></li>
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
                    <button class="btn btn-success btn-sm add-marks-btn"
                            data-atid="<?= (int)$c['atid'] ?>"
                            data-section="<?= (int)$c['section_id'] ?>"
                            data-subject="<?= (int)$c['subject_id'] ?>"
                            data-year="<?= htmlspecialchars($c['academic_year']) ?>"
                            data-class="<?= htmlspecialchars($c['section_name'].' - '.$c['class_type']) ?>"
                            data-subject-name="<?= htmlspecialchars($c['subject_abbr'].' ('.$c['subject_name'].')') ?>">
                      View Results
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
<div class="modal fade" id="addMarksModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="marksForm">
        <div class="modal-header">
          <h5 class="modal-title">
            View Results
            <small id="modalTitleInfo" class="text-muted d-block fw-bold"></small>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label>Semester:</label>
              <select id="semesterSelect" name="semester" class="form-control w-100">
                <option value="1">1st Semester</option>
                <option value="2">2nd Semester</option>
              </select>
            </div>
            <div class="col-md-6">
              <label>Search Student:</label>
              <input type="text" id="searchStudent" class="form-control" placeholder="Search by Name or SID">
            </div>
          </div>
          <div id="studentsContainer">Loading...</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  let current = {};

  function loadStudents(){
    const sem = document.getElementById('semesterSelect').value;
    fetch(`ajax_fetch_students_marks.php?atid=${current.atid}&section_id=${current.section}&subject_id=${current.subject}&year=${current.year}&semester=${sem}`)
      .then(res=>res.text())
      .then(html=>{
        document.getElementById('studentsContainer').innerHTML = html;
      });
  }

  // Open modal
  document.querySelectorAll('.add-marks-btn').forEach(btn=>{
    btn.addEventListener('click', function(){
      current.atid = this.dataset.atid;
      current.section = this.dataset.section;
      current.subject = this.dataset.subject;
      current.year = this.dataset.year;

      // Update modal title dynamically
      document.getElementById('modalTitleInfo').textContent = 
        `${this.dataset.class} | ${this.dataset.subjectName} | Year: ${this.dataset.year}`;

      const form = document.getElementById('marksForm');
      ['atid','academic_year','section_id','subject_id'].forEach(k=>{
        let i=form.querySelector(`input[name="${k}"]`);
        if(!i){ i=document.createElement('input'); i.type='hidden'; i.name=k; form.appendChild(i);}
        if(k==='academic_year') i.value = current.year;
        else if(k==='subject_id') i.value = current.subject;
        else if(k==='section_id') i.value = current.section;
        else i.value = current.atid;
      });

      loadStudents();
      new bootstrap.Modal(document.getElementById('addMarksModal')).show();
    });
  });

  // Semester change reload
  document.getElementById('semesterSelect').addEventListener('change', loadStudents);

  // Search filter
  document.getElementById('searchStudent').addEventListener('keyup', function(){
    const filter = this.value.toLowerCase();
    document.querySelectorAll('#studentsContainer tbody tr').forEach(tr=>{
      tr.style.display = tr.textContent.toLowerCase().includes(filter)?'':'none';
    });
  });
});
</script>

<?php include('../Admin/footer.php'); ?>
