<?php
include('directorHeader.php'); 

// Handle form submission
if (isset($_POST['assign'])) {
    $instructor_id = $_POST['instructor_id'] ?? '';
    $section_id    = $_POST['section_id'] ?? '';
    $academic_year = $_POST['academic_year'] ?? '';

    if ($instructor_id && $section_id && $academic_year) {
        // Check for duplicate assignment
        $check = mysqli_query($conn, "SELECT * FROM assign_instructor 
                                      WHERE instructor_id='$instructor_id' 
                                      AND section_id='$section_id' 
                                      AND academic_year='$academic_year'");
        if (mysqli_num_rows($check) == 0) {
            // Insert new assignment
            if (mysqli_query($conn, "INSERT INTO assign_instructor (instructor_id, section_id, academic_year) 
                                     VALUES ('$instructor_id', '$section_id', '$academic_year')")) {
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                Swal.fire({
                    title: '✅ Success!',
                    text: 'Instructor assigned successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => { window.location.reload(); });
                </script>";
                exit();
            } else {
                echo "<div class='alert alert-danger'>Database Error: " . mysqli_error($conn) . "</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>⚠ This instructor is already assigned to that class for this academic year.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>⚠ Please fill in all required fields.</div>";
    }
}

// Load data
$instructors = mysqli_query($conn, "SELECT * FROM users WHERE user_type = 4 ORDER BY first_name ASC");
$sections    = mysqli_query($conn, "SELECT * FROM sections ORDER BY section_name ASC, class_type ASC");
?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Assign Instructor</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Manage Instructor</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Assign Instructor</a></li>
            </ul>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-primary text-white rounded-top-4 py-3">
                        <h4 class="mb-0"><i class="bi bi-person-check"></i> Assign Instructor to Class</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="post" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <!-- Instructor -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-person-badge"></i> Instructor</label>
                                    <select name="instructor_id" class="form-select select2" required>
                                        <option value="">-- Search & Select Instructor --</option>
                                        <?php while ($t = mysqli_fetch_assoc($instructors)): ?>
                                            <option value="<?= $t['uid'] ?>">
                                                <?= htmlspecialchars($t['first_name']) . ' ' . htmlspecialchars($t['father_name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Section -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-people"></i> Class / Section</label>
                                    <select name="section_id" class="form-select select2" required>
                                        <option value="">-- Search & Select Section --</option>
                                        <?php 
                                        $grouped_sections = [];
                                        while ($sec = mysqli_fetch_assoc($sections)) {
                                            $grouped_sections[$sec['class_type']][] = $sec;
                                        }
                                        foreach ($grouped_sections as $class_type => $secs): ?>
                                            <optgroup label="<?= htmlspecialchars($class_type) ?>">
                                                <?php foreach ($secs as $sec): ?>
                                                    <option value="<?= $sec['cid'] ?>">
                                                        <?= htmlspecialchars($sec['section_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Academic Year -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><i class="bi bi-calendar-event"></i> Academic Year</label>
                                    <input type="text" name="academic_year" id="academicYear" class="form-control" placeholder="e.g. 2024-2025" required>
                                </div>

                                <!-- Submit -->
                                <div class="col-12 mt-3">
                                    <button type="submit" name="assign" class="btn btn-primary w-100 py-2 fw-bold">
                                        <i class="bi bi-check-circle"></i> Assign Instructor
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    $('.select2').select2({
        placeholder: "Search...",
        allowClear: true,
        width: '100%'
    });
    const now = new Date();
    const year1 = now.getFullYear();
    const year2 = now.getMonth() >= 8 ? year1 + 1 : year1; 
    document.getElementById("academicYear").value = `${year1}-${year2}`;
});
</script>

<?php include('../Admin/footer.php'); ?>
