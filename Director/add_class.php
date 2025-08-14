<?php
include('directorHeader.php') ;
?>
<!-- Page Header -->
   <div class="container">
         <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Register Class</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Manage Class</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Register Class</a></li>
    </ul>
 </div>
<!-- End Page Header -->
<?php
$role_type = $section_name = $success = "";
$role_type_err = $section_name_err = $allErr = "";
$test = true;
$ctid = "";
if (isset($_POST["register"]) and ($_SERVER["REQUEST_METHOD"] = "POST")) {
    //validate section_name
    if (empty($_POST["Section_name"])) {
        $section_name_err = "Please enter your Section_name";
        $test = false;
    } else if (validateIdNumber($_POST["Section_name"]) == 0){
        $section_name_err = "Please enter valid Section_name";
        $test = false;
    } else {
        $section_name = $_POST["Section_name"];
    }
    //validate role_type
    if (empty($_POST["role_type"])) {
        $role_type_err = "Please enter your role_type";
        $test = false;
    } else if (validateName($_POST["role_type"]) == 0){
        $role_type_err = "Please enter valid role_type";
        $test = false;
    } else {
        $role_type = $_POST["role_type"];
    }
if ($test == true) {
if (sectionExist($ctid) == 0) {
 if(add_section($section_name,$role_type) == 1) {
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
                    <h4>Register Class</h4>
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
            <label for="Section_name">Section_name<span class="text-danger">*</span></label>
            <input id="Section_name" type="text" class="form-control" name="Section_name"/>
            <span class="text-danger"><?php echo $section_name_err; ?></span>
        </div>
         <div class="form-group col-12 col-md-6 mb-3">
                    <label for="role_type">Class Type<span class="text-danger">*</span></label>
                    <select name="role_type" id="role_type" class="form-control">
                      <option value="">-- Select --</option>
                      <option value="general" <?php echo $role_type == 'general' ? 'selected' : ''; ?>>General</option>
                      <option value="natural" <?php echo $role_type == 'natural' ? 'selected' : ''; ?>>Natural</option>
                      <option value="social" <?php echo $role_type == 'social' ? 'selected' : ''; ?>>Social</option>
                    </select>
                    <span class="text-danger"><?php echo htmlspecialchars($role_type_err); ?></span>
                  </div>
                </div>
        
<div class="form-group">
    <input type="submit" name="register" class="btn btn-primary btn-lg btn-block" value="Register" />
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
include('../Admin/footer.php');
?>
