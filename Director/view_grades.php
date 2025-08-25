<?php
include('directorHeader.php');

// Auth guard
if (!isset($_SESSION['uid'])) die('You must be logged in.');
$user = getUserByID($_SESSION['uid']);
if (!$user || getRoleNameById($user['user_type']) !== 'Director') die('Not authorized.');
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Manage Grade</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Grade Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Grade</a></li>
      </ul>
    </div>

    <!-- Filter first -->
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:800px; width:100%;">
          <div class="text-center mb-3">
            <h5 class="fw-bold">Select Section and Academic Year</h5>
            <p class="text-muted mb-0">Filter by section and year to list students.</p>
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
                <?php
                $yrs = mysqli_query($conn, "SELECT DISTINCT academic_year FROM assign_student ORDER BY academic_year DESC");
                while($r = mysqli_fetch_assoc($yrs)){
                  $ay = htmlspecialchars($r['academic_year']);
                  echo "<option value='{$ay}'>{$ay}</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-2 d-grid">
              <button type="button" id="showStudentsBtn" class="btn btn-primary btn-md">View</button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Students Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Students</h5>
        <div class="ms-auto" data-role="modal-search" style="min-width:260px;">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-primary text-white"><i class="bi bi-search"></i></span>
            <input type="text" id="studentSearch" class="form-control" placeholder="Search students...">
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="studentsWrap">Loading...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Grade Modal -->
<div class="modal fade" id="gradeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Grades</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="gradesWrap">Loading...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
  let studentsModal, gradeModal;

  document.getElementById('showStudentsBtn').addEventListener('click', function(){
    const secSel = document.getElementById('sectionSelect');
    const yearSel = document.getElementById('academicYear');
    const sectionId = secSel.value;
    const year = yearSel.value;
    if(!sectionId || !year){
      return Swal.fire('Warning','Please select section and academic year','warning');
    }

  // Fetch students for this section+year
    fetch('fetch_section_students.php', {
      method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`section_id=${encodeURIComponent(sectionId)}&academic_year=${encodeURIComponent(year)}`
    }).then(r=>r.json()).then(data=>{
      if(!Array.isArray(data) || data.length===0){
        document.getElementById('studentsWrap').innerHTML = '<div class="text-center text-muted py-4">No students found</div>';
      } else {
        const rows = data.map((s,idx)=>{
          const name = `${s.first_name} ${s.father_name}`;
          return `<tr>
            <td>${idx+1}</td>
            <td>${s.student_id}</td>
            <td>${name}</td>
            <td>${s.class_type||''}</td>
      <td><button class="btn btn-sm btn-primary view-grade" data-sid="${s.sid}" data-year="${year}" data-name="${name.replace(/\"/g,'&quot;')}">View Grade</button></td>
          </tr>`;
        }).join('');
        document.getElementById('studentsWrap').innerHTML = `
          <div class="table-responsive">
      <table class="table table-bordered align-middle text-center" id="studentsTable">
              <thead class="table-primary">
                <tr>
          <th>#</th><th>Student ID</th><th>Full Name</th><th>Class Type</th><th>Action</th>
                </tr>
              </thead>
              <tbody>${rows}</tbody>
            </table>
          </div>`;
      }
    const modalEl = document.getElementById('studentsModal');
    const sectionName = secSel.options[secSel.selectedIndex]?.text || '';
    const classType = secSel.options[secSel.selectedIndex]?.getAttribute('data-type') || '';
    modalEl.querySelector('.modal-title').innerText = `Students • ${sectionName} - ${classType} (${year})`;
    studentsModal = new bootstrap.Modal(modalEl);
      studentsModal.show();
    }).catch(()=> Swal.fire('Error','Failed to load students','error'));
  });

  // Delegate view-grade click
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.view-grade');
    if(!btn) return;
    const sid = btn.getAttribute('data-sid');
    const year = btn.getAttribute('data-year');
    if(!sid || !year) return;

    // Load grades
    fetch(`view_grades_student.php?student_id=${encodeURIComponent(sid)}&academic_year=${encodeURIComponent(year)}`)
      .then(r=>r.text())
      .then(html=>{
        document.getElementById('gradesWrap').innerHTML = html;
        const gm = document.getElementById('gradeModal');
        const studentName = btn.getAttribute('data-name') || '';
        gm.querySelector('.modal-title').innerText = `Grades • ${studentName} (${year})`;
        gradeModal = new bootstrap.Modal(gm);
        gradeModal.show();
      })
      .catch(()=> Swal.fire('Error','Failed to load grades','error'));
  });

  // Client-side search for students in modal
  document.addEventListener('input', function(e){
    if (e.target && e.target.id === 'studentSearch'){
      const term = e.target.value.toLowerCase();
      const tbl = document.getElementById('studentsTable');
      if (!tbl || !tbl.tBodies || !tbl.tBodies[0]) return;
      Array.from(tbl.tBodies[0].rows).forEach(row => {
        const txt = row.textContent ? row.textContent.toLowerCase() : '';
        row.style.display = txt.includes(term) ? '' : 'none';
      });
    }
  });
})();
</script>

<?php include('../Admin/footer.php'); ?>
