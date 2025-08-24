<?php
include('directorHeader.php');

// Check director role
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);
if (!isset($_SESSION["uid"]) || $roleName != "Director") {
    echo "You are not authorized to view this page.";
    exit;
}

// Set current academic year
$current_year = date("Y");

// Handle transfer form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['transfer_student'])) {
    $sid = intval($_POST['sid']);
    $new_section_id = intval($_POST['to_section']);

    if (!$new_section_id) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
        Swal.fire({
          title: 'Error!',
          text: 'Please select a valid class.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
        </script>";
        exit;
    } else {
        $student = getStudentCurrentSection($conn, $sid);

        if ($student && $student['current_section_id'] == $new_section_id) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
            Swal.fire({
              title: 'Error!',
              text: 'Student is already in the selected class.',
              icon: 'error',
              confirmButtonText: 'OK'
            });
            </script>";
            exit;
        } else {
            mysqli_begin_transaction($conn);
            try {
                transferStudent($conn, $sid, $student['current_section_id'], $new_section_id);
                mysqli_commit($conn);

                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                Swal.fire({
                  title: 'Success!',
                  text: 'Student transferred successfully.',
                  icon: 'success',
                  confirmButtonText: 'OK'
                }).then(() => {
                  window.location.href = window.location.href;
                });
                </script>";
                exit;

            } catch (Exception $e) {
                mysqli_rollback($conn);
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                Swal.fire({
                  title: 'Error!',
                  text: 'Failed to transfer student: " . addslashes($e->getMessage()) . "',
                  icon: 'error',
                  confirmButtonText: 'OK'
                });
                </script>";
                exit;
            }
        }
    }
}

// Fetch students with current section
$search = $_GET['search'] ?? '';
$students = fetchAssignedStudents($conn, $search);

// Fetch sections for transfer dropdown
$sections = mysqli_query($conn, "SELECT * FROM sections ORDER BY section_name ASC");
?>

<div class="container">
 <div class="page-inner">
   <div class="page-header">
     <h3 class="fw-bold mb-3">Transfer Student</h3>
     <ul class="breadcrumbs mb-3">
       <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">Manage Student</a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">Transfer Student</a></li>
     </ul>
  </div>
        <!-- Search Bar -->
        <div class="mb-3">
          <form method="GET">
    <div class="search-box">
  <div class="input-group">
    <span class="input-group-text bg-primary text-white">
      <i class="fas fa-search"></i>
    </span>
    <input type="text" name="search" id="userSearch" 
           class="form-control search-input"
           placeholder="Search by ID, Name, or Role...">
    <button class="btn btn-primary" type="button">
      Search
    </button>
        </div>

        <!-- Students Table -->
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle text-center" id="userTable">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Photo</th>
                            <th>First Name</th>
                            <th>Father Name</th>
                            <th>Current Class</th>
                            <th>View Details</th>
                            <th>Transfer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($students) > 0):
                            $no = 1;
                            while ($user = mysqli_fetch_assoc($students)):
                        ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= htmlspecialchars($user['student_id']) ?></td>
                            <td><img src="<?= $user['student_photo'] ?>" alt="Profile" class="profile-img"></td>
                            <td><?= htmlspecialchars($user['first_name']) ?></td>
                            <td><?= htmlspecialchars($user['father_name']) ?></td>
                            <td><?= htmlspecialchars($user['section_name'] .'-'. $user['class_type'] ?? 'Not Assigned') ?></td>
                            <td>
                                <a href="view_studentDetail.php?sid=<?= $user['sid'] ?>" class="btn btn-sm btn-info">
                                    <i class="fa fa-eye"></i> Details
                                </a>
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-warning transfer-btn"
                                   data-sid="<?= $user['sid'] ?>"
                                   data-student-id="<?= $user['student_id'] ?>"
                                   data-current-section="<?= htmlspecialchars($user['section_name'] .'-'. $user['class_type'] ?? 'Not Assigned') ?>">
                                   <i class="fa fa-exchange-alt"></i> Transfer
                                </a>
                            </td>
                        </tr>
                        <?php 
                            $no++;
                            endwhile;
                        else: ?>
                        <tr>
                            <td colspan="8" class="text-danger">No students found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="transferModalLabel">Transfer Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="sid" id="modal_sid">
            <div class="mb-3">
                <label>Student ID</label>
                <input type="text" id="modal_student_id" class="form-control" readonly>
            </div>
            <div class="mb-3">
                <label>Current Class</label>
                <input type="text" id="modal_current_section" class="form-control" readonly>
            </div>
            <div class="mb-3">
                <label>Transfer To</label>
                <select name="to_section" id="modal_to_section" class="form-control">
                    <option value="">-- Select Class --</option>
                    <?php
                    mysqli_data_seek($sections, 0);
                    while ($sec = mysqli_fetch_assoc($sections)):
                        echo "<option value='{$sec['cid']}'>" . $sec['section_name'] . " - " . $sec['class_type'] . "</option>";
                    endwhile;
                    ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" name="transfer_student" class="btn btn-primary">Transfer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JS: Transfer Modal -->
<script>
document.querySelectorAll('.transfer-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('modal_sid').value = this.dataset.sid;
        document.getElementById('modal_student_id').value = this.dataset.studentId;
        document.getElementById('modal_current_section').value = this.dataset.currentSection;

        var transferModal = new bootstrap.Modal(document.getElementById('transferModal'));
        transferModal.show();
    });
});
</script>

<!-- CSS: Profile Image -->
<style>
.profile-img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
</style>

<?php include('../Admin/footer.php'); ?>

