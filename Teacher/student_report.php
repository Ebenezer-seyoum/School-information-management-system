<?php
include('teacherHeader.php'); // ✅ teacher header

// --- Login & Role Check ---
if (!isset($_SESSION['uid'])) {
    die("You must be logged in to view this page.");
}
$user = getUserByID($_SESSION['uid']);
if (!$user || getRoleNameById($user['user_type']) !== "Teacher") {
    die("Not authorized.");
}

$teacher_id    = $_SESSION['uid']; // ✅ force logged-in teacher ID
$subject_id    = $_GET['subject_id'] ?? '';
$class_id      = $_GET['class_id'] ?? '';
$student_id    = $_GET['student_id'] ?? '';
$academic_year = $_GET['academic_year'] ?? '';
$semester      = $_GET['semester'] ?? '';
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">My Students Report</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Reports</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Student Reports</a></li>
      </ul>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <form method="GET" action="">
          <div class="row g-2">

            <!-- Subject -->
            <div class="col-md-3">
              <label>Subject</label>
              <select name="subject_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Subjects</option>
                <?php
                $subjects = mysqli_query($conn, "SELECT DISTINCT sub.suid, sub.subject_name 
                          FROM assign_teacher at
                          JOIN subjects sub ON at.subject_id=sub.suid
                          WHERE at.teacher_id='$teacher_id'");
                while ($s = mysqli_fetch_assoc($subjects)) {
                    $sel = ($subject_id == $s['suid']) ? 'selected' : '';
                    echo "<option value='{$s['suid']}' $sel>{$s['subject_name']}</option>";
                }
                ?>
              </select>
            </div>

            <!-- Section -->
            <div class="col-md-3">
              <label>Section</label>
              <select name="class_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Sections</option>
                <?php
                $secs = mysqli_query($conn, "SELECT DISTINCT sec.cid, sec.section_name, sec.class_type
                          FROM assign_teacher at
                          JOIN sections sec ON at.section_id=sec.cid
                          WHERE at.teacher_id='$teacher_id'");
                while ($c = mysqli_fetch_assoc($secs)) {
                    $sel = ($class_id == $c['cid']) ? 'selected' : '';
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
                $yrs = mysqli_query($conn, "SELECT DISTINCT academic_year 
                          FROM assign_teacher 
                          WHERE teacher_id='$teacher_id'
                          ORDER BY academic_year DESC");
                while ($y = mysqli_fetch_assoc($yrs)) {
                    $selected = ($academic_year == $y['academic_year']) ? 'selected' : '';
                    echo "<option value='{$y['academic_year']}' $selected>{$y['academic_year']}</option>";
                }
                ?>
              </select>
            </div>

            <!-- Semester -->
            <div class="col-md-3">
              <label>Semester</label>
              <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Semesters</option>
                <?php
                $semRes = mysqli_query($conn, "SELECT DISTINCT semester FROM marks WHERE teacher_id='$teacher_id' ORDER BY semester ASC");
                while ($sem = mysqli_fetch_assoc($semRes)) {
                    if ($sem['semester'] === null || $sem['semester'] === '') continue;
                    $sel = ($semester == $sem['semester']) ? 'selected' : '';
                    echo "<option value='{$sem['semester']}' $sel>Semester {$sem['semester']}</option>";
                }
                ?>
              </select>
            </div>

            <!-- Student -->
            <div class="col-md-3">
              <label>Student</label>
              <select name="student_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Students</option>
                <?php
                $stuQuery = "SELECT DISTINCT u.sid, CONCAT(u.first_name,' ',u.father_name) AS student_name
                             FROM assign_student ast
                             JOIN students u ON ast.student_id=u.sid
                             JOIN assign_teacher at ON ast.section_id=at.section_id
                             WHERE at.teacher_id='$teacher_id'";
                if ($class_id) $stuQuery .= " AND ast.section_id='$class_id'";
                if ($academic_year) $stuQuery .= " AND ast.academic_year='$academic_year'";

                $students = mysqli_query($conn, $stuQuery);
                while ($st = mysqli_fetch_assoc($students)) {
                    $sel = ($student_id == $st['sid']) ? 'selected' : '';
                    echo "<option value='{$st['sid']}' $sel>{$st['student_name']}</option>";
                }
                ?>
              </select>
            </div>

            <!-- Reset -->
            <div class="col-md-3 align-self-end">
              <a href="student_report.php" class="btn btn-secondary btn-sm w-100">Reset Filters</a>
            </div>

          </div>
        </form>
      </div>
    </div>

    <!-- Report Table -->
    <div class="card mt-4">
      <div class="card-body">
        <div class="table-responsive">
          <table id="studentReportTable" class="table table-hover table-bordered text-center" style="width:100%;">
            <thead class="table-secondary">
              <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Subject</th>
                <th>Section</th>
                <th>Academic Year</th>
                <th>Semester</th>
                <th>Total Marks</th>
                <th>Average %</th>
              </tr>
            </thead>
            <tbody>
              <?php
$sql = "SELECT m.student_id, u.first_name, u.father_name, sub.subject_name, sec.section_name, at.academic_year, m.semester,
               SUM(m.result) AS total_marks, AVG(m.result) AS avg_marks
        FROM marks m
        JOIN students u ON m.student_id=u.sid
        JOIN subjects sub ON m.subject_id=sub.suid
        JOIN sections sec ON m.section_id=sec.cid
        JOIN assign_teacher at ON m.teacher_id=at.teacher_id AND m.section_id=at.section_id
        WHERE m.teacher_id='$teacher_id'";

if ($subject_id) $sql .= " AND m.subject_id='".mysqli_real_escape_string($conn,$subject_id)."'";
if ($class_id) $sql .= " AND m.section_id='".mysqli_real_escape_string($conn,$class_id)."'";
if ($student_id) $sql .= " AND m.student_id='".mysqli_real_escape_string($conn,$student_id)."'";
if ($academic_year) $sql .= " AND at.academic_year='".mysqli_real_escape_string($conn,$academic_year)."'";
if ($semester) $sql .= " AND m.semester='".mysqli_real_escape_string($conn,$semester)."'";

$sql .= " GROUP BY m.student_id, m.subject_id, m.section_id, at.academic_year, m.semester";

$res = mysqli_query($conn, $sql);
if(mysqli_num_rows($res) > 0){
    $no = 1;
    while($r = mysqli_fetch_assoc($res)){
        $student_name = $r['first_name'].' '.$r['father_name'];
        echo "<tr>
                <td>".$no++."</td>
                <td>".htmlspecialchars($student_name)."</td>
                <td>".htmlspecialchars($r['subject_name'])."</td>
                <td>".htmlspecialchars($r['section_name'])."</td>
                <td>".htmlspecialchars($r['academic_year'])."</td>
                <td>".htmlspecialchars($r['semester'])."</td>
                <td>".round($r['total_marks'],2)."</td>
                <td>".round($r['avg_marks'],2)."%</td>
              </tr>";
    }
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
    // Clean any rows that don't match header cell count to avoid "Incorrect column count"
    var $table = $('#studentReportTable');
    var headerCount = $table.find('thead th').length;
    $table.find('tbody tr').each(function(){
      var cells = $(this).children('td,th').length;
      if (cells !== headerCount) { $(this).remove(); }
    });

    $table.DataTable({
      dom: 'Bfrtip',
      pageLength: 25,
      columns: [null, null, null, null, null, null, null, null],
      language: { emptyTable: 'No student reports found' },
      buttons: [
        { extend: 'copyHtml5', text: 'Copy', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { search: 'applied', order: 'applied', page: 'all' } } },
        { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { search: 'applied', order: 'applied', page: 'all' } } },
        { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { search: 'applied', order: 'applied', page: 'all' } } },
        {
          extend: 'pdfHtml5',
          text: 'PDF',
          className: 'btn btn-sm btn-secondary',
          orientation: 'landscape',
          pageSize: 'A3',
          exportOptions: { columns: ':visible', modifier: { search: 'applied', order: 'applied', page: 'all' } },
          customize: function (doc) {
            var colCount = headerCount;
            if (doc.content && doc.content[1] && doc.content[1].table && doc.content[1].table.body) {
              doc.content[1].table.widths = Array(colCount).fill('*');
            }
            doc.defaultStyle = doc.defaultStyle || {};
            doc.defaultStyle.fontSize = 9;
            doc.styles = doc.styles || {};
            doc.styles.tableHeader = doc.styles.tableHeader || {};
            doc.styles.tableHeader.fontSize = 10;
          }
        },
        { extend: 'print', text: 'Print', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { search: 'applied', order: 'applied', page: 'all' } } }
      ]
    });
  }
});
</script>
