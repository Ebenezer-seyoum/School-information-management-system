<?php
include('directorHeader.php');// or directorHeader.php if director login

$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);

if (isset($_SESSION["uid"]) && ($roleName == "Director")) { // Director role

?>

<!-- CSS for profile image -->
<style>
  .profile-img {
    width: 30px; 
    height: 30px;
    border-radius: 50%; 
    object-fit: cover; 
  }
</style>

<!-- Page Header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Manage Students</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Students</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Delete Student</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <section class="section">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="row w-100 align-items-center">
                  <div class="col-12 col-md-6 mb-2 mb-md-0">
                    <h4 class="mb-0">View all students</h4>
                  </div>
                  <div class="col-12 col-md-6">
                    <form method="GET">
                      <div class="search-box">
                        <div class="input-group">
                          <span class="input-group-text bg-primary text-white">
                            <i class="fas fa-search"></i>
                          </span>
                          <input type="text" name="search" id="userSearch" class="form-control search-input" 
                                 style="font-weight: bold;" placeholder="Search by ID, Name, or Role..."
                                 value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                          <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

<?php
$success = $allErr = "";

// Delete student
if (isset($_GET["dsid"])) {
    $dsid = basics($_GET["dsid"]);
    $studentQuery = mysqli_query($conn, "SELECT sid, first_name, father_name FROM students WHERE sid = '$dsid'");
    $studentData = mysqli_fetch_assoc($studentQuery);

    if ($studentData) {
        $sidDisplay = $studentData['sid'];
        $studentName = $studentData['first_name'] . " " . $studentData['father_name'];

        // Check if student has linked records (marks, assignments, etc.)
        $checkQuery = mysqli_query($conn, "SELECT * FROM marks WHERE student_id = '$dsid' LIMIT 1");
        if (mysqli_num_rows($checkQuery) > 0) {
            $allErr = "Student ({$studentName}, ID: {$sidDisplay}) cannot be deleted because they have linked records.";
        } else {
            $deleteQuery = mysqli_query($conn, "DELETE FROM students WHERE sid = '$dsid'");
            if ($deleteQuery) {
                $success = "Student ({$studentName}, ID: {$sidDisplay}) has been deleted successfully.";
            } else {
                $allErr = "Unable to delete student ({$studentName}, ID: {$sidDisplay}).";
            }
        }
    }
}

// Fetch students for table
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = basics($_GET['search']);
    $studentsQuery = mysqli_query($conn, "SELECT * FROM students WHERE sid LIKE '%$searchTerm%' OR first_name LIKE '%$searchTerm%' OR father_name LIKE '%$searchTerm%' ORDER BY sid DESC");
} else {
    $studentsQuery = mysqli_query($conn, "SELECT * FROM students ORDER BY sid DESC");
}
?>

<div class="card-body">
  <div class="table-responsive">
    <table class="table table-hover align-middle text-center" style="border: 2px solid black; border-collapse: collapse; width: 100%; background-color: white;">
      <thead class="table-secondary">
        <tr>
          <th style="border: 2px solid black;">#</th>
          <th style="border: 2px solid black;">Student ID</th>
          <th style="border: 2px solid black;">First Name</th>
          <th style="border: 2px solid black;">Father Name</th>
          <th style="border: 2px solid black;">View Details</th>
          <th style="border: 2px solid black;">Actions</th>
        </tr>
      </thead>
      <tbody>
<?php
$no = 1;
if (mysqli_num_rows($studentsQuery) > 0) {
    while ($student = mysqli_fetch_assoc($studentsQuery)) {
?>
        <tr>
          <td style="border: 2px solid black;"><?php echo $no; ?></td>
          <td style="border: 2px solid black;"><?php echo $student['sid']; ?></td>
          <td style="border: 2px solid black;"><?php echo $student['first_name']; ?></td>
          <td style="border: 2px solid black;"><?php echo $student['father_name']; ?></td>
          <td style="border: 2px solid black;">
            <a href="delete_student.php?sid=<?= $student['sid']; ?>" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
          </td>
          <td style="border: 2px solid black;">
            <a href="#" class="btn btn-danger shadow btn-xs sharp" onclick="deleteStudent(<?php echo $student['sid']; ?>)">
              <i class="fa fa-trash fa-lg"></i>
            </a>
          </td>
        </tr>
<?php
        $no++;
    }
} else {
    echo '<tr><td colspan="6" class="text-center text-danger" style="border: 2px solid black;">No students found.</td></tr>';
}
?>
      </tbody>
    </table>
  </div>
</div>
</div>
</div>
</div>
</section>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteStudent(id) {
  Swal.fire({
    title: 'Are you sure?',
    text: "The student's data will be permanently removed.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "?dsid=" + id;
    }
  });
}

// Show SweetAlert popups based on PHP success/error
<?php if (!empty($success)) { ?>
Swal.fire({
  icon: 'success',
  title: 'Student Successfully Deleted!',
  text: '<?php echo addslashes($success); ?>',
  confirmButtonColor: '#3085d6'
});
<?php } ?>
<?php if (!empty($allErr)) { ?>
Swal.fire({
  icon: 'error',
  title: 'Unable to Delete Student!',
  text: '<?php echo addslashes($allErr); ?>',
  confirmButtonColor: '#d33'
});
<?php } ?>
</script>

<?php
} else {
    echo "You are not authorized to view this page.";
}



?>
<?php include('../Admin/footer.php'); ?>
