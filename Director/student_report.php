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
$academic_year   = $_GET['academic_year'] ?? '';
$disabilities     = $_GET['disabilities'] ?? '';
$class_id        = $_GET['class_id'] ?? '';
$gender          = $_GET['gender'] ?? '';
$from_admission  = $_GET['from_admission'] ?? '';
$to_admission    = $_GET['to_admission'] ?? '';
$from_birth      = $_GET['from_birth'] ?? '';
$to_birth        = $_GET['to_birth'] ?? '';
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Student Reports</h3>
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

            <!-- Sections -->
            <div class="col-md-3">
              <label>Sections</label>
              <select name="class_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Sections</option>
                <?php
                $cls = mysqli_query($conn, "SELECT * FROM sections");
                while ($c = mysqli_fetch_assoc($cls)) {
                    $selected = ($class_id == $c['cid']) ? 'selected' : '';
                    echo "<option value='{$c['cid']}' $selected>{$c['section_name']} ({$c['class_type']})</option>";
                }
                ?>
              </select>
            </div>
            <!-- Gender -->
            <div class="col-md-3">
              <label>Gender</label>
              <select name="gender" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="M" <?= ($gender=="M") ? 'selected' : '' ?>>M</option>
                <option value="F" <?= ($gender=="F") ? 'selected' : '' ?>>F</option>
              </select>
            </div>
          <!-- disabilities -->
            <div class="col-md-3">
              <label>Disabilities</label>
              <select name="disabilities" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="yes" <?= ($disabilities=="yes") ? 'selected' : '' ?>>yes</option>
                <option value="none" <?= ($disabilities=="none") ? 'selected' : '' ?>>none</option>
              </select>
            </div>
            <!-- Reset -->
            <div class="col-md-3 align-self-end">
              <a href="student_report.php" class="btn btn-secondary btn-sm w-100">Reset Filters</a>
            </div>
          </div>

          <!-- Date Filters -->
        
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
                <th>Student ID</th>
                <th>Full Name</th>
                <th>Gender</th>
                <th>Region</th>
                <th>Zone</th>
                <th>Woreda</th>
                <th>Section</th>
                <th>Class Type</th>
                <th>Academic Year</th>
                <th>Admission Date</th>
                <th>Birth Date</th>
                <th>disabilities</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT s.sid, s.student_id, s.first_name, s.father_name, s.grand_father_name, s.gender,
                             r.name AS region, z.name AS zone, W.name AS woreda, s.disabilities,
                             sec.section_name, sec.class_type, a.academic_year, s.created_at AS admission_date,
                              s.dob AS birth_date
                      FROM students s
                      JOIN assign_student a ON s.sid = a.student_id
                      JOIN sections sec ON a.section_id = sec.cid
                      LEFT JOIN regions r ON s.region = r.id
                      LEFT JOIN zones z ON s.zone = z.id
                      LEFT JOIN woredas w ON s.woreda = w.id
                      WHERE 1=1";

              if($academic_year) $sql .= " AND a.academic_year='".mysqli_real_escape_string($conn,$academic_year)."'";
              if($class_id) $sql .= " AND a.section_id='".mysqli_real_escape_string($conn,$class_id)."'";
              if($gender) $sql .= " AND s.gender='".mysqli_real_escape_string($conn,$gender)."'";
              if($disabilities) $sql .= " AND s.disabilities='".mysqli_real_escape_string($conn,$disabilities)."'";
              if($from_admission && $to_admission) $sql .= " AND s.created_at BETWEEN '".mysqli_real_escape_string($conn,$from_admission)."' AND '".mysqli_real_escape_string($conn,$to_admission)."'";
              if($from_birth && $to_birth) $sql .= " AND s.dob BETWEEN '".mysqli_real_escape_string($conn,$from_birth)."' AND '".mysqli_real_escape_string($conn,$to_birth)."'";

        $res = mysqli_query($conn, $sql);
        if(mysqli_num_rows($res) > 0){
          $no = 1;
          while($r = mysqli_fetch_assoc($res)){
                      echo "<tr>
                              <td>".$no++."</td>
                              <td>".htmlspecialchars($r['student_id'])."</td>
                              <td>".htmlspecialchars($r['first_name']." ".$r['father_name']." ".$r['grand_father_name'])."</td>
                              <td>".htmlspecialchars($r['gender'])."</td>
                              <td>".htmlspecialchars($r['region'])."</td>
                              <td>".htmlspecialchars($r['zone'])."</td>
                              <td>".htmlspecialchars($r['woreda'])."</td>
                              <td>".htmlspecialchars($r['section_name'])."</td>
                              <td>".htmlspecialchars($r['class_type'])."</td>
                              <td>".htmlspecialchars($r['academic_year'])."</td>
                              <td>".htmlspecialchars($r['admission_date'])."</td>
                              <td>".htmlspecialchars($r['birth_date'])."</td>
                              <td>".htmlspecialchars($r['disabilities'])."</td>
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
function toggleSection(type){
  const section = document.getElementById(type+'DateRange');
  section.classList.toggle('d-none');
}
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
  columns: [null, null, null, null, null, null, null, null, null, null, null, null,null],
      language: { emptyTable: 'No students found' },
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
            // Fit all columns across the page
            var body = doc.content[1].table.body;
            if (body && body.length && body[0]) {
              var colCount = body[0].length;
              doc.content[1].table.widths = new Array(colCount).fill('*');
            }
            // Tweak fonts for readability
            doc.defaultStyle.fontSize = 8;
            if (doc.styles && doc.styles.tableHeader) {
              doc.styles.tableHeader.fontSize = 9;
              doc.styles.tableHeader.alignment = 'center';
            }
          }
        },
        { extend: 'print', text: 'Print', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { page: 'all' } } }
      ]
    });
  } else {
    console.error('DataTables or Buttons not loaded');
  }
});
</script>
