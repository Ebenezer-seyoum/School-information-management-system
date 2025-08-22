<?php
include('directorHeader.php');
include('../connection/connection.php');

// --- Login & Role Check ---
if (!isset($_SESSION['uid'])) die("You must be logged in.");
$user = getUserByID($_SESSION['uid']);
if (!$user || getRoleNameById($user['user_type']) !== "Director") die("Not authorized.");

// --- Filters ---
$student_id = $_GET['student_id'] ?? '';
$subject_id = $_GET['subject_id'] ?? '';
$class_id   = $_GET['class_id'] ?? '';
$semester   = $_GET['semester'] ?? '';
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

            <!-- Student Filter -->
<div class="col-md-3">
  <label>Student</label>
  <select name="student_id" class="form-select form-select-sm" onchange="this.form.submit()">
    <option value="">All Students</option>
    <?php
    // Fetch only students who are assigned
    $students = mysqli_query($conn,"
      SELECT u.sid, u.first_name, u.father_name
      FROM students u
      JOIN assign_student a ON u.sid = a.student_id
      GROUP BY u.sid, u.first_name, u.father_name
      ORDER BY u.first_name ASC
    ");

    while($s = mysqli_fetch_assoc($students)){
        $sel = ($student_id == $s['sid']) ? 'selected' : '';
        echo "<option value='{$s['sid']}' $sel>{$s['first_name']} {$s['father_name']}</option>";
    }
    ?>
  </select>
</div>


            <!-- Subject Filter -->
            <div class="col-md-3">
              <label>Subject</label>
              <select name="subject_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Subjects</option>
                <?php
                $subjects = mysqli_query($conn,"SELECT suid, subject_name FROM subjects");
                while($sub=mysqli_fetch_assoc($subjects)){
                    $sel = ($subject_id==$sub['suid']) ? 'selected':'';
                    echo "<option value='{$sub['suid']}' $sel>{$sub['subject_name']}</option>";
                }
                ?>
              </select>
            </div>

            <!-- Section Filter -->
            <div class="col-md-3">
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
            <!-- Semester Filter -->
            <div class="col-md-2">
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

            <!-- Reset Button -->
       <div class="col-md-3 align-self-end">
              <a href="academic_report_student.php" class="btn btn-secondary btn-sm w-100">Reset Filters</a>
            </div>

          </div>
        </form>
      </div>
    </div>

    <!-- Student Report Table -->
    <div class="card mt-4">
      <div class="card-body">
        <div class="table-responsive">
          <table id="studentReport" class="table table-hover table-bordered text-center">
            <thead class="table-secondary">
              <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Subject</th>
                <th>Section</th>
                <th>Class Type</th>
                <th>Academic Year</th>
                <th>Semester</th>
                <th>Result</th>
              </tr>
            </thead>
            <tbody>
<?php
$no=1;
$sql="SELECT u.sid, CONCAT(u.first_name,' ',u.father_name) AS student_name,
             sub.subject_name, sec.section_name, sec.class_type, r.academic_year ,
             r.result, r.semester
      FROM marks r
      JOIN students u ON r.student_id= u.sid
      JOIN subjects sub ON r.subject_id=sub.suid
      JOIN sections sec ON r.section_id=sec.cid
      WHERE   1=1";

if($student_id) $sql.=" AND r.student_id='".mysqli_real_escape_string($conn,$student_id)."'";
if($subject_id) $sql.=" AND r.subject_id='".mysqli_real_escape_string($conn,$subject_id)."'";
if($class_id) $sql.=" AND r.section_id='".mysqli_real_escape_string($conn,$class_id)."'";
if($semester) $sql.=" AND r.semester='".mysqli_real_escape_string($conn,$semester)."'";
if($academic_year) $sql.=" AND r.academic_year='".mysqli_real_escape_string($conn,$academic_year)."'";

$res=mysqli_query($conn,$sql);
while($r=mysqli_fetch_assoc($res)){
    echo "<tr>
            <td>".$no++."</td>
            <td>".htmlspecialchars($r['student_name'])."</td>
            <td>".htmlspecialchars($r['subject_name'])."</td>
            <td>".htmlspecialchars($r['section_name'])."</td>
            <td>".htmlspecialchars($r['class_type'])."</td>
            <td>".htmlspecialchars($r['academic_year'])."</td>
            <td>".htmlspecialchars($r['semester'])."</td>
            <td>".htmlspecialchars($r['result'])." </td>
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
    var $tbl = $('#studentReport');
    var expectedCols = $tbl.find('thead th').length;
    $tbl.find('tbody tr').each(function(){
      var cells = $(this).children('td,th').length;
      if (cells !== expectedCols) { $(this).remove(); }
    });
    $tbl.DataTable({
      dom: 'Bfrtip',
      pageLength: 25,
      columns: [null,null,null,null,null,null,null,null],
      language: { emptyTable: 'No academic records found' },
      buttons: [
        { extend: 'copyHtml5', text: 'Copy', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { page: 'all' } } },
        { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { page: 'all' } } },
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