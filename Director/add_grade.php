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
<!-- Page Header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Manage Students</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Students</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Delete Student</a></li>
      </ul>
    </div>

    <!-- Filter First: Section + Academic Year (like view_class.php) -->
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:800px; width:100%;">
          <div class="text-center mb-3">
            <h5 class="fw-bold">Select Section and Academic Year</h5>
            <p class="text-muted mb-0">Choose a section and year to add/edit marks.</p>
          </div>
          <div class="row g-3 align-items-end">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Section</label>
              <select id="sectionSelect" class="form-select form-select-lg">
                <option value="">-- Select Section --</option>
                <?php
                $sections = mysqli_query($conn,"SELECT cid, section_name, class_type FROM sections ORDER BY class_type, section_name ASC");
                $grouped_sections = [];
                while($sec=mysqli_fetch_assoc($sections)) $grouped_sections[$sec['class_type']][] = $sec;
                foreach($grouped_sections as $type => $secs): ?>
                  <optgroup label="<?= htmlspecialchars($type) ?>">
                    <?php foreach($secs as $s): ?>
                      <option value="<?= (int)$s['cid'] ?>" data-type="<?= htmlspecialchars($s['class_type']) ?>"><?= htmlspecialchars($s['section_name']) ?></option>
                    <?php endforeach; ?>
                  </optgroup>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Academic Year</label>
              <select id="academicYear" class="form-select form-select-lg">
                <option value="">-- Select Academic Year --</option>
                <?php foreach($years as $y): ?>
                  <option value="<?= htmlspecialchars($y) ?>" <?= ($y==$selectedYear)?'selected':'' ?>><?= htmlspecialchars($y) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2 d-grid">
              <button type="button" id="showSubjectsBtn" class="btn btn-primary btn-md">Show Sections</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Subjects picker Modal -->
<div class="modal fade" id="subjectsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Subjects for <span id="subjectsModalSection"></span> • <span id="subjectsModalYear"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th><th>Subject</th><th>Assigned Teacher</th><th>Action</th>
              </tr>
            </thead>
            <tbody id="subjectsTableBody"></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
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

  // Filter-first: show subjects list in modal
  document.getElementById('showSubjectsBtn').addEventListener('click', function(){
    const secSel = document.getElementById('sectionSelect');
    const yearSel = document.getElementById('academicYear');
    const sectionId = secSel.value;
    const year = yearSel.value;
    const sectionName = secSel.options[secSel.selectedIndex]?.text || '';
    if(!sectionId || !year){
      Swal.fire('Warning','Please select section and academic year','warning');
      return;
    }

    document.getElementById('subjectsModalSection').textContent = sectionName;
    document.getElementById('subjectsModalYear').textContent = year;
    const body = document.getElementById('subjectsTableBody');
    body.innerHTML = '<tr><td colspan="4" class="text-center"><span class="spinner-border spinner-border-sm me-2"></span>Loading subjects…</td></tr>';
    new bootstrap.Modal(document.getElementById('subjectsModal')).show();

    // Fetch subjects and teacher assignments (single request each)
    Promise.all([
      fetch('fetch_section_subjects.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'section_id='+encodeURIComponent(sectionId)
      }).then(r=>r.json()),
      fetch('fetch_section_teachers.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`section_id=${encodeURIComponent(sectionId)}&academic_year=${encodeURIComponent(year)}`
      }).then(r=>r.json())
    ])
    .then(([subjects, assignments])=>{
      if(!Array.isArray(subjects) || subjects.length===0){
        body.innerHTML = '<tr><td colspan="4" class="text-center text-danger">No subjects found.</td></tr>';
        return;
      }
      const assignMap = new Map();
      if(Array.isArray(assignments)){
        assignments.forEach(a=> assignMap.set(String(a.suid||a.subject_id), a));
      }
      body.innerHTML='';
      let idx=0;
      subjects.forEach(sub=>{
        const a = assignMap.get(String(sub.suid)) || {};
        const teacherName = a.assigned_teacher_name || '';
        const assigned = !!teacherName;
        const badge = assigned 
          ? '<span class="badge bg-success">Assigned</span>' 
          : '<span class="badge bg-secondary">Unassigned</span>';
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${++idx}</td>
          <td>${(sub.abbreviation_name?sub.abbreviation_name+ ' - ':'') + sub.subject_name} ${badge}</td>
          <td>${teacherName || '<span class="text-muted">No Teacher</span>'}</td>
          <td>
            <button class="btn ${assigned?'btn-success':'btn-secondary'} btn-sm openAddMarksBtn" 
                    data-section="${sectionId}" data-year="${year}" data-subject="${sub.suid}" 
                    data-subject-name="${sub.subject_name}" data-section-name="${sectionName}" 
                    data-teacher="${teacherName}" data-assigned="${assigned?1:0}" ${assigned?'':'disabled'}>
              ${assigned?'Add/Edit Marks':'No Teacher Assigned'}
            </button>
          </td>`;
        body.appendChild(tr);
      });

      // bind open buttons
      document.querySelectorAll('.openAddMarksBtn').forEach(btn=>{
        btn.addEventListener('click', function(){
          const assigned = this.dataset.assigned==='1';
          if(!assigned){ Swal.fire('Error','No teacher assigned for this subject','error'); return; }
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
    })
    .catch(()=>{
      body.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Failed to load data.</td></tr>';
    });
  });

  function loadStudents(){
    const sem = document.getElementById('semesterSelect').value;
    fetch(`ajax_fetch_students.php?section_id=${current.section}&subject_id=${current.subject}&year=${current.year}&semester=${sem}`)
      .then(res=>res.text())
      .then(html=>{
        document.getElementById('studentsContainer').innerHTML = html;
      });
  }

  // legacy binding removed; buttons are bound after subjects table renders

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
