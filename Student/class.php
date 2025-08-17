<?php
include('studentHeader.php');
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">View Classes & Subjects</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Class Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">View Classes</a></li>
      </ul>
    </div>

    <!-- Select Academic Year -->
    <div class="d-flex justify-content-center mb-4">
      <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:600px; width:100%;">
        <div class="text-center mb-3">
          <h5 class="fw-bold">Filter by Academic Year</h5>
          <p class="text-muted">Choose an academic year to view all classes</p>
        </div>
        <div class="row g-3 align-items-end">
          <div class="col-md-8">
            <label class="form-label fw-semibold">Academic Year</label>
            <input type="text" id="academicYear" class="form-control form-control-lg" placeholder="e.g. 2017">
          </div>
          <div class="col-md-4 d-grid">
            <button type="button" id="showClassesBtn" class="btn btn-primary btn-md">Show Classes</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Classes list -->
    <div id="classesContainer" class="row g-4"></div>
  </div>
</div>

<!-- Modal to show class subjects -->
<div class="modal fade" id="classSubjectsModal" tabindex="-1" aria-labelledby="classSubjectsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="classSubjectsModalLabel">Class Subjects & Teachers</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Subject</th>
              <th>Teacher</th>
            </tr>
          </thead>
          <tbody id="classSubjectsTableBody"></tbody>
        </table>
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
    // Show classes for selected year
    $('#showClassesBtn').click(function(){
        const year = $('#academicYear').val();
        if(!year){
            Swal.fire('Warning','Please enter academic year','warning');
            return;
        }

        $.post('fetch_classes.php',{academic_year:year},function(res){
            let html = '';
            if(res.length === 0){
                html = '<div class="col-12 text-center"><div class="alert alert-warning">No classes found for this year.</div></div>';
            } else {
                res.forEach(cls=>{
                    html += `
                        <div class="col-md-4">
                          <div class="card shadow-sm border-0 rounded-3 p-3 h-100">
                            <h5 class="fw-bold">${cls.section_name}</h5>
                            <p class="text-muted mb-2">${cls.class_type}</p>
                            <button class="btn btn-outline-primary btn-sm viewSubjectsBtn" data-id="${cls.cid}" data-year="${year}">View Subjects</button>
                          </div>
                        </div>
                    `;
                });
            }
            $('#classesContainer').html(html);
        },'json');
    });

    // Show subjects in modal
    $(document).on('click','.viewSubjectsBtn',function(){
        const sectionId = $(this).data('id');
        const year = $(this).data('year');

        $.post('fetch_class_subjects.php',{section_id:sectionId, academic_year:year},function(res){
            let html = '';
            if(res.length===0){
                html = '<tr><td colspan="3" class="text-center">No subjects found.</td></tr>';
            } else {
                res.forEach((item,idx)=>{
                    html += `
                        <tr>
                          <td>${idx+1}</td>
                          <td>${item.subject_name}</td>
                          <td>${item.teacher_name ? item.teacher_name : 'N/A'}</td>
                        </tr>
                    `;
                });
            }
            $('#classSubjectsTableBody').html(html);
            $('#classSubjectsModal').modal('show');
        },'json');
    });
});
</script>

<?php include('../Admin/footer.php'); ?>
