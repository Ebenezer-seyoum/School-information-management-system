<?php
include('loHeader.php') ;
?>
<!-- Page Header -->
   <div class="container">
         <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Register Cases</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Manage Cases</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Register Cases</a></li>
    </ul>
 </div>
<!-- End Page Header -->
<?php
$case_id = $plaintiff = $defendant = $case_type= $appointment_reason = $decision = $success = "";
$case_id_err = $plaintiff_err = $defendant_err = $case_type_err = $allErr = "";
$test = true;
$getNextCaseId = getNextCaseId($case_type);

if (isset($_POST["register"]) and ($_SERVER["REQUEST_METHOD"] = "POST")) {
    //validate case id
    if (empty($_POST["case_id"])) {
        $case_id_err = "Please enter your case_id";
        $test = false;
    } else if (validateIdNumber($_POST["case_id"]) == 0){
        $case_id_err = "Please enter valid case_id";
        $test = false;
    } else {
        $case_id = $_POST["case_id"];
    }
    //validate Plaintiff
    if (empty($_POST["plaintiff"])) {
        $plaintiff_err = "Please enter your plaintiff name";
        $test = false;
    } else if (validateIdNumber($_POST["plaintiff"]) == 0) {
        $plaintiff_err = "Please enter valid plaintiff name";
        $test = false;
    } else {
        $plaintiff = $_POST["plaintiff"];
    }
    //validate defendant
    if (empty($_POST["defendant"])) {
        $defendant_err = "Please enter your defendant name";
        $test = false;
    } else if (validateIdNumber($_POST["defendant"]) == 0) {
        $defendant_err = "Please enter valid defendant name";
        $test = false;
    } else {
        $defendant = $_POST["defendant"];
    }   
    //validate case type
    if (empty($_POST["case_type"])) {
        $case_type_err = "Please enter your case type";
        $test = false;
    } else if (validateIdNumber($_POST["case_type"]) == 0) {
        $case_type_err = "Please enter valid case type";
        $test = false;
    } else {
        $case_type = $_POST["case_type"];
    }
if ($test == true) {
    $case_status = 0;
    $decision = NULL;
if (caseExist($case_id) == 0) {
 if(addCase($case_id, $plaintiff ,$defendant,$case_type, $decision, $case_status) == 1) {
        $success = "Successfully registered";
        header('refresh:2');
        } else {
        $allErr = "There was error while registration";
        }
    } else {
        $allErr = "This case has already been registered";
    }
  }
} //if (isset($_POST["register"]) and ($_SERVER["REQUEST_METHOD"] = "POST")) {
?>
         <!-- Main Content -->
<div class="main-content">
   <section class="section">
       <div class="row">
         <div class="col-2"></div>
             <div class="col-8 col-sm-8 col-lg-8">
                <div class="card ">
                   <div class="card-header">
                    <h4>Register New Case</h4>
                     </div>
<?php if (!empty($success)) { ?>
 <div class=" form-control bg-success">
    <?php echo $success; ?>
 </div>
    <?php  } ?>
<?php if (!empty($allErr)) { ?>
 <div class=" form-control bg-danger">
    <?php echo $allErr; ?>
 </div>
    <?php  } ?>

<div class="card-body">                                
   <form action="" method="POST" enctype="multipart/form-data">
   <div class="row">
    <div class="form-group col-md-6">
            <label for="case_id">case_id</label>
            <input id="case_id" type="text" class="form-control" name="case_id"
             value="<?php echo $getNextCaseId; ?>" readonly/>
            <span class="text-danger"><?php echo $case_id_err; ?></span>
    </div>
    <div class="form-group col-md-6">
            <label for="case_type">case_type</label>
            <select class="form-control" id="case_type" name="case_type">
                <option value="">Select case_type</option>
                <?php
                $types =  getAllCaseType();     
                foreach ($types as $type) {
                ?>
             <option value="<?php echo $type["ctid"]; ?>"><?php echo $type["name"]; ?></option>
                <?php } ?>
            </select>
            <span class="text-danger"><?php echo $case_type_err; ?></span>
        </div> 
    </div>
        <div class="row mt-3">
        <div class="form-group col-md-6">
           <label for="plaintiff">plaintiff</label>
            <input id="plaintiff" type="text" class="form-control" name="plaintiff"/>
            <span class="text-danger"><?php echo $plaintiff_err; ?></span>
        </div>
     <div class="form-group col-md-6">
     <label for="defendant">defendant</label>
         <input id="defendant" type="text" class="form-control" name="defendant" />
         <span class="text-danger"><?php echo $defendant_err; ?></span>
        </div>      
  </div>
<div class="form-group">
    <input type="submit" name="register" class="btn btn-primary btn-lg btn-block" value="Register case" />
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
<?php
include('../admin/footer.php');
?>
