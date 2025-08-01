<?php
include('loHeader.php');
$kid = $_GET['kid'] ?? null;

$cases = [];
if ($kid) {
    $cases = getAllCasesByCid($kid); // Get all rows related to this KID
}
?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h2 class="fw-bold">Litigant Information</h2>
        <hr class="w-25 mx-auto">
    </div>

    <?php if (!empty($cases)) { ?>
    <div class="row justify-content-center">
        <?php foreach ($cases as $case): ?>
            <?php
                $region = getRegionById($case["region"]);
                $zone = getZoneById($case["zone"]);
                $woreda = getWoredaById($case["woreda"]);
            ?>
            <div class="col-md-6 col-lg-6 d-flex align-items-stretch mb-4">
                <div class="card shadow border-0 w-100">
                    <div class="card-body p-4">
                        <h5 class="text-center text-primary fw-bold mb-4">
                            <?= htmlspecialchars($case['litigant_type']) ?> -  
                            <?= htmlspecialchars($case['first_name']) ?>
                            <?= htmlspecialchars($case['father_name']) ?>
                        </h5>
                        <table class="table table-bordered table-hover mt-4">
                            <tbody class="table-light">
                                <tr>
                                    <th>First Name</th>
                                    <td><?= htmlspecialchars($case['first_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Father Name</th>
                                    <td><?= htmlspecialchars($case['father_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Grandfather</th>
                                    <td><?= htmlspecialchars($case['grandfather_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td><?= htmlspecialchars($case['gender']) ?></td>
                                </tr>
                                <tr>
                                    <th>Region</th>
                                    <td><?= htmlspecialchars($region) ?></td>
                                </tr>
                                <tr>
                                    <th>Zone</th>
                                    <td><?= htmlspecialchars($zone) ?></td>
                                </tr>
                                <tr>
                                    <th>Woreda</th>
                                    <td><?= htmlspecialchars($woreda) ?></td>
                                </tr>
                                <tr>
                                    <th>Kebele</th>
                                    <td><?= htmlspecialchars($case['kebele']) ?></td>
                                </tr>
                                <tr>
                                    <th>Wogen</th>
                                    <td><?= htmlspecialchars($case['wogen']) ?></td>
                                </tr>
                                <tr>
                                    <th>Argument Money</th>
                                    <td><?= htmlspecialchars($case['argument_money']) ?></td>
                                </tr>
                                <tr>
                                    <th>Judgement Money</th>
                                    <td><?= htmlspecialchars($case['judgement_money']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
   <div class="text-center mt-4">
                <a href="javascript:history.back()" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left"></i> Back to Litigant List
                </a>
            </div>
    <?php } else { ?>
        <div class="alert alert-warning text-center">No litigant details found.</div>
    <?php } ?>
</div>

<?php include('../Admin/footer.php'); ?>
