<?php
include('directorHeader.php');

$success = $allErr = "";
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);

if (!isset($_SESSION["uid"]) || $roleName != "Director") {
    echo "You are not authorized to view this page.";
    exit;
}
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sid'])) {
    $sid = mysqli_real_escape_string($conn, $_POST['sid']);
    
    // Fetch existing student data
    $studentQuery = mysqli_query($conn, "SELECT * FROM students WHERE sid = '$sid'");
    $studentData = mysqli_fetch_assoc($studentQuery);

    if (!$studentData) {
        $allErr = "Student with ID $sid not found.";
    } else {
        // Collect and sanitize form inputs
        $student_id = mysqli_real_escape_string($conn, $_POST['student_id'] ?? $studentData['student_id']);
        $firstName = mysqli_real_escape_string($conn, $_POST['first_name'] ?? $studentData['first_name']);
        $fatherName = mysqli_real_escape_string($conn, $_POST['father_name'] ?? $studentData['father_name']);
        $gFatherName = mysqli_real_escape_string($conn, $_POST['grandfather_name'] ?? $studentData['grandfather_name']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender'] ?? $studentData['gender']);
        $email = mysqli_real_escape_string($conn, $_POST['email'] ?? $studentData['email']);
        $nationality = mysqli_real_escape_string($conn, $_POST['nationality'] ?? $studentData['nationality']);
        $region = mysqli_real_escape_string($conn, $_POST['region'] ?? $studentData['region']);
        $zone = mysqli_real_escape_string($conn, $_POST['zone'] ?? $studentData['zone']);
        $woreda = mysqli_real_escape_string($conn, $_POST['woreda'] ?? $studentData['woreda']);
        $kebele = mysqli_real_escape_string($conn, $_POST['kebele'] ?? $studentData['kebele']);
        $dob = mysqli_real_escape_string($conn, $_POST['date_of_birth'] ?? $studentData['date_of_birth']);
        $birth_place = mysqli_real_escape_string($conn, $_POST['birth_place'] ?? $studentData['birth_place']);
        $emergency_contact_name = mysqli_real_escape_string($conn, $_POST['emergency_contact_name'] ?? $studentData['emergency_contact_name']);
        $emergency_contact_phone = mysqli_real_escape_string($conn, $_POST['emergency_contact_phone'] ?? $studentData['emergency_contact_phone']);
        $username = mysqli_real_escape_string($conn, $_POST['username'] ?? $studentData['username']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? $studentData['phone']);
        $father_full_name = mysqli_real_escape_string($conn, $_POST['father_full_name'] ?? $studentData['father_full_name']);
        $mother_name = mysqli_real_escape_string($conn, $_POST['mother_name'] ?? $studentData['mother_name']);
        $father_contact = mysqli_real_escape_string($conn, $_POST['father_contact'] ?? $studentData['father_contact']);
        $mother_contact = mysqli_real_escape_string($conn, $_POST['mother_contact'] ?? $studentData['mother_contact']);
        $father_occupation = mysqli_real_escape_string($conn, $_POST['father_occupation'] ?? $studentData['father_occupation']);
        $mother_occupation = mysqli_real_escape_string($conn, $_POST['mother_occupation'] ?? $studentData['mother_occupation']);
        $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group'] ?? $studentData['blood_group']);
        $medical_condition = mysqli_real_escape_string($conn, $_POST['medical_condition'] ?? $studentData['medical_condition']);
        $other_condition = mysqli_real_escape_string($conn, $_POST['other_condition'] ?? $studentData['other_condition']);
        $disabilities = mysqli_real_escape_string($conn, $_POST['disabilities'] ?? $studentData['disabilities']);
        $previous_school = mysqli_real_escape_string($conn, $_POST['previous_school'] ?? $studentData['previous_school']);
        
        // Basic validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
            $allErr = "Invalid email format.";
        }
     // Validate phone number (+251XXXXXXXXX)
elseif (!preg_match("/^\+251\d{9}$/", $phone) && !empty($phone)) {
    $allErr = "Phone number must start with +251 and be followed by 9 digits.";
} elseif (!preg_match("/^\+251\d{9}$/", $emergency_contact_phone) && !empty($emergency_contact_phone)) {
    $allErr = "Emergency contact phone must start with +251 and be followed by 9 digits.";
}

         else {
            // Handle file uploads
            $student_photo = $studentData['profile_picture'];
            if (isset($_FILES['student_photo']) && $_FILES['student_photo']['error'] == UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($_FILES['student_photo']['type'], $allowed_types) && $_FILES['student_photo']['size'] <= 2 * 1024 * 1024) {
                    $upload_dir = 'Uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $student_photo = $upload_dir . basename($_FILES['student_photo']['name']);
                    move_uploaded_file($_FILES['student_photo']['tmp_name'], $student_photo);
                } else {
                    $allErr = "Invalid photo format or size (max 2MB, JPEG/PNG/GIF only).";
                }
            }

            $previous_documents = $studentData['previous_documents'];
            if (isset($_FILES['previous_documents']) && $_FILES['previous_documents']['error'] == UPLOAD_ERR_OK) {
                $allowed_types = ['application/pdf'];
                if (in_array($_FILES['previous_documents']['type'], $allowed_types) && $_FILES['previous_documents']['size'] <= 5 * 1024 * 1024) {
                    $upload_dir = 'Uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $previous_documents = $upload_dir . basename($_FILES['previous_documents']['name']);
                    move_uploaded_file($_FILES['previous_documents']['tmp_name'], $previous_documents);
                } else {
                    $allErr = "Invalid document format or size (max 5MB, PDF only).";
                }
            }

            if (empty($allErr)) {
                // Handle password
                $encryptedPassword = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $studentData['password'];

                // Update student within a transaction
                mysqli_begin_transaction($conn);
                try {
                    $result = updateStudent(
                        $sid, $student_id, $student_photo, $firstName, $fatherName, $gFatherName, $gender,
                        $email, $nationality, $region, $zone, $woreda, $kebele, $dob, $birth_place,
                        $emergency_contact_name, $emergency_contact_phone, $username, $encryptedPassword,
                        $phone, $father_full_name, $mother_name, $father_contact, $mother_contact,
                        $father_occupation, $mother_occupation, $blood_group, $medical_condition,
                        $other_condition, $disabilities, $previous_school, $previous_documents
                    );

                    if ($result === 1) {
                        mysqli_commit($conn);
                        $success = "Student (ID: $sid) updated successfully.";
                    } else {
                        throw new Exception($result);
                    }
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $allErr = "Failed to update student (ID: $sid). Error: " . $e->getMessage();
                }
            }
        }
    }
}

// Fetch student data for the form
$sid = isset($_GET['sid']) ? mysqli_real_escape_string($conn, $_GET['sid']) : '';
$studentData = null;
if ($sid) {
    $studentQuery = mysqli_query($conn, "SELECT * FROM students WHERE sid = '$sid'");
    $studentData = mysqli_fetch_assoc($studentQuery);
    if (!$studentData) {
        $allErr = "Student with ID $sid not found.";
    }
}
?>

<!-- CSS for profile image and form styling -->
<style>
  .profile-img {
    width: 30px; 
    height: 30px;
    border-radius: 50%; 
    object-fit: cover; 
  }
  .form-group {
    margin-bottom: 1rem;
  }
  .alert {
    margin-bottom: 1rem;
  }
</style>

<!-- Page Header -->
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Update Student</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Manage Student</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Update Student</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">Update Student Details</h4>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($success)) { ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                                <?php } ?>
                                <?php if (!empty($allErr)) { ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($allErr); ?></div>
                                <?php } ?>

                                <?php if ($studentData) { ?>
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="sid" value="<?php echo htmlspecialchars($sid); ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="student_id">Student ID</label>
                                                    <input type="text" name="student_id" class="form-control" value="<?php echo htmlspecialchars($studentData['student_id']); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="first_name">First Name</label>
                                                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($studentData['first_name']); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="father_name">Father Name</label>
                                                    <input type="text" name="father_name" class="form-control" value="<?php echo htmlspecialchars($studentData['father_name']); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="grandfather_name">Grandfather Name</label>
                                                    <input type="text" name="grandfather_name" class="form-control" value="<?php echo htmlspecialchars($studentData['grand_father_name']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="gender">Gender</label>
                                                    <select name="gender" class="form-control" required>
                                                        <option value="Male" <?php echo $studentData['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                                        <option value="Female" <?php echo $studentData['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($studentData['email']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="nationality">Nationality</label>
                                                    <input type="text" name="nationality" class="form-control" value="<?php echo htmlspecialchars($studentData['nationality']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="region">Region</label>
                                                    <input type="text" name="region" class="form-control" value="<?php echo htmlspecialchars($studentData['region']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="zone">Zone</label>
                                                    <input type="text" name="zone" class="form-control" value="<?php echo htmlspecialchars($studentData['zone']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="woreda">Woreda</label>
                                                    <input type="text" name="woreda" class="form-control" value="<?php echo htmlspecialchars($studentData['woreda']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="kebele">Kebele</label>
                                                    <input type="text" name="kebele" class="form-control" value="<?php echo htmlspecialchars($studentData['kebele']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="date_of_birth">Date of Birth</label>
                                                    <input type="date" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($studentData['dob']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="birth_place">Birth Place</label>
                                                    <input type="text" name="birth_place" class="form-control" value="<?php echo htmlspecialchars($studentData['birth_place']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="emergency_contact_name">Emergency Contact Name</label>
                                                    <input type="text" name="emergency_contact_name" class="form-control" value="<?php echo htmlspecialchars($studentData['emergency_contact_name']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="emergency_contact_phone">Emergency Contact Phone</label>
                                                    <input type="text" name="emergency_contact_phone" class="form-control" value="<?php echo htmlspecialchars($studentData['emergency_contact_phone']); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="username">Username</label>
                                                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($studentData['username']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="password">Password (leave blank to keep unchanged)</label>
                                                    <input type="password" name="password" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="phone">Phone</label>
                                                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($studentData['phone']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="father_full_name">Father Full Name</label>
                                                    <input type="text" name="father_full_name" class="form-control" value="<?php echo htmlspecialchars($studentData['father_full_name']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="mother_name">Mother Name</label>
                                                    <input type="text" name="mother_name" class="form-control" value="<?php echo htmlspecialchars($studentData['mother_name']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="father_contact">Father Contact</label>
                                                    <input type="text" name="father_contact" class="form-control" value="<?php echo htmlspecialchars($studentData['father_contact']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="mother_contact">Mother Contact</label>
                                                    <input type="text" name="mother_contact" class="form-control" value="<?php echo htmlspecialchars($studentData['mother_contact']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="father_occupation">Father Occupation</label>
                                                    <input type="text" name="father_occupation" class="form-control" value="<?php echo htmlspecialchars($studentData['father_occupation']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="mother_occupation">Mother Occupation</label>
                                                    <input type="text" name="mother_occupation" class="form-control" value="<?php echo htmlspecialchars($studentData['mother_occupation']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="blood_group">Blood Group</label>
                                                    <input type="text" name="blood_group" class="form-control" value="<?php echo htmlspecialchars($studentData['blood_group']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="medical_condition">Medical Condition</label>
                                                    <input type="text" name="medical_condition" class="form-control" value="<?php echo htmlspecialchars($studentData['medical_condition']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="other_condition">Other Condition</label>
                                                    <input type="text" name="other_condition" class="form-control" value="<?php echo htmlspecialchars($studentData['other_condition']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="disabilities">Disabilities</label>
                                                    <input type="text" name="disabilities" class="form-control" value="<?php echo htmlspecialchars($studentData['disabilities']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="previous_school">Previous School</label>
                                                    <input type="text" name="previous_school" class="form-control" value="<?php echo htmlspecialchars($studentData['previous_school']); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="student_photo">Student Photo (JPEG/PNG/GIF, max 2MB)</label>
                                                    <input type="file" name="student_photo" class="form-control">
                                                    <?php if ($studentData['student_photo']) { ?>
                                                        <img src="<?php echo htmlspecialchars($studentData['student_photo']); ?>" class="profile-img mt-2" alt="Current Photo">
                                                    <?php } ?>
                                                </div>
                                                <div class="form-group">
                                                    <label for="previous_documents">Previous Documents (PDF, max 5MB)</label>
                                                    <input type="file" name="previous_documents" class="form-control">
                                                    <?php if ($studentData['previous_documents']) { ?>
                                                        <a href="<?php echo htmlspecialchars($studentData['previous_documents']); ?>" target="_blank">View Current Document</a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update Student</button>
                                        <a href="view_studentForUpdate.php" class="btn btn-secondary">Cancel</a>
                                    </form>
                                <?php } else { ?>
                                    <div class="alert alert-warning">No student data available to update.</div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- SweetAlert for Success/Error Messages -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php if (!empty($success)) { ?>
Swal.fire({
    icon: 'success',
    title: 'Student Updated!',
    text: '<?php echo addslashes($success); ?>',
    confirmButtonColor: '#3085d6'
});
<?php } ?>
<?php if (!empty($allErr)) { ?>
Swal.fire({
    icon: 'error',
    title: 'Update Failed!',
    text: '<?php echo addslashes($allErr); ?>',
    confirmButtonColor: '#d33'
});
<?php } ?>
</script>

<?php include('../Admin/footer.php'); ?>