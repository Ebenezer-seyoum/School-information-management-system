<?php
include('directorHeader.php'); // sets $conn & session

// --- Helpers ---
function fetchAssignedYears($conn){
    $res = mysqli_query($conn,"SELECT DISTINCT academic_year FROM assign_student ORDER BY academic_year DESC");
    $years = [];
    while($r=mysqli_fetch_assoc($res)) $years[]=$r['academic_year'];
    return $years;
}

function fetchClassesByYear($conn,$year){
    $year = mysqli_real_escape_string($conn,$year);
    $sql = "SELECT DISTINCT s.cid, s.section_name, s.class_type
            FROM assign_student ast
            JOIN sections s ON ast.section_id = s.cid
            WHERE ast.academic_year='$year'
            ORDER BY s.class_type ASC, s.section_name ASC";
    $res = mysqli_query($conn,$sql);
    $rows = [];
    while($r=mysqli_fetch_assoc($res)) $rows[]=$r;
    return $rows;
}

function fetchSubjectsByClass($conn,$class_id){
    $class_id = (int)$class_id;
    $sql = "SELECT sub.suid, sub.subject_name, sub.abbreviation_name AS subject_abbr
            FROM curriculum_subjects cs
            JOIN subjects sub ON cs.subject_id = sub.suid
            WHERE cs.class_id=$class_id
            ORDER BY sub.subject_name ASC";
    $res = mysqli_query($conn,$sql);
    $rows = [];
    while($r=mysqli_fetch_assoc($res)) $rows[]=$r;
    return $rows;
}

// --- Main ---
$years = fetchAssignedYears($conn);
$selectedYear = $_GET['academic_year'] ?? ($years[0]??null);
$classes = $selectedYear ? fetchClassesByYear($conn,$selectedYear) : [];
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Add Marks (Director)</h3>
    </div>

    <!-- Academic Year Selection -->
    <form method="GET" class="mb-3">
      <label>Academic Year:</label>
      <select name="academic_year" class="form-control w-auto d-inline-block">
        <?php foreach($years as $y): ?>
          <option value="<?= htmlspecialchars($y) ?>" <?= ($y==$selectedYear)?'selected':'' ?>><?= htmlspecialchars($y) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-primary btn-sm">Show Classes</button>
    </form>

    <!-- Classes List -->
    <div class="accordion" id="classesAccordion">
      <?php if(count($classes)>0): $classNo=0; ?>
        <?php foreach($classes as $c): $classNo++; ?>
          <div class="accordion-item">
            <h2 class="accordion-header" id="heading<?= $classNo ?>">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $classNo ?>">
                <?= htmlspecialchars($c['section_name'].' - '.$c['class_type']) ?>
              </button>
            </h2>
            <div id="collapse<?= $classNo ?>" class="accordion-collapse collapse" data-bs-parent="#classesAccordion">
              <div class="accordion-body">
                <ul class="list-group">
                  <?php 
                    $subjects = fetchSubjectsByClass($conn,$c['cid']);
                    if(count($subjects)>0):
                      foreach($subjects as $s):
                        // Check assigned teacher
                        $teacher_res = mysqli_query($conn,"SELECT teacher_id FROM assign_teacher 
                            WHERE section_id=".$c['cid']." AND subject_id=".$s['suid']." 
                              AND academic_year='$selectedYear' LIMIT 1");
                        $teacher_assigned = mysqli_num_rows($teacher_res)>0;

                        if($teacher_assigned){
                            $teacher_row = mysqli_fetch_assoc($teacher_res);
                            $teacher_info = getUserByID($teacher_row['teacher_id']);
                            $teacher_name = $teacher_info['first_name'].' '.$teacher_info['father_name'];
                        } else {
                            $teacher_name = '';
                        }
                  ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <?= htmlspecialchars($s['subject_abbr'].' ('.$s['subject_name'].')') ?>
                      <button class="btn <?= $teacher_assigned?'btn-success':'btn-secondary' ?> btn-sm add-marks-btn"
                              data-section="<?= (int)$c['cid'] ?>"
                              data-subject="<?= (int)$s['suid'] ?>"
                              data-year="<?= htmlspecialchars($selectedYear) ?>"
                              data-teacher="<?= htmlspecialchars($teacher_name) ?>"
                              data-subject-name="<?= htmlspecialchars($s['subject_name']) ?>"
                              data-section-name="<?= htmlspecialchars($c['section_name'].' - '.$c['class_type']) ?>"
                              data-assigned="<?= $teacher_assigned?'1':'0' ?>">
                        <?= $teacher_assigned?'Add/Edit Marks':'No Teacher Assigned' ?>
                      </button>
                    </li>
                  <?php endforeach; else: ?>
                    <li class="list-group-item text-danger">No subjects found for this class.</li>
                  <?php endif; ?>
                </ul>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-danger">No classes found for the selected year.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal for Marks -->
<div class="modal fade" id="addMarksModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="marksForm">
        <div class="modal-header">
          <h5 class="modal-title">Add/Edit Marks</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <strong>Academic Year: <span id="modalYear"></span></strong><br>
            <strong>Section: <span id="modalSection"></span></strong><br>
            <strong>Subject: <span id="modalSubject"></span></strong><br>
            <strong>Assigned Teacher: <span id="teacherName"></span></strong>
          </div>
          <div class="mb-3">
            <label>Semester:</label>
            <select id="semesterSelect" name="semester" class="form-control w-auto">
              <option value="1">1st Semester</option>
              <option value="2">2nd Semester</option>
            </select>
          </div>
          <div id="studentsContainer">Loading...</div>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="academic_year">
          <input type="hidden" name="section_id">
          <input type="hidden" name="subject_id">
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
    fetch(`ajax_fetch_students.php?section_id=${current.section}&subject_id=${current.subject}&year=${current.year}&semester=${sem}`)
      .then(res=>res.text())
      .then(html=>{
        document.getElementById('studentsContainer').innerHTML = html;
      });
  }

  document.querySelectorAll('.add-marks-btn').forEach(btn=>{
    btn.addEventListener('click', function(){
      const assigned = this.dataset.assigned==='1';
      if(!assigned){
        Swal.fire('Error','No teacher assigned for this subject','error'); 
        return;
      }

      current.section = this.dataset.section;
      current.subject = this.dataset.subject;
      current.year = this.dataset.year;

      document.getElementById('modalYear').innerText = current.year;
      document.getElementById('modalSection').innerText = this.dataset.sectionName;
      document.getElementById('modalSubject').innerText = this.dataset.subjectName;
      document.getElementById('teacherName').innerText = this.dataset.teacher;

      const form = document.getElementById('marksForm');
      form.querySelector('input[name="academic_year"]').value = current.year;
      form.querySelector('input[name="section_id"]').value = current.section;
      form.querySelector('input[name="subject_id"]').value = current.subject;

      loadStudents();
      new bootstrap.Modal(document.getElementById('addMarksModal')).show();
    });
  });

  document.getElementById('semesterSelect').addEventListener('change', loadStudents);

  document.getElementById('marksForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    fetch('ajax_insert_marks.php',{method:'POST',body:formData})
      .then(res=>res.text())
      .then(txt=>{
        if(txt.trim()=='success') Swal.fire('Success','Marks saved successfully','success').then(()=>location.reload());
        else Swal.fire('Error',txt,'error');
      });
  });
});
</script>

<?php include('../Admin/footer.php'); ?>
