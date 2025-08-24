<?php
include('directorHeader.php');

// Fetch sections
$sections_q = mysqli_query($conn, "SELECT * FROM sections ORDER BY section_name ASC");
$sections_array = [];
while($s = mysqli_fetch_assoc($sections_q)) {
    $sections_array[$s['cid']] = $s['section_name'] . ' - ' . $s['class_type'];
}

// Fetch academic years from assign_student for selects
$years_q = mysqli_query($conn, "SELECT DISTINCT academic_year FROM assign_student ORDER BY academic_year DESC");
$years = [];
while($y = mysqli_fetch_assoc($years_q)) { $years[] = $y['academic_year']; }
?>
<!-- Page Header -->
<div class="container">
         <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Assign Student</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Student Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Assign Student</a></li>
    </ul>
 </div>
<!-- End Page Header -->
        <div class="card shadow-lg p-4 mb-4">
            <div class="row g-3 align-items-end">
                <h3 class="fw-bold mb-3 text-center">Assign Students to section</h3>
                <p class="text-muted mb-0 text-center">Choose a section and year to assign students.</p>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Select Section</label>
                    <select id="sectionSelect" class="form-select">
                        <option value="">-- Select Section --</option>
                        <?php foreach($sections_array as $cid => $name): ?>
                        <option value="<?= $cid ?>"><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                                <div class="col-md-5">
                                        <label class="form-label fw-semibold">Academic Year</label>
                                        <select id="academicYear" class="form-select">
                                                <option value="">-- Select Academic Year --</option>
                                                <?php foreach($years as $yr): ?>
                                                    <option value="<?= htmlspecialchars($yr) ?>"><?= htmlspecialchars($yr) ?></option>
                                                <?php endforeach; ?>
                                        </select>
                                </div>
                <div class="col-md-2 d-grid">
                    <button type="button" id="showCountsBtn" class="btn btn-primary">Show Students</button>
                </div>
            </div>
        </div>

        <div id="studentCounts" class="mb-4" style="display:none;">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Students in Selected Section</h5>
                    <div>
                        <button class="btn btn-info me-2" id="viewAllBtn">View All</button>
                        <button class="btn btn-success" id="assignStudentsBtn">Assign Students</button>
                    </div>
                </div>
                <div class="mt-2">
                    <span id="totalStudents">Total: 0</span> | 
                    <span id="maleStudents">Male: 0</span> | 
                    <span id="femaleStudents">Female: 0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View All Students Modal -->
<div class="modal fade" id="viewAllModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">All Students in Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                        </tr>
                    </thead>
                    <tbody id="allStudentsBody"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Students Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">Assign Students to Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignStudentsForm">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Select</th>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>Gender</th>
                            </tr>
                        </thead>
                        <tbody id="assignStudentsBody"></tbody>
                    </table>
                    <div class="text-end mt-2">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Assign Selected</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){

    // Show counts
    $('#showCountsBtn').click(function(){
        const sectionId = $('#sectionSelect').val();
        const year = $('#academicYear').val();
        if(!sectionId || !year){
            Swal.fire('Warning','Please select section and academic year','warning');
            return;
        }

        $.post('fetch_student_counts.php',{section_id:sectionId, academic_year:year}, function(res){
            if(res.status){
                $('#studentCounts').show();
                $('#totalStudents').text('Total: '+res.total);
                $('#maleStudents').text('Male: '+res.male);
                $('#femaleStudents').text('Female: '+res.female);
            } else {
                Swal.fire('Info','No students found','info');
            }
        },'json');
    });

    // View All Students
    $('#viewAllBtn').click(function(){
        const sectionId = $('#sectionSelect').val();
        const year = $('#academicYear').val();
        $.post('fetch_all_students.php',{section_id:sectionId, academic_year:year}, function(res){
            let html = '';
            res.forEach((s,i)=>{
                html += `<tr>
                    <td>${i+1}</td>
                    <td>${s.student_id}</td>
                    <td>${s.first_name} ${s.father_name}</td>
                    <td>${s.gender}</td>
                </tr>`;
            });
            $('#allStudentsBody').html(html);
            $('#viewAllModal').modal('show');
        },'json');
    });

    // Assign Students
    $('#assignStudentsBtn').click(function(){
        const sectionId = $('#sectionSelect').val();
        const year = $('#academicYear').val();
        $.post('fetch_unassigned_students.php',{section_id:sectionId, academic_year:year}, function(res){
            let html = '';
            res.forEach((s,i)=>{
                html += `<tr>
                    <td>${i+1}</td>
                    <td><input type="checkbox" name="student_ids[]" value="${s.sid}"></td>
                    <td>${s.student_id}</td>
                    <td>${s.first_name} ${s.father_name}</td>
                    <td>${s.gender}</td>
                </tr>`;
            });
            $('#assignStudentsBody').html(html);
            $('#assignModal').modal('show');
        },'json');
    });

    // Submit assign form
    $('#assignStudentsForm').submit(function(e){
        e.preventDefault();
        const sectionId = $('#sectionSelect').val();
        const year = $('#academicYear').val();
        const formData = $(this).serialize() + '&section_id='+sectionId+'&academic_year='+year;

        $.post('assign_students_action.php', formData, function(res){
            Swal.fire(res.status?'Success':'Error', res.message, res.status?'success':'error')
            .then(()=> { if(res.status) $('#assignModal').modal('hide'); });
        },'json');
    });

});
</script>
