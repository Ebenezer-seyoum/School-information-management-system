<?php
include('cdheader.php') ;
?>
<?php
$case_id = null;
 if (isset($_GET['case_id'])) 
    $case_id = $_GET['case_id'];
 if (isset($_GET['cid'])) 
    $cid = $_GET['cid'];
?>
<!-- Page Header -->
   <div class="container">
         <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Update Case_detail</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
         <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">Update Case</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
         <li class="nav-item"><a href="#">detail</a></li>
    </ul>
 </div>
         <!-- Main Content -->
<div class="main-content">
   <section class="section">
       <div class="row">
         <div class="col-2"></div>
             <div class="col-8 col-sm-8 col-lg-8">
                <div class="card ">
                   <div class="card-header">
                    <h4>Detail</h4>
                     </div>
<div class="card-body">
    <div class="row">
        <div class="form-group col-6 text-center">
            <label for="profile">Profile Detail</label><br>
            <a href="viewCase_detailByid.php?case_id=<?= $case_id;?>&cid=<?= $cid;?>" class="btn btn-primary mt-2">View Profile Detail</a>
        </div>
        <div class="form-group col-6 text-center">
            <label for="file">File Detail</label><br>
            <a href="case_fileByid.php?case_id=<?= $case_id;?>&cid=<?= $cid;?>" class="btn btn-secondary mt-2">View File Detail</a>
        </div>
    </div>
</div>

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
