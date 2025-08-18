<?php
include('../connection/connection.php');
session_start();

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 0; // 0 = All
$view = isset($_GET['view']) ? $_GET['view'] : 'weekly'; // weekly, monthly, yearly, range
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';

if ($class_id <= 0) { echo "<div class='text-danger'>Invalid class ID</div>"; exit; }

// Get section & academic year
$classInfo = mysqli_fetch_assoc(mysqli_query(
    $conn, "SELECT section_id, academic_year FROM assign_instructor WHERE hid=$class_id LIMIT 1"
));
if (!$classInfo) { echo "<div class='text-danger'>Class not found</div>"; exit; }

$section_id = (int)$classInfo['section_id'];
$academic_year = mysqli_real_escape_string($conn, $classInfo['academic_year']);

// Student list
$res = mysqli_query($conn, "SELECT u.sid, CONCAT(u.first_name,' ',u.father_name,' ',u.grand_father_name) AS full_name
                            FROM assign_student ast
                            LEFT JOIN students u ON ast.student_id=u.sid
                            WHERE ast.section_id=$section_id 
                            ORDER BY u.first_name ASC");

if (mysqli_num_rows($res) > 0) {
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
              <input type='text' id='searchInput' class='form-control form-control-sm' placeholder='Search student...'>
            </div>
          </div>";

    echo "<div class='table-responsive'>
            <table class='table table-bordered table-hover text-center align-middle'>
              <thead class='table-dark'>
                <tr>
                  <th style='width:5%'>#</th>
                  <th style='width:30%'>Full Name</th>
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

        // Base condition
        $cond = "student_id=$sid AND section_id=$section_id AND academic_year='$academic_year'";
        if ($semester > 0) $cond .= " AND semester=$semester";

        // Add filter by view
        if ($view == 'weekly') {
            $today = new DateTime();
            $monday = (clone $today)->modify('monday this week')->format("Y-m-d");
            $friday = (clone $today)->modify('friday this week')->format("Y-m-d");
            $cond .= " AND attendance_date BETWEEN '$monday' AND '$friday'";
        }
        elseif ($view == 'monthly') {
            $month = date('m');
            $year = date('Y');
            $cond .= " AND MONTH(attendance_date)=$month AND YEAR(attendance_date)=$year";
        }
        elseif ($view == 'yearly') {
            $year = date('Y');
            $cond .= " AND YEAR(attendance_date)=$year";
        }
        elseif ($view == 'range' && $from && $to) {
            $cond .= " AND attendance_date BETWEEN '".mysqli_real_escape_string($conn,$from)."' 
                                               AND '".mysqli_real_escape_string($conn,$to)."'";
        }

        // Count per status
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
          // Search filter
          document.getElementById('searchInput').addEventListener('keyup', function() {
              var val = this.value.toLowerCase();
              document.querySelectorAll('#studentTable tr').forEach(function(row) {
                  row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
              });
          });

          // Semester/view filter reload
          function reload() {
              var sem = document.getElementById('semesterSelect').value;
              var view = document.getElementById('viewSelect').value;
              var from = document.getElementById('fromDate')?.value || '';
              var to = document.getElementById('toDate')?.value || '';
              var url = '?class_id=$class_id&semester='+sem+'&view='+view+'&from='+from+'&to='+to;
              window.location.href = url;
          }
          document.getElementById('semesterSelect').addEventListener('change', reload);
          document.getElementById('viewSelect').addEventListener('change', reload);
          document.getElementById('rangeBtn')?.addEventListener('click', reload);

          // Show/hide range inputs
          document.getElementById('viewSelect').addEventListener('change', function() {
              document.getElementById('rangeInputs').style.display = (this.value==='range') ? 'block' : 'none';
          });

          // Close
          document.getElementById('closeAttendanceBtn').addEventListener('click', function() {
              document.querySelector('.table-responsive').remove();
              this.parentElement.remove();
          });
          </script>";

} else {
    echo "<div class='text-danger'>No students in this class.</div>";
}
?>
