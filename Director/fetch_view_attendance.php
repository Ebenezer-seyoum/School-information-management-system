<?php
include('../connection/connection.php');
session_start();

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 0;
$view     = $_GET['view'] ?? 'weekly';
$from     = $_GET['from'] ?? '';
$to       = $_GET['to'] ?? '';

if ($class_id <= 0) { echo "<div class='text-danger'>Invalid class ID</div>"; exit; }

$classInfo = mysqli_fetch_assoc(mysqli_query(
    $conn, "SELECT section_id, academic_year FROM assign_instructor WHERE hid=$class_id LIMIT 1"
));
if (!$classInfo) { echo "<div class='text-danger'>Class not found</div>"; exit; }

$section_id = (int)$classInfo['section_id'];
$academic_year = mysqli_real_escape_string($conn, $classInfo['academic_year']);


// Get students
$res = mysqli_query($conn, "SELECT u.sid, CONCAT(u.first_name,' ',u.father_name,' ',u.grand_father_name) AS full_name
                            FROM assign_student ast
                            LEFT JOIN students u ON ast.student_id=u.sid
                            WHERE ast.section_id=$section_id 
                            ORDER BY u.first_name ASC");

if (mysqli_num_rows($res) == 0){
    echo "<div class='text-danger'>No students in this class.</div>";
    exit;
}

// Filters
$condBase = "section_id=$section_id AND academic_year='$academic_year'";
if ($semester > 0) $condBase .= " AND semester=$semester";

$dateFilter = "";
if ($view == 'weekly') {
    $monday = date("Y-m-d", strtotime("monday this week"));
    $friday = date("Y-m-d", strtotime("friday this week"));
    $dateFilter = " AND attendance_date BETWEEN '$monday' AND '$friday'";
}
elseif ($view == 'monthly') {
    $dateFilter = " AND MONTH(attendance_date)=".date('m')." AND YEAR(attendance_date)=".date('Y');
}
elseif ($view == 'yearly') {
    $dateFilter = " AND YEAR(attendance_date)=".date('Y');
}
elseif ($view == 'range' && $from && $to) {
    $from = mysqli_real_escape_string($conn,$from);
    $to   = mysqli_real_escape_string($conn,$to);
    $dateFilter = " AND attendance_date BETWEEN '$from' AND '$to'";
}

echo "<div class='d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2'>
        <div>
          <label class='fw-bold me-2'>Semester:</label>
          <select id='semesterSelect' class='form-select form-select-sm d-inline-block w-auto'>
            <option value='0' ".($semester==0?'selected':'').">All</option>
            <option value='1' ".($semester==1?'selected':'').">Semester 1</option>
            <option value='2' ".($semester==2?'selected':'').">Semester 2</option>
          </select>
          
          <label class='fw-bold ms-3 me-2'>View:</label>
          <select id='viewSelect' class='form-select form-select-sm d-inline-block w-auto'>
            <option value='weekly' ".($view=='weekly'?'selected':'').">Weekly</option>
            <option value='monthly' ".($view=='monthly'?'selected':'').">Monthly</option>
            <option value='yearly' ".($view=='yearly'?'selected':'').">Yearly</option>
            <option value='range' ".($view=='range'?'selected':'').">Custom Range</option>
          </select>
        </div>
        
        <div id='rangeInputs' style='display:".($view=='range'?'block':'none').";'>
          <label class='me-1'>From:</label>
          <input type='date' id='fromDate' value='$from' class='form-control form-control-sm d-inline-block w-auto'>
          <label class='ms-2 me-1'>To:</label>
          <input type='date' id='toDate' value='$to' class='form-control form-control-sm d-inline-block w-auto'>
          <button id='rangeBtn' class='btn btn-sm btn-primary ms-2'>Apply</button>
        </div>
        
        <div>
          <div class='search-box w-100'>
            <div class='input-group input-group-sm'>
              <span class='input-group-text bg-primary text-white'><i class='fas fa-search'></i></span>
              <input type='text' id='searchInput' class='form-control form-control-sm search-input' placeholder='Search by ID, Name, or Role...'>
              <button class='btn btn-primary btn-sm' type='button' id='searchTrigger' aria-label='Search'>Search</button>
            </div>
          </div>
        </div>
      </div>";

echo "<div class='table-responsive'>
        <table class='table table-bordered table-hover text-center align-middle'>
          <thead class='table-dark'>
            <tr>
              <th>#</th>
              <th>Full Name</th>
              <th>✅ Present</th>
              <th>❌ Absent</th>
              <th>Late</th>
              <th>Excused</th>
              <th>% Attendance</th>
            </tr>
          </thead>
          <tbody id='studentTable'>";

$no = 1;
while ($s = mysqli_fetch_assoc($res)) {
    $sid = (int)$s['sid'];
    $cond = "student_id=$sid AND $condBase $dateFilter";

    $stats = ['Present'=>0,'Absent'=>0,'Late'=>0,'Excused'=>0];
    $result2 = mysqli_query($conn, "SELECT status, COUNT(*) as cnt FROM attendance WHERE $cond GROUP BY status");
    while ($r = mysqli_fetch_assoc($result2)) {
        $stats[$r['status']] = (int)$r['cnt'];
    }

    $total = array_sum($stats);
    $percent = $total>0 ? round(($stats['Present']/$total)*100,1) : 0;

    echo "<tr>
            <td>".$no++."</td>
            <td>".htmlspecialchars($s['full_name'])."</td>
            <td>".$stats['Present']."</td>
            <td>".$stats['Absent']."</td>
            <td>".$stats['Late']."</td>
            <td>".$stats['Excused']."</td>
            <td><span class='fw-bold ".($percent<75?'text-danger':'text-success')."'>".$percent."%</span></td>
          </tr>";
}

echo "  </tbody>
        </table>
      </div>
      <div class='text-end mt-3'>
        <button id='closeAttendanceBtn' type='button' class='btn btn-secondary px-4'>Close</button>
      </div>

      <script>
      // search filter
      document.getElementById('searchInput').addEventListener('keyup', function(){
        var val = this.value.toLowerCase();
        document.querySelectorAll('#studentTable tr').forEach(function(row){
          row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
      });
      </script>";
?>
