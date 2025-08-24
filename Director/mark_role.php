<?php
include('directorHeader.php'); // $conn & session

$uid = $_SESSION['uid'] ?? 0;
$profile = getUserByID($uid);
if(getRoleNameById($profile['user_type'])!=='Director'){
    echo "Not authorized"; exit;
}

// Fetch Academic Years (from assign_student for marks)
$years_res = mysqli_query($conn,"SELECT DISTINCT academic_year FROM assign_student ORDER BY academic_year DESC");
$years = [];
while($row = mysqli_fetch_assoc($years_res)) $years[] = $row['academic_year'];
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Manage grade</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Grade Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Grade</a></li>
      </ul>
    </div>

    <!-- Filter First: Section + Academic Year -->
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-center">
        <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:800px; width:100%;">
          <div class="text-center mb-3">
            <h5 class="fw-bold">Select Section and Academic Year</h5>
            <p class="text-muted mb-0">Choose a section and year to manage mark lock status.</p>
          </div>
          <div class="row g-3 align-items-end">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Section</label>
              <select id="sectionSelect" class="form-select form-select-lg">
                <option value="">-- Select Section --</option>
                <?php
                $sections = mysqli_query($conn,"SELECT cid, section_name, class_type FROM sections ORDER BY class_type, section_name ASC");
                $grouped = [];
                while($sec = mysqli_fetch_assoc($sections)) $grouped[$sec['class_type']][] = $sec;
                foreach($grouped as $type=>$secs): ?>
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
                  <option value="<?= htmlspecialchars($y) ?>"><?= htmlspecialchars($y) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2 d-grid">
              <button id="showSubjectsBtn" class="btn btn-primary btn-md">Show sections</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Subjects Modal -->
<div class="modal fade" id="subjectsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Subjects for <span id="subjectsModalSection"></span> • <span id="subjectsModalYear"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th style="width:5%">#</th>
                <th>Subject</th>
                <th>Assigned Teacher</th>
                <th style="width:20%">Action</th>
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

<!-- Marks Modal (Bootstrap) -->
<div class="modal fade" id="marksModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalTitle" class="modal-title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex gap-2 mb-3 align-items-center">
          <select id="modalSemester" class="form-select w-auto">
            <option value="1">1st Sem</option>
            <option value="2">2nd Sem</option>
          </select>
        </div>
        <div class="table-responsive" style="max-height:420px;">
          <table class="table table-bordered table-sm align-middle">
            <thead class="table-dark">
              <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Mark</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="modalMarksBody">
              <tr><td colspan="5" class="text-center text-muted">Select a subject from previous step</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const sectionSelect = document.getElementById('sectionSelect');
  const yearSelect = document.getElementById('academicYear');
  const showSubjectsBtn = document.getElementById('showSubjectsBtn');
  const subjectsModalEl = document.getElementById('subjectsModal');
  const subjectsModal = new bootstrap.Modal(subjectsModalEl);
  const subjectsTableBody = document.getElementById('subjectsTableBody');

  const marksModalEl = document.getElementById('marksModal');
  const marksModal = new bootstrap.Modal(marksModalEl);
  const modalTitle = document.getElementById('modalTitle');
  const modalSemester = document.getElementById('modalSemester');
  const modalMarksBody = document.getElementById('modalMarksBody');

  const state = { sectionId:null, sectionName:'', classType:'', year:'', subjectId:null, teacherName:'' };

  showSubjectsBtn.addEventListener('click', function(){
    const sectionId = sectionSelect.value;
    const year = yearSelect.value;
    const sectionName = sectionSelect.options[sectionSelect.selectedIndex]?.text || '';
    const classType = sectionSelect.options[sectionSelect.selectedIndex]?.getAttribute('data-type') || '';
    if(!sectionId || !year){
      Swal.fire('Warning','Please select section and academic year','warning');
      return;
    }
    state.sectionId = sectionId; state.sectionName = sectionName; state.classType = classType; state.year = year;
    document.getElementById('subjectsModalSection').textContent = sectionName;
    document.getElementById('subjectsModalYear').textContent = year;
    subjectsTableBody.innerHTML = '<tr><td colspan="4" class="text-center"><span class="spinner-border spinner-border-sm me-2"></span>Loading…</td></tr>';
    subjectsModal.show();

    fetch(`ajax_role_subjects.php?year=${encodeURIComponent(year)}&class_id=${encodeURIComponent(sectionId)}`)
      .then(r=>r.json())
      .then(list=>{
        if(!Array.isArray(list) || list.length===0){
          subjectsTableBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">No subjects found.</td></tr>';
          return;
        }
        subjectsTableBody.innerHTML='';
        let idx=0;
        list.forEach(sub=>{
          const suid = sub.suid || sub.subject_id;
          const teacherName = sub.teacher_name || '';
          const assigned = !!teacherName;
          const badge = assigned ? '<span class="badge bg-success">Assigned</span>' : '<span class="badge bg-secondary">Unassigned</span>';
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${++idx}</td>
            <td>${(sub.abbreviation_name?sub.abbreviation_name+' - ':'') + (sub.subject_name||'')} ${badge}</td>
            <td>${teacherName || '<span class="text-muted">No Teacher</span>'}</td>
            <td>
              <button class="btn ${assigned?'btn-primary':'btn-secondary'} btn-sm openMarksBtn" ${assigned?'':'disabled'}
                      data-subject-id="${suid}" data-teacher="${teacherName}">
                ${assigned?'Open Marks':'No Teacher Assigned'}
              </button>
            </td>`;
          subjectsTableBody.appendChild(tr);
        });

        document.querySelectorAll('.openMarksBtn').forEach(btn=>{
          btn.addEventListener('click', function(){
            state.subjectId = this.dataset.subjectId;
            state.teacherName = this.dataset.teacher || '';
            openMarks();
          });
        });
      })
      .catch(()=> subjectsTableBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Failed to load.</td></tr>');
  });

  function openMarks(){
    modalTitle.textContent = `${state.sectionName} - ${state.classType} | ${state.year} | ${state.teacherName}`;
    modalMarksBody.innerHTML = '<tr><td colspan="5" class="text-center">Loading…</td></tr>';
    loadMarks();
    marksModal.show();
  }

  function loadMarks(){
    const semester = modalSemester.value || 1;
    fetch(`ajax_role_students_for_marks.php?year=${encodeURIComponent(state.year)}&class_id=${encodeURIComponent(state.sectionId)}&subject_id=${encodeURIComponent(state.subjectId)}&semester=${encodeURIComponent(semester)}`)
      .then(r=>r.json())
      .then(data=>{
        if(!Array.isArray(data) || data.length===0){
          modalMarksBody.innerHTML = '<tr><td colspan="5" class="text-center">No data</td></tr>';
          return;
        }
        modalMarksBody.innerHTML='';
        data.forEach(st=>{
          const sid = st.student_id ?? '';
          const sname = st.student_name ?? '';
          const markValue = (st.result ?? '') === '' ? '-' : st.result;
          const status = st.mark_status ?? 1;
          const markId = st.mid ?? st.mark_id ?? 0;
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${sid}</td>
            <td>${sname}</td>
            <td>${markValue}</td>
            <td><span class="badge ${status==1?'bg-success':'bg-danger'}">${status==1?'Editable':'Locked'}</span></td>
            <td><button class="btn btn-sm toggleStatusBtn ${status==1?'btn-success':'btn-danger'}" data-mark="${markId}" data-status="${status}">
              ${status==1?'<i class="fas fa-unlock"></i>':'<i class="fas fa-lock"></i>'}
            </button></td>`;
          modalMarksBody.appendChild(tr);
        });
        bindToggleButtons();
      })
      .catch(()=> modalMarksBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load.</td></tr>');
  }

  modalSemester.addEventListener('change', loadMarks);

  function bindToggleButtons(){
    document.querySelectorAll('.toggleStatusBtn').forEach(btn=>{
      btn.onclick = ()=>{
        const markId = btn.dataset.mark;
        const currentStatus = parseInt(btn.dataset.status);
        const actionText = currentStatus==1 ? 'lock' : 'unlock';
        const newStatus = currentStatus==1 ? 2 : 1;

        Swal.fire({
          title: `Are you sure you want to ${actionText} this mark?`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: `Yes, ${actionText}`,
          cancelButtonText: 'Cancel',
          backdrop: true,
          didOpen: ()=>{
            const c = document.querySelector('.swal2-container');
            if(c) c.style.zIndex='11000';
          }
        }).then(result=>{
          if(result.isConfirmed){
            fetch('ajax_unlock_mark.php',{
              method:'POST',
              headers:{'Content-Type':'application/x-www-form-urlencoded'},
              body:`mark_id=${markId}&new_status=${newStatus}`
            })
            .then(r=>r.json())
            .then(resp=>{
              if(resp.success){
                Swal.fire({
                  title: 'Success',
                  text: `Mark has been ${actionText}ed`,
                  icon: 'success',
                  didOpen: ()=>{ const c = document.querySelector('.swal2-container'); if(c) c.style.zIndex='11000'; }
                });
                const badge = btn.closest('tr').querySelector('span.badge');
                if(newStatus == 1){
                  btn.classList.replace('btn-danger','btn-success');
                  btn.innerHTML='<i class="fas fa-unlock"></i>';
                  btn.dataset.status=1;
                  badge.classList.replace('bg-danger','bg-success');
                  badge.textContent='Editable';
                } else {
                  btn.classList.replace('btn-success','btn-danger');
                  btn.innerHTML='<i class="fas fa-lock"></i>';
                  btn.dataset.status=2;
                  badge.classList.replace('bg-success','bg-danger');
                  badge.textContent='Locked';
                }
              } else {
                Swal.fire({
                  title:'Error',
                  text: resp.message||'Something went wrong',
                  icon:'error',
                  didOpen: ()=>{ const c = document.querySelector('.swal2-container'); if(c) c.style.zIndex='11000'; }
                });
              }
            });
          }
        });
      };
    });
  }
});
</script>
<?php include('../Admin/footer.php'); ?>
