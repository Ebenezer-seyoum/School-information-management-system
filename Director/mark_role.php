<?php
include('directorHeader.php'); // $conn & session

$uid = $_SESSION['uid'] ?? 0;
$profile = getUserByID($uid);
if(getRoleNameById($profile['user_type'])!=='Director'){
    echo "Not authorized"; exit;
}

// Fetch Academic Years
$years_res = mysqli_query($conn,"SELECT DISTINCT academic_year FROM assign_student ORDER BY academic_year DESC");
$years = mysqli_fetch_all($years_res,MYSQLI_ASSOC);
?>
<!-- page header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Account Details</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Account</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Account Details</a></li>
      </ul>
  </div>
<!-- end page header -->
<div class="container mt-4">
  <div class="card shadow-sm p-4">
<h3 class="fw-bold mb-3">Director: Unlock/Edit Marks</h3>
    <!-- Academic Year Selection -->
    <div class="d-flex justify-content-center align-items-center flex-wrap mb-4">
      <label for="yearSelect" class="form-label fw-bold me-2 mb-2 mb-md-0">Select Academic Year:</label>
      <select id="yearSelect" class="form-select w-auto me-2 mb-2 mb-md-0">
        <option value="">-- Select Year --</option>
        <?php foreach($years as $y): ?>
          <option value="<?= htmlspecialchars($y['academic_year']) ?>"><?= htmlspecialchars($y['academic_year']) ?></option>
        <?php endforeach; ?>
      </select>
      <button id="showClassesBtn" class="btn btn-primary mb-2 mb-md-0">Show Classes</button>
    </div>

    <!-- Classes Buttons -->
    <div id="classesContainer" class="d-flex flex-column gap-2 mb-4"></div>
  </div>
</div>

<!-- Marks Modal -->
<div id="marksModal" class="modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:9999;">
  <div class="modal-content" style="background:#fff; max-width:900px; margin:50px auto; padding:20px; border-radius:8px; position:relative;">
    <!-- Changed × to Close -->
    <button id="closeModal" style="position:absolute; top:12px; right:16px; font-size:14px; padding:4px 8px; cursor:pointer;" class="btn btn-sm btn-outline-danger">Close</button>
    <h4 id="modalTitle" class="mb-3 fw-bold"></h4>

    <div class="d-flex gap-2 mb-3">
      <select id="modalSubject" class="form-select" style="flex:1;">
        <option value="">-- Select Subject --</option>
      </select>
      <select id="modalSemester" class="form-select" style="width:110px;">
        <option value="1">1st Sem</option>
        <option value="2">2nd Sem</option>
      </select>
    </div>

    <div style="max-height:400px; overflow:auto;">
      <table class="table table-bordered table-sm">
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
          <tr><td colspan="5" class="text-center text-muted">Select subject and semester</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const yearSelect = document.getElementById('yearSelect');
  const showClassesBtn = document.getElementById('showClassesBtn');
  const classesContainer = document.getElementById('classesContainer');

  const modal = document.getElementById('marksModal');
  const closeModal = document.getElementById('closeModal');
  const modalTitle = document.getElementById('modalTitle');
  const modalSubject = document.getElementById('modalSubject');
  const modalSemester = document.getElementById('modalSemester');
  const modalMarksBody = document.getElementById('modalMarksBody');

  let currentYear='', currentSection='', currentType='';

  // Show Classes
  showClassesBtn.addEventListener('click', function(){
    const selectedYear = yearSelect.value;
    classesContainer.innerHTML = '<p>Loading classes...</p>';
    if(!selectedYear){ classesContainer.innerHTML=''; return; }

    fetch(`ajax_role_classes.php?year=${encodeURIComponent(selectedYear)}`)
      .then(res=>res.json())
      .then(data=>{
        classesContainer.innerHTML = '';
        if(!Array.isArray(data) || data.length===0){
          classesContainer.innerHTML = '<p class="text-danger">No classes found</p>';
          return;
        }
        data.forEach(c=>{
          const btn = document.createElement('button');
          btn.className='btn btn-outline-primary w-100 text-start';
          const cid = c.cid ?? c.class_id ?? c.section_id;
          const section = c.section_name ?? c.section ?? '';
          const type = c.class_type ?? c.type ?? '';
          btn.textContent = `${section} - ${type}`;
          btn.addEventListener('click', ()=> openModal(cid, selectedYear, section, type));
          classesContainer.appendChild(btn);
        });
      });
  });

  // Modal close
  closeModal.addEventListener('click', ()=> modal.style.display='none');
  window.addEventListener('click', e=> { if(e.target===modal) modal.style.display='none'; });

  function openModal(classId, year, section, type){
    currentYear=year; currentSection=section; currentType=type;
    modalTitle.textContent = `${section} - ${type}`; 
    modal.style.display='block';
    modalMarksBody.innerHTML='<tr><td colspan="5" class="text-center text-muted">Select subject and semester</td></tr>';
    modalSubject.innerHTML='<option value="">-- Loading subjects --</option>';

    // Load subjects
    fetch(`ajax_role_subjects.php?year=${encodeURIComponent(year)}&class_id=${encodeURIComponent(classId)}`)
      .then(r=>r.json())
      .then(subjects=>{
        modalSubject.innerHTML='<option value="">-- Select Subject --</option>';
        subjects.forEach(s=>{
          const id = s.suid ?? s.subject_id;
          const name = s.subject_name ?? '';
          const teacher = s.teacher_name ?? '';
          const opt = document.createElement('option');
          opt.value=id;
          opt.textContent=name;
          opt.dataset.teacher=teacher;
          modalSubject.appendChild(opt);
        });
      });

    modalSubject.addEventListener('change', ()=> loadMarks(classId, year));
    modalSemester.addEventListener('change', ()=> loadMarks(classId, year));
  }

  function loadMarks(classId, year){
    const subjectId = modalSubject.value;
    const semester = modalSemester.value;
    if(!subjectId){
      modalMarksBody.innerHTML='<tr><td colspan="5" class="text-center text-muted">Select subject</td></tr>'; 
      return;
    }

    const teacher = modalSubject.options[modalSubject.selectedIndex].dataset.teacher || '';
    modalTitle.textContent = `${currentSection} - ${currentType} | ${year} | ${teacher}`;

    modalMarksBody.innerHTML='<tr><td colspan="5" class="text-center">Loading...</td></tr>';

    fetch(`ajax_role_students_for_marks.php?year=${encodeURIComponent(year)}&class_id=${encodeURIComponent(classId)}&subject_id=${encodeURIComponent(subjectId)}&semester=${encodeURIComponent(semester)}`)
      .then(r=>r.json())
      .then(data=>{
        if(!data.length){ 
          modalMarksBody.innerHTML='<tr><td colspan="5" class="text-center">No data</td></tr>'; 
          return; 
        }

        modalMarksBody.innerHTML='';
        data.forEach(st=>{
          const sid = st.student_id ?? '';
          const sname = st.student_name ?? '';
          const markValue = st.result ?? '-';
          const status = st.mark_status ?? 1;
          const markId = st.mid ?? st.mark_id ?? 0;

          const tr = document.createElement('tr');
          tr.innerHTML=`
            <td>${sid}</td>
            <td>${sname}</td>
            <td>${markValue}</td>
            <td><span class="badge ${status==1?'bg-success':'bg-danger'}">${status==1?'Editable':'Locked'}</span></td>
            <td><button class="btn btn-sm toggleStatusBtn ${status==1?'btn-success':'btn-danger'}" data-mark="${markId}" data-status="${status}">
              ${status==1?'<i class="fas fa-unlock"></i>':'<i class="fas fa-lock"></i>'}
            </button></td>
          `;
          modalMarksBody.appendChild(tr);
        });

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
                document.querySelector('.swal2-container').style.zIndex='11000'; // in front of modal
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
                    // Success alert in front of modal
                    Swal.fire({
                      title: 'Success',
                      text: `Mark has been ${actionText}ed`,
                      icon: 'success',
                      didOpen: ()=>{
                        document.querySelector('.swal2-container').style.zIndex='11000';
                      }
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
                      didOpen: ()=>{
                        document.querySelector('.swal2-container').style.zIndex='11000';
                      }
                    });
                  }
                });
              }
            });
          };
        });
      });
  }
});
</script>
<?php include('../Admin/footer.php'); ?>
