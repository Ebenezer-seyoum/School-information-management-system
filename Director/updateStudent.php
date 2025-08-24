<?php
include('directorHeader.php');

// Fetch regions, zones, and woredas from the database
$regions = mysqli_query($conn, "SELECT id, name FROM regions ORDER BY name");
$zones = mysqli_query($conn, "SELECT id, name FROM zones ORDER BY name");
$woredas = mysqli_query($conn, "SELECT id, name FROM woredas ORDER BY name");

// Ensure defaults to avoid undefined variable warnings
// Status flags/messages
$success = $success ?? '';
$allErr = $allErr ?? '';
$test = true;

// Personal info errors
$firstName_err = $firstName_err ?? '';
$fatherName_err = $fatherName_err ?? '';
$gFatherName_err = $gFatherName_err ?? '';
$gender_err = $gender_err ?? '';
$dob_err = $dob_err ?? '';
$email_err = $email_err ?? '';
$phone_err = $phone_err ?? '';
$birth_place_err = $birth_place_err ?? '';
$nationality_err = $nationality_err ?? '';
$region_err = $region_err ?? '';
$zone_err = $zone_err ?? '';
$woreda_err = $woreda_err ?? '';
$kebele_err = $kebele_err ?? '';

// Account fields
$password_err = $password_err ?? '';
$confirmPassword_err = $confirmPassword_err ?? '';
$decrypted_password = $decrypted_password ?? '';

// Parent/guardian
$father_full_name_err = $father_full_name_err ?? '';
$mother_name_err = $mother_name_err ?? '';
$father_contact_err = $father_contact_err ?? '';
$mother_contact_err = $mother_contact_err ?? '';
$father_occupation_err = $father_occupation_err ?? '';
$mother_occupation_err = $mother_occupation_err ?? '';

// Emergency
$emergency_contact_name_err = $emergency_contact_name_err ?? '';
$emergency_contact_phone_err = $emergency_contact_phone_err ?? '';

// Health
$blood_group_err = $blood_group_err ?? '';
$medical_condition_err = $medical_condition_err ?? '';
$other_condition_err = $other_condition_err ?? '';
$disabilities_err = $disabilities_err ?? '';

// Academic/docs
$previous_school_err = $previous_school_err ?? '';
$profile_pic_err = $profile_pic_err ?? '';
$documents_err = $documents_err ?? '';

if (isset($_GET["sid"])) {
    $sid = basics($_GET["sid"]);
    $userProfile = getStudentSidByID($sid);
  // Decrypt current password to prefill the password input
  if ($userProfile && !empty($userProfile['password'])) {
    $decrypted_password = decryptPassword($userProfile['password']) ?: '';
  }
  
  // Process only on POST submit
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
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
    } else {
      $gender = basics($_POST["gender"]);
      if (!in_array($gender, ['M','F'], true)) {
        $gender_err = "Invalid gender selected";
        $test = false;
      }
    }

    // Date of Birth
    if (empty($_POST["dob"])) {
      $dob_err = "Please enter date of birth";
      $test = false;
    } else if (!checkDateOfBirth($_POST["dob"])) {
      $dob_err = "Please enter a valid date of birth";
      $test = false;
    } else {
      $dob = basics($_POST["dob"]);
    }
  // Email
  if (empty($_POST["email"])) {
    $email_err = "Please enter email";
    $test = false;
  } else if (!validateEmail($_POST["email"])) {
    $email_err = "Please enter valid email";
    $test = false;
  } else {
    $email = basics($_POST["email"]);
  }

  // Phone
  if (empty($_POST["phone"])) {
    $phone_err = "Please enter phone";
    $test = false;
  } else if (!validatePhoneNumber($_POST["phone"])) {
    $phone_err = "Please enter valid phone";
    $test = false;
  } else {
    $phone = basics($_POST["phone"]);
  }

  // Place of Birth
  if (empty($_POST["birth_place"])) {
    $birth_place_err = "Please enter place of birth";
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
  } else {
    $region = (int)$_POST["region"];
    if ($region <= 0) { $region_err = "Invalid region selected"; $test = false; }
  }

  // Zone
  if (empty($_POST["zone"])) {
    $zone_err = "Please select zone";
    $test = false;
  } else {
    $zone = (int)$_POST["zone"];
    if ($zone <= 0) { $zone_err = "Invalid zone selected"; $test = false; }
  }

  // Woreda
  if (empty($_POST["woreda"])) {
    $woreda_err = "Please select woreda";
    $test = false;
  } else {
    $woreda = (int)$_POST["woreda"];
    if ($woreda <= 0) { $woreda_err = "Invalid woreda selected"; $test = false; }
  }

  // Kebele
  if (empty($_POST["kebele"])) {
    $kebele_err = "Please enter kebele";
    $test = false;
  } else {
    $kebele = basics($_POST["kebele"]);
  }

  // Father's Full Name
  if (empty($_POST["father_full_name"])) {
    $father_full_name_err = "Please enter father's full name";
    $test = false;
  } else if (!validateName($_POST["father_full_name"])) {
    $father_full_name_err = "Father's full name must contain only letters and spaces";
    $test = false;
  } else {
    $father_full_name = basics($_POST["father_full_name"]);
  }

  // Mother Name
  if (empty($_POST["mother_name"])) {
    $mother_name_err = "Please enter mother's full name";
    $test = false;
  } else if (!validateName($_POST["mother_name"])) {
    $mother_name_err = "Mother's name must contain only letters and spaces";
    $test = false;
  } else {
    $mother_name = basics($_POST["mother_name"]);
  }

  // Father Contact
  if (empty($_POST["father_contact"])) {
    $father_contact_err = "Please enter father contact";
    $test = false;
  } else if (!validatePhoneNumber($_POST["father_contact"])) {
    $father_contact_err = "Please enter valid father contact";
    $test = false;
  } else {
    $father_contact = basics($_POST["father_contact"]);
  }

  // Mother Contact
  if (empty($_POST["mother_contact"])) {
    $mother_contact_err = "Please enter mother contact";
    $test = false;
  } else if (!validatePhoneNumber($_POST["mother_contact"])) {
    $mother_contact_err = "Please enter valid mother contact";
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

  if ($test === true) {
    if (studentExist($sid) == 1) {
      // Optional password update
      $encryptedPassword = '';
      if (!empty($_POST['password'])) {
        $encryptedPassword = encryptPassword($_POST['password']);
      }

      if (updateStudent(
        $sid,
        $student_photo,
        $first_name,
        $father_name,
        $grand_father_name,
        $gender,
        $email,
        $nationality,
        $region,
        $zone,
        $woreda,
        $kebele,
        $dob,
        $birth_place,
        $emergency_contact_name,
        $emergency_contact_phone,
        $userProfile['username'],
        $encryptedPassword,
  $phone,
  $father_full_name,
        $mother_name,
        $father_contact,
        $mother_contact,
        $father_occupation,
        $mother_occupation,
        $blood_group,
        $medical_condition,
        $other_condition,
        $disabilities,
        $previous_school,
        $previous_documents
      ) == 1) {
        $success = "Successfully updated";
        $Notif_msg = "your account detail updated.";
        $sql_Notif = "INSERT INTO notifications (sid, message) VALUES ('$sid', '$Notif_msg')";
        mysqli_query($conn, $sql_Notif);
      } else {
        $allErr = "Something went wrong";
      }
    } else {
      $allErr = "There is no user associated with the given information";
    }
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
       <li class="nav-item"><a href="#">Student Management</a></li>
       <li class="separator"><i class="icon-arrow-right"></i></li>
       <li class="nav-item"><a href="#">Update student</a></li>
     </ul>
   </div>

   <div class="main-content">
     <section class="section">
  <form method="POST" enctype="multipart/form-data" name="update" id="updateForm" class="swal-only-errors">
        <input type="hidden" name="update" value="1" />
         <!-- Personal Information -->
         <div class="card mb-3">
           <div class="card-header bg-primary text-white" data-bs-toggle="collapse" href="#personalInfo" style="cursor:pointer;">
             <h4 class="mb-0">
               Personal Information
               <span class="float-end collapse-arrow">&#9660;</span>
             </h4>
           </div>
           <div class="collapse" id="personalInfo">
             <div class="card-body">
               <div class="row">
                 <div class="col-lg-4 mb-4 text-center">
                   <label class="form-label text-primary">Photo<span class="text-danger">*</span></label>
                   <div class="avatar-upload">
                     <div class="avatar-preview mb-2">
                       <div class="user-img">
                         <img class="profile-images" src="<?php echo htmlspecialchars($userProfile['student_photo']); ?>" alt="Profile Picture" width="120" height="120">
                       </div>
                     </div>
                     <input type="file" class="form-control d-none" id="imageUpload" name="student_photo" accept="image/jpeg,image/png,image/gif">
                     <label for="imageUpload" class="btn btn-primary btn-sm mb-1">Choose File</label>
                     <button type="button" id="removeImage" class="btn btn-danger btn-sm ms-2">Remove</button><br>
                     <?php if ($profile_pic_err): ?><span class="text-danger"><?php echo htmlspecialchars($profile_pic_err); ?></span><?php endif; ?>
                   </div>
                 </div>
                 <div class="col-lg-8">
                   <!-- First Name + Father Name -->
                   <div class="row mb-3">
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">First Name</label>
                       <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($userProfile['first_name']); ?>" required>
                       <?php if ($firstName_err): ?><div class="text-danger"><?php echo $firstName_err; ?></div><?php endif; ?>
                     </div>
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Father Name</label>
                       <input type="text" class="form-control" name="father_name" value="<?php echo htmlspecialchars($userProfile['father_name']); ?>" required>
                       <?php if ($fatherName_err): ?><div class="text-danger"><?php echo $fatherName_err; ?></div><?php endif; ?>
                     </div>
                   </div>

                   <!-- Grand Father + Gender -->
                   <div class="row mb-3">
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Grand Father Name</label>
                       <input type="text" class="form-control" name="grand_father_name" value="<?php echo htmlspecialchars($userProfile['grand_father_name']); ?>" required>
                       <?php if ($gFatherName_err): ?><div class="text-danger"><?php echo $gFatherName_err; ?></div><?php endif; ?>
                     </div>
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Gender</label>
                       <select class="form-control" name="gender" required>
                         <option value="M" <?php echo $userProfile['gender'] == 'M' ? 'selected' : ''; ?>>Male</option>
                         <option value="F" <?php echo $userProfile['gender'] == 'F' ? 'selected' : ''; ?>>Female</option>
                       </select>
                       <?php if ($gender_err): ?><div class="text-danger"><?php echo $gender_err; ?></div><?php endif; ?>
                     </div>
                   </div>

                   <!-- DOB + Email -->
                   <div class="row mb-3">
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Date of Birth</label>
                       <input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($userProfile['dob']); ?>" required>
                       <?php if ($dob_err): ?><div class="text-danger"><?php echo $dob_err; ?></div><?php endif; ?>
                     </div>
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Email</label>
                       <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($userProfile['email']); ?>" required>
                       <?php if ($email_err): ?><div class="text-danger"><?php echo $email_err; ?></div><?php endif; ?>
                     </div>
                   </div>

                   <!-- Phone + Place of Birth -->
                   <div class="row mb-3">
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Phone</label>
                       <div class="input-group">
                         <span class="input-group-text">+251</span>
                         <input id="phone" type="tel" class="form-control" name="phone" placeholder="9XXXXXXXX" value="<?php echo htmlspecialchars($userProfile['phone']); ?>" required>
                       </div>
                       <small class="text-muted">Enter the number without country code; it will be saved with +251.</small>
                       <?php if ($phone_err): ?><div class="text-danger"><?php echo $phone_err; ?></div><?php endif; ?>
                     </div>
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Place of Birth</label>
                       <input type="text" class="form-control" name="birth_place" value="<?php echo htmlspecialchars($userProfile['birth_place']); ?>" required>
                       <?php if ($birth_place_err): ?><div class="text-danger"><?php echo $birth_place_err; ?></div><?php endif; ?>
                     </div>
                   </div>

                   <!-- Nationality + Region -->
                   <div class="row mb-3">
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Nationality</label>
                       <input type="text" class="form-control" name="nationality" value="<?php echo htmlspecialchars($userProfile['nationality']); ?>" required>
                       <?php if ($nationality_err): ?><div class="text-danger"><?php echo $nationality_err; ?></div><?php endif; ?>
                     </div>
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Region</label>
                       <select class="form-control" name="region" required>
                         <option value="">Select Region</option>
                         <?php while ($row = mysqli_fetch_assoc($regions)): ?>
                           <option value="<?php echo (int)$row['id']; ?>" <?php echo ((int)$userProfile['region'] === (int)$row['id']) ? 'selected' : ''; ?>>
                             <?php echo htmlspecialchars($row['name']); ?>
                           </option>
                         <?php endwhile; ?>
                       </select>
                       <?php if ($region_err): ?><div class="text-danger"><?php echo $region_err; ?></div><?php endif; ?>
                     </div>
                   </div>

                   <!-- Zone + Woreda -->
                   <div class="row mb-3">
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Zone</label>
                       <select class="form-control" name="zone" required>
                         <option value="">Select Zone</option>
                         <?php while ($row = mysqli_fetch_assoc($zones)): ?>
                           <option value="<?php echo (int)$row['id']; ?>" <?php echo ((int)$userProfile['zone'] === (int)$row['id']) ? 'selected' : ''; ?>>
                             <?php echo htmlspecialchars($row['name']); ?>
                           </option>
                         <?php endwhile; ?>
                       </select>
                       <?php if ($zone_err): ?><div class="text-danger"><?php echo $zone_err; ?></div><?php endif; ?>
                     </div>
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Woreda</label>
                       <select class="form-control" name="woreda" required>
                         <option value="">Select Woreda</option>
                         <?php while ($row = mysqli_fetch_assoc($woredas)): ?>
                           <option value="<?php echo (int)$row['id']; ?>" <?php echo ((int)$userProfile['woreda'] === (int)$row['id']) ? 'selected' : ''; ?>>
                             <?php echo htmlspecialchars($row['name']); ?>
                           </option>
                         <?php endwhile; ?>
                       </select>
                       <?php if ($woreda_err): ?><div class="text-danger"><?php echo $woreda_err; ?></div><?php endif; ?>
                     </div>
                   </div>

                   <!-- Kebele + Username -->
                   <div class="row mb-3">
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Kebele</label>
                       <input type="text" class="form-control" name="kebele" value="<?php echo htmlspecialchars($userProfile['kebele']); ?>" >
                       <?php if ($kebele_err): ?><div class="text-danger"><?php echo $kebele_err; ?></div><?php endif; ?>
                     </div>
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="form-label">Username</label>
                       <input type="text" class="form-control" value="<?php echo htmlspecialchars($userProfile['username']); ?>">
                     </div>
                   </div>

                   <!-- Password (prefilled) -->
                   <div class="row mb-3">
                     <div class="form-group col-12 col-md-6 mb-3">
                       <label class="d-block">Password <small class="text-muted">(leave blank to keep current)</small></label>
                       <div class="input-group">
                         <input type="password" id="upd_password" name="password" class="form-control" value="<?php echo htmlspecialchars($decrypted_password); ?>" />
                         <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#upd_password">Show</button>
                       </div>
                       <?php if ($password_err): ?><div class="text-danger"><?php echo $password_err; ?></div><?php endif; ?>
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
                   <label class="form-label">Father’s Full Name</label>
                   <input type="text" class="form-control" name="father_full_name" value="<?php echo htmlspecialchars($userProfile['father_full_name'] ?? ''); ?>" required>
                   <?php if (!empty($father_full_name_err)): ?><div class="text-danger"><?php echo $father_full_name_err; ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Mother’s Full Name</label>
                   <input type="text" class="form-control" name="mother_name" value="<?php echo htmlspecialchars($userProfile['mother_name']); ?>" required>
                   <?php if ($mother_name_err): ?><div class="text-danger"><?php echo $mother_name_err; ?></div><?php endif; ?>
                 </div>
               </div>
               <div class="row mb-3">
                 <div class="col-md-6">
                   <label class="form-label">Father’s Contact</label>
                   <div class="input-group">
                     <span class="input-group-text">+251</span>
                     <input id="father_contact" type="tel" class="form-control" name="father_contact" placeholder="9XXXXXXXX" value="<?php echo htmlspecialchars($userProfile['father_contact']); ?>" required>
                   </div>
                   <small class="text-muted">Enter the number without country code; it will be saved with +251.</small>
                   <?php if ($father_contact_err): ?><div class="text-danger"><?php echo $father_contact_err; ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Mother’s Contact</label>
                   <div class="input-group">
                     <span class="input-group-text">+251</span>
                     <input id="mother_contact" type="tel" class="form-control" name="mother_contact" placeholder="9XXXXXXXX" value="<?php echo htmlspecialchars($userProfile['mother_contact']); ?>" required>
                   </div>
                   <small class="text-muted">Enter the number without country code; it will be saved with +251.</small>
                   <?php if ($mother_contact_err): ?><div class="text-danger"><?php echo $mother_contact_err; ?></div><?php endif; ?>
                 </div>
               </div>
               <div class="row mb-3">
                 <div class="col-md-6">
                   <label class="form-label">Father’s Occupation</label>
                   <input type="text" class="form-control" name="father_occupation" value="<?php echo htmlspecialchars($userProfile['father_occupation']); ?>" required>
                   <?php if ($father_occupation_err): ?><div class="text-danger"><?php echo $father_occupation_err; ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Mother’s Occupation</label>
                   <input type="text" class="form-control" name="mother_occupation" value="<?php echo htmlspecialchars($userProfile['mother_occupation']); ?>" required>
                   <?php if ($mother_occupation_err): ?><div class="text-danger"><?php echo $mother_occupation_err; ?></div><?php endif; ?>
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
                   <?php if ($emergency_contact_name_err): ?><div class="text-danger"><?php echo $emergency_contact_name_err; ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Contact Phone</label>
                   <div class="input-group">
                     <span class="input-group-text">+251</span>
                     <input id="emergency_contact_phone" type="tel" class="form-control" name="emergency_contact_phone" placeholder="9XXXXXXXX" value="<?php echo htmlspecialchars($userProfile['emergency_contact_phone']); ?>" required>
                   </div>
                   <small class="text-muted">Enter the number without country code; it will be saved with +251.</small>
                   <?php if ($emergency_contact_phone_err): ?><div class="text-danger"><?php echo $emergency_contact_phone_err; ?></div><?php endif; ?>
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
                   <?php if ($blood_group_err): ?><div class="text-danger"><?php echo $blood_group_err; ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Medical Condition</label>
                   <input type="text" class="form-control" name="medical_condition" value="<?php echo htmlspecialchars($userProfile['medical_condition']); ?>" required>
                   <?php if ($medical_condition_err): ?><div class="text-danger"><?php echo $medical_condition_err; ?></div><?php endif; ?>
                 </div>
               </div>
               <div class="row mb-3">
                 <div class="col-md-6">
                   <label class="form-label">Other Condition</label>
                   <input type="text" class="form-control" name="other_condition" value="<?php echo htmlspecialchars($userProfile['other_condition']); ?>">
                   <?php if ($other_condition_err): ?><div class="text-danger"><?php echo $other_condition_err; ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Disabilities</label>
                   <input type="text" class="form-control" name="disabilities" value="<?php echo htmlspecialchars($userProfile['disabilities']); ?>" required>
                   <?php if ($disabilities_err): ?><div class="text-danger"><?php echo $disabilities_err; ?></div><?php endif; ?>
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
                   <?php if ($previous_school_err): ?><div class="text-danger"><?php echo $previous_school_err; ?></div><?php endif; ?>
                 </div>
                 <div class="col-md-6">
                   <label class="form-label">Previous Documents</label>
                   <?php if (!empty($userProfile['previous_documents'])): ?>
                     <a href="<?php echo htmlspecialchars($userProfile['previous_documents']); ?>" target="_blank" class="d-inline-block mb-2">View Current Document</a>
                   <?php endif; ?>
                   <input type="file" name="previous_documents" class="form-control" accept=".pdf,.doc,.docx">
                   <?php if ($documents_err): ?><div class="text-danger"><?php echo $documents_err; ?></div><?php endif; ?>
                 </div>
               </div>
             </div>
           </div>
         </div>

         <!-- Submit and Cancel Buttons -->
         <div class="text-center mt-4">
           <button type="submit" name="update" class="btn btn-primary btn-lg px-5 py-3 shadow-sm rounded-3">Update Student</button>
           <a href="view_studentForUpdate.php" class="btn btn-danger btn-lg px-5 py-3 shadow-sm rounded-3">Back</a>
         </div>
       </form>
     </section>
   </div>
 </div>
</div>

<?php include('../Admin/footer.php'); ?>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const successMsg = <?php echo json_encode($success ?? ''); ?>;
  const fieldErrors = <?php
    $errorMessages = [];
    foreach ([
      $firstName_err,$fatherName_err,$gFatherName_err,$gender_err,$email_err,$phone_err,$birth_place_err,$nationality_err,$region_err,
      $zone_err,$woreda_err,$kebele_err,$mother_name_err,$father_contact_err,$mother_contact_err,$father_occupation_err,$mother_occupation_err,$father_full_name_err,
      $emergency_contact_name_err,$emergency_contact_phone_err,$dob_err,$blood_group_err,$medical_condition_err,$other_condition_err,
      $disabilities_err,$previous_school_err,$profile_pic_err,$documents_err,$allErr
    ] as $err){ if(!empty($err)) $errorMessages[] = $err; }
    echo json_encode(array_values(array_unique($errorMessages)));
  ?>;

  // ✅ Show success
  if (successMsg) {
    Swal.fire({
      icon: 'success',
      title: 'Update Successful',
      text: successMsg,
      confirmButtonText: 'OK'
    }).then(() => { window.location.href = "view_studentForUpdate.php"; });
  } 
  // ❌ Show errors
  else if (fieldErrors && fieldErrors.length) {
    const listHtml = '<ul style="text-align:left;margin:0;padding-left:20px;">' + fieldErrors.map(e => '<li>'+e+'</li>').join('') + '</ul>';
    Swal.fire({
      icon: 'error',
      title: 'Please fix the following',
      html: listHtml,
      confirmButtonText: 'Got it'
    });
  }

  // ✅ Handle form submit
  const form = document.getElementById('updateForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();

      // normalize Ethiopian phone numbers
      ['phone','father_contact','mother_contact','emergency_contact_phone'].forEach(id => {
        const input = document.getElementById(id);
        if (input && input.value) {
          let val = input.value.trim();
          val = val.replace(/^\+?251/, ''); // remove existing +251 if present
          if (val.startsWith('0')) val = val.substring(1); // remove leading 0
          input.value = '+251' + val;
        }
      });

      Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to update this student’s profile?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  }

  // ✅ Remove image preview
  const removeBtn = document.getElementById('removeImage');
  const previewImg = document.getElementById('preview');
  const fileInput = document.getElementById('profile_pic');

  if (removeBtn && previewImg && fileInput) {
    removeBtn.addEventListener('click', function(){
      previewImg.src = ''; // clear preview
      fileInput.value = ''; // reset file input
      removeBtn.style.display = 'none';
    });
  }
});
</script>