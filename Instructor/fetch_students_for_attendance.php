<?php
include('../connection/connection.php');
session_start();

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 1;

if ($class_id <= 0) { echo "<div class='text-danger'>Invalid class ID</div>"; exit; }

// Get section & academic year
$classInfo = mysqli_fetch_assoc(mysqli_query(
    $conn, "SELECT section_id, academic_year, instructor_id 
            FROM assign_instructor WHERE hid=$class_id LIMIT 1"
));
if (!$classInfo) { echo "<div class='text-danger'>Class not found</div>"; exit; }

// Students
$res = mysqli_query($conn, "SELECT u.sid, CONCAT(u.first_name,' ',u.father_name,' ',u.grand_father_name) AS full_name
                            FROM assign_student ast
                            LEFT JOIN students u ON ast.student_id=u.sid
                            WHERE ast.section_id=" . (int)$classInfo['section_id'] . " 
                            ORDER BY u.first_name ASC");

if (mysqli_num_rows($res) > 0) {

    echo "<div class='d-flex justify-content-between align-items-center mb-3'>
            <div>
              <label for='semesterSelect' class='fw-bold me-2'>Semester:</label>
              <select id='semesterSelect' class='form-select form-select-sm d-inline-block w-auto'>
                <option value='1' " . ($semester == 1 ? 'selected' : '') . ">Semester 1</option>
                <option value='2' " . ($semester == 2 ? 'selected' : '') . ">Semester 2</option>
              </select>
            </div>
            <div>
              <input type='text' id='searchInput' class='form-control form-control-sm' placeholder='Search student...'>
            </div>
          </div>";

    echo "<form id='attendanceForm'>
            <input type='hidden' name='section_id' value='" . (int)$classInfo['section_id'] . "'>
            <input type='hidden' name='academic_year' value='" . htmlspecialchars($classInfo['academic_year']) . "'>
            <input type='hidden' name='instructor_id' value='" . (int)$classInfo['instructor_id'] . "'>
            <input type='hidden' name='semester' value='" . (int)$semester . "'>";

    // Current week days (Mon–Fri)
    $today = new DateTime();
    $monday = (clone $today)->modify('monday this week');
    $days = [];
    for ($i = 0; $i < 5; $i++) {
        $days[] = $monday->format("Y-m-d");
        $monday->modify("+1 day");
    }

    echo "<div class='table-responsive'>
            <table class='table table-striped table-hover text-center align-middle'>
              <thead class='table-secondary'>
                <tr>
                  <th style='width:5%'>#</th>
                  <th style='width:35%'>Full Name</th>";
    foreach ($days as $d) { 
        echo "<th class='text-nowrap'>".date("D", strtotime($d))."<br>".date("d M", strtotime($d))."</th>"; 
    }
    echo "    </tr>
              </thead>
              <tbody id='studentTable'>";

    $no = 1;
    while ($s = mysqli_fetch_assoc($res)) {
        echo "<tr>
                <td>" . $no++ . "</td>
                <td>" . htmlspecialchars($s['full_name']) . "</td>";

        foreach ($days as $d) {
            // Check if already inserted
            $check = mysqli_fetch_assoc(mysqli_query($conn, 
                "SELECT status FROM attendance 
                 WHERE student_id={$s['sid']} 
                   AND section_id=".(int)$classInfo['section_id']." 
                   AND academic_year='".mysqli_real_escape_string($conn,$classInfo['academic_year'])."' 
                   AND semester=$semester 
                   AND attendance_date='$d' 
                   LIMIT 1"));

            if ($check) {
                // Already exists → show readonly value
                echo "<td><input type='text' class='form-control form-control-sm text-center' 
                        value='" . htmlspecialchars($check['status']) . "' readonly></td>";
            } else {
                // Not inserted yet → show select with default null
                echo "<td>
                        <select name='attendance[" . (int)$s['sid'] . "][" . $d . "]' class='form-select form-select-sm'>
                            <option value='' selected disabled>--</option>
                            <option value='Present'>Present</option>
                            <option value='Absent'>Absent</option>
                            <option value='Late'>Late</option>
                            <option value='Excused'>Excused</option>
                        </select>
                      </td>";
            }
        }
        echo "</tr>";
    }
    echo "  </tbody>
            </table>
          </div>
          <div class='text-end'>
            <button id='saveAttendanceBtn' type='button' class='btn btn-success px-4 me-2'>Save Attendance</button>
            <button id='closeAttendanceBtn' type='button' class='btn btn-secondary px-4'>Close</button>
          </div>
          </form>

          <!-- Alert container -->
          <div id='messageContainer' class='mt-3'></div>

          <script>
          document.getElementById('closeAttendanceBtn').addEventListener('click', function() {
              document.getElementById('attendanceForm').remove();
          });

          document.getElementById('saveAttendanceBtn').addEventListener('click', function() {
              var formData = new FormData(document.getElementById('attendanceForm'));
              fetch('save_attendance.php', {
                  method: 'POST',
                  body: formData
              })
              .then(response => response.text())
              .then(data => {
                  // Success message
                  var msg = document.createElement('div');
                  msg.className = 'alert alert-success alert-dismissible fade show';
                  msg.role = 'alert';
                  msg.innerHTML = 'Attendance saved successfully!';
                  document.getElementById('messageContainer').appendChild(msg);
                  setTimeout(() => msg.remove(), 3000);

                  // Close form
                  document.getElementById('attendanceForm').remove();
              })
              .catch(err => {
                  var msg = document.createElement('div');
                  msg.className = 'alert alert-danger alert-dismissible fade show';
                  msg.role = 'alert';
                  msg.innerHTML = 'Error saving attendance.';
                  document.getElementById('messageContainer').appendChild(msg);
                  setTimeout(() => msg.remove(), 3000);
              });
          });
          </script>";

} else {
    echo "<div class='text-danger'>No students in this class.</div>";
}
?>
