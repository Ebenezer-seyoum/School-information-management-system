<?php
include('directorHeader.php');

// ---- Variables ----
$subject_name = $abbreviation_name = $success = "";
$subject_name_err = $abbreviation_name_err = $section_err = $allErr = "";
$test = true;

// ---- Handle Form Submit ----
if (isset($_POST["register"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $section_ids = $_POST["section_ids"] ?? [];

    // validate subject_name
    if (empty($_POST["subject_name"])) {
        $subject_name_err = "Please enter subject name";
        $test = false;
    } else {
        $subject_name = trim($_POST["subject_name"]);
    }

    // validate abbreviation_name
    if (empty($_POST["abbreviation_name"])) {
        $abbreviation_name_err = "Please enter abbreviation";
        $test = false;
    } else {
        $abbreviation_name = trim($_POST["abbreviation_name"]);
    }

    // validate sections
    if (empty($section_ids)) {
        $section_err = "Please select at least one section";
        $test = false;
    }

    if ($test) {
        // check if subject already exists
        $check = mysqli_query($conn, "SELECT * FROM subjects WHERE subject_name='" . mysqli_real_escape_string($conn,$subject_name) . "'");
        if (mysqli_num_rows($check) > 0) {
            $allErr = "This subject already exists";
        } else {
            // insert subject
            $insert_subject = "INSERT INTO subjects (subject_name, abbreviation_name) VALUES ('" . mysqli_real_escape_string($conn,$subject_name) . "', '" . mysqli_real_escape_string($conn,$abbreviation_name) . "')";
            if (mysqli_query($conn, $insert_subject)) {
                $subject_id = mysqli_insert_id($conn);

                // insert into curriculum_subjects for each selected section
                foreach ($section_ids as $sec_id) {
                    $sec_id = (int)$sec_id;
                    mysqli_query($conn, "INSERT INTO curriculum_subjects (class_id, subject_id) VALUES ($sec_id, $subject_id)");
                }

                $success = "✅ Successfully registered subject and assigned to sections!";
                header("refresh:2"); // reload after 2 seconds
            } else {
                $allErr = "Error while inserting subject: " . mysqli_error($conn);
            }
        }
    }
}

// ---- Fetch Sections ----
$sections = mysqli_query($conn, "SELECT cid, section_name, class_type FROM sections ORDER BY class_type, section_name ASC");

// ---- Group Sections by class_type ----
$grouped_sections = [];
while ($row = mysqli_fetch_assoc($sections)) {
    $grouped_sections[$row['class_type']][] = $row;
}
// Enforce display order: GENERAL and NATURAL on first row, SOCIAL below
$preferredOrder = ['GENERAL','NATURAL','SOCIAL'];
$ordered_groups = [];
foreach ($preferredOrder as $key) {
    if (isset($grouped_sections[$key])) {
        $ordered_groups[$key] = $grouped_sections[$key];
        unset($grouped_sections[$key]);
    }
}
// Append any remaining groups (if any)
foreach ($grouped_sections as $k=>$v) { $ordered_groups[$k] = $v; }
?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Register Subject</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Manage Subject</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Register Subject</a></li>
            </ul>
        </div>

        <div class="main-content">
            <section class="section">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card shadow-lg">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0">Register Subject</h4>
                            </div>

                            <?php if (!empty($success)) { ?>
                                <div class="alert alert-success m-3"><?php echo $success; ?></div>
                            <?php } ?>
                            <?php if (!empty($allErr)) { ?>
                                <div class="alert alert-danger m-3"><?php echo $allErr; ?></div>
                            <?php } ?>

                            <div class="card-body">
                                <form method="POST">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="subject_name" class="form-label fw-bold">Subject Name</label>
                                            <input id="subject_name" type="text" class="form-control" name="subject_name" value="<?= htmlspecialchars($subject_name) ?>">
                                            <span class="text-danger"><?= $subject_name_err ?></span>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="abbreviation_name" class="form-label fw-bold">Abbreviation</label>
                                            <input id="abbreviation_name" type="text" class="form-control" name="abbreviation_name" value="<?= htmlspecialchars($abbreviation_name) ?>">
                                            <span class="text-danger"><?= $abbreviation_name_err ?></span>
                                        </div>
                                    </div>

                                    <!-- Sections Selection -->
                                    <div class="form-group mt-3">
                                        <label class="fw-bold mb-2">Assign to Sections</label>
                                                <div class="row g-3"> <!-- g-3 = spacing between cards -->
                                                    <?php foreach ($ordered_groups as $class_type => $secs) { ?>
                                                    <div class="col-12 col-md-6"> <!-- 2 per row on md+, full width on xs -->
                                                    <div class="card h-100 border shadow-sm">
                                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                            <span class="fw-bold"><?= htmlspecialchars($class_type) ?></span>
                                                            <div>
                                                                <input type="checkbox" class="form-check-input select-all" data-group="<?= htmlspecialchars($class_type) ?>" id="select_all_<?= htmlspecialchars($class_type) ?>">
                                                                <label class="form-check-label small" for="select_all_<?= htmlspecialchars($class_type) ?>">Select All</label>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-2" style="max-height:220px; overflow-y:auto;">
                                                            <?php foreach ($secs as $s) { ?>
                                                                <div class="form-check my-1">
                                                                    <input class="form-check-input <?= htmlspecialchars($class_type) ?>" type="checkbox" name="section_ids[]" value="<?= $s['cid'] ?>" id="section_<?= $s['cid'] ?>">
                                                                    <label class="form-check-label" for="section_<?= $s['cid'] ?>"><?= htmlspecialchars($s['section_name']) ?></label>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <span class="text-danger"><?= $section_err ?></span>
                                    </div>

                                    <div class="form-group mt-4 text-center">
                                        <input type="submit" name="register" class="btn btn-primary btn-lg px-5" value="Register Subject">
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

<script>
    // Handle Select All for each group
    document.querySelectorAll('.select-all').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var group = this.dataset.group;
            var checkboxes = document.querySelectorAll('.' + group);
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    });
</script>

<?php include('../Admin/footer.php'); ?>
