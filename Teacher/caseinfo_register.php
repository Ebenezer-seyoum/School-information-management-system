<?php
include('loHeader.php');
?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Register Litigant</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Manage Cases</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Register Form</a></li>
            </ul>
        </div>

<?php
// Initialize variables
$case_id = $first_name = $father_name = $gfather_name = $gender = $email = "";
$region = $zone = $woreda = $kebele = $wogen = $argument_money = "";
$judgement_money = $phone = $litigant_type = $file_pages = $file = "";
$case_id_err = $firstName_err = $fatherName_err = $gFatherName_err = $gender_err = $email_err = "";
$region_err = $zone_err = $woreda_err = $kebele_err = $wogen_err = $argumentMoney_err = "";
$judgementMoney_err = $phone_err = $litigantType_err = $file_pages_err = $file_err = "";
$success = $allErr = "";
$test = true;
$cid = null;

// Fetch case_id if provided
if (isset($_GET["case_id"])) {
    $case_id = $_GET["case_id"];
    $case = getCaseByCase_ID($case_id);
    if (isset($case['cid'])) {
        $cid = $case['cid'];
    }
}

// Handle form submission
if (isset($_POST["register"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    

// Validate first name
if (empty($_POST["first_name"])) {
    $firstName_err = "Please enter your first name";
    $test = false;
} else if (!validateName($_POST["first_name"])) {
    $firstName_err = "Please enter a valid first name";
    $test = false;
} else {
    $first_name = $_POST["first_name"];
}

// Validate father name
if (empty($_POST["father_name"])) {
    $fatherName_err = "Please enter your father name";
    $test = false;
} else if (!validateName($_POST["father_name"])) {
    $fatherName_err = "Please enter a valid father name";
    $test = false;
} else {
    $father_name = $_POST["father_name"];
}


// Validate grand father name
if (empty($_POST["gfather_name"])) {
    $gFatherName_err = "Please enter your grand father name";
    $test = false;
} else if (!validateName($_POST["gfather_name"])) {
    $gFatherName_err = "Please enter a valid grand father name";
    $test = false;
} else {
    $gfather_name = $_POST["gfather_name"];
}


  
// Validate gender
  
if (empty($_POST["gender"])) {
    $gender_err = "Please select your gender";
    $test = false;
} else if (!validateGender($_POST["gender"])) {
    $gender_err = "Invalid gender selected";
    $test = false;
} else {
    $gender = $_POST["gender"];
}

// Validate email
if (empty($_POST["email"])) {
    $email_err = "Please enter your email";
    $test = false;
} else {
    $email = trim($_POST["email"]);
    if (!validateEmail($email)) {
        $email_err = "Please enter a valid email address (example: user@domain.com)";
        $test = false;
    } else {
        $email = $_POST["email"];
    }
}

// Validate region
if (empty($_POST["region"])) {
    $region_err = "Please select your region";
    $test = false;
} else {
    $region = $_POST["region"];
}

// Validate zone
if (empty($_POST["zone"])) {
    $zone_err = "Please select your zone";
    $test = false;
} else {
    $zone = $_POST["zone"];
}

// Validate woreda
if (empty($_POST["woreda"])) {
    $woreda_err = "Please select your woreda";
    $test = false;
} else {
    $woreda = $_POST["woreda"];
}

// Validate kebele
if (empty($_POST["kebele"])) {
    $kebele_err = "Please enter your kebele";
    $test = false;
} else {
    $kebele = $_POST["kebele"];
}

// Validate wogen
if (empty($_POST["wogen"])) {
    $wogen_err = "Please enter your wogen";
    $test = false;
} else {
    $wogen = $_POST["wogen"];
}

// Validate argument money
if (empty($_POST["argument_money"])) {
    $argumentMoney_err = "Please enter argument money";
    $test = false;
} else if (!validateNumber($_POST["argument_money"])) {
    $argumentMoney_err = "Please enter a valid number";
    $test = false;
} else {
    $argument_money = $_POST["argument_money"];
}

// Validate judgement money
if (empty($_POST["judgement_money"])) {
    $judgementMoney_err = "Please enter judgment money";
    $test = false;
} else if (!validateNumber($_POST["judgement_money"])) {
    $judgementMoney_err = "Please enter a valid number";
    $test = false;
} else {
    $judgement_money = $_POST["judgement_money"];
}

// Validate phone
if (empty($_POST["phone"])) {
    $phone_err = "Please enter your phone number";
    $test = false;
} else if (!validatePhoneNumber($_POST["phone"])) {
    $phone_err = "Please enter a valid phone number";
    $test = false;
} else {
    $phone = $_POST["phone"];
}

// Validate litigant type
if (empty($_POST["litigant_type"])) {
    $litigantType_err = "Please select litigant type";
    $test = false;
} else {
    $litigant_type = $_POST["litigant_type"];
}

// Validate file pages
if ($litigant_type === 'plaintiff') {
if (empty($_POST["file_pages"])) {
    $filePages_err = "Please enter the number of file pages";
    $test = false;
} else if (!validateIdNumber($_POST["file_pages"])) {
    $filePages_err = "Please enter a valid number";
    $test = false;
} else {
    $file_pages = $_POST["file_pages"];
}
} else {
    $file_pages = NULL;
}
// Validate file upload
if ($litigant_type === 'plaintiff') {
if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
    $allowed = ["pdf" => "application/pdf", "doc" => "application/msword", "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document"];
    $file_name = $_FILES["file"]["name"];
    $file_type = $_FILES["file"]["type"];
    $file_size = $_FILES["file"]["size"];
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!array_key_exists($ext, $allowed) || $file_size > 5 * 1024 * 1024 || !in_array($file_type, $allowed)) {
        $file_err = "Invalid file. Allowed: PDF, DOC, DOCX under 5MB.";
        $test = false;
    } else {
        $destination = "../assets/case_files/" . $file_name;
        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
            $file_err = "Failed to upload the file.";
            $test = false;
        } else {
            $file = $destination;
        }
    }
} else {
    $file_err = "Please upload a file.";
    $test = false;
}}else {
    $file = NULL; 
}

    if ($test == true) {
        if (addCaseInfo($cid, $first_name, $father_name, $gfather_name, $gender, $email, $region, $zone, $woreda, $kebele, $wogen, $argument_money, $judgement_money, $phone, $litigant_type, $file_pages, $file) == 1) {
              $checkQuery = "
        SELECT 
            SUM(CASE WHEN litigant_type = 'plaintiff' THEN 1 ELSE 0 END) AS plaintiff_count,
            SUM(CASE WHEN litigant_type = 'defendant' THEN 1 ELSE 0 END) AS defendant_count
        FROM case_info
        WHERE case_id = '$cid'
    ";
     $result = mysqli_query($conn, $checkQuery);
    if ($result && mysqli_num_rows($result) > 0) {
        $counts = mysqli_fetch_assoc($result);
        if ($counts['plaintiff_count'] >= 1 && $counts['defendant_count'] >= 1) {
            // Update case status to 1
            $updateStatus = "UPDATE `case` SET case_status = 1 WHERE cid = '$cid'";
            mysqli_query($conn, $updateStatus);
             // Notify all case_distributer users
    $distributer_Query = "SELECT uid FROM users WHERE user_type = 'Case_distributer'";
    $distributer_Result = mysqli_query($conn, $distributer_Query);

    if ($distributer_Result && mysqli_num_rows($distributer_Result) > 0) {
        while ($user = mysqli_fetch_assoc($distributer_Result)) {
            $user_id = $user['uid'];
            $message = "Case ID: $case_id is now ready for distribution.";

            // Insert notification
            $notifyQuery = "INSERT INTO notifications (user_id, message) 
                            VALUES ('$user_id', '$message')";
            mysqli_query($conn, $notifyQuery);
        }
    }
}
        }
    }

    $success = "Successfully registered";
    header('refresh:2');
        } 
    else {
            $allErr = "There was an error during registration";
            
        }
    }
?>

<div class="main-content">
    <section class="section">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <div class="card">
                    <div class="card-header"><h4>Register Litigant</h4></div>

                    <?php if (!empty($success)): ?>
                        <div class="form-control bg-success text-white"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($allErr)): ?>
                        <div class="form-control bg-danger text-white"><?php echo $allErr; ?></div>
                    <?php endif; ?>

                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label>Case ID</label>
                                    <input type="text" class="form-control" name="case_id" value="<?php echo isset($case['case_id']) ? $case['case_id'] : ''; ?>" readonly />
                                </div>
                                <div class="form-group col-6">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" name="first_name" />
                                    <span class="text-danger"><?php echo $firstName_err ; ?></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label>Father Name</label>
                                    <input type="text" class="form-control" name="father_name" />
                                    <span class="text-danger"><?php echo $fatherName_err; ?></span>
                                </div>
                                <div class="form-group col-6">
                                    <label>Grandfather Name</label>
                                    <input type="text" class="form-control" name="gfather_name" />
                                    <span class="text-danger"><?php echo $gFatherName_err; ?></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label>Gender</label>
                                    <select class="form-control" name="gender">
                                        <option value="">..</option>
                                        <option value="M">M</option>
                                        <option value="F">F</option>
                                    </select>
                                    <span class="text-danger"><?php echo $gender_err; ?></span>
                                </div>
                                <div class="form-group col-6">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" />
                                    <span class="text-danger"><?php echo $email_err; ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label>Region</label>
                                    <select class="form-control" name="region">
                                        <option value="">Select Region</option>
                                        <?php
                                        $regions = getAllRegions();
                                        foreach ($regions as $regionItem): ?>
                                            <option value="<?php echo $regionItem['id']; ?>"><?php echo $regionItem['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="text-danger"><?php echo $region_err; ?></span>
                                </div>
                                <div class="form-group col-6">
                                <label>Zone</label>
                     <select class="form-control" name="zone" id="zone">
                  <option value="">Select Zone</option>
                  </select>
                 <span class="text-danger"><?php echo $zone_err; ?></span>
                           </div>
                         </div>
                            <div class="row">
                                <div class="form-group col-6">
                                <label>Woreda</label>
        <select class="form-control" name="woreda" id="woreda">
            <option value="">Select Woreda</option>
        </select>
        <span class="text-danger"><?php echo $woreda_err; ?></span>
    </div>
                                <div class="form-group col-6">
                                    <label>Kebele</label>
                                    <input type="text" class="form-control" name="kebele" />
                                    <span class="text-danger"><?php echo $kebele_err; ?></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label>Wogen</label>
                                    <select class="form-control" name="wogen">
                                        <option value="">..</option>
                                        <option value="Individual">Individual</option>
                                        <option value="Organization">Organization</option>
                                    </select>
                                    <span class="text-danger"><?php echo $wogen_err; ?></span>
                                </div>
                                <div class="form-group col-6">
                                    <label>Argument Money</label>
                                    <input type="text" class="form-control" name="argument_money" />
                                    <span class="text-danger"><?php echo $argumentMoney_err; ?></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label>Judgement Money</label>
                                    <input type="text" class="form-control" name="judgement_money" />
                                    <span class="text-danger"><?php echo $judgementMoney_err; ?></span>
                                </div>
                                <div class="form-group col-6">
                                    <label>Phone</label>
                                    <input type="text" class="form-control" name="phone" value="+251" />
                                    <span class="text-danger"><?php echo $phone_err; ?></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-6">
                                    <label>Litigant Type</label>
                                    <select class="form-control" name="litigant_type">
                                        <option value="">..</option>
                                        <option value="plaintiff">Plaintiff</option>
                                        <option value="defendant">Defendant</option>
                                    </select>
                                    <span class="text-danger"><?php echo $litigantType_err; ?></span>
                                </div>
             <div class="row">                   
<div class="row" id="plaintiff-section" style="display: none;">
    <div class="form-group col-6">
        <label>File Pages</label>
        <input type="text" class="form-control" name="file_pages" />
        <span class="text-danger"><?php echo $filePages_err ?? ''; ?></span>
    </div>
    <div class="form-group col-6">
        <label>Attach File (PDF/DOC/DOCX)</label>
        <input type="file" class="form-control" name="file" />
        <span class="text-danger"><?php echo $file_err ?? ''; ?></span>
    </div>
</div>



                            <div class="form-group mt-3">
                                <input type="submit" name="register" class="btn btn-primary btn-lg btn-block" value="Register" />
                                <a href="view_litigant_register.php" class="btn btn-secondary btn-lg btn-block">Back</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
<?php
include('../Admin/footer.php');
?>