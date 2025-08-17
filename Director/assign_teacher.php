<?php
include('directorHeader.php');

// Fetch all teachers
$teachers_q = mysqli_query($conn, "SELECT uid, CONCAT(first_name,' ',father_name) AS full_name FROM users WHERE user_type=1 ORDER BY first_name ASC");
$teachers_array = [];
while($t=mysqli_fetch_assoc($teachers_q)) $teachers_array[$t['uid']] = htmlspecialchars($t['full_name']);
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

    <!-- Section + Academic Year Selection (Centered) -->
    <div class="d-flex justify-content-center mb-4">
      <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:700px; width:100%;">
        <div class="text-center mb-3">
          <h5 class="fw-bold">Select Section and Academic Year</h5>
          <p class="text-muted">Choose the section and academic year to view/assign subjects to teachers</p>
        </div>
        <div class="row g-3 align-items-end">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Section</label>
            <select id="sectionSelect" class="form-select form-select-lg">
              <option value="">-- Select Section --</option>
              <?php
              $sections = mysqli_query($conn,"SELECT * FROM sections ORDER BY class_type, section_name ASC");
              $grouped_sections = [];
              while($sec=mysqli_fetch_assoc($sections)) $grouped_sections[$sec['class_type']][] = $sec;
              foreach($grouped_sections as $type => $secs): ?>
                <optgroup label="<?= htmlspecialchars($type) ?>">
                  <?php foreach($secs as $s): ?>
                    <option value="<?= $s['cid'] ?>"><?= htmlspecialchars($s['section_name']) ?></option>
                  <?php endforeach; ?>
                </optgroup>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Academic Year</label>
            <input type="text" id="academicYear" class="form-control form-control-lg" placeholder="e.g. 2017">
          </div>
          <div class="col-md-2 d-grid">
            <button type="button" id="showSubjectsBtn" class="btn btn-primary btn-md">Show Subjects</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Subjects Modal -->
<div class="modal fade" id="subjectsModal" tabindex="-1" aria-labelledby="subjectsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="subjectsModalLabel">Subjects & Assign Teachers</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="assignTeachersForm">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Subject Name</th>
                <th>Assign Teacher</th>
              </tr>
            </thead>
            <tbody id="subjectsTableBody"></tbody>
          </table>
          <div class="text-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Assign Teachers</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){
    const teachers = <?php echo json_encode($teachers_array); ?>;

    // Show subjects modal
    $('#showSubjectsBtn').click(function(){
        const sectionId = $('#sectionSelect').val();
        const year = $('#academicYear').val();
        if(!sectionId || !year){
            Swal.fire('Warning','Please select section and academic year','warning');
            return;
        }

        $.post('fetch_section_teachers.php', { section_id: sectionId, academic_year: year }, function(res){
            let html = '';
            if(res.length === 0){
                html = '<tr><td colspan="3" class="text-center">No subjects found for this section.</td></tr>';
            } else {
                res.forEach((item, index)=>{
                    if(item.assigned_teacher){
                        html += `<tr>
                            <td>${index+1}</td>
                            <td>${item.subject_name}</td>
                            <td>${teachers[item.assigned_teacher]}</td>
                        </tr>`;
                    } else {
                        html += `<tr>
                            <td>${index+1}</td>
                            <td>${item.subject_name}</td>
                            <td>
                              <select name="subject_teacher[${item.suid}]" class="form-select select2">
                                <option value="">Assign Teacher</option>
                                ${Object.entries(teachers).map(([tid, tname]) => `<option value="${tid}">${tname}</option>`).join('')}
                              </select>
                            </td>
                        </tr>`;
                    }
                });
            }
            $('#subjectsTableBody').html(html);
            $('#subjectsModal').modal('show');
            $('.select2').select2({ placeholder: "Select teacher...", width:'100%' });
        }, 'json');
    });

    // Submit assigned teachers
    $('#assignTeachersForm').submit(function(e){
        e.preventDefault();
        const sectionId = $('#sectionSelect').val();
        const year = $('#academicYear').val();

        // Count unassigned subjects
        let unassignedCount = 0;
        $('#subjectsTableBody select').each(function(){
            if(!$(this).val()) unassignedCount++;
        });

        const submitForm = function(){
            // Only send selected teachers
            let formData = '';
            $('#subjectsTableBody select').each(function(){
                const val = $(this).val();
                if(val) formData += encodeURIComponent($(this).attr('name')) + '=' + encodeURIComponent(val) + '&';
            });
            formData += `section_id=${sectionId}&academic_year=${year}`;

            $.post('assign_teachers_action.php', formData, function(res){
                Swal.fire({
                    title: 'Success!',
                    text: res.message || 'Selected teachers assigned successfully.',
                    icon: 'success'
                }).then(()=>{
                    $('#subjectsModal').modal('hide');
                    $('#showSubjectsBtn').trigger('click'); // Refresh modal to show readonly assigned teachers
                });
            }, 'json');
        };

        if(unassignedCount > 0){
            Swal.fire({
                title: 'Some subjects are not assigned!',
                text: `You have ${unassignedCount} subjects without a teacher. Do you want to assign only the selected ones?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, assign selected',
                cancelButtonText: 'No, go back'
            }).then((result)=>{
                if(result.isConfirmed){
                    submitForm();
                }
            });
        } else {
            submitForm();
        }
    });
});
</script>

<?php include('../Admin/footer.php'); ?>
