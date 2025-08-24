<?php
include('studentHeader.php'); 
?>
<?php
// Academic years from assign_student for student view
$yrsRes = mysqli_query($conn, "SELECT DISTINCT academic_year FROM assign_student ORDER BY academic_year DESC");
$assignYears = [];
while($y = mysqli_fetch_assoc($yrsRes)) $assignYears[] = $y['academic_year'];
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">View Classes & Marks</h3>
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
          <p class="text-muted">Choose an academic year to view your classes</p>
        </div>
        <div class="row g-3 align-items-end">
          <div class="col-md-8">
            <label class="form-label fw-semibold">Academic Year</label>
            <select id="academicYear" class="form-select form-select-lg">
              <option value="">-- Select Academic Year --</option>
              <?php foreach($assignYears as $ay): ?>
                <option value="<?= htmlspecialchars($ay) ?>"><?= htmlspecialchars($ay) ?></option>
              <?php endforeach; ?>
            </select>
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

<!-- Modal: Subjects & Marks -->
<div class="modal fade" id="classSubjectsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header d-flex justify-content-between align-items-center">
        <h5 class="modal-title fw-bold" id="modalClassTitle">Subjects & Marks</h5>
        <div class="d-flex align-items-center gap-2">
          <label class="mb-0 me-2 fw-semibold">Semester:</label>
          <select id="semesterSelect" class="form-select form-select-sm w-auto">
            <option value="1">1st Semester</option>
            <option value="2">2nd Semester</option>
          </select>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>

      <div class="modal-body">
        <table id="classSubjectsTable" class="table table-bordered table-striped w-100">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Subject</th>
              <th>Mark</th>
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

<!-- DataTables + Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(function(){

  // Fetch and render classes for a selected year
  $('#showClassesBtn').click(function(){
    const year = $('#academicYear').val();
    if(!year){
      Swal.fire('Warning','Please select academic year','warning');
      return;
    }

    $.post('fetch_classes.php', {academic_year: year}, function(res){
      let html = '';
      if(!res || res.length === 0){
        html = '<div class="col-12 text-center"><div class="alert alert-warning">No classes found for this year.</div></div>';
      } else {
        res.forEach(cls=>{
          html += `
            <div class="col-md-4">
              <div class="card shadow-sm border-0 rounded-3 p-3 h-100">
                <h5 class="fw-bold">${cls.section_name}</h5>
                <p class="text-muted mb-2">${cls.class_type}</p>
                <button class="btn btn-outline-primary btn-sm viewSubjectsBtn"
                        data-id="${cls.cid}" data-year="${year}" data-classname="${cls.section_name}">
                  View Marks
                </button>
              </div>
            </div>`;
        });
      }
      $('#classesContainer').html(html);
    }, 'json');
  });

  // Open modal and load marks
  $(document).on('click', '.viewSubjectsBtn', function(){
    const sectionId = $(this).data('id');
    const year = $(this).data('year');
    const className = $(this).data('classname');

    // Set modal title as class name
    $('#modalClassTitle').text(`Subjects & Marks: ${className}`);

    // Load marks
    loadMarks(sectionId, year);

    // Reload marks on semester change
    $('#semesterSelect').off('change').on('change', function(){
      loadMarks(sectionId, year);
    });

    const modal = new bootstrap.Modal(document.getElementById('classSubjectsModal'));
    modal.show();
  });

  function loadMarks(sectionId, year){
    const semester = $('#semesterSelect').val() || 1;

    $.post('fetch_class_marks.php',
      { section_id: sectionId, academic_year: year, semester: semester},
      function(res){
        let rows = '';
        if(!res || res.length === 0){
          rows = '<tr><td colspan="3" class="text-center">No subjects found.</td></tr>';
        } else {
          res.forEach((item, idx)=>{
            rows += `
              <tr>
                <td>${idx+1}</td>
                <td>${item.subject_name}</td>
                <td>${item.marks}</td>
              </tr>`;
          });
        }
        $('#classSubjectsTableBody').html(rows);

        // Reinitialize DataTable with buttons
        if ($.fn.DataTable.isDataTable('#classSubjectsTable')) {
          $('#classSubjectsTable').DataTable().destroy();
        }
        $('#classSubjectsTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
            { extend: 'copy',  title: `Marks_${year}_Sem${semester}` },
            { extend: 'csv',   title: `Marks_${year}_Sem${semester}` },
            { extend: 'excel', title: `Marks_${year}_Sem${semester}` },
            { extend: 'pdf',   title: `Marks_${year}_Sem${semester}` },
            { extend: 'print', title: `Marks ${year} - Semester ${semester}` }
          ]
        });
      },
      'json'
    );
  }

});
</script>

<?php include('../Admin/footer.php'); ?>
