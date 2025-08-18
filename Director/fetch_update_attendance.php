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
            $check = mysqli_fetch_assoc(mysqli_query($conn, 
                "SELECT status FROM attendance 
                 WHERE student_id={$s['sid']} 
                   AND section_id=".(int)$classInfo['section_id']." 
                   AND academic_year='".mysqli_real_escape_string($conn,$classInfo['academic_year'])."' 
                   AND semester=$semester 
                   AND attendance_date='$d' 
                   LIMIT 1"));

            $currentStatus = $check ? htmlspecialchars($check['status']) : '';

            echo "<td>
                    <select name='attendance[" . (int)$s['sid'] . "][" . $d . "]' class='form-select form-select-sm'>
                        <option value='' ".($currentStatus==''?'selected':'').">--</option>
                        <option value='Present' ".($currentStatus=='Present'?'selected':'').">Present</option>
                        <option value='Absent' ".($currentStatus=='Absent'?'selected':'').">Absent</option>
                        <option value='Late' ".($currentStatus=='Late'?'selected':'').">Late</option>
                        <option value='Excused' ".($currentStatus=='Excused'?'selected':'').">Excused</option>
                    </select>
                  </td>";
        }
        echo "</tr>";
    }
    echo "  </tbody>
            </table>
          </div>
          <div class='text-end'>
            <button id='saveAttendanceBtn' type='button' class='btn btn-success px-4 me-2'>Update Attendance</button>
            <button id='closeAttendanceBtn' type='button' class='btn btn-secondary px-4'>Close</button>
          </div>
          </form>

          <!-- SweetAlert2 -->
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

          <script>
          document.getElementById('closeAttendanceBtn').addEventListener('click', function() {
              document.getElementById('attendanceForm').remove();
          });

          document.getElementById('saveAttendanceBtn').addEventListener('click', function() {
              Swal.fire({
                  title: 'Are you sure?',
                  text: 'You are about to update attendance records.',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, update it!'
              }).then((result) => {
                  if (result.isConfirmed) {
                      var formData = new FormData(document.getElementById('attendanceForm'));
                      fetch('update_attendance_list.php', {
                          method: 'POST',
                          body: formData
                      })
                      .then(response => response.json())
                      .then(data => {
                          if (data.success) {
                              Swal.fire({
                                  icon: 'success',
                                  title: 'Updated!',
                                  text: 'Attendance updated successfully.',
                                  timer: 2000,
                                  showConfirmButton: false
                              });
                          } else {
                              Swal.fire({
                                  icon: 'error',
                                  title: 'Update Failed',
                                  text: data.message || 'Error updating attendance.'
                              });
                          }
                      })
                      .catch(err => {
                          Swal.fire({
                              icon: 'error',
                              title: 'Network Error',
                              text: 'Could not connect to server. Please try again.'
                          });
                      });
                  }
              });
          });
          </script>";

} else {
    echo "<div class='text-danger'>No students in this class.</div>";
}
?>
