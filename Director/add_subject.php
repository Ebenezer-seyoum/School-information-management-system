<?php
include('directorHeader.php');

// ---- Variables ----
$subject_name = $abbreviation_name = $success = "";
$subject_name_err = $abbreviation_name_err = $section_err = $allErr = "";
$test = true;

// ---- Handle Form Submit ----
if (isset($_POST["register"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $section_ids = $_POST["section_ids"] ?? [];

    // Validate subject_name
    if (empty($_POST["subject_name"])) {
        $subject_name_err = "Please enter subject name";
        $test = false;
    } else {
        $subject_name = trim($_POST["subject_name"]);
    }

    // Validate abbreviation_name
    if (empty($_POST["abbreviation_name"])) {
        $abbreviation_name_err = "Please enter abbreviation";
        $test = false;
    } else {
        $abbreviation_name = trim($_POST["abbreviation_name"]);
    }

    // Validate sections
    if (empty($section_ids)) {
        $section_err = "Please select at least one section";
        $test = false;
    } else {
        // Define allowed class types for specific subjects
        $restricted_subjects = [
            'biology' => ['GENERAL', 'NATURAL'],
            'chemistry' => ['GENERAL', 'NATURAL'],
            'physics' => ['GENERAL', 'NATURAL'],
            'geography' => ['SOCIAL']
        ];

        // Check if the subject has restrictions
        $subject_key = strtolower($subject_name);
        if (array_key_exists($subject_key, $restricted_subjects)) {
            $allowed_class_types = $restricted_subjects[$subject_key];
            // Fetch class types of selected sections
            $invalid_sections = [];
            foreach ($section_ids as $sec_id) {
                $sec_id = (int)$sec_id;
                $query = mysqli_query($conn, "SELECT class_type FROM sections WHERE cid = $sec_id");
                $section = mysqli_fetch_assoc($query);
                if (!in_array($section['class_type'], $allowed_class_types)) {
                    $invalid_sections[] = $section['class_type'];
                }
            }
            if (!empty($invalid_sections)) {
                $section_err = "The subject '$subject_name' can only be registered for " . implode(" or ", $allowed_class_types) . " sections.";
                $test = false;
            }
        }
    }

    if ($test) {
        // Check if subject already exists
        $check = mysqli_query($conn, "SELECT * FROM subjects WHERE subject_name='" . mysqli_real_escape_string($conn, $subject_name) . "'");
        if (mysqli_num_rows($check) > 0) {
            $allErr = "This subject already exists";
        } else {
            // Insert subject
            $insert_subject = "INSERT INTO subjects (subject_name, abbreviation_name) VALUES ('" . mysqli_real_escape_string($conn, $subject_name) . "', '" . mysqli_real_escape_string($conn, $abbreviation_name) . "')";
            if (mysqli_query($conn, $insert_subject)) {
                $subject_id = mysqli_insert_id($conn);

                // Insert into curriculum_subjects for each selected section
                foreach ($section_ids as $sec_id) {
                    $sec_id = (int)$sec_id;
                    mysqli_query($conn, "INSERT INTO curriculum_subjects (class_id, subject_id) VALUES ($sec_id, $subject_id)");
                }

                $success = "✅ Successfully registered subject and assigned to sections!";
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
$preferredOrder = ['GENERAL', 'NATURAL', 'SOCIAL'];
$ordered_groups = [];
foreach ($preferredOrder as $key) {
    if (isset($grouped_sections[$key])) {
        $ordered_groups[$key] = $grouped_sections[$key];
        unset($grouped_sections[$key]);
    }
}
// Append any remaining groups (if any)
foreach ($grouped_sections as $k => $v) {
    $ordered_groups[$k] = $v;
}

// Preserve checked state when form validation fails
$preselectedIds = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $preselectedIds = array_map('intval', $_POST['section_ids'] ?? []);
}
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
                            <?php
                            // Prepare SweetAlert message once after POST
                            $swalType = '';
                            $swalTitle = '';
                            $swalMsg = '';
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                if (!empty($success)) {
                                    $swalType = 'success';
                                    $swalTitle = 'Subject Registered';
                                    $swalMsg = $success;
                                } else {
                                    $errs = array_filter([$allErr, $subject_name_err, $abbreviation_name_err, $section_err]);
                                    if (!empty($errs)) {
                                        $swalType = 'error';
                                        $swalTitle = 'Validation Error';
                                        $swalMsg = implode("\n", $errs);
                                    }
                                }
                            }
                            ?>
                            <?php if (!empty($swalType)) { ?>
                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function(){
                                        Swal.fire({
                                            icon: <?php echo json_encode($swalType); ?>,
                                            title: <?php echo json_encode($swalTitle); ?>,
                                            html: <?php echo json_encode(nl2br($swalMsg)); ?>
                                        });
                                    });
                                </script>
                            <?php } ?>

                            <div class="card-body">
                                <form method="POST">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="subject_name" class="form-label fw-bold">Subject Name</label>
                                            <input id="subject_name" type="text" class="form-control" name="subject_name" value="<?= htmlspecialchars($subject_name) ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="abbreviation_name" class="form-label fw-bold">Abbreviation</label>
                                            <input id="abbreviation_name" type="text" class="form-control" name="abbreviation_name" value="<?= htmlspecialchars($abbreviation_name) ?>">
                                        </div>
                                    </div>

                                    <!-- Sections Selection -->
                                    <div class="form-group mt-3">
                                        <div class="row g-3">
                                            <?php foreach ($ordered_groups as $class_type => $secs) { ?>
                                                <div class="col-12 col-md-4 section-group" data-class-type="<?= htmlspecialchars($class_type) ?>">
                                                    <div class="card h-100 border border-2 border-primary shadow-sm">
                                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                            <span class="fw-bold"><?= htmlspecialchars($class_type) ?></span>
                                                            <div>
                                                                <input type="checkbox" class="form-check-input select-all" data-group="<?= htmlspecialchars($class_type) ?>" id="select_all_<?= htmlspecialchars($class_type) ?>">
                                                                <label class="form-check-label small" for="select_all_<?= htmlspecialchars($class_type) ?>">Select All</label>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-2">
                                                            <div class="d-flex flex-wrap gap-2">
                                                                <?php foreach ($secs as $s) { 
                                                                    $cid = (int)$s['cid'];
                                                                    $checked = in_array($cid, $preselectedIds) ? 'checked' : '';
                                                                ?>
                                                                    <input class="btn-check <?= htmlspecialchars($class_type) ?>" type="checkbox" name="section_ids[]" value="<?= $cid ?>" id="section_<?= $cid ?>" <?= $checked ?>>
                                                                    <label class="btn btn-sm btn-outline-primary" for="section_<?= $cid ?>"><?= htmlspecialchars($s['section_name']) ?></label>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
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
    document.addEventListener('DOMContentLoaded', function(){
        const restrictedSubjects = {
            'biology': ['GENERAL', 'NATURAL'],
            'chemistry': ['GENERAL', 'NATURAL'],
            'physics': ['GENERAL', 'NATURAL'],
            'geography': ['SOCIAL']
        };

        function updateSectionVisibility() {
            const subjectInput = document.getElementById('subject_name').value.trim().toLowerCase();
            const sectionGroups = document.querySelectorAll('.section-group');
            sectionGroups.forEach(group => {
                const classType = group.dataset.classType;
                if (restrictedSubjects[subjectInput]) {
                    if (restrictedSubjects[subjectInput].includes(classType)) {
                        group.style.display = 'block';
                    } else {
                        group.style.display = 'none';
                        // Uncheck all checkboxes in hidden groups
                        group.querySelectorAll('input.btn-check').forEach(cb => cb.checked = false);
                        updateGroupUI(classType);
                    }
                } else {
                    group.style.display = 'block';
                }
            });
        }

        function updateGroupUI(group) {
            const selectAll = document.getElementById('select_all_' + group);
            const card = selectAll ? selectAll.closest('.card') : null;
            const chips = document.querySelectorAll('input.btn-check.' + group);
            const total = chips.length;
            let checked = 0;
            chips.forEach(cb => { if (cb.checked) checked++; });
            if (selectAll) {
                selectAll.checked = (checked === total && total > 0);
                selectAll.indeterminate = (checked > 0 && checked < total);
                selectAll.classList.remove('sa-primary', 'sa-success', 'sa-warning');
                if (checked === 0) selectAll.classList.add('sa-primary');
                else if (checked === total) selectAll.classList.add('sa-success');
                else selectAll.classList.add('sa-warning');
            }
            if (card) {
                card.classList.remove('border-primary', 'border-success', 'border-warning');
                if (checked === 0) card.classList.add('border-primary');
                else if (checked === total) card.classList.add('border-success');
                else card.classList.add('border-warning');
            }
        }

        // Select All -> toggle children and update UI
        document.querySelectorAll('.select-all').forEach(function(checkbox) {
            const group = checkbox.dataset.group;
            checkbox.addEventListener('change', function() {
                const chips = document.querySelectorAll('input.btn-check.' + group);
                chips.forEach(cb => cb.checked = this.checked);
                updateGroupUI(group);
            });
            // Initialize on load
            updateGroupUI(group);
        });

        // Individual chips -> update UI
        document.querySelectorAll('input.btn-check[name="section_ids[]"]').forEach(function(cb) {
            cb.addEventListener('change', function() {
                const classes = Array.from(this.classList);
                const group = classes.find(c => c !== 'btn-check');
                if (group) updateGroupUI(group);
            });
        });

        // Update section visibility on subject name change
        document.getElementById('subject_name').addEventListener('input', updateSectionVisibility);
        // Initial call to set visibility based on pre-filled subject name
        updateSectionVisibility();
    });
</script>
<style>
    .select-all.sa-primary { accent-color: #0d6efd; }
    .select-all.sa-success { accent-color: #198754; }
    .select-all.sa-warning { accent-color: #ffc107; }
</style>

<?php include('../Admin/footer.php'); ?>