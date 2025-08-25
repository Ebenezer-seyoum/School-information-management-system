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
      <h3 class="fw-bold mb-3"> Add Marks</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Marks</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Add Marks</a></li>
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
      <div class="card-body">
        <?php if(count($classes)>0): ?>
          <div class="row g-3">
            <?php foreach($classes as $c): ?>
              <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm">
                  <div class="card-body d-flex flex-column">
                    <h6 class="mb-1"><?= htmlspecialchars($c['section_name']) ?></h6>
                    <div class="text-muted small mb-2"><?= htmlspecialchars($c['class_type']) ?></div>
                    <div class="small mb-2"><b>Subject:</b> <?= htmlspecialchars($c['subject_abbr'].' ('.$c['subject_name'].')') ?></div>
                    <div class="small text-secondary mb-3">Year: <?= htmlspecialchars($c['academic_year']) ?></div>
                    <div class="mt-auto">
                      <button class="btn btn-success btn-sm w-100 add-marks-btn"
                              data-atid="<?= (int)$c['atid'] ?>"
                              data-section="<?= (int)$c['section_id'] ?>"
                              data-subject="<?= (int)$c['subject_id'] ?>"
                              data-year="<?= htmlspecialchars($c['academic_year']) ?>"
                              data-class="<?= htmlspecialchars($c['section_name'].' - '.$c['class_type']) ?>"
                              data-subject-name="<?= htmlspecialchars($c['subject_abbr'].' ('.$c['subject_name'].')') ?>">
                        Add/Edit Marks
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
<div class="modal fade" id="addMarksModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="marksForm">
        <div class="modal-header">
          <h5 class="modal-title">
            Add/Edit Marks
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
              <div class="search-box w-100">
                <div class="input-group">
                  <span class="input-group-text bg-primary text-white"><i class="fas fa-search"></i></span>
                  <input type="text" id="searchStudent" class="form-control search-input" placeholder="Search by ID, Name, or Role...">
                  <button class="btn btn-primary" type="button" id="searchTrigger" aria-label="Search">Search</button>
                </div>
              </div>
            </div>
          </div>
          <div id="studentsContainer">Loading...</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save All Marks</button>
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
    fetch(`ajax_fetch_students.php?atid=${current.atid}&section_id=${current.section}&subject_id=${current.subject}&year=${current.year}&semester=${sem}`)
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

  // Save all marks
  document.getElementById('marksForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    fetch('ajax_insert_marks.php',{method:'POST',body:formData})
      .then(res=>res.text())
      .then(txt=>{
        if(txt.trim()=='success') Swal.fire('Success','Marks saved successfully','success');
        else Swal.fire('Error',txt,'error');
      });
  });

  // Edit individual mark
  document.addEventListener('click', function(e){
    const btn=e.target.closest('.edit-mark-btn');
    if(!btn) return;

    const sid = btn.dataset.sid;
    const status = parseInt(btn.dataset.status);
    const input = document.getElementById('markInput'+sid);

    if(status==2){
      Swal.fire('Info','Please contact director','info');
      return;
    }

    Swal.fire({
      title: 'Are you sure you want to edit this mark?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: 'Cancel',
      backdrop: true,
      didOpen: ()=>{ document.querySelector('.swal2-container').style.zIndex='11000'; }
    }).then(result=>{
      if(result.isConfirmed){
        const mark = input.value;
        const formData = new FormData();
        formData.append('student_id[]', sid);
        formData.append('mark[]', mark);
        formData.append('section_id', current.section);
        formData.append('subject_id', current.subject);
        formData.append('academic_year', current.year);
        formData.append('semester', document.getElementById('semesterSelect').value);
        formData.append('atid', current.atid);

        fetch('ajax_insert_marks.php',{method:'POST',body:formData})
          .then(res=>res.text())
          .then(txt=>{
            if(txt.trim()=='success'){
              Swal.fire({title:'Success', text:'Mark updated successfully', icon:'success'});
            } else Swal.fire('Error',txt,'error');
          });
      }
    });
  });
});
</script>

<?php include('../Admin/footer.php'); ?>
