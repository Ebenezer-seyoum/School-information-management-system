<?php
include('directorHeader.php');

// --- Check login ---
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);
if (!isset($_SESSION["uid"]) || $roleName != "Director") {
    echo "You are not authorized to view this page.";
    exit;
}

// --- Helpers ---
function fetchAcademicYears($conn) {
    $res = mysqli_query($conn, "SELECT DISTINCT academic_year 
                                FROM assign_instructor
                                ORDER BY academic_year DESC");
    $years = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $years[] = $r['academic_year'];
    }
    return $years;
}
function fetchAllClasses($conn, $year) {
    $res = mysqli_query($conn, "SELECT at.hid, at.section_id, at.academic_year,
                                       s.section_name, s.class_type, i.first_name, i.father_name
                                FROM assign_instructor at
                                LEFT JOIN sections s ON at.section_id = s.cid
                                LEFT JOIN users i ON at.instructor_id = i.uid
                                WHERE at.academic_year = '".mysqli_real_escape_string($conn,$year)."'
                                ORDER BY s.section_name ASC");
    $tmp = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $tmp[] = $r;
    }
    return $tmp;
}

// --- Main ---
$years = fetchAcademicYears($conn);
$selectedYear = $_GET['academic_year'] ?? ($years[0] ?? null);
$classes = $selectedYear ? fetchAllClasses($conn, $selectedYear) : [];
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">All Classes (Director View)</h3>
    </div>

    <form method="GET" class="mb-3">
      <label for="academic_year">Select Academic Year:</label>
      <select name="academic_year" id="academic_year" class="form-control w-auto d-inline-block">
        <?php foreach ($years as $year): ?>
          <option value="<?= htmlspecialchars($year) ?>" <?= ($year == $selectedYear) ? 'selected' : '' ?>>
            <?= htmlspecialchars($year) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Show Classes</button>
    </form>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-hover text-center align-middle">
          <thead class="table-secondary">
            <tr>
              <th>#</th>
              <th>Class</th>
              <th>Class Type</th>
              <th>Instructor</th>
              <th>Academic Year</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($classes) > 0): $no=1; ?>
              <?php foreach ($classes as $c): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($c['section_name']) ?></td>
                  <td><?= htmlspecialchars($c['class_type']) ?></td>
                  <td><?= htmlspecialchars($c['first_name'].' '.$c['father_name']) ?></td>
                  <td><?= htmlspecialchars($c['academic_year']) ?></td>
                  <td>
                    <button type="button"
                            class="btn btn-warning btn-sm view-students"
                            data-class-id="<?= (int)$c['hid'] ?>"
                            data-class-name="<?= htmlspecialchars($c['section_name'].' - '.$c['class_type']) ?>"
                            data-year="<?= htmlspecialchars($c['academic_year']) ?>">
                      Update Attendance
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-danger">No classes for this year.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentsModalLabel">Update Attendance</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="studentsTable">Loading...</div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const studentsModalEl = document.getElementById("studentsModal");
  const studentsModal = new bootstrap.Modal(studentsModalEl);
  const studentsTable = document.getElementById("studentsTable");

  // Open modal -> fetch attendance
  document.querySelectorAll(".view-students").forEach(btn => {
    btn.addEventListener("click", function () {
      const classId = this.dataset.classId;
      const className = this.dataset.className;
      const year = this.dataset.year;

      document.getElementById("studentsModalLabel").innerText =
        "Update Attendance • " + className + " (" + year + ")";

      studentsTable.innerHTML = "<div class='text-center p-3'>Loading...</div>";

      fetch("fetch_update_attendance.php?class_id=" + encodeURIComponent(classId) + "&semester=1")
        .then(res => res.text())
        .then(html => {
          studentsTable.innerHTML = html;
        })
        .catch(() => {
          studentsTable.innerHTML = "<div class='text-danger'>Failed to load attendance data.</div>";
        });

      studentsModal.show();
    });
  });

  // Delegated actions inside modal
  studentsTable.addEventListener('click', function(e){
    // Close button
    if (e.target && e.target.id === 'closeAttendanceBtn') {
      studentsModal.hide();
    }

    // Save attendance
    if (e.target && e.target.id === 'saveAttendanceBtn') {
      const form = document.getElementById('attendanceForm');
      if (!form) return;

      Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to update attendance records.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!'
      }).then((result) => {
        if (result.isConfirmed) {
          const btn = e.target;
          btn.disabled = true;
          const originalText = btn.innerText;
          btn.innerText = 'Updating...';

          const formData = new FormData(form);

          fetch('update_attendance_list.php', {
            method: 'POST',
            body: formData
          })
          .then(r => {
            if (!r.ok) throw new Error("HTTP " + r.status);
            return r.json();
          })
          .then(data => {
            if (data && data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'Attendance updated successfully.',
                timer: 2000,
                showConfirmButton: false
              });
              studentsModal.hide();
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: data && data.message ? data.message : 'Error updating attendance.'
              });
            }
          })
          .catch(err => {
            Swal.fire({
              icon: 'error',
              title: 'Network / JSON Error',
              text: 'Could not connect to server or invalid response. ' + err
            });
          })
          .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
          });
        }
      });
    }
  });

  // Search filter
  studentsTable.addEventListener('input', function(e){
    if (e.target && e.target.id === 'searchInput') {
      const q = e.target.value.toLowerCase();
      document.querySelectorAll('#studentTable tr').forEach(row => {
        const name = row.children[1] ? row.children[1].innerText.toLowerCase() : '';
        row.style.display = name.includes(q) ? '' : 'none';
      });
    }
  });

  // Semester change
  studentsTable.addEventListener('change', function(e){
    if (e.target && e.target.id === 'semesterSelect') {
      const form = document.getElementById('attendanceForm');
      const classId = form?.dataset.classId || '';
      const sem = e.target.value || 1;

      studentsTable.innerHTML = "<div class='text-center p-3'>Loading...</div>";
      fetch("fetch_update_attendance.php?class_id=" + encodeURIComponent(classId) + "&semester=" + encodeURIComponent(sem))
        .then(res => res.text())
        .then(html => { studentsTable.innerHTML = html; })
        .catch(() => { studentsTable.innerHTML = "<div class='text-danger'>Failed to load attendance data.</div>"; });
    }
  });

  // Cleanup
  studentsModalEl.addEventListener('hidden.bs.modal', function () {
    studentsTable.innerHTML = '';
  });
});
</script>

<?php include('../Admin/footer.php'); ?>
