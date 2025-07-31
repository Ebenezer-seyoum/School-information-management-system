<?php
include('adminHeader.php');
?>
<!-- Page content start -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Create Account</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Account</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Create Account</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <section class="section">
        <form action="" method="POST" enctype="multipart/form-data"> 
          <div class="row">
            <div class="col-12 col-lg-4 mb-4">
              <label class="form-label text-primary">Photo</label>
              <div class="avatar-upload">
                <div class="avatar-preview">
                  <div class="user-img">
                    <img class="profile-images" src="../assets/img/no.png" alt="Profile Picture" width="100" height="100">
                  </div>
                </div>
                <div class="change-btn mt-2 mb-lg-0 mb-3">
                  <input type="file" class="form-control d-none" id="imageUpload" name="profile_picture">
                  <label for="imageUpload" class="dlab-upload mb-0 btn btn-primary btn-sm">Choose File</label>
                  <button type="button" id="removeImage" class="btn btn-danger light remove-img ms-2 btn-sm">Remove</button><br>
                  <span class="text-danger"></span>
                </div>
              </div>
            </div>

            <div class="col-8 col-sm-8 col-lg-8">
              <div class="card">
                <div class="card-header">
                  <h4>Create account</h4>
                </div>

                <div class="card-body">                                
                  <div class="row">
                    <div class="form-group col-12 col-md-6 mb-3">
                      <label for="id_number">ID Number</label>
                      <input id="id_number" type="text" class="form-control" name="idNumber" value="" readonly/>
                      <span class="text-danger"></span>
                    </div>
                    <div class="form-group col-12 col-md-6 mb-3">
                      <label for="first_name">First Name</label>
                      <input id="first_name" type="text" class="form-control" name="first_name"/>
                      <span class="text-danger"></span>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-12 col-md-6 mb-3">
                      <label for="father_name">Father Name</label>
                      <input id="father_name" type="text" class="form-control" name="father_name"/>
                      <span class="text-danger"></span>
                    </div>
                    <div class="form-group col-12 col-md-6 mb-3">
                      <label for="grand_father_name">Grand Father Name</label>
                      <input id="grand_father_name" type="text" class="form-control" name="grand_father_name"/>
                      <span class="text-danger"></span>
                    </div>       
                  </div>

                  <div class="row">
                    <div class="form-group col-12 col-md-6 mb-3">
                      <label for="gender">Gender</label>
                      <select name="gender" id="gender" class="form-control">
                        <option value="">...</option>
                        <option value="M">M</option>
                        <option value="F">F</option>
                      </select>
                      <span class="text-danger"></span>
                    </div> 
                    <div class="form-group col-12 col-md-6 mb-3">
                      <label for="Email">Email</label>
                      <input id="Email" type="text" class="form-control" name="email" />
                      <span class="text-danger"></span>
                    </div>                                  
                  </div>

                  <div class="row">
                    <div class="form-group col-12 col-md-6 mb-3">
                      <label for="password" class="d-block">Password</label>
                      <input type="password" id="password" name="password" class="form-control" onkeyup="checkADDPassword()" />
                      <ul id="password-checklist" style="list-style: none; padding: 0; display: none;">
                        <li id="lower" style="color: red;">❌ One lowercase letter</li>
                        <li id="upper" style="color: red;">❌ One uppercase letter</li>
                        <li id="special" style="color: red;">❌ One special character (@#$%^&+=!)</li>
                        <li id="length" style="color: red;">❌ At least 8 characters</li>
                      </ul>
                      <span class="text-danger"></span>
                    </div>
                    <div class="form-group col-12 col-md-6 mb-3">
                      <label for="password2" class="d-block">Password Confirmation</label>
                      <input id="password2" type="password" class="form-control" name="confirm_password" />
                      <span class="text-danger"></span>
                    </div>
                    <div class="form-group col-12 col-md-6 mb-3">
                      <label for="username">Username</label>
                      <input id="username" type="text" class="form-control" name="username" />
                      <span class="text-danger"></span>
                    </div> 
                    <div class="form-group col-6">
                      <label>Phone</label>
                      <input type="text" class="form-control" name="phone" value="+251" />
                      <span class="text-danger"></span>
                    </div>
                    <div class="form-group col-12 col-md-6 mb-3">
                      <label for="user_type">User Type</label>
                      <select name="user_type" id="user_type" class="form-control">
                        <option value="">...</option>
                        <option value="admin">admin</option>
                        <option value="case_distributer">case_distributer</option>
                        <option value="law_officer">law_officer</option>
                        <option value="judge">judge</option>
                        <option value="president">president</option>
                      </select>
                      <span class="text-danger"></span>
                    </div> 
                  </div>

                  <div class="form-group">
                    <input type="submit" name="register" class="btn btn-primary btn-lg btn-block" value="Register"/>   
                    <input type="reset" name="reset"  class="btn btn-danger btn-lg btn-block" value="Reset"/>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </section>
    </div>
  </div>
</div>


<?php
include('footer.php');
?>
