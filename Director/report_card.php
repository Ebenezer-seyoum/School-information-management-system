<?php
include('directorHeader.php');
?>
<?php
$success = $allErr = "";
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);

if (isset($_SESSION["uid"]) && ($roleName == "Director")) {
?>
<!-- CSS for profile image -->
<style>
  .profile-img { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; }
  .modal-body { max-height: 400px; overflow-y: auto; }
  .form-inline { display: flex; gap: 10px; align-items: center; }
</style>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Generate Report Card</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Student</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Generate Report card</a></li>
      </ul>
    </div>

    <div class="main-content">
      <section class="section">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">All Classes</h4>
                <form method="GET" class="d-flex">
                  <input type="text" name="search" id="userSearch" class="form-control" 
                    placeholder="Search by section name or class type...">
                </form>
              </div>

              <div class="card-body table-responsive">
                <table class="table table-hover align-middle text-center" id="classTable">
                  <thead class="table-secondary">
                    <tr>
                      <th>#</th>
                      <th>Section Name</th>
                      <th>Class Type</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $classes = getAllSections();
                    $no = 1;
                    if (!empty($classes)) {
                      foreach ($classes as $cls) {
                        ?>
                        <tr>
                          <td><?= $no ?></td>
                          <td><?= $cls['section_name'] ?></td>
                          <td><?= $cls['class_type'] ?></td>
                          <td>
                            <button class="btn btn-info btn-sm view-students-btn" 
                              data-section-id="<?= $cls['cid'] ?>" 
                              data-section-name="<?= $cls['section_name'] ?>">
                              View Students
                            </button>
                          </td>
                        </tr>
                        <?php
                        $no++;
                      }
                    } else {
                      echo '<tr><td colspan="4" class="text-center text-danger">No classes found.</td></tr>';
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentsModalLabel">Students in Class</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-inline mb-3">
          <div>
            <label for="academicYearModal" class="form-label">Academic Year</label>
            <input type="text" id="academicYearModal" class="form-control" placeholder="e.g. 2024/2025">
          </div>
          <div>
            <label for="semesterModal" class="form-label">Semester</label>
            <select id="semesterModal" class="form-control">
              <option value="">Select Semester</option>
              <option value="1">Semester 1</option>
              <option value="2">Semester 2</option>
            </select>
          </div>
        </div>
        <div id="studentsList">Please select academic year and semester...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
// Event listener for "View Students" button
document.querySelectorAll('.view-students-btn').forEach(button => {
  button.addEventListener('click', function() {
    const sectionId = this.dataset.sectionId;
    const sectionName = this.dataset.sectionName;
    const modalTitle = document.getElementById('studentsModalLabel');
    const studentsList = document.getElementById('studentsList');

    // Show modal
    var studentsModal = new bootstrap.Modal(document.getElementById('studentsModal'));
    studentsModal.show();

    // Reset inputs
    const academicYearInput = document.getElementById('academicYearModal');
    const semesterInput = document.getElementById('semesterModal');
    academicYearInput.value = '';
    semesterInput.value = '';
    modalTitle.textContent = `Students in Class: ${sectionName}`;
    studentsList.innerHTML = 'Please select academic year and semester...';

    // Fetch function
    function fetchStudents() {
      const year = academicYearInput.value.trim();
      const semester = semesterInput.value;
      if(year && semester) {
        modalTitle.textContent = `Students in Class: ${sectionName} | ${year} | Semester ${semester}`;
        fetch(`ajax_fetch_students_results.php?section_id=${sectionId}&academic_year=${year}&semester=${semester}`)
          .then(res => res.text())
          .then(data => { studentsList.innerHTML = data; })
          .catch(err => { studentsList.innerHTML = 'Error fetching students'; });
      } else {
        modalTitle.textContent = `Students in Class: ${sectionName}`;
        studentsList.innerHTML = 'Please select academic year and semester...';
      }
    }

    academicYearInput.oninput = fetchStudents;
    semesterInput.onchange = fetchStudents;

  });
});
</script>

<?php
} else {
  echo "You are not authorized to view this page.";
}
include('../Admin/footer.php');
?>
