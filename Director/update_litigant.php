<?php
include('adminHeader.php');
?>
<!-- page header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
    <h3 class="fw-bold mb-3">Update Case</h3>
     <ul class="breadcrumbs mb-3">
         <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Manage Cases</a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Update Case</a></li>
     </ul>
 </div>
<!-- End Page Header -->
<?php
//form validation
$case_id = $first_name = $father_name = $gfather_name = $gender = $email = $region = "";
$zone = $woreda = $kebele = $wogen = $argument_money = $judgement_money = "";
$phone = $litigant_type = $file_pages = $file = $success = "";
//for error messages
$case_id_err = $first_name_err = $father_name_err = $gfather_name_err = $gender_err = $email_err = "";
$region_err = $zone_err = $woreda_err = $kebele_err = $wogen_err = $argument_money_err = "";
$judgement_money_err = $phone_err = $litigant_type_err = $file_pages_err = $file_err = $allErr = "";
$test = true;
$case_info = "";
//get the user id from the database
$caseInfo = null;
if (isset($_GET["kid"])) {
    $case_id = basics($_GET["kid"]);
    $caseInfo = getCaseInfoForUpdateId($case_id);
    if ($caseInfo) {
        $region = getRegionById($caseInfo["region"]);
        $zone = getZoneById($caseInfo["zone"]);
        $woreda = getWoredaById($caseInfo["woreda"]);
        $kid = $caseInfo["kid"];  
        $cid = $caseInfo["cid"];        
    } else {
        $allErr = "Invalid case ID or case not found.";
    }
} else {
    $allErr = "Missing case ID.";
}

if (isset($_POST["update"]) && ($_SERVER["REQUEST_METHOD"] == "POST")) {
    // Get wogen and litigant_type first for conditional validation
    $wogen = $_POST["wogen"] ?? '';
    $litigant_type = $_POST["litigant_type"] ?? '';
    
    //validate case id
    if (empty($_POST["case_id"])) {
        $case_id_err = "Please enter your case_id";
        $test = false;
    } else if (validateIdNumber($_POST["case_id"]) == 0) {
        $case_id_err = "Please enter valid case_id";
        $test = false;
    } else {
        $case_id = $_POST["case_id"];
    }
    
    //validate first_name
    if (empty($_POST["first_name"])) {
        $first_name_err = "Please enter your first name";
        $test = false;
    } else if (validateName($_POST["first_name"]) == 0) {
        $first_name_err = "Please enter valid first_name name";
        $test = false;
    } else {
        $first_name = $_POST["first_name"];
    }
    
    //validate father_name - only if wogen is Individual
    if ($wogen === 'Individual') {
        if (empty($_POST["father_name"])) {
            $father_name_err = "Please enter your father_name";
            $test = false;
        } else if (validateName($_POST["father_name"]) == 0) {
            $father_name_err = "Please enter valid father_name ";
            $test = false;
        } else {
            $father_name = $_POST["father_name"];
        }
    } else {
        $father_name = NULL;
    }
    
    //validate grandfather_name - only if wogen is Individual
    if ($wogen === 'Individual') {
        if (empty($_POST["grandfather_name"])) {
            $gfather_name_err = "Please enter your grandfather_name";
            $test = false;
        } else if (validateName($_POST["grandfather_name"]) == 0) {
            $gfather_name_err = "Please enter valid grandfather_name ";
            $test = false;
        } else {
            $gfather_name = $_POST["grandfather_name"];
        }
    } else {
        $gfather_name = NULL;
    }
    
    //validate gender - only if wogen is Individual
    if ($wogen === 'Individual') {
        if (empty($_POST["gender"])) {
            $gender_err = "Please select your gender";
            $test = false;
        } else if (validateGender($_POST["gender"]) == 0) {
            $gender_err = "Invalid input";
            $test = false;
        } else {
            $gender = $_POST["gender"];
        }
    } else {
        $gender = NULL;
    }
    
    //validate email 
    if (empty($_POST["email"])) {
        $email_err = "Please enter your email";
        $test = false;
    } else if (validateEmail($_POST["email"]) == 0) {
        $email_err = "Please enter valid email";
        $test = false;
    } else {
        $email = $_POST["email"];
    }
    
    //validate region
    if (empty($_POST["region"])) {
        $region_err = "Please enter your region";
        $test = false;
    } else if (validateIdNumber($_POST["region"]) == 0) {
        $region_err = "Please enter valid region name";
        $test = false;
    } else {
        $region = $_POST["region"];
    }
    
    //validate zone
    if (empty($_POST["zone"])) {
        $zone_err = "Please enter your zone";
        $test = false;
    } else if (validateIdNumber($_POST["zone"]) == 0) {
        $zone_err = "Please enter valid zone name";
        $test = false;
    } else {
        $zone = $_POST["zone"];
    }
    
    //validate woreda
    if (empty($_POST["woreda"])) {
        $woreda_err = "Please enter your woreda";
        $test = false;
    } else if (validateIdNumber($_POST["woreda"]) == 0) {
        $woreda_err = "Please enter valid woreda name";
        $test = false;
    } else {
        $woreda = $_POST["woreda"];
    }
    
    //validate kebele
    if (empty($_POST["kebele"])) {
        $kebele_err = "Please enter your kebele";
        $test = false;
    } else if (validateIdNumber($_POST["kebele"]) == 0) {
        $kebele_err = "Please enter valid kebele name";
        $test = false;
    } else {
        $kebele = $_POST["kebele"];
    }
    
    //validate wogen
    if (empty($_POST["wogen"])) {
        $wogen_err = "Please enter your wogen";
        $test = false;
    } else if (validateName($_POST["wogen"]) == 0) {
        $wogen_err = "Please enter valid wogen name";
        $test = false;
    } else {
        $wogen = $_POST["wogen"];
    }
    
    //validate argument_money
    if (empty($_POST["argument_money"])) {
        $argument_money_err = "Please enter your argument_money";
        $test = false;
    } else if (validateIdNumber($_POST["argument_money"]) == 0) {
        $argument_money_err = "Please enter valid argument_money";
        $test = false;
    } else {
        $argument_money = $_POST["argument_money"];
    }
    
    //validate judgement_money
    if (empty($_POST["judgement_money"])) {
        $judgement_money_err = "Please enter your judgement_money";
        $test = false;
    } else if (validateIdNumber($_POST["judgement_money"]) == 0) {
        $judgement_money_err = "Please enter valid judgement_money";
        $test = false;
    } else {
        $judgement_money = $_POST["judgement_money"];
    }
    
    //validate phone
    if (empty($_POST["phone"])) {
        $phone_err = "Please enter your phone";
        $test = false;
    } else if (validatePhoneNumber($_POST["phone"]) == 0) {
        $phone_err = "Please enter valid phone number";
        $test = false;
    } else {
        $phone = $_POST["phone"];
    }
    
    //validate litigant_type
    if (empty($_POST["litigant_type"])) {
        $litigant_type_err = "Please enter your litigant_type";
        $test = false;
    } else if (validateName($_POST["litigant_type"]) == 0) {
        $litigant_type_err = "Please enter valid litigant_type name";
        $test = false;
    } else {
        $litigant_type = $_POST["litigant_type"];
    }
    
    // Validate file pages and file upload - only if litigant_type is plaintiff
    if ($litigant_type === 'plaintiff') {
        if (empty($_POST["file_pages"])) {
            $file_pages_err = "Please enter the number of file pages";
            $test = false;
        } else if (validateIdNumber($_POST["file_pages"]) == 0) {
            $file_pages_err = "Please enter a valid number";
            $test = false;
        } else {
            $file_pages = $_POST["file_pages"];
        }
        
        // Check if a new file is being uploaded
        if ($_FILES["file"]["error"] == 0) {
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
                    // Delete old file if it exists
                    if (!empty($caseInfo["file"]) && file_exists($caseInfo["file"])) {
                        unlink($caseInfo["file"]);
                    }
                }
            }
        } else {
            // Keep the existing file if no new file is uploaded
            $file = $caseInfo["file"] ?? NULL;
        }
    } else {
        $file_pages = NULL;
        $file = NULL;
        // Delete old file if changing from plaintiff to defendant
        if ($caseInfo["litigant_type"] === 'plaintiff' && !empty($caseInfo["file"]) && file_exists($caseInfo["file"])) {
            unlink($caseInfo["file"]);
        }
    }
    
    if ($test == true) {
        if (updateCaseInfo($kid, $first_name, $father_name, $gfather_name, $gender, $email, $region,
            $zone, $woreda, $kebele, $wogen, $argument_money, $judgement_money,
            $phone, $litigant_type, $file_pages, $file) == 1) {
            $success = "Successfully updated the case information";
            header('refresh:2');
            $caseInfo = getCaseInfoForUpdateId($kid); 
            // Get all judges assigned to this case
            $sql_judges = "SELECT user_id FROM assigned_judges WHERE case_id = '$cid'";
            $result_judges = mysqli_query($conn, $sql_judges);
            // Prepare notification message
            $notif_msg = "An update has been sucessfully for Case ID: $case_id.";
            // Send notification to each assigned judge
            if ($result_judges && mysqli_num_rows($result_judges) > 0) {
                while ($row = mysqli_fetch_assoc($result_judges)) {
                    $judge_id = $row['user_id'];
                    $sql_notif = "INSERT INTO notifications (user_id, message) VALUES ('$judge_id', '$notif_msg')";
                    mysqli_query($conn, $sql_notif);
                }
            }
        } else {
            $allErr = "There was error while registration";
        }
    }
}
?>

<?php
$profile = getUserByID($_SESSION["uid"]);
if (isset($_SESSION["uid"]) && ($profile["user_type"] == "Admin")) {
?>
<div class="main-content">
    <section class="section">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8 col-sm-8 col-lg-8">
                <div class="card ">
                 <div class="card-header">
                     <h4>Update Case</h4>
                 </div>
    <?php if (!empty($success)) { ?>
        <div class=" form-control bg-success">
            <?php echo $success; ?>
        </div>
    <?php } ?>
    <?php if (!empty($allErr)) { ?>
        <div class=" form-control bg-danger">
            <?php echo $allErr; ?>
        </div>
    <?php } ?>

<div class="card-body">
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="row">
    <div class="form-group col-6">
        <label for="case_id">case_id</label>
        <input id="case_id" type="text" class="form-control" name="case_id"
               value="<?php echo $caseInfo["case_id"]; ?>" readonly/>
        <span class="text-danger"><?php echo $case_id_err;?></span>
    </div>
    <div class="form-group col-6">
        <label for="first_name">first_name</label>
        <input id="first_name" type="text" class="form-control" name="first_name"
               value="<?php echo $caseInfo["first_name"]; ?>" />
        <span class="text-danger"><?php echo $first_name_err; ?></span>
    </div>
</div>

<div class="row" id="individual-fields" style="display: <?php echo ($caseInfo["wogen"] == 'Individual') ? 'flex' : 'none'; ?>;">
   <div class="form-group col-6">
       <label for="father_name">father_name</label>
       <input id="father_name" type="text" class="form-control" name="father_name"
              value="<?php echo $caseInfo["father_name"]; ?>" autofocus />
       <span class="text-danger"><?php echo $father_name_err; ?></span>
   </div>
   <div class="form-group col-6">
       <label for="grandfather_name">grandfather_name</label>
       <input id="grandfather_name" type="text" class="form-control" name="grandfather_name"
              value="<?php echo $caseInfo["grandfather_name"]; ?>" autofocus />
       <span class="text-danger"><?php echo $gfather_name_err; ?></span>
   </div>
</div>

<div class="row" id="gender-field" style="display: <?php echo ($caseInfo["wogen"] == 'Individual') ? 'block' : 'none'; ?>;">
    <div class="form-group col-6">
        <label for="gender">Gender</label>
        <select id="gender" class="form-control" name="gender">
            <option value="">..</option>
            <option value="M" <?php if ($caseInfo["gender"] == "M") echo "selected=selected"; ?>>M</option>
            <option value="F" <?php if ($caseInfo["gender"] == "F") echo "selected=selected"; ?>>F</option>
        </select>
        <span class="text-danger"><?php echo $gender_err; ?></span>
    </div>
</div>

<div class="row">
    <div class="form-group col-6">
        <label for="email">Email</label>
        <input id="email" type="email" class="form-control" name="email"
               value="<?php echo $caseInfo["email"]; ?>" />
        <span class="text-danger"><?php echo $email_err; ?></span>
    </div>
</div>

<div class="row">
    <div class="form-group col-6">
        <label for="region">Region</label>
       <select class="form-control" name="region">
            <option value="<?php echo $caseInfo["region"] ?> "><?php echo $region ?></option>
            <?php
            $regions = getAllRegions();
            foreach ($regions as $regionItem): ?>
                <option value="<?php echo $regionItem['id']; ?>"><?php echo $regionItem['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <span class="text-danger"><?php echo $region_err ?? ''; ?></span>
    </div>
    <div class="form-group col-6">
        <label for="zone">Zone</label>
       <select class="form-control" name="zone" id="zone">
            <option value="<?php echo $caseInfo["zone"] ?>"><?php echo $zone ?></option>
        </select>
        <span class="text-danger"><?php echo $zone_err ?? ''; ?></span>
    </div>
</div>

<div class="row">
    <div class="form-group col-6">
        <label for="woreda">Woreda</label>
         <select class="form-control" name="woreda" id="woreda">
            <option value="<?php echo $caseInfo["woreda"] ?>"><?php echo $woreda ?></option>
        </select>
        <span class="text-danger"><?php echo $woreda_err ?? ''; ?></span>
    </div>
    <div class="form-group col-6">
        <label for="kebele">Kebele</label>
        <input id="kebele" type="text" class="form-control" name="kebele"
               value="<?php echo $caseInfo["kebele"]; ?>" />
        <span class="text-danger"><?php echo $kebele_err; ?></span>
    </div>
</div>

<div class="row">
    <div class="form-group col-6">
        <label for="wogen">Wogen</label>
        <select id="wogen" class="form-control" name="wogen" onchange="toggleIndividualFields()">
            <option value="">..</option>
            <option value="Individual" <?php if ($caseInfo["wogen"] == "Individual") echo "selected=selected"; ?>>Individual</option>
            <option value="Organization" <?php if ($caseInfo["wogen"] == "Organization") echo "selected=selected"; ?>>Organization</option>
        </select>
        <span class="text-danger"><?php echo $wogen_err; ?></span>
    </div>
    <div class="form-group col-6">
        <label for="argument_money">Argument Money</label>
        <input id="argument_money" type="text" class="form-control" name="argument_money"
               value="<?php echo $caseInfo["argument_money"]; ?>" />
        <span class="text-danger"><?php echo $argument_money_err; ?></span>
    </div>
</div>

<div class="row">
    <div class="form-group col-6">
        <label for="judgement_money">Judgement Money</label>
        <input id="judgement_money" type="text" class="form-control" name="judgement_money"
               value="<?php echo $caseInfo["judgement_money"]; ?>" />
        <span class="text-danger"><?php echo $judgement_money_err; ?></span>
    </div>
    <div class="form-group col-6">
        <label for="phone">phone</label>
        <input id="phone" type="text" class="form-control" name="phone"
               value="<?php echo $caseInfo["phone"]; ?>" />
        <span class="text-danger"><?php echo $phone_err; ?></span>
    </div>
</div>

<div class="row">
    <div class="form-group col-6">
        <label for="litigant_type">Litigant Type</label>
        <select id="litigant_type" class="form-control" name="litigant_type" onchange="togglePlaintiffFields()">
            <option value="">..</option>
            <option value="plaintiff" <?php if ($caseInfo["litigant_type"] == "plaintiff") echo "selected=selected"; ?>>plaintiff</option>
            <option value="defendant" <?php if ($caseInfo["litigant_type"] == "defendant") echo "selected=selected"; ?>>defendant</option>
        </select>
        <span class="text-danger"><?php echo $litigant_type_err; ?></span>
    </div>
</div>

<div class="row" id="plaintiff-fields" style="display: <?php echo ($caseInfo["litigant_type"] == 'plaintiff') ? 'flex' : 'none'; ?>;">
    <div class="form-group col-6">
        <label for="file_pages">File Pages</label>
        <input id="file_pages" type="text" class="form-control" name="file_pages"
               value="<?php echo $caseInfo["file_pages"]; ?>" />
        <span class="text-danger"><?php echo $file_pages_err ?? ''; ?></span>
    </div>
    <div class="form-group col-6">
        <label for="file">Attach File (PDF/DOC/DOCX)</label>
        <input id="file" type="file" class="form-control" name="file" />
        <span class="text-danger"><?php echo $file_err ?? ''; ?></span>
        <?php if ($caseInfo["litigant_type"] == 'plaintiff' && !empty($caseInfo["file"])): ?>
            <small class="text-muted">Current file: <?php echo basename($caseInfo["file"]); ?></small>
        <?php endif; ?>
    </div>
</div>

<div class="form-group">
    <input type="submit" name="update" class="btn btn-primary btn-lg btn-block" value="Update" />
    <a href="view_updateLitigant.php?case_id=<?php echo $caseInfo["case_id"];?>&cid=<?php echo $caseInfo["cid"];?>" class="btn btn-danger btn-lg btn-block">Back</a>
</div>
</form>
</div>
</div>
</div>
</div>
</section>
</div>
</div>
</div>
</div>

<script>
function toggleIndividualFields() {
    var wogen = document.getElementById("wogen").value;
    var individualFields = document.getElementById("individual-fields");
    var genderField = document.getElementById("gender-field");
    
    if (wogen === 'Individual') {
        individualFields.style.display = 'flex';
        genderField.style.display = 'block';
    } else {
        individualFields.style.display = 'none';
        genderField.style.display = 'none';
    }
}

function togglePlaintiffFields() {
    var litigantType = document.getElementById("litigant_type").value;
    var plaintiffFields = document.getElementById("plaintiff-fields");
    
    if (litigantType === 'plaintiff') {
        plaintiffFields.style.display = 'flex';
    } else {
        plaintiffFields.style.display = 'none';
    }
}
</script>

<?php
}else {
    //header('location: ../index.php');
}
?>
<?php
include('footer.php');
?>