<?php
include('loHeader.php') ;
?>
<!-- Page Header -->
   <div class="container">
         <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Register Case_Type</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Register Case Info</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Register Case_Type</a></li>
    </ul>
 </div>
<!-- End Page Header -->
<?php
$appointment_reason = $reason_id = $success = "";
$appointment_reason_err = $reason_id_err = $allErr = "";
$test = true;
$ctid = "";
if (isset($_POST["register"]) and ($_SERVER["REQUEST_METHOD"] = "POST")) {
    //validate reason_id
    if (empty($_POST["name"])) {
        $name_err = "Please enter your name";
        $test = false;
    } else if (validateIdNumber($_POST["name"]) == 0){
        $name_err = "Please enter valid name";
        $test = false;
    } else {
        $name = $_POST["name"];
    }
    //validate abbreviation_name
    if (empty($_POST["abbreviation_name"])) {
        $abbreviation_name_err = "Please enter your abbreviation_name";
        $test = false;
    } else if (validateName($_POST["abbreviation_name"]) == 0){
        $abbreviation_name_err = "Please enter valid abbreviation_name";
        $test = false;
    } else {
        $abbreviation_name = $_POST["abbreviation_name"];
    }
if ($test == true) {
if (reasonExist($ctid) == 0) {
 if(addCaseType($name,$abbreviation_name) == 1) {
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
                    <h4>Register Case_Type</h4>
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
      <div class="form-group col-6">
            <label for="name">name</label>
            <input id="name" type="text" class="form-control" name="name"/>
            <span class="text-danger"><?php echo $reason_id_err; ?></span>
        </div>
        <div class="form-group col-6">
            <label for="abbreviation_name">abbreviation_name</label>
            <input id="abbreviation_name" type="text" class="form-control" name="abbreviation_name"/>
            <span class="text-danger"><?php echo $appointment_reason_err; ?></span>
        </div>
<div class="form-group">
    <input type="submit" name="register" class="btn btn-primary btn-lg btn-block" value="Register Case_Type" />
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
