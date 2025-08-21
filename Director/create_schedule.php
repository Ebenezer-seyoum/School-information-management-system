<?php
include('directorHeader.php');
if (getRoleNameById(getUserByID($_SESSION["uid"])["user_type"]) !== "Director") {
    echo "Not authorized."; exit;
}
include('../connection/connection.php');

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id   = $_POST['class_id'];
    $periods    = $_POST['period']; // array [day][period] => [subject,teacher]

    $inserted = 0;
    foreach ($periods as $day => $rows) {
        foreach ($rows as $time => $data) {
            if (empty($data['subject']) || empty($data['teacher'])) continue;
            
            $subject_id = $data['subject'];
            $teacher_id = $data['teacher'];
            [$start_time, $end_time] = explode("-", $time);

            $sql = "INSERT INTO timetable (class_id,subject_id,teacher_id,day_of_week,start_time,end_time) 
                    VALUES ('$class_id','$subject_id','$teacher_id','$day','$start_time','$end_time')";
            if (mysqli_query($conn,$sql)) $inserted++;
        }
    }

    if ($inserted > 0) $success = "$inserted schedule rows added.";
}
?>

<style>
/* Compact table styling */
.table-timetable {
  font-size: 13px;
}
.table-timetable select {
  font-size: 12px;
  padding: 2px;
  height: auto;
}
.table-timetable th, 
.table-timetable td {
  vertical-align: middle;
  padding: 6px;
}

/* Day background colors */
.day-monday    { background-color: #f8d7da; }  /* light red */
.day-tuesday   { background-color: #d1ecf1; }  /* light blue */
.day-wednesday { background-color: #d4edda; }  /* light green */
.day-thursday  { background-color: #fff3cd; }  /* light yellow */
.day-friday    { background-color: #e2e3e5; }  /* light gray */
</style>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Create Class Schedule</h3>
    </div>

    <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST">
      <div class="row mb-3">
        <div class="col-md-6">
          <label class="fw-bold">Grade/Class</label>
          <select name="class_id" class="form-control" required>
            <?php
            $res = mysqli_query($conn,"SELECT * FROM sections");
            while($r=mysqli_fetch_assoc($res)){
              echo "<option value='{$r['cid']}'>{$r['section_name']} ({$r['class_type']})</option>";
            }
            ?>
          </select>
        </div>
      </div>

      <table class="table table-bordered text-center table-timetable">
        <thead class="table-dark">
          <tr>
            <th>Time</th>
            <th class="day-monday">Monday</th>
            <th class="day-tuesday">Tuesday</th>
            <th class="day-wednesday">Wednesday</th>
            <th class="day-thursday">Thursday</th>
            <th class="day-friday">Friday</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $slots = [
            "08:00-08:45","08:45-09:30","09:30-10:15",
            "10:15-11:00","11:30-12:15","12:15-13:00","13:00-13:45"
          ];
          $days = ["Monday","Tuesday","Wednesday","Thursday","Friday"];

          // load dropdown data
          $subjects = mysqli_query($conn,"SELECT * FROM subjects");
          $subject_opts = "";
          while($s=mysqli_fetch_assoc($subjects)) {
            $subject_opts .= "<option value='{$s['suid']}'>{$s['subject_name']}</option>";
          }
          $teachers = mysqli_query($conn,"SELECT uid,first_name FROM users WHERE user_type=1");
          $teacher_opts = "";
          while($t=mysqli_fetch_assoc($teachers)) {
            $teacher_opts .= "<option value='{$t['uid']}'>{$t['first_name']}</option>";
          }

          foreach($slots as $slot){
            echo "<tr><td><b>$slot</b></td>";
            foreach($days as $day){
              $dayClass = "day-" . strtolower($day);
              echo "<td class='$dayClass'>
                <select name='period[$day][$slot][subject]' class='form-control mb-1'>
                  <option value=''>--Subject--</option>$subject_opts
                </select>
                <select name='period[$day][$slot][teacher]' class='form-control'>
                  <option value=''>--Teacher--</option>$teacher_opts
                </select>
              </td>";
            }
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>

      <button type="submit" class="btn btn-primary">Save Weekly Timetable</button>
    </form>
  </div>
</div>
