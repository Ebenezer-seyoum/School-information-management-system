<?php
include('studentHeader.php'); 
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">My Attendance</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Attendance</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">View Attendance</a></li>
      </ul>
    </div>

    <!-- Filter Options -->
    <div class="d-flex justify-content-center mb-4">
      <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:700px; width:100%;">
        <div class="text-center mb-3">
          <h5 class="fw-bold">Filter Attendance</h5>
          <p class="text-muted">Choose academic year and filter type (weekly / monthly / yearly)</p>
        </div>
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Academic Year</label>
            <input type="text" id="academicYear" class="form-control form-control-lg" placeholder="e.g. 2024">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Filter</label>
            <select id="filterType" class="form-select form-select-lg">
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
              <option value="yearly">Yearly</option>
            </select>
          </div>
          <div class="col-md-4 d-grid">
            <button type="button" id="showAttendanceBtn" class="btn btn-primary btn-md">Show</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Attendance Results -->
    <div class="card">
      <div class="card-body table-responsive">
        <table id="attendanceTable" class="table table-hover text-center align-middle">
          <thead class="table-secondary">
            <tr>
              <th>#</th>
              <th>Period</th>
              <th>Total Days</th>
              <th>Present</th>
              <th>Absent</th>
              <th>Percentage</th>
            </tr>
          </thead>
          <tbody id="attendanceTableBody">
            <tr><td colspan="6" class="text-muted">No data yet. Please select filters.</td></tr>
          </tbody>
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

  $('#showAttendanceBtn').click(function(){
    const year = $('#academicYear').val().trim();
    const filter = $('#filterType').val();

    if(!year){
      Swal.fire('Warning','Please enter academic year','warning');
      return;
    }

    $.post('fetch_attendance_summary.php', 
      { academic_year: year, filter: filter }, 
      function(res){
        let rows = '';
        if(!res || res.length === 0){
          rows = '<tr><td colspan="6" class="text-center text-danger">No attendance records found.</td></tr>';
        } else {
          res.forEach((item, idx)=>{
            rows += `
              <tr>
                <td>${idx+1}</td>
                <td>${item.period}</td>
                <td>${item.total_days}</td>
                <td><span class="badge bg-success">${item.present}</span></td>
                <td><span class="badge bg-danger">${item.absent}</span></td>
                <td><b>${item.percentage}%</b></td>
              </tr>`;
          });
        }
        $('#attendanceTableBody').html(rows);

        // Reinitialize DataTable with export buttons
        if ($.fn.DataTable.isDataTable('#attendanceTable')) {
          $('#attendanceTable').DataTable().destroy();
        }
        $('#attendanceTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
            { extend: 'copy',  title: `Attendance_${year}_${filter}` },
            { extend: 'csv',   title: `Attendance_${year}_${filter}` },
            { extend: 'excel', title: `Attendance_${year}_${filter}` },
            { extend: 'pdf',   title: `Attendance_${year}_${filter}` },
            { extend: 'print', title: `Attendance ${year} (${filter})` }
          ]
        });
      }, 'json'
    );
  });

});
</script>

<?php include('../Admin/footer.php'); ?>
