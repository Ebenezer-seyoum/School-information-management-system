<?php
include('directorHeader.php'); 

// Handle assignment form submission
if (isset($_POST['assign'])) {
    if (!empty($_POST['selected_students']) && !empty($_POST['section_id']) && !empty($_POST['academic_year'])) {
        $selected_students = $_POST['selected_students'];
        $section_id = $_POST['section_id'];
        $academic_year = $_POST['academic_year'];

        foreach ($selected_students as $student_id) {
            // Check if already assigned
            $check = mysqli_query($conn, "SELECT * FROM assign_student WHERE student_id='$student_id' AND section_id='$section_id' AND academic_year='$academic_year'");
            if (mysqli_num_rows($check) == 0) {
                $query = "INSERT INTO assign_student (student_id, section_id, academic_year) VALUES ('$student_id', '$section_id', '$academic_year')";
                if (!mysqli_query($conn, $query)) {
                    echo "Error assigning student ID $student_id: " . mysqli_error($conn) . "<br>";
                }
            }
        }

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
            title: 'Success!',
            text: 'Students successfully assigned to section.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => { window.location.href = window.location.href; });
        </script>";
        exit();
    } else {
        echo "Please select students, section, and academic year.";
    }
}

$sections = mysqli_query($conn, "SELECT * FROM sections ORDER BY section_name ASC");
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
       <h3 class="fw-bold mb-3">Assign Students to Class</h3>
    </div>

    <form method="post">
      <div class="row">
        <!-- Left column: students -->
        <div class="col-6">
          <div class="card">
            <div class="card-header">
              <div class="card-title">Students</div>
              <input type="text" id="studentSearch" class="form-control" placeholder="Search by Name or ID..." />
            </div>
            <div class="card-body scrollable-table">
              <table class="table table-hover align-middle text-center">
                <thead class="table-secondary">
                  <tr>
                    <th>#</th>
                    <th>Select</th>
                    <th>Student ID</th>
                    <th>Full Name</th>               
                  </tr>
                </thead>
                <tbody>
                <?php 
 $no = 1;
$students = getUnassignedStudents($conn);
if (is_array($students) || is_object($students)) {
    foreach ($students as $student): 
?>
                  <tr>
                    <td><?= $no ?></td>
                    <td><input type="checkbox" name="selected_students[]" value="<?= $student['sid'] ?>"></td>
                    <td><?= $student['student_id'] ?></td>
                    <td><?= $student['first_name'] . ' ' . $student['father_name'] ?></td>          
                  <?php $no++; endforeach; } else { ?>
                  <tr>
                      <td colspan="4">No students found or an error occurred.</td>
                  </tr>
                  <?php } ?>
                  <?php $no++; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- Right column: section & year selection -->
        <div class="col-6">
          <div class="card">
            <div class="card-header"><div class="card-title">Assign Class</div></div>
            <div class="card-body">
              <div class="mb-3">
                <label for="section_id" class="form-label">Select Section</label>
                <select name="section_id" id="section_id" class="form-control" >
                  <option value="">-- Select Section --</option>
                  <?php while ($section = mysqli_fetch_assoc($sections)): ?>
                    <option value="<?= $section['cid'] ?>"><?= $section['section_name'] . ' - ' . $section['class_type'] ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="academic_year" class="form-label">Academic Year</label>
                <input type="text" name="academic_year" id="academic_year" class="form-control" placeholder="e.g. 2012" required>
              </div>
              <button type="submit" name="assign" class="btn btn-primary">Assign Students</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<?php include('../admin/footer.php'); ?>
