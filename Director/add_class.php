<?php
include('directorHeader.php');
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
        if (isset($_POST["register"]) && ($_SERVER["REQUEST_METHOD"] == "POST")) {
            // Validate section_name
            if (empty($_POST["Section_name"])) {
                $section_name_err = "Please enter your Section_name";
                $test = false;
            } else if (!preg_match('/^(9|10|11|12)[A-Za-z]$/', $_POST["Section_name"]) || validateIdNumber($_POST["Section_name"]) == 0) {
                $section_name_err = "Please enter a valid Section_name (e.g., 9A, 10B, 11C, or 12D)";
                $test = false;
            } else {
                $section_name = $_POST["Section_name"];
            }
            // Validate role_type
            if (empty($_POST["role_type"])) {
                $role_type_err = "Please select a Class Type";
                $test = false;
            } else if (validateName($_POST["role_type"]) == 0) {
                $role_type_err = "Please select a valid Class Type";
                $test = false;
            } else {
                $role_type = $_POST["role_type"];
            }
            if ($test == true) {
                if (sectionExist($ctid) == 0) {
                    if (add_section($section_name, $role_type) == 1) {
                        $success = "Successfully registered";
                        header('refresh:2');
                    } else {
                        $allErr = "There was an error while registering";
                    }
                } else {
                    $allErr = "This class has already been registered";
                }
            }
        }
        ?>

        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="row">
                    <div class="col-2"></div>
                    <div class="col-8 col-sm-8 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Register Class</h4>
                            </div>
                            <?php if (!empty($success)) { ?>
                                <div class="form-control bg-success">
                                    <?php echo $success; ?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($allErr)) { ?>
                                <div class="form-control bg-danger">
                                    <?php echo $allErr; ?>
                                </div>
                            <?php } ?>
                            <div class="card-body">
                                <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label for="Section_name">Section Name<span class="text-danger">*</span></label>
                                            <input id="Section_name" type="text" class="form-control" name="Section_name" placeholder="e.g., 9A" value="<?php echo htmlspecialchars($section_name); ?>"/>
                                            <span class="text-danger" id="section_name_err"><?php echo $section_name_err; ?></span>
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

<!-- JavaScript for Dynamic Role Type and Validation -->
<script>
function validateForm() {
    const sectionName = document.getElementById('Section_name').value.trim();
    const sectionNameErr = document.getElementById('section_name_err');
    const regex = /^(9|10|11|12)[A-Za-z]$/;
    
    if (!regex.test(sectionName)) {
        sectionNameErr.textContent = "Please enter a valid Section_name (e.g., 9A, 10B, 11C, or 12D)";
        return false;
    }
    return true;
}

document.getElementById('Section_name').addEventListener('input', function() {
    const sectionName = this.value.trim().toLowerCase();
    const roleTypeSelect = document.getElementById('role_type');
    const generalOption = roleTypeSelect.querySelector('option[value="general"]');
    const naturalOption = roleTypeSelect.querySelector('option[value="natural"]');
    const socialOption = roleTypeSelect.querySelector('option[value="social"]');
    const sectionNameErr = document.getElementById('section_name_err');
    
    // Extract the numeric part of the section name (e.g., "9A" -> "9")
    const sectionNumber = parseInt(sectionName.match(/\d+/));
    
    // Reset all options to hidden initially
    generalOption.style.display = 'none';
    naturalOption.style.display = 'none';
    socialOption.style.display = 'none';
    
    // Validate section name format
    const regex = /^(9|10|11|12)[A-Za-z]$/;
    if (!regex.test(sectionName)) {
        sectionNameErr.textContent = "Please enter a valid Section_name (e.g., 9A, 10B, 11C, or 12D)";
        // Show all options for invalid input
        generalOption.style.display = 'block';
        naturalOption.style.display = 'block';
        socialOption.style.display = 'block';
        roleTypeSelect.value = '';
    } else {
        sectionNameErr.textContent = ''; // Clear error message
        // Show appropriate options based on section number
        if (sectionNumber === 9 || sectionNumber === 10) {
            generalOption.style.display = 'block';
            roleTypeSelect.value = 'general'; // Default to General
        } else if (sectionNumber === 11 || sectionNumber === 12) {
            naturalOption.style.display = 'block';
            socialOption.style.display = 'block';
            roleTypeSelect.value = ''; // Reset to "-- Select --"
        }
    }
});
</script>

<?php
include('../Admin/footer.php');
?>