<?php
include('adminHeader.php');
$uid = $_GET['uid'] ?? null;

$user = null;
if ($uid) {
    $user = getUserByID($uid); // Get one user row
}
?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h2 class="fw-bold text-uppercase text-primary">User Profile Overview</h2>
        <p class="text-muted">Detailed information for system user account</p>
        <hr class="w-25 mx-auto">
    </div>

    <?php if (!empty($user)) { ?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card border-0 shadow-lg">
    <div class="card-body p-4">
        <!-- Profile Image -->
        <div class="text-center mb-4">
            <?php if (!empty($user['profile_pic'])): ?>
                <img src="<?= htmlspecialchars($user['profile_pic']) ?>" 
                     alt="Profile Picture" 
                     class="rounded-circle border border-2" 
                     style="width: 100px; height: 100px; object-fit: cover;">
            <?php else: ?>
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                     style="width: 80px; height: 80px; font-size: 28px;">
                    <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                </div>
            <?php endif; ?>

            <h4 class="mt-3 text-primary"><?= htmlspecialchars($user['first_name']) ?>'s Profile</h4>
            <span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($user['user_type']) ?></span>
        </div>

        <!-- User Details -->
       <!-- User Details Table -->
<table class="table table-bordered table-hover mt-4">
    <tbody class="table-light">
        <tr>
            <th style="width: 40%;">First Name</th>
            <td><?= htmlspecialchars($user['first_name']) ?></td>
        </tr>
        <tr>
            <th>Father's Name</th>
            <td><?= htmlspecialchars($user['father_name']) ?></td>
        </tr>
        <tr>
            <th>Grandfather's Name</th>
            <td><?= htmlspecialchars($user['gfather_name']) ?></td>
        </tr>
        <tr>
            <th>Gender</th>
            <td><?= htmlspecialchars($user['gender']) ?></td>
        </tr>
        <tr>
            <th>Email Address</th>
            <td><?= htmlspecialchars($user['email']) ?></td>
        </tr>
        <tr>
            <th>Phone Number</th>
            <td><?= htmlspecialchars($user['phone']) ?></td>
        </tr>
        <tr>
            <th>Username</th>
            <td><?= htmlspecialchars($user['username']) ?></td>
        </tr>
    </tbody>
</table>
            </div>
            </div>


            <div class="text-center mt-4">
                <a href="javascript:history.back()" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left"></i> Back to User List
                </a>
            </div>
        </div>
    </div>
    <?php } else { ?>
        <div class="alert alert-warning text-center">No user details found.</div>
    <?php } ?>
</div>


<?php include('footer.php'); ?>

