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
$student_id   = $_GET['student_id'] ?? '';
$class_id     = $_GET['class_id'] ?? '';
$filter_type  = $_GET['filter_type'] ?? '';
$anchor_date  = $_GET['anchor_date'] ?? '';
$from_date    = $_GET['from_date'] ?? '';
$to_date      = $_GET['to_date'] ?? '';

// Build date range based on filter_type
$startDate = $endDate = '';
if ($filter_type === 'weekly') {
  $anchor = $anchor_date ?: date('Y-m-d');
  $d = new DateTime($anchor);
  // Set to Monday of the current week
  if ($d->format('N') != 1) { $d->modify('last monday'); }
  $startDate = $d->format('Y-m-d');
  $endDate = (clone $d)->modify('+6 days')->format('Y-m-d');
} elseif ($filter_type === 'monthly') {
  $anchor = $anchor_date ?: date('Y-m-d');
  $d = new DateTime($anchor);
  $startDate = $d->modify('first day of this month')->format('Y-m-d');
  $endDate = (new DateTime($anchor))->modify('last day of this month')->format('Y-m-d');
} elseif ($filter_type === 'yearly') {
  $anchor = $anchor_date ?: date('Y-m-d');
  $y = (new DateTime($anchor))->format('Y');
  $startDate = $y . '-01-01';
  $endDate   = $y . '-12-31';
} elseif ($filter_type === 'range') {
  if ($from_date && $to_date) {
    $startDate = (new DateTime($from_date))->format('Y-m-d');
    $endDate   = (new DateTime($to_date))->format('Y-m-d');
  }
}
?>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Attendance Reports</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Reports</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Attendance Reports</a></li>
      </ul>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <form method="GET" action="">
          <div class="row g-2">

            <!-- Student -->
            <div class="col-md-3">
              <label>Student</label>
              <select name="student_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Students</option>
                <?php
                $students = mysqli_query($conn, "SELECT sid, first_name, father_name FROM students");
                while ($s = mysqli_fetch_assoc($students)) {
                    $sel = ($student_id == $s['sid']) ? 'selected' : '';
                    echo "<option value='{$s['sid']}' $sel>{$s['first_name']} {$s['father_name']}</option>";
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

            <!-- Date Filters -->
            <div class="col-md-3">
              <label>Filter Type</label>
              <select name="filter_type" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Dates</option>
                <option value="weekly"  <?= $filter_type==='weekly'?'selected':''; ?>>Weekly</option>
                <option value="monthly" <?= $filter_type==='monthly'?'selected':''; ?>>Monthly</option>
                <option value="yearly"  <?= $filter_type==='yearly'?'selected':''; ?>>Yearly</option>
                <option value="range"   <?= $filter_type==='range'?'selected':''; ?>>Range</option>
              </select>
            </div>

            <div class="col-md-3" id="anchorDateWrap" <?= in_array($filter_type,['weekly','monthly','yearly']) ? '' : 'style="display:none"'; ?>>
              <label>Anchor Date</label>
              <input type="date" name="anchor_date" value="<?= htmlspecialchars($anchor_date) ?>" class="form-control form-control-sm" onchange="this.form.submit()">
            </div>

            <div class="col-md-3" id="fromDateWrap" <?= $filter_type==='range' ? '' : 'style="display:none"'; ?>>
              <label>From</label>
              <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>" class="form-control form-control-sm" onchange="this.form.submit()">
            </div>

            <div class="col-md-3" id="toDateWrap" <?= $filter_type==='range' ? '' : 'style="display:none"'; ?>>
              <label>To</label>
              <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>" class="form-control form-control-sm" onchange="this.form.submit()">
            </div>

            <!-- Reset -->
            <div class="col-md-3 align-self-end">
              <a href="attendance_report.php" class="btn btn-secondary btn-sm w-100">Reset Filters</a>
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
                <th>Student Name</th>
                <th>Section</th>
                <th>Class Type</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Late</th>
                <th>Excused</th>
                <th>Total Days</th>
                <th>Present %</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT a.student_id,
                             CONCAT(u.first_name,' ',u.father_name) AS student_name,
                             sec.section_name, sec.class_type,
                             SUM(a.status='Present') AS present_count,
                             SUM(a.status='Absent')  AS absent_count,
                             SUM(a.status='Late')    AS late_count,
                             SUM(a.status='Excused') AS excused_count,
                             COUNT(*) AS total_days
                      FROM attendance a
                      JOIN students u ON a.student_id = u.sid
                      JOIN sections sec ON a.section_id = sec.cid
                      WHERE 1=1";

              if ($student_id) $sql .= " AND a.student_id='".mysqli_real_escape_string($conn,$student_id)."'";
              if ($class_id)   $sql .= " AND a.section_id='".mysqli_real_escape_string($conn,$class_id)."'";
              if ($startDate && $endDate) {
                  $sql .= " AND a.attendance_date BETWEEN '".mysqli_real_escape_string($conn,$startDate)."' AND '".mysqli_real_escape_string($conn,$endDate)."'";
              }

              $sql .= " GROUP BY a.student_id, student_name, sec.section_name, sec.class_type
                        ORDER BY student_name ASC";

              $res = mysqli_query($conn, $sql);
              if($res && mysqli_num_rows($res) > 0){
                $no = 1;
                while($r = mysqli_fetch_assoc($res)){
                    $present = (int)$r['present_count'];
                    $total   = (int)$r['total_days'];
                    $pct     = $total ? round(($present / $total) * 100, 2) : 0;
                    echo "<tr>
                            <td>".$no++."</td>
                            <td>".htmlspecialchars($r['student_name'])."</td>
                            <td>".htmlspecialchars($r['section_name'])."</td>
                            <td>".htmlspecialchars($r['class_type'])."</td>
                            <td>".$present."</td>
                            <td>".(int)$r['absent_count']."</td>
                            <td>".(int)$r['late_count']."</td>
                            <td>".(int)$r['excused_count']."</td>
                            <td>".$total."</td>
                            <td>".$pct."%</td>
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
      columns: [null, null, null, null, null, null, null, null, null, null],
      language: { emptyTable: 'No attendance records found' },
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
        { extend: 'print', text: 'Print', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { page: 'all' } } }
      ]
    });
  } else {
    console.error('DataTables or Buttons not loaded');
  }
});
</script>
<script>
// Toggle date inputs visibility on client side without full reload
document.addEventListener('DOMContentLoaded', function(){
  const typeSel = document.querySelector('select[name="filter_type"]');
  const anchorWrap = document.getElementById('anchorDateWrap');
  const fromWrap = document.getElementById('fromDateWrap');
  const toWrap = document.getElementById('toDateWrap');
  if(!typeSel) return;
  function updateVisibility(){
    const v = typeSel.value;
    anchorWrap && (anchorWrap.style.display = (v==='weekly'||v==='monthly'||v==='yearly') ? '' : 'none');
    const isRange = (v==='range');
    fromWrap && (fromWrap.style.display = isRange ? '' : 'none');
    toWrap && (toWrap.style.display = isRange ? '' : 'none');
  }
  typeSel.addEventListener('change', updateVisibility);
  updateVisibility();
});
</script>
