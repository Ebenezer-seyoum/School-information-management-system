<?php
include('directorHeader.php'); 
include('../connection/connection.php');

// --- Fetch only Teachers from users ---
$teachers_q = mysqli_query($conn, "
  SELECT uid, user_type, CONCAT(first_name,' ',father_name) AS full_name 
  FROM users ORDER BY first_name ASC
");
$instructors_array = [];
while($t = mysqli_fetch_assoc($teachers_q)) {
  $isTeacher = false;
  // Accept numeric role id '1' (from seed data) and fallback to name check if helper exists
  if ((string)$t['user_type'] === '1') {
    $isTeacher = true;
  } elseif (function_exists('getRoleNameById')) {
    $roleName = getRoleNameById($t['user_type']);
    if ($roleName === 'Teacher') $isTeacher = true;
  }
  if ($isTeacher) {
    $instructors_array[$t['uid']] = htmlspecialchars($t['full_name']);
  }
}
?>
<!-- page header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Assign Instructor</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Instructor</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Assign Instructor</a></li>
      </ul>
  </div>
    <!-- Class & Academic Year -->
    <div class="d-flex justify-content-center mb-4">
      <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:700px; width:100%;">
        <div class="text-center mb-3">
            <h5 class="fw-bold">Select Section and Academic Year</h5>
            <p class="text-muted">Choose the section and academic year to view/assign subjects to teachers</p>
            <div class="row g-3 align-items-end">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Class Type</label>
            <select id="classTypeSelect" class="form-select form-select-lg">
              <option value="">-- Select Class --</option>
              <option value="9">9 General</option>
              <option value="10">10 General</option>
              <option value="11S">11 Social</option>
              <option value="11N">11 Natural</option>
              <option value="12S">12 Social</option>
              <option value="12N">12 Natural</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Academic Year</label>
            <input type="text" id="academicYear" class="form-control form-control-lg" placeholder="e.g. 2017">
          </div>
          <div class="col-md-2 d-grid">
            <button type="button" id="showSectionsBtn" class="btn btn-primary btn-md">Assign Instructor</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="sectionsModal" tabindex="-1" aria-labelledby="sectionsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="sectionsModalLabel">Sections & Assign Instructors</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="assignInstructorsForm">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Section</th>
                <th>Class Type</th>
                <th>Assign Teacher (Instructor)</th>
              </tr>
            </thead>
            <tbody id="sectionsTableBody"></tbody>
          </table>
          <div class="text-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Assign Selected</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Ensure Select2 dropdowns render above the Bootstrap modal */
.select2-container { z-index: 2000 !important; }
.select2-container .select2-dropdown { z-index: 2000 !important; }
</style>

<script>
$(function(){
  const instructors = <?php echo json_encode($instructors_array); ?>;

  // Show Sections
  $('#showSectionsBtn').click(function(){
      const selectedClass = $('#classTypeSelect').val();
      const classText = $('#classTypeSelect option:selected').text();
      const year = $('#academicYear').val();

      if(!selectedClass || !year){
          Swal.fire('Warning','Please select class and academic year','warning');
          return;
      }

      $.post('fetch_sections_instructor.php', { class_type: selectedClass, academic_year: year }, function(res){
          let html = '';
      if(!Array.isArray(res) || res.length === 0){
        html = '<tr><td colspan="4" class="text-center">No sections found.</td></tr>';
          } else {
              res.forEach((item,index)=>{
                  const assignedId = item.assigned_instructor;
                  html += `<tr>
                      <td>${index+1}</td>
            <td>${item.section_name}</td>
            <td>${item.class_type ?? ''}</td>
            <td>
                        <select name="instructor_id[${item.cid}]" class="form-select select2">
                          <option value="">Assign Teacher</option>
                          ${Object.entries(instructors).map(([id,name])=>
                            `<option value="${id}" ${assignedId==id?"selected":""}>${name}</option>`
                          ).join('')}
                        </select>
            </td>
                  </tr>`;
              });
          }
          $('#sectionsModalLabel').text(`Sections & Assign Instructors (${classText} - ${year})`);
          $('#sectionsTableBody').html(html);
          $('#sectionsModal').modal('show');
          $('.select2').select2({
            placeholder: "Select Instructor...",
            width: '100%',
            dropdownParent: $('#sectionsModal')
          });
      }, 'json');
  });

  // Assign
  $('#assignInstructorsForm').submit(function(e){
      e.preventDefault();
      const formData = $(this).serialize() + `&academic_year=${$('#academicYear').val()}`;
      $.post('assign_instructor_action.php', formData, function(res){
          Swal.fire(res.status?'Success':'Error', res.message, res.status?'success':'error')
              .then(()=> { if(res.status) $('#sectionsModal').modal('hide'); });
      }, 'json');
  });
});
</script>

<?php include('../Admin/footer.php'); ?>
