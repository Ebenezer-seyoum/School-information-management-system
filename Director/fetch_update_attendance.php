<?php
include('../connection/connection.php');
session_start();

// --- Parameters ---
$class_id   = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$semester   = isset($_GET['semester']) ? (int)$_GET['semester'] : 1;
$from       = $_GET['from'] ?? '';
$to         = $_GET['to'] ?? '';
$search     = trim($_GET['search'] ?? '');

if ($class_id <= 0) { echo "<div class='text-danger'>Invalid class ID</div>"; exit; }

// --- Class Info ---
$sqlInfo = "SELECT section_id, academic_year, instructor_id 
            FROM assign_instructor 
            WHERE hid=$class_id 
            LIMIT 1";
$classInfo = mysqli_fetch_assoc(mysqli_query($conn, $sqlInfo));
if (!$classInfo) { echo "<div class='text-danger'>Class not found</div>"; exit; }

$section_id    = (int)$classInfo['section_id'];
$academic_year = mysqli_real_escape_string($conn, $classInfo['academic_year']);
$instructor_id = (int)$classInfo['instructor_id'];

// --- Students ---
$sqlStudents = "SELECT u.sid, CONCAT(u.first_name,' ',u.father_name,' ',u.grand_father_name) AS full_name
                FROM assign_student ast
                LEFT JOIN students u ON ast.student_id=u.sid
                WHERE ast.section_id=$section_id";
if ($search !== '') {
    $escSearch = mysqli_real_escape_string($conn, $search);
    $sqlStudents .= " AND (u.first_name LIKE '%$escSearch%' 
                       OR u.father_name LIKE '%$escSearch%' 
                       OR u.grand_father_name LIKE '%$escSearch%')";
}
$sqlStudents .= " ORDER BY u.first_name ASC";
$res = mysqli_query($conn, $sqlStudents);

if (mysqli_num_rows($res) <= 0) {
    echo "<div class='text-danger'>No students found.</div>";
    exit;
}

// --- Date range ---
$days = [];
if ($from && $to) {
    try {
        $fromDate = new DateTime($from);
        $toDate   = new DateTime($to);
        if ($fromDate > $toDate) {
            $tmp = $fromDate; $fromDate = $toDate; $toDate = $tmp;
        }
        while ($fromDate <= $toDate) {
            $days[] = $fromDate->format("Y-m-d");
            $fromDate->modify("+1 day");
        }
    } catch (Exception $e) {
        // fallback
    }
}
if (empty($days)) {
    $today = new DateTime();
    $monday = (clone $today)->modify('monday this week');
    for ($i=0; $i<5; $i++) {
        $days[] = $monday->format("Y-m-d");
        $monday->modify("+1 day");
    }
}
?>

<div class='d-flex justify-content-between align-items-center mb-3'>
  <div>
    <label for='semesterSelect' class='fw-bold me-2'>Semester:</label>
    <select id='semesterSelect' class='form-select form-select-sm d-inline-block w-auto'>
      <option value='1' <?= ($semester==1?'selected':'') ?>>Semester 1</option>
      <option value='2' <?= ($semester==2?'selected':'') ?>>Semester 2</option>
    </select>
  </div>
</div>

<form id='attendanceForm' data-class-id='<?= (int)$class_id ?>'>
  <input type='hidden' name='section_id' value='<?= $section_id ?>'>
  <input type='hidden' name='academic_year' value='<?= htmlspecialchars($academic_year) ?>'>
  <input type='hidden' name='instructor_id' value='<?= $instructor_id ?>'>
  <input type='hidden' name='semester' value='<?= (int)$semester ?>'>

  <div class='table-responsive'>
    <table class='table table-striped table-hover text-center align-middle'>
      <thead class='table-secondary'>
        <tr>
          <th style='width:5%'>#</th>
          <th style='width:35%'>Full Name</th>
          <?php foreach ($days as $d): ?>
            <th class='text-nowrap'><?= date("D", strtotime($d)) ?><br><?= date("d M", strtotime($d)) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody id='studentTable'>
        <?php 
        $no = 1;
        mysqli_data_seek($res, 0);
        while ($s = mysqli_fetch_assoc($res)): 
          $sid = (int)$s['sid']; ?>
          <tr>
            <td><?= $no++ ?></td>
            <td class='text-start'><?= htmlspecialchars($s['full_name']) ?></td>
            <?php foreach ($days as $d):
              $dEsc = mysqli_real_escape_string($conn, $d);
              $q = "SELECT attend_id, status 
                    FROM attendance 
                    WHERE student_id=$sid 
                      AND section_id=$section_id
                      AND academic_year='$academic_year'
                      AND semester=$semester
                      AND attendance_date='$dEsc'
                    LIMIT 1";
              $check = mysqli_fetch_assoc(mysqli_query($conn, $q));
              $attendId = $check ? (int)$check['attend_id'] : 0;
              $currentStatus = $check ? htmlspecialchars($check['status']) : '';
            ?>
              <td>
                <input type="hidden" name="attendance[<?= $sid ?>][<?= $d ?>][attend_id]" value="<?= $attendId ?>">
                <select name='attendance[<?= $sid ?>][<?= $d ?>][status]' class='form-select form-select-sm'>
                  <option value=''  <?= ($currentStatus=='' ? 'selected' : '') ?>>--</option>
                  <option value='Present' <?= ($currentStatus=='Present' ? 'selected' : '') ?>>Present</option>
                  <option value='Absent'  <?= ($currentStatus=='Absent'  ? 'selected' : '') ?>>Absent</option>
                  <option value='Late'    <?= ($currentStatus=='Late'    ? 'selected' : '') ?>>Late</option>
                  <option value='Excused' <?= ($currentStatus=='Excused' ? 'selected' : '') ?>>Excused</option>
                </select>
              </td>
            <?php endforeach; ?>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class='text-end'>
    <button id='saveAttendanceBtn' type='button' class='btn btn-success px-4 me-2'>Update Attendance</button>
    <button id='closeAttendanceBtn' type='button' class='btn btn-secondary px-4' data-bs-dismiss='modal'>Close</button>
  </div>
</form>
