<?php
include('directorHeader.php');
include('../connection/connection.php');

// --- Login & Role Check ---
if (!isset($_SESSION['uid'])) die("You must be logged in.");
$user = getUserByID($_SESSION['uid']);
if (!$user || getRoleNameById($user['user_type']) !== "Director") die("Not authorized.");

// --- Filters ---
$class_id = $_GET['class_id'] ?? '';
$semester = $_GET['semester'] ?? '';
$academic_year = $_GET['academic_year'] ?? '';
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Academic Reports</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Reports</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Academic Reports</a></li>
      </ul>
    </div>


    <!-- Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <form method="GET" action="">
          <div class="row g-2">
     <!-- Academic Year -->
            <div class="col-md-3">
              <label>Academic Year</label>
              <select name="academic_year" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Years</option>
                <?php
                $yrs = mysqli_query($conn, "SELECT DISTINCT academic_year FROM assign_student ORDER BY academic_year DESC");
                while ($y = mysqli_fetch_assoc($yrs)) {
                    $selected = ($academic_year == $y['academic_year']) ? 'selected' : '';
                    echo "<option value='{$y['academic_year']}' $selected>{$y['academic_year']}</option>";
                }
                ?>
              </select>
            </div>
            <!-- Section Filter -->
            <div class="col-md-4">
              <label>Section</label>
              <select name="class_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Sections</option>
                <?php
                $secs = mysqli_query($conn,"SELECT * FROM sections");
                while($c=mysqli_fetch_assoc($secs)){
                    $sel = ($class_id==$c['cid']) ? 'selected':'';
                    echo "<option value='{$c['cid']}' $sel>{$c['section_name']} ({$c['class_type']})</option>";
                }
                ?>
              </select>
            </div>

            <!-- Semester Filter -->
            <div class="col-md-3">
              <label>Semester</label>
              <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Semesters</option>
                <?php
                $semesters = mysqli_query($conn,"SELECT DISTINCT semester FROM marks ORDER BY semester ASC");
                while($sem=mysqli_fetch_assoc($semesters)){
                    $sel = ($semester==$sem['semester'])?'selected':'';
                    echo "<option value='{$sem['semester']}' $sel>Semester {$sem['semester']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-2 align-self-end">
              <a href="academic_report_section.php" class="btn btn-secondary btn-sm w-100">Reset Filters</a>
            </div>

          </div>
        </form>
      </div>
    </div>

    <!-- Section Report Table -->
    <div class="card mt-4">
      <div class="card-body">
        <div class="table-responsive">
          <table id="sectionReport" class="table table-hover table-bordered text-center" style="width:100%">
            <thead class="table-secondary">
              <tr>
                <th>#</th>
                <th>Section</th>
                <th>Class Type</th>
                <th>Number of Students</th>
                <th>Average Score</th>
                <th>Academic year</th>
                <th>Semester</th>
              </tr>
            </thead>
            <tbody>
<?php
            $no=1;
            $sql = "SELECT s.cid, s.section_name, s.class_type,
                COUNT(DISTINCT r.student_id) AS student_count,
                AVG(r.result) AS avg_score,
                r.academic_year,
                r.semester
              FROM sections s
              LEFT JOIN marks r ON s.cid = r.section_id
              WHERE 1=1";

if ($academic_year) $sql .= " AND r.academic_year='".mysqli_real_escape_string($conn,$academic_year)."'";
if ($class_id)      $sql .= " AND s.cid='".mysqli_real_escape_string($conn,$class_id)."'";
if ($semester)      $sql .= " AND r.semester='".mysqli_real_escape_string($conn,$semester)."'";


            $sql .= " GROUP BY s.cid, s.section_name, s.class_type, r.academic_year, r.semester
                ORDER BY s.section_name ASC";

            $res = mysqli_query($conn,$sql);
         while($r = mysqli_fetch_assoc($res)){
    $avg  = isset($r['avg_score']) ? number_format((float)$r['avg_score'], 2) : '-';
    $year = $r['academic_year'] ?? '-';
    $sem  = $r['semester'] ?? '-';

    echo "<tr>
      <td>".$no++."</td>
      <td>".htmlspecialchars($r['section_name'] ?? '')."</td>
      <td>".htmlspecialchars($r['class_type'] ?? '')."</td>
      <td>".htmlspecialchars($r['student_count'] ?? '0')."</td>
      <td>".$avg."</td>
      <td>".htmlspecialchars($year)."</td>
      <td>".htmlspecialchars($sem)."</td>
    </tr>";
}

?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
<?php include('../Admin/footer.php'); ?>
<script>
$(function(){
  if ($.fn.DataTable && $.fn.dataTable && $.fn.dataTable.Buttons) {
    var $tbl = $('#sectionReport');
    var expectedCols = $tbl.find('thead th').length;
    $tbl.find('tbody tr').each(function(){
      var cells = $(this).children('td,th').length;
      if (cells !== expectedCols) { $(this).remove(); }
    });
    $tbl.DataTable({
      dom: 'Bfrtip',
      pageLength: 25,
      columns: [null,null,null,null,null,null,null],
      language: { emptyTable: 'No academic records found' },
      buttons: [
        { extend: 'copyHtml5', text: 'Copy', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { page: 'all' } } },
        { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-sm btn-danger', exportOptions: { columns: ':visible', modifier: { page: 'all' } } },
        { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { page: 'all' } } },
        {
          extend: 'pdfHtml5',
          text: 'PDF',
          className: 'btn btn-sm btn-secondary',
          exportOptions: { columns: ':visible', modifier: { page: 'all' } },
          orientation: 'landscape',
          pageSize: 'A3',
          customize: function (doc) {
            var body = doc.content[1].table.body;
            if(body && body.length && body[0]){
              var colCount = body[0].length;
              doc.content[1].table.widths = new Array(colCount).fill('*');
            }
            doc.defaultStyle.fontSize = 8;
            if(doc.styles && doc.styles.tableHeader){
              doc.styles.tableHeader.fontSize = 9;
              doc.styles.tableHeader.alignment = 'center';
            }
          }
        },
        { extend: 'print', text: 'Print', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { page: 'all' } } }
      ]
    });
  } else { console.error('DataTables or Buttons not loaded'); }
});
</script>