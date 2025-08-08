<?php
ob_start();
session_start();
//error_reporting(0);
include '../connection/connection.php';
include '../connection/function.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>CIMS  Dashboard</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="../assets/img/icon.png" type="image/x-icon"/>
	<!-- Fonts and icons -->
	<script src="../assets/js/plugin/webfont/webfont.min.js"></script>
	<script>
	WebFont.load({
	google: {"families":["Public Sans:300,400,500,600,700"]},
	custom: {"families":["Font Awesome 5 Solid","Font Awesome 5 Regular","Font Awesome 5 Brands","simple-line-icons"], urls: ['../assets/css/fonts.min.css']},
		active: function() {
		sessionStorage.fonts = true;
			}
		});
	</script>
	<!-- CSS Files -->
	<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="../assets/css/plugins.min.css">
	<link rel="stylesheet" href="../assets/css/kaiadmin.min.css">
</head>
<body>
<div class="wrapper">
	<div class="sidebar" data-background-color="dark">
	    <div class="sidebar-logo">
		<!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
	<img src="../assets/img/logo.png" alt="navbar brand" class="navbar-brand" height="60" width="170"></a>
	    <div class="nav-toggle">
		 <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
		 <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button></div>
		 <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button></div>
	</div>	
	   <!-- End Logo Header -->

	   <!-- side bar -->
<div class="sidebar-wrapper scrollbar scrollbar-inner">
	<div class="sidebar-content">
		<ul class="nav nav-secondary">
		  <li class="nav-item active">
    <a href="case_distributer.php" class="collapsed"><i class="fas fa-home"></i><p>Case distributer</p></a></li>
<!-- Judge Control -->
<li class="nav-item">
    <a data-bs-toggle="collapse" href="#judgeControl"><i class="fas fa-user-tie"></i>
        <p>Judge Control</p><span class="caret"></span></a>
    <div class="collapse" id="judgeControl">
      <ul class="nav nav-collapse">
	  <li><a href="view_judge.php"><i class="fas fa-gavel"></i>View Judges</a></li>					
      </ul>
  </div>
</li>
<!-- Case Management -->
<li class="nav-item">
    <a data-bs-toggle="collapse" href="#caseManagement"><i class="fas fa-briefcase"></i>
        <p>Case Management</p><span class="caret"></span></a>
    <div class="collapse" id="caseManagement">
      <ul class="nav nav-collapse">
	    <li><a href="view_AllCases.php"><i class="fa fa-folder-open"></i>All Cases</a></li>			
        <li><a href="viewAssignCase.php"><i class="fa fa-eye"></i>Distributed Cases</a></li>	
        <li><a href="Assign_case.php"><i class="fa fa-tasks"></i> Assign Cases</a></li>
		<li><a href="view_transfer_cases.php"><i class="fa fa-eye"></i>Transfer Cases</a></li>
      </ul>
    </div>
</li>
<!-- Case Status -->
<li class="nav-item">
    <a data-bs-toggle="collapse" href="#caseStatus"><i class="fas fa-balance-scale"></i>
        <p>Case Status</p><span class="caret"></span></a>
    <div class="collapse" id="caseStatus">
        <ul class="nav nav-collapse">
		<li><a href="open_case.php"><i class="fas fa-folder-open"></i> Open Cases</a></li>
        <li><a href="distributed_case.php"><i class="fas fa-gavel"></i> Distributed Cases</a></li>
        <li><a href="pending_appointment.php"><i class="fas fa-hourglass-end"></i> Pending Appointment Cases</a></li>
        <li><a href="appointed_case.php"><i class="fas fa-calendar-check"></i> Appointed Cases</a></li>
        <li><a href="pending_decision.php"><i class="fas fa-hourglass-end"></i> Pending Decision Cases</a></li>
        <li><a href="decided_case.php"><i class="fas fa-check-circle"></i> Decided Cases</a></li>    			
        </ul>
    </div>
</li>
<li class="nav-item">
	<a data-bs-toggle="collapse" href="#tables"><i class="fas fa-table"></i><p>Reports</p><span class="caret"></span></a>
	    <div class="collapse" id="tables">
		<ul class="nav nav-collapse">
			<li><a href="tables/tables.html"><span class="sub-item">Basic Table</span></a></li>
			<li><a href="tables/datatables.html"><span class="sub-item">Datatables</span></a></li>
		</ul>
	</div>
</li>	
	      </ul>
		</div>
	</div>
</div>
		<!-- End Sidebar -->		
<div class="main-panel">
	<div class="main-header">
		<div class="main-header-logo">
				<!-- Logo Header -->
	<div class="logo-header" data-background-color="dark">
		<img src="../assets/img/logo.png" alt="navbar brand" class="navbar-brand" height="60" width="170">
	<div class="nav-toggle">
		<button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
		<button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button></div>
		<button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button></div>
				<!-- End Logo Header -->
			</div>
	 <!--Notification -->
	 <?php
$profile = getUserByID($_SESSION["uid"]);
if (isset($_SESSION["uid"]) and ($profile["user_type"] == "Case_distributer")) {
?>	
<!-- CSS for profile image -->
<style>
  .profile-img {
    width: 40px; 
    height: 40px;
    border-radius: 80%; 
    object-fit: cover; 
}
</style>
<!-- end CSS for profile image -->	  
<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
  <div class="container-fluid">
	<div class="input-group">
	  <div class="input-group-prepend">									
		<h4>Dashboard</h4></div>				
<ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

 <li class="nav-item topbar-icon dropdown hidden-caret">
   <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
     <i class="fa fa-bell"></i><span class="notification" id="notifCount">0</span>
   </a>
<ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
  <li><div class="dropdown-title" id="notifTitle">You have 0 new notification</div></li>
	<li>
      <div class="notif-scroll scrollbar-outer" style="max-height: 300px; overflow-y: auto;">
        <div class="notif-center" id="notifList">
         <!-- Notifications will be loaded here -->
        </div>
      </div>
     </li>
   <li><a class="see-all" href="view_notifications.php">See all notifications<i class="fa fa-angle-right"></i> </a></li>
 </ul>
</li>
			  <!-- End Notification -->		  
<!-- User Profile -->
<li class="nav-item topbar-user dropdown hidden-caret">
  <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
	<div class="avatar-sm">
	<img class="profile-img" src="<?php echo $profile["profile_pic"]; ?>" 
	    alt="Profile Picture" width="100" height="100"></div>
	<span class="profile-username"><span class="op-7"><?php echo "$profile[username]";?></span> 
	<span class="fw-bold"></span></span>
   </a>
<ul class="dropdown-menu dropdown-user animated fadeIn">
   <div class="dropdown-user-scroll scrollbar-outer">
 <li>
<div class="user-box">
  <div class="avatar-lg">
    <img class="profile-img" src="<?php echo $profile["profile_pic"]; ?>" 
	alt="Profile Picture" width="100" height="100"></div>
  <div class="u-text">
	 <h4><?php echo "$profile[username]";?></h4>
		<p class="text-muted"><?php echo "$profile[email]";?></p>
            <a href="profile.php?uid=<?php echo $profile['uid']; ?>" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
            <a href="../connection/logout.php" class="btn btn-xs btn-secondary btn-sm">Logout</a>
								    </div>
								</div>
							</li>									
						</div>
					</ul>
				</li>
			</ul>
		</div>
	</nav>
</div>
	<!-- End Navbar -->  
<?php
} else {
    //header('location: ../index.php');
}
?>



    