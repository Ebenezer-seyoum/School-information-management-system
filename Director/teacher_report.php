<?php
include('directorHeader.php');

// --- Login & Role Check ---
if (!isset($_SESSION['uid'])) {
    die("You must be logged in to view this page.");
}
$user = getUserByID($_SESSION['uid']);
if (!$user || getRoleNameById($user['user_type']) !== "Director") {
    die("Not authorized.");
}

// --- Filters ---
$teacher_id   = $_GET['teacher_id'] ?? '';
$subject_id   = $_GET['subject_id'] ?? '';
$class_id     = $_GET['class_id'] ?? '';
$academic_year = $_GET['academic_year'] ?? '';
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Teacher Reports</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Reports</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Teacher Reports</a></li>
      </ul>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <form method="GET" action="">
          <div class="row g-2">

            <!-- Teacher -->
            <div class="col-md-3">
              <label>Teacher</label>
              <select name="teacher_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Teachers</option>
                <?php
                $teachers = mysqli_query($conn, "SELECT uid, first_name, father_name FROM users WHERE user_type= 1"); 
                while ($t = mysqli_fetch_assoc($teachers)) {
                    $sel = ($teacher_id == $t['uid']) ? 'selected' : '';
                    echo "<option value='{$t['uid']}' $sel>{$t['first_name']} {$t['father_name']}</option>";
                }
                ?>
              </select>
            </div>

            <!-- Subject -->
            <div class="col-md-3">
              <label>Subject</label>
              <select name="subject_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Subjects</option>
                <?php
                $subjects = mysqli_query($conn, "SELECT suid, subject_name FROM subjects");
                while ($s = mysqli_fetch_assoc($subjects)) {
                    $sel = ($subject_id == $s['suid']) ? 'selected' : '';
                    echo "<option value='{$s['suid']}' $sel>{$s['subject_name']}</option>";
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
                $yrs = mysqli_query($conn, "SELECT DISTINCT academic_year FROM assign_teacher ORDER BY academic_year DESC");
                while ($y = mysqli_fetch_assoc($yrs)) {
                    $selected = ($academic_year == $y['academic_year']) ? 'selected' : '';
                    echo "<option value='{$y['academic_year']}' $selected>{$y['academic_year']}</option>";
                }
                ?>
              </select>
            </div>

            <!-- Section / Class -->
            <div class="col-md-3">
              <label>Section</label>
              <select name="class_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Sections</option>
                <?php
                $secs = mysqli_query($conn, "SELECT * FROM sections");
                while ($c = mysqli_fetch_assoc($secs)) {
                    $sel = ($class_id == $c['cid']) ? 'selected' : '';
                    echo "<option value='{$c['cid']}' $sel>{$c['section_name']} ({$c['class_type']})</option>";
                }
                ?>
              </select>
            </div>

            <!-- Reset -->
            <div class="col-md-3 align-self-end">
              <a href="teacher_report.php" class="btn btn-secondary btn-sm w-100">Reset Filters</a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Report Table -->
    <div class="card mt-4">
      <div class="card-body">
        <div class="table-responsive">
          <table id="reportTable" class="table table-hover table-bordered text-center" style="width:100%;">
            <thead class="table-secondary">
              <tr>
                <th>#</th>
                <th>Teacher Name</th>
                <th>Subject</th>
                <th>Section</th>
                <th>Class Type</th>
                <th>Number of Students</th>
                <th>Average Grade</th>
                <th>Academic Year</th>
              </tr>
            </thead>
            <tbody>
              <?php
$sql = "SELECT u.uid, CONCAT(u.first_name,' ',u.father_name) AS teacher_name,
               sub.subject_name, sec.section_name, sec.class_type, 
               at.section_id, at.subject_id, at.teacher_id, at.academic_year
        FROM assign_teacher at
        JOIN users u ON at.teacher_id=u.uid
        JOIN subjects sub ON at.subject_id=sub.suid
        JOIN sections sec ON at.section_id=sec.cid
        WHERE 1=1";

if ($teacher_id) $sql .= " AND at.teacher_id='".mysqli_real_escape_string($conn,$teacher_id)."'";
if ($subject_id) $sql .= " AND at.subject_id='".mysqli_real_escape_string($conn,$subject_id)."'";
if ($class_id) $sql .= " AND at.section_id='".mysqli_real_escape_string($conn,$class_id)."'";
if ($academic_year) $sql .= " AND at.academic_year='".mysqli_real_escape_string($conn,$academic_year)."'";

$res = mysqli_query($conn, $sql);
if(mysqli_num_rows($res) > 0){
    $no = 1;
    while($r = mysqli_fetch_assoc($res)){
        $section_id_row = $r['section_id'];
        $subject_id_row = $r['subject_id'];
        $teacher_id_row = $r['teacher_id'];

        // Count students and average marks from marks table
        $student_sql = "SELECT COUNT(DISTINCT m.student_id) AS student_count, 
                               AVG(m.result) AS avg_grade
                        FROM marks m
                        JOIN assign_student s 
                          ON m.student_id = s.student_id 
                         AND m.section_id = s.section_id";

        $student_sql .= " WHERE m.section_id='".mysqli_real_escape_string($conn,$section_id_row)."' 
                          AND m.subject_id='".mysqli_real_escape_string($conn,$subject_id_row)."' 
                          AND m.teacher_id='".mysqli_real_escape_string($conn,$teacher_id_row)."'";

        if($academic_year){
            $student_sql .= " AND s.academic_year='".mysqli_real_escape_string($conn,$academic_year)."'";
        }

        $student_res = mysqli_fetch_assoc(mysqli_query($conn,$student_sql));

        echo "<tr>
                <td>".$no++."</td>
                <td>".htmlspecialchars($r['teacher_name'])."</td>
                <td>".htmlspecialchars($r['subject_name'])."</td>
                <td>".htmlspecialchars($r['section_name'])."</td>
                <td>".htmlspecialchars($r['class_type'])."</td>
                <td>".($student_res['student_count'] ?? 0)."</td>
                <td>".round((float)($student_res['avg_grade'] ?? 0), 2)."%"."</td>
                <td>".htmlspecialchars($r['academic_year'])."</td>

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
    var $tbl = $('#reportTable');
    var expectedCols = $tbl.find('thead th').length;
    $tbl.find('tbody tr').each(function(){
      var cells = $(this).children('td,th').length;
      if (cells !== expectedCols) {
        $(this).remove();
      }
    });
    $tbl.DataTable({
      dom: 'Bfrtip',
      pageLength: 25,
      columns: [null, null, null, null, null,null,null, null],
      language: { emptyTable: 'No teachers found' },
      buttons: [
        { extend: 'copyHtml5', text: 'Copy', className: 'btn btn-sm btn-secondary' },
        { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-sm btn-secondary' },
        { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-sm btn-secondary' },
        {
          extend: 'pdfHtml5',
          text: 'PDF',
          className: 'btn btn-sm btn-secondary',
          orientation: 'landscape',
          pageSize: 'A3',
          customize: function (doc) {
            var body = doc.content[1].table.body;
            if (body && body.length && body[0]) {
              var colCount = body[0].length;
              doc.content[1].table.widths = new Array(colCount).fill('*');
            }
            doc.defaultStyle.fontSize = 8;
            if (doc.styles && doc.styles.tableHeader) {
              doc.styles.tableHeader.fontSize = 9;
              doc.styles.tableHeader.alignment = 'center';
            }
          }
        },
        { extend: 'print', text: 'Print', className: 'btn btn-sm btn-secondary' }
      ]
    });
  } else {
    console.error('DataTables or Buttons not loaded');
  }
});
</script>
