<?php
include('directorHeader.php');

// Fetch regions, zones, and woredas from the database
$regions = mysqli_query($conn, "SELECT id, name FROM regions ORDER BY name");
$zones = mysqli_query($conn, "SELECT id, name FROM zones ORDER BY name");
$woredas = mysqli_query($conn, "SELECT id, name FROM woredas ORDER BY name");

if (isset($_GET["sid"])) {
    $sid = basics($_GET["sid"]);
    $userProfile = getStudentSidByID($sid);
    if (!$userProfile) {
        echo "<p>No student found.</p>";
        include('footer.php');
        exit;
    }
} else {
    echo "<p>User ID not provided.</p>";
    include('footer.php');
    exit;
}

// Initialize error and success variables
$firstName_err = $fatherName_err = $gFatherName_err = $gender_err = $email_err = $phone_err = "";
$birth_place_err = $nationality_err = $region_err = $zone_err = $woreda_err = $kebele_err = "";
$mother_name_err = $father_contact_err = $mother_contact_err = $father_occupation_err = "";
$mother_occupation_err = $emergency_contact_name_err = $emergency_contact_phone_err = $dob_err = "";
$blood_group_err = $medical_condition_err = $other_condition_err = $disabilities_err = "";
$previous_school_err = $academic_status_err = $profile_pic_err = $documents_err = $allErr = $success = "";
$test = true;

// Process form submission
if (isset($_POST["update"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    // First Name
    if (empty($_POST["first_name"])) {
        $firstName_err = "Please enter first name";
        $test = false;
    } else if (!validateName($_POST["first_name"])) {
        $firstName_err = "First name must contain only letters and spaces";
        $test = false;
    } else {
        $first_name = basics($_POST["first_name"]);
    }

    // Father Name
    if (empty($_POST["father_name"])) {
        $fatherName_err = "Please enter father name";
        $test = false;
    } else if (!validateName($_POST["father_name"])) {
        $fatherName_err = "Father name must contain only letters and spaces";
        $test = false;
    } else {
        $father_name = basics($_POST["father_name"]);
    }

    // Grand Father Name
    if (empty($_POST["grand_father_name"])) {
        $gFatherName_err = "Please enter grand father name";
        $test = false;
    } else if (!validateName($_POST["grand_father_name"])) {
        $gFatherName_err = "Grand father name must contain only letters and spaces";
        $test = false;
    } else {
        $grand_father_name = basics($_POST["grand_father_name"]);
    }

    // Gender
    if (empty($_POST["gender"])) {
        $gender_err = "Please select gender";
        $test = false;
    } else if (!validateGender($_POST["gender"])) {
        $gender_err = "Invalid gender selection";
        $test = false;
    } else {
        $gender = basics($_POST["gender"]);
    }

    // Date of Birth
    if (empty($_POST["dob"])) {
        $dob_err = "Please enter date of birth";
        $test = false;
    } else if (!checkDateOfBirth($_POST["dob"])) {
        $dob_err = "Please enter a valid date (YYYY-MM-DD)";
        $test = false;
    } else {
        $dob = basics($_POST["dob"]);
    }

    // Email
    if (empty($_POST["email"])) {
        $email_err = "Please enter email";
        $test = false;
    } else if (!validateEmail($_POST["email"])) {
        $email_err = "Please enter a valid email address";
        $test = false;
    } else {
        $email = basics($_POST["email"]);
    }

    // Phone
    if (empty($_POST["phone"])) {
        $phone_err = "Please enter phone number";
        $test = false;
    } else if (!validatePhoneNumber($_POST["phone"])) {
        $phone_err = "Please enter valid phone number";
        $test = false;
    } else {
        $phone = basics($_POST["phone"]);
    }

    // Place of Birth
    if (empty($_POST["birth_place"])) {
        $birth_place_err = "Please enter place of birth";
        $test = false;
    } else if (!validateName($_POST["birth_place"])) {
        $birth_place_err = "Place of birth must contain only letters and spaces";
        $test = false;
    } else {
        $birth_place = basics($_POST["birth_place"]);
    }

    // Nationality
    if (empty($_POST["nationality"])) {
        $nationality_err = "Please enter nationality";
        $test = false;
    } else if (!validateName($_POST["nationality"])) {
        $nationality_err = "Nationality must contain only letters and spaces";
        $test = false;
    } else {
        $nationality = basics($_POST["nationality"]);
    }

    // Region
    if (empty($_POST["region"])) {
        $region_err = "Please select region";
        $test = false;
    } else if (!validateName($_POST["region"])) {
        $region_err = "Region must contain only letters and spaces";
        $test = false;
    } else {
        $region = basics($_POST["region"]);
    }

    // Zone
    if (empty($_POST["zone"])) {
        $zone_err = "Please select zone";
        $test = false;
    } else if (!validateName($_POST["zone"])) {
        $zone_err = "Zone must contain only letters and spaces";
        $test = false;
    } else {
        $zone = basics($_POST["zone"]);
    }

    // Woreda
    if (empty($_POST["woreda"])) {
        $woreda_err = "Please select woreda";
        $test = false;
    } else if (!validateName($_POST["woreda"])) {
        $woreda_err = "Woreda must contain only letters and spaces";
        $test = false;
    } else {
        $woreda = basics($_POST["woreda"]);
    }

    // Kebele
    if (empty($_POST["kebele"])) {
        $kebele_err = "Please enter kebele";
        $test = false;
    } else if (!validateName($_POST["kebele"])) {
        $kebele_err = "Kebele must contain only letters and spaces";
        $test = false;
    } else {
        $kebele = basics($_POST["kebele"]);
    }

    // Mother Name
    if (empty($_POST["mother_name"])) {
        $mother_name_err = "Please enter mother name";
        $test = false;
    } else if (!validateName($_POST["mother_name"])) {
        $mother_name_err = "Mother name must contain only letters and spaces";
        $test = false;
    } else {
        $mother_name = basics($_POST["mother_name"]);
    }

    // Father Contact
    if (empty($_POST["father_contact"])) {
        $father_contact_err = "Please enter father contact";
        $test = false;
    } else if (!validatePhoneNumber($_POST["father_contact"])) {
        $father_contact_err = "Please enter valid father contact number";
        $test = false;
    } else {
        $father_contact = basics($_POST["father_contact"]);
    }

    // Mother Contact
    if (empty($_POST["mother_contact"])) {
        $mother_contact_err = "Please enter mother contact";
        $test = false;
    } else if (!validatePhoneNumber($_POST["mother_contact"])) {
        $mother_contact_err = "Please enter valid mother contact number";
        $test = false;
    } else {
        $mother_contact = basics($_POST["mother_contact"]);
    }

    // Father Occupation
    if (empty($_POST["father_occupation"])) {
        $father_occupation_err = "Please enter father occupation";
        $test = false;
    } else if (!validateName($_POST["father_occupation"])) {
        $father_occupation_err = "Father occupation must contain only letters and spaces";
        $test = false;
    } else {
        $father_occupation = basics($_POST["father_occupation"]);
    }

    // Mother Occupation
    if (empty($_POST["mother_occupation"])) {
        $mother_occupation_err = "Please enter mother occupation";
        $test = false;
    } else if (!validateName($_POST["mother_occupation"])) {
        $mother_occupation_err = "Mother occupation must contain only letters and spaces";
        $test = false;
    } else {
        $mother_occupation = basics($_POST["mother_occupation"]);
    }

    // Emergency Contact Name
    if (empty($_POST["emergency_contact_name"])) {
        $emergency_contact_name_err = "Please enter emergency contact name";
        $test = false;
    } else if (!validateName($_POST["emergency_contact_name"])) {
        $emergency_contact_name_err = "Emergency contact name must contain only letters and spaces";
        $test = false;
    } else {
        $emergency_contact_name = basics($_POST["emergency_contact_name"]);
    }

    // Emergency Contact Phone
    if (empty($_POST["emergency_contact_phone"])) {
        $emergency_contact_phone_err = "Please enter emergency contact phone";
        $test = false;
    } else if (!validatePhoneNumber($_POST["emergency_contact_phone"])) {
        $emergency_contact_phone_err = "Please enter valid emergency contact phone";
        $test = false;
    } else {
        $emergency_contact_phone = basics($_POST["emergency_contact_phone"]);
    }

    // Blood Group
    if (empty($_POST["blood_group"])) {
        $blood_group_err = "Please enter blood group";
        $test = false;
    } else if (!validateBloodGroup($_POST["blood_group"])) {
        $blood_group_err = "Please enter valid blood group (e.g., A+, A-, B+, etc.)";
        $test = false;
    } else {
        $blood_group = basics($_POST["blood_group"]);
    }

    // Medical Condition
    if (empty($_POST["medical_condition"])) {
        $medical_condition_err = "Please enter medical condition";
        $test = false;
    } else {
        $medical_condition = basics($_POST["medical_condition"]);
    }

    // Other Condition
    if (!empty($_POST["other_condition"])) {
        $other_condition = basics($_POST["other_condition"]);
    } else {
        $other_condition = "";
    }

    // Disabilities
    if (empty($_POST["disabilities"])) {
        $disabilities_err = "Please enter disabilities (or 'None' if none)";
        $test = false;
    } else {
        $disabilities = basics($_POST["disabilities"]);
    }

    // Previous School
    if (empty($_POST["previous_school"])) {
        $previous_school_err = "Please enter previous school";
        $test = false;
    } else if (!validateName($_POST["previous_school"])) {
        $previous_school_err = "Previous school must contain only letters and spaces";
        $test = false;
    } else {
        $previous_school = basics($_POST["previous_school"]);
    }

    // Academic Status
    if (empty($_POST["academic_status"])) {
        $academic_status_err = "Please enter academic status";
        $test = false;
    } else {
        $academic_status = basics($_POST["academic_status"]);
    }

    // Validate profile picture
    if (!empty($_FILES["student_photo"]["name"])) {
        if ($_FILES["student_photo"]["error"] !== UPLOAD_ERR_OK) {
            $profile_pic_err = "Error uploading file. Error code: " . $_FILES["student_photo"]["error"];
            $test = false;
        } else if (validateProfilePicture($_FILES["student_photo"]) !== true) {
            $profile_pic_err = validateProfilePicture($_FILES["student_photo"]);
            $test = false;
        } else {
            $uploadDir = '../assets/img/';
            $uploadFile = $uploadDir . basename($_FILES["student_photo"]["name"]);
            if (move_uploaded_file($_FILES["student_photo"]["tmp_name"], $uploadFile)) {
                $student_photo = $uploadFile;
            } else {
                $profile_pic_err = "Failed to upload the profile picture.";
                $test = false;
            }
        }
    } else {
        $student_photo = $userProfile['student_photo'];
    }

    // Validate previous documents
    if (!empty($_FILES["previous_documents"]["name"])) {
        if ($_FILES["previous_documents"]["error"] !== UPLOAD_ERR_OK) {
            $documents_err = "Error uploading document. Error code: " . $_FILES["previous_documents"]["error"];
            $test = false;
        } else {
            $uploadDir = '../assets/documents/';
            $uploadFile = $uploadDir . basename($_FILES["previous_documents"]["name"]);
            if (move_uploaded_file($_FILES["previous_documents"]["tmp_name"], $uploadFile)) {
                $previous_documents = $uploadFile;
            } else {
                $documents_err = "Failed to upload the document.";
                $test = false;
            }
        }
    } else {
        $previous_documents = $userProfile['previous_documents'];
    }

    if ($test) {
        if (studentExist($sid)) {
            $stmt = mysqli_prepare($conn, "UPDATE students SET first_name=?, father_name=?, grand_father_name=?, gender=?, dob=?, email=?, phone=?, birth_place=?, nationality=?, region=?, zone=?, woreda=?, kebele=?, mother_name=?, father_contact=?, mother_contact=?, father_occupation=?, mother_occupation=?, emergency_contact_name=?, emergency_contact_phone=?, blood_group=?, medical_condition=?, other_condition=?, disabilities=?, previous_school=?, academic_status=?, student_photo=?, previous_documents=? WHERE sid=?");
            mysqli_stmt_bind_param($stmt, "sssssssssssssssssssssssssssss", 
                $first_name, $father_name, $grand_father_name, $gender, $dob, $email, $phone, 
                $birth_place, $nationality, $region, $zone, $woreda, $kebele, $mother_name, 
                $father_contact, $mother_contact, $father_occupation, $mother_occupation, 
                $emergency_contact_name, $emergency_contact_phone, $blood_group, 
                $medical_condition, $other_condition, $disabilities, $previous_school, 
                $academic_status, $student_photo, $previous_documents, $sid
            );

            if (mysqli_stmt_execute($stmt)) {
                $success = "Successfully updated";
                $Notif_msg = "Your account details have been updated.";
                $stmt_notif = mysqli_prepare($conn, "INSERT INTO notifications (sid, message) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt_notif, "ss", $sid, $Notif_msg);
                mysqli_stmt_execute($stmt_notif);
                mysqli_stmt_close($stmt_notif);
                header('refresh:2');
                $userProfile = getStudentSidByID($sid);
            } else {
                $allErr = "Something went wrong during update";
            }
            mysqli_stmt_close($stmt);
        } else {
            $allErr = "No student found with the given ID";
        }
    }
}
?>

<div class="container">
 <div class="page-inner">
   <div class="page-header">
     <h3 class="fw-bold mb-3">Update Student Profile</h3>
     <ul class="breadcrumbs mb-3">
       <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">Manage Student</a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">Update Profile</a></li>
     </ul>
   </div>

   <div class="main-content">
     <section class="section">
       <?php if ($success): ?>
         <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
       <?php endif; ?>
       <?php if ($allErr): ?>
         <div class="alert alert-danger"><?php echo htmlspecialchars($allErr); ?></div>
       <?php endif; ?>
       <form method="POST" enctype="multipart/form-data" name="update">
         <!-- Personal Information -->
         <div class="card mb-3">
           <div class="card-header bg-primary text-white" data-bs-toggle="collapse" href="#personalInfo" style="cursor:pointer;">
             <h4 class="mb-0">
               Personal Information
               <span class="float-end collapse-arrow">&#9660;</span>
             </h4>
           </div>
           <div class="collapse show" id="personalInfo">
             <div class="card-body">
               <div class="row">
                 <div class="col-lg-4 mb-4 text-center">
                   <img src="<?php echo !empty($userProfile['student_photo']) ? htmlspecialchars($userProfile['student_photo']) : '../assets/img/no.png'; ?>" 
                        alt="Profile Picture" width="120" height="120" class="mb-2">
                   <input type="file" name="student_photo" class="form-control" accept="image/*">
                   <?php if ($profile_pic_err): ?><div class="text-danger"><?php echo htmlspecialchars($profile_pic_err); ?></div><?php endif; ?>
                 </div>
                 <div class="col-lg-8">
                   <div class="row mb-3">
                     <div class="col-md-6">
                       <label class="form-label">Student ID</label>
                       <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['student_id']); ?>" disabled>
                     </div>
                     <div class="col-md-6">
                       <label class="form-label">First Name</label>
                       <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($userProfile['first_name']); ?>" required>
                       <?php if ($firstName_err): ?><div class="text-danger"><?php echo htmlspecialchars($firstName_err); ?></div><?php endif; ?>
                     </div>
                   </div>
                   <div class="row mb-3">
                     <div class="col-md-6">
                       <label class="form-label">Father Name</label>
                       <input type="text" class="form-control" name="father_name" value="<?php echo htmlspecialchars($userProfile['father_name']); ?>" required>
                       <?php if ($fatherName_err): ?><div class="text-danger"><?php echo htmlspecialchars($fatherName_err); ?></div><?php endif; ?>
                     </div>
                     <div class="col-md-6">
                       <label class="form-label">Grand Father Name</label>
                       <input type="text" class="form-control" name="grand_father_name" value="<?php echo htmlspecialchars($userProfile['grand_father_name']); ?>" required>
                       <?php if ($gFatherName_err): ?><div class="text-danger"><?php echo htmlspecialchars($gFatherName_err); ?></div><?php endif; ?>
                     </div>
                   </div>
                   <div class="row mb-3">
                     <div class="col-md-6">
                       <label class="form-label">Gender</label>
                       <select class="form-control" name="gender" required>
                         <option value="M" <?php echo $userProfile['gender'] == 'M' ? 'selected' : ''; ?>>Male</option>
                         <option value="F" <?php echo $userProfile['gender'] == 'F' ? 'selected' : ''; ?>>Female</option>
                       </select>
                       <?php if ($gender_err): ?><div class="text-danger"><?php echo htmlspecialchars($gender_err); ?></div><?php endif; ?>
                     </div>
                     <div class="col-md-6">
                       <label class="form-label">Date of Birth</label>
                       <input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($userProfile['dob']); ?>" required>
                       <?php if ($dob_err): ?><div class="text-danger"><?php echo htmlspecialchars($dob_err); ?></div><?php endif; ?>
                     </div>
                   </div>
                   <div class="row mb-3">
                     <div class="col-md-6">
                       <label class="form-label">Email</label>
                       <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($userProfile['email']); ?>" required>
                       <?php if ($email_err): ?><div class="text-danger"><?php echo htmlspecialchars($email_err); ?></div><?php endif; ?>
                     </div>
                     <div class="col-md-6">
                       <label class="form-label">Phone</label>
                       <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($userProfile['phone']); ?>" required>
                       <?php if ($phone_err): ?><div class="text-danger"><?php echo htmlspecialchars($phone_err); ?></div><?php endif; ?>
                     </div>
                   </div>
                   <div class="row mb-3">
                     <div class="col-md-6">
                       <label class="form-label">Place of Birth</label>
                       <input type="text" class="form-control" name="birth_place" value="<?php echo htmlspecialchars($userProfile['birth_place']); ?>" required>
                       <?php if ($birth_place_err): ?><div class="text-danger"><?php echo htmlspecialchars($birth_place_err); ?></div><?php endif; ?>
                     </div>
                     <div class="col-md-6">
                       <label class="form-label">Nationality</label>
                       <input type="text" class="form-control" name="nationality" value="<?php echo htmlspecialchars($userProfile['nationality']); ?>" required>
                       <?php if ($nationality_err): ?><div class="text-danger"><?php echo htmlspecialchars($nationality_err); ?></div><?php endif; ?>
                     </div>
                   </div>
                   <div class="row mb-3">
                     <div class="col-md-6">
                       <label class="form-label">Region</label>
                       <select class="form-control" name="region" required>
                         <option value="">Select Region</option>
                         <?php 
                         mysqli_data_seek($regions, 0); // Reset regions cursor
                         while ($row = mysqli_fetch_assoc($regions)): ?>
                           <option value="<?php echo htmlspecialchars($row['name']); ?>" <?php echo $userProfile['region'] == $row['name'] ? 'selected' : ''; ?>>
                             <?php echo htmlspecialchars($row['name']); ?>
                           </option>
                         <?php endwhile; ?>
                       </select>
                       <?php if ($region_err): ?><div class="text-danger"><?php echo htmlspecialchars($region_err); ?></div><?php endif; ?>
                     </div>
                     <div class="col-md-6">
                       <label class="form-label">Zone</label>
                       <select class="form-control" name="zone" required>
                         <option value="">Select Zone</option>
                         <?php 
                         mysqli_data_seek($zones, 0); // Reset zones cursor
                         while ($row = mysqli_fetch_assoc($zones)): ?>
                           <option value="<?php echo htmlspecialchars($row['name']); ?>" <?php echo $userProfile['zone'] == $row['name'] ? 'selected' : ''; ?>>
                             <?php echo htmlspecialchars($row['name']); ?>
                           </option>
                         <?php endwhile; ?>
                       </select>
                       <?php if ($zone_err): ?><div class="text-danger"><?php echo htmlspecialchars($zone_err); ?></div><?php endif; ?>
                     </div>
                   </div>
                   <div class="row mb-3">
                     <div class="col-md-6">
                       <label class="form-label">Woreda</label>
                       <select class="form-control" name="woreda" required>
                         <option value="">Select Woreda</option>
                         <?php 
                         mysqli_data_seek($woredas, 0); // Reset woredas cursor
                         while ($row = mysqli_fetch_assoc($woredas)): ?>
                           <option value="<?php echo htmlspecialchars($row['name']); ?>" <?php echo $userProfile['woreda'] == $row['name'] ? 'selected' : ''; ?>>
                             <?php echo htmlspecialchars($row['name']); ?>
                           </option>
                         <?php endwhile; ?>
                       </select>
                       <?php if ($woreda_err): ?><div class="text-danger"><?php echo htmlspecialchars($woreda_err); ?></div><?php endif; ?>
                     </div>
                     <div class="col-md-6">
                       <label class="form-label">Kebele</label>
                       <input type="text" class="form-control" name="kebele" value="<?php echo htmlspecialchars($userProfile['kebele']); ?>" required>
                       <?php if ($kebele_err): ?><div class="text-danger"><?php echo htmlspecialchars($kebele_err); ?></div><?php endif; ?>
                     </div>
                   </div>
                   <div class="row mb-3">
                     <div class="col-md-6">
                       <label class="form-label">Username</label>
                       <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['username']); ?>" disabled>
                     </div>
                   </div>
                 </div>
               </div>
             </div>
           </div>
         </div>

         <!-- Parent / Guardian Information -->
         <div class="card mb-3">
           <div class="card-header bg-primary text-white" data-bs-toggle="collapse" href="#parentInfo" style="cursor:pointer;">
             <h4 class="mb-0">
               Parent / Guardian Information
               <span class="float-end collapse-arrow">&#9660;</span>
             </h4>
           </div>
           <div class="collapse" id="parentInfo">
             <div class="card-body">
               <div class="row mb-3">
                 <div class="col-md-6">
                   <label class="form-label">Mother’s Full Name</label>
                   <input type="text" class="form-control" name="mother_name" value="<?php echo htmlspecialchars($userProfile['mother_name']); ?>" required>
                   <?php if ($mother_name_err): ?><div class="text-danger"><?php echo htmlspecialchars($mother_name_err); ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Father’s Contact</label>
                   <input type="tel" class="form-control" name="father_contact" value="<?php echo htmlspecialchars($userProfile['father_contact']); ?>" required>
                   <?php if ($father_contact_err): ?><div class="text-danger"><?php echo htmlspecialchars($father_contact_err); ?></div><?php endif; ?>
                 </div>
               </div>
               <div class="row mb-3">
                 <div class="col-md-6">
                   <label class="form-label">Mother’s Contact</label>
                   <input type="tel" class="form-control" name="mother_contact" value="<?php echo htmlspecialchars($userProfile['mother_contact']); ?>" required>
                   <?php if ($mother_contact_err): ?><div class="text-danger"><?php echo htmlspecialchars($mother_contact_err); ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Father’s Occupation</label>
                   <input type="text" class="form-control" name="father_occupation" value="<?php echo htmlspecialchars($userProfile['father_occupation']); ?>" required>
                   <?php if ($father_occupation_err): ?><div class="text-danger"><?php echo htmlspecialchars($father_occupation_err); ?></div><?php endif; ?>
                 </div>
               </div>
               <div class="row mb-3">
                 <div class="col-md-6">
                   <label class="form-label">Mother’s Occupation</label>
                   <input type="text" class="form-control" name="mother_occupation" value="<?php echo htmlspecialchars($userProfile['mother_occupation']); ?>" required>
                   <?php if ($mother_occupation_err): ?><div class="text-danger"><?php echo htmlspecialchars($mother_occupation_err); ?></div><?php endif; ?>
                 </div>
               </div>
             </div>
           </div>
         </div>

         <!-- Emergency Information -->
         <div class="card mb-3">
           <div class="card-header bg-warning text-white" data-bs-toggle="collapse" href="#emergencyInfo" style="cursor:pointer;">
             <h4 class="mb-0">
               Emergency Information
               <span class="float-end collapse-arrow">&#9660;</span>
             </h4>
           </div>
           <div class="collapse" id="emergencyInfo">
             <div class="card-body">
               <div class="row mb-3">
                 <div class="col-md-6">
                   <label class="form-label">Contact Name</label>
                   <input type="text" class="form-control" name="emergency_contact_name" value="<?php echo htmlspecialchars($userProfile['emergency_contact_name']); ?>" required>
                   <?php if ($emergency_contact_name_err): ?><div class="text-danger"><?php echo htmlspecialchars($emergency_contact_name_err); ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Contact Phone</label>
                   <input type="tel" class="form-control" name="emergency_contact_phone" value="<?php echo htmlspecialchars($userProfile['emergency_contact_phone']); ?>" required>
                   <?php if ($emergency_contact_phone_err): ?><div class="text-danger"><?php echo htmlspecialchars($emergency_contact_phone_err); ?></div><?php endif; ?>
                 </div>
               </div>
             </div>
           </div>
         </div>

         <!-- Health Information -->
         <div class="card mb-3">
           <div class="card-header bg-danger text-white" data-bs-toggle="collapse" href="#healthInfo" style="cursor:pointer;">
             <h4 class="mb-0">
               Health Information
               <span class="float-end collapse-arrow">&#9660;</span>
             </h4>
           </div>
           <div class="collapse" id="healthInfo">
             <div class="card-body">
               <div class="row mb-3">
                 <div class="col-md-6">
                   <label class="form-label">Blood Group</label>
                   <input type="text" class="form-control" name="blood_group" value="<?php echo htmlspecialchars($userProfile['blood_group']); ?>" required>
                   <?php if ($blood_group_err): ?><div class="text-danger"><?php echo htmlspecialchars($blood_group_err); ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Medical Condition</label>
                   <input type="text" class="form-control" name="medical_condition" value="<?php echo htmlspecialchars($userProfile['medical_condition']); ?>" required>
                   <?php if ($medical_condition_err): ?><div class="text-danger"><?php echo htmlspecialchars($medical_condition_err); ?></div><?php endif; ?>
                 </div>
               </div>
               <div class="row mb-3">
                 <div class="col-md-6">
                   <label class="form-label">Other Condition</label>
                   <input type="text" class="form-control" name="other_condition" value="<?php echo htmlspecialchars($userProfile['other_condition']); ?>">
                   <?php if ($other_condition_err): ?><div class="text-danger"><?php echo htmlspecialchars($other_condition_err); ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Disabilities</label>
                   <input type="text" class="form-control" name="disabilities" value="<?php echo htmlspecialchars($userProfile['disabilities']); ?>" required>
                   <?php if ($disabilities_err): ?><div class="text-danger"><?php echo htmlspecialchars($disabilities_err); ?></div><?php endif; ?>
                 </div>
               </div>
             </div>
           </div>
         </div>

         <!-- Academic Information -->
         <div class="card mb-3">
           <div class="card-header bg-success text-white" data-bs-toggle="collapse" href="#academicInfo" style="cursor:pointer;">
             <h4 class="mb-0">
               Academic Information
               <span class="float-end collapse-arrow">&#9660;</span>
             </h4>
           </div>
           <div class="collapse" id="academicInfo">
             <div class="card-body">
               <div class="row mb-3">
                 <div class="col-md-6">
                   <label class="form-label">Previous School</label>
                   <input type="text" class="form-control" name="previous_school" value="<?php echo htmlspecialchars($userProfile['previous_school']); ?>" required>
                   <?php if ($previous_school_err): ?><div class="text-danger"><?php echo htmlspecialchars($previous_school_err); ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Academic Status</label>
                   <input type="text" class="form-control" name="academic_status" value="<?php echo htmlspecialchars($userProfile['academic_status']); ?>" required>
                   <?php if ($academic_status_err): ?><div class="text-danger"><?php echo htmlspecialchars($academic_status_err); ?></div><?php endif; ?>
                 </div>
               </div>
               <div class="row mb-3">
                 <div class="col-md-12">
                   <label class="form-label">Previous Documents</label>
                   <?php if (!empty($userProfile['previous_documents'])): ?>
                     <a href="<?php echo htmlspecialchars($userProfile['previous_documents']); ?>" target="_blank" class="form-control d-block mb-2">View Current Document</a>
                   <?php endif; ?>
                   <input type="file" name="previous_documents" class="form-control" accept=".pdf,.doc,.docx">
                   <?php if ($documents_err): ?><div class="text-danger"><?php echo htmlspecialchars($documents_err); ?></div><?php endif; ?>
                 </div>
               </div>
             </div>
           </div>
         </div>

         <!-- Submit and Cancel Buttons -->
         <div class="text-center mt-4">
           <button type="submit" name="update" class="btn btn-primary">Update Student</button>
           <a href="view_studentForUpdate.php" class="btn btn-secondary">Cancel</a>
         </div>
       </form>
     </section>
   </div>
 </div>
</div>

<?php include('../Admin/footer.php'); ?>