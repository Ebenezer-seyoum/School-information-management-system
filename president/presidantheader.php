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
	<img src="../assets/img/logo.png" alt="navbar brand" class="navbar-brand" height="60" width="170">
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
           <a data-bs-toggle="collapse" href="" class="collapsed"><i class="fas fa-home"></i><p>presidant</p></a></li>

<li class="nav-item">
  <a data-bs-toggle="collapse" href="#reports"><i class="fas fa-chart-bar"></i><p>Reports</p><span class="caret"></span></a>
    <div class="collapse" id="reports">
      <ul class="nav nav-collapse"><li><a href="general_report.php"><i class="fas fa-file-alt me-2"></i>general report</span> </a></li>
      <li><a href="litigant_report.php"><i class="fas fa-file-alt me-2"></i> litigant report</a></li>
    
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
		<a href="index.html" class="logo">
		<img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20"></a>
	<div class="nav-toggle">
		<button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
		<button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button></div>
		<button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button></div>
				<!-- End Logo Header -->
			</div>

			     <!-- Navbar Header -->
				  <!--Notification -->
<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
   <div class="container-fluid">
		<div class="input-group">
			<div class="input-group-prepend">									
				<h4> Dashboard </h4></div>				
		<ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
			<li class="nav-item topbar-icon dropdown hidden-caret">
	<a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" 
	         aria-expanded="false">
			<i class="fa fa-bell"></i><span class="notification">4</span></a>
		<ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
			<li><div class="dropdown-title">You have 4 new notification</div></li>
			<li><div class="notif-scroll scrollbar-outer">
					<div class="notif-center">
						<a href="#"><div class="notif-icon notif-primary"> <i class="fa fa-user-plus"></i>
					 </div>
					<div class="notif-content">
						<span class="block">New user registered</span>
						<span class="time">5 minutes ago</span></div></a>
					</div></div></li>
	        <li><a class="see-all" href="javascript:void(0);">See all notifications<i class="fa fa-angle-right"></i> </a></li>
		</ul>
	</li>
			  <!-- End Notification -->	
				
			  <!-- User Profile -->
<li class="nav-item topbar-user dropdown hidden-caret">
	<a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
		<div class="avatar-sm">
			<img src="../assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle"></div>
			    <span class="profile-username">
					<span class="op-7">Hi,</span> <span class="fw-bold">Hizrian</span>
					  </span>
					</a>
			<ul class="dropdown-menu dropdown-user animated fadeIn">
				<div class="dropdown-user-scroll scrollbar-outer">
					<li>
	    <div class="user-box">
	        <div class="avatar-lg"><img src="../assets/img/profile.jpg" alt="image profile" class="avatar-img rounded"></div>
				<div class="u-text">
					<h4>Hizrian</h4>
					    <p class="text-muted">hello@example.com</p>
                            <a href="profile.html" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                                <a href="profile.html" class="btn btn-xs btn-secondary btn-sm">Logout</a>
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




    