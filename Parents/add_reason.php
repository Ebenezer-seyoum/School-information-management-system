<?php
include('loHeader.php') ;
?>
<!-- Page Header -->
   <div class="container">
         <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Register Reason</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Register Case Info</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Register Reason</a></li>
    </ul>
 </div>
<!-- End Page Header -->

<?php
$appointment_reason  = $success = "";
$appointment_reason_err = $reason_id_err = $allErr = "";
$test = true;

if (isset($_POST["register"]) and ($_SERVER["REQUEST_METHOD"] = "POST")) {
    //validate appointment_reason
    if (empty($_POST["appointment_reason"])) {
        $appointment_reason_err = "Please enter your appointment_reason";
        $test = false;
    } else if (validateName($_POST["appointment_reason"]) == 0){
        $appointment_reason_err = "Please enter valid appointment_reason";
        $test = false;
    } else {
        $appointment_reason = $_POST["appointment_reason"];
    }
if ($test == true) {
if (reasonExist($appointment_reason) == 0) {
 if(addReason($appointment_reason) == 1) {
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
                    <h4>Register Reason</h4>
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
            <label for="appointment_reason">appointment_reason</label>
            <input id="appointment_reason" type="text" class="form-control" name="appointment_reason"/>
            <span class="text-danger"><?php echo $appointment_reason_err; ?></span>
        </div>
<div class="form-group">
    <input type="submit" name="register" class="btn btn-primary btn-lg btn-block" value="Register Reason" />
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
