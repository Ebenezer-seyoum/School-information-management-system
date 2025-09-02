<?php
include('directorHeader.php');

// Fetch all teachers (build a list so JSON preserves IDs)
$teachers_q = mysqli_query($conn, "SELECT uid, CONCAT(first_name,' ',father_name) AS full_name FROM users WHERE user_type=1 ORDER BY first_name ASC");
$teachers_list = [];
while($t = mysqli_fetch_assoc($teachers_q)){
  $teachers_list[] = [
    'uid' => (int)$t['uid'],
    'name' => htmlspecialchars($t['full_name'])
  ];
}
?>

<!-- page header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Assign Teacher</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Teacher Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Assign Teacher</a></li>
      </ul>
    </div>
<!-- end page header -->

    <!-- Section + Academic Year Selection (Centered) -->
    <div class="d-flex justify-content-center mb-4">
      <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:700px; width:100%;">
        <div class="text-center mb-3">
          <h5 class="fw-bold">Select Section and Academic Year</h5>
          <p class="text-muted">Choose the section and academic year to view/assign subjects to teachers</p>
        </div>
        <div class="row g-3 align-items-end">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Section</label>
            <select id="sectionSelect" class="form-select form-select-lg">
              <option value="">-- Select Section --</option>
              <?php
              $sections = mysqli_query($conn,"SELECT * FROM sections ORDER BY class_type, section_name ASC");
              $grouped_sections = [];
              while($sec=mysqli_fetch_assoc($sections)) $grouped_sections[$sec['class_type']][] = $sec;
              foreach($grouped_sections as $type => $secs): ?>
                <optgroup label="<?= htmlspecialchars($type) ?>">
                  <?php foreach($secs as $s): ?>
                    <option value="<?= $s['cid'] ?>"><?= htmlspecialchars($s['section_name']) ?></option>
                  <?php endforeach; ?>
                </optgroup>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Academic Year</label>
            <select id="academicYear" class="form-select form-select-lg">
              <option value="">-- Select Academic Year --</option>
              <?php
              $yrs = mysqli_query($conn, "SELECT DISTINCT academic_year FROM assign_student ORDER BY academic_year DESC");
              while($r = mysqli_fetch_assoc($yrs)){
                $ay = htmlspecialchars($r['academic_year']);
                echo "<option value='{$ay}'>{$ay}</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-2 d-grid">
            <button type="button" id="showSubjectsBtn" class="btn btn-primary btn-md">Assign Teacher</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Subjects Modal -->
<div class="modal fade" id="subjectsModal" tabindex="-1" aria-labelledby="subjectsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="subjectsModalLabel">Subjects & Assign Teachers</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="assignTeachersForm">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Subject Name</th>
                <th>Assign Teacher</th>
              </tr>
            </thead>
            <tbody id="subjectsTableBody"></tbody>
          </table>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" form="assignTeachersForm">Assign Teachers</button>
      </div>
    </div>
  </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){
  // Fallback teacher list
  const fallbackTeachers = <?php echo json_encode($teachers_list); ?>;

  // Show subjects modal
  $('#showSubjectsBtn').click(function(){
    const sectionId = $('#sectionSelect').val();
    const year = $('#academicYear').val();
    if(!sectionId || !year){
      Swal.fire('Warning', 'Please select section and academic year', 'warning');
      return;
    }

    $.post('fetch_section_teachers.php', { section_id: sectionId, academic_year: year }, function(res){
      let html = '';
      if(res.length === 0){
        html = '<tr><td colspan="3" class="text-center">No subjects found for this section.</td></tr>';
      } else {
        res.forEach((item, index) => {
          const teacherEntries = item.teachers && typeof item.teachers === 'object' && Object.keys(item.teachers).length
            ? Object.entries(item.teachers).map(([tid, name]) => ({ uid: tid, name }))
            : fallbackTeachers;

          // Always show dropdown, pre-selecting the assigned teacher if any
          const options = [
            `<option value="">Assign Teacher</option>`,
            ...teacherEntries.map(t => `<option value="${t.uid}" ${t.uid == item.assigned_teacher ? 'selected' : ''}>${t.name}</option>`)
          ].join('');
          html += `<tr>
            <td>${index+1}</td>
            <td>${item.subject_name}</td>
            <td>
              <select name="subject_teacher[${item.suid}]" class="form-select select2" data-subject="${item.subject_name}">
                ${options}
              </select>
            </td>
          </tr>`;
        });
      }
      $('#subjectsTableBody').html(html);
      const $modal = $('#subjectsModal');
      $modal.modal('show');

      // Initialize Select2 within modal
      $('.select2').each(function(){
        const $sel = $(this);
        $sel.select2({
          placeholder: 'Select teacher...',
          width: '100%',
          dropdownParent: $modal,
          allowClear: true
        });
      });

      // Track selected teachers and update dropdowns
      const updateTeacherDropdowns = function(){
        const selectedTeachers = [];
        $('.select2').each(function(){
          const val = $(this).val();
          if(val) selectedTeachers.push(val);
        });

        $('.select2').each(function(){
          const $select = $(this);
          const currentVal = $select.val();
          const suid = $select.attr('name').match(/\[(\d+)\]/)[1];
          
          const teacherOptions = res.find(item => item.suid == suid)?.teachers
            ? Object.entries(res.find(item => item.suid == suid).teachers).map(([tid, name]) => ({ uid: tid, name }))
            : fallbackTeachers;

          const options = [
            `<option value="">Assign Teacher</option>`,
            ...teacherOptions
              .filter(t => !selectedTeachers.includes(t.uid) || t.uid === currentVal)
              .map(t => `<option value="${t.uid}" ${t.uid === currentVal ? 'selected' : ''}>${t.name}</option>`)
          ].join('');

          const wasOpen = $select.data('select2') && $select.data('select2').isOpen();
          $select.html(options);
          $select.val(currentVal);
          $select.trigger('change.select2');
          if(wasOpen) $select.select2('open');
        });
      };

      $('.select2').on('select2:select select2:unselect', function(){
        updateTeacherDropdowns();
      });
    }, 'json').fail(function(){
      Swal.fire('Error', 'Failed to fetch subjects. Please try again.', 'error');
    });
  });

  // Function to fetch and display teacher's current assignments
  const showTeacherAssignments = function(teacherId, teacherName, subjectName) {
    const academicYear = $('#academicYear').val();
    if (!teacherId || !academicYear) return;

    $.post('fetch_teacher_assignments.php', { teacher_id: teacherId, academic_year: academicYear }, function(res) {
      if (res.error) {
        Swal.fire('Error', res.error, 'error');
        return;
      }

      if (res.length === 0) {
        Swal.fire({
          title: 'Teacher Assignments',
          text: `${teacherName} is not assigned to any subjects in ${academicYear}.`,
          icon: 'info'
        });
      } else {
        let assignmentList = '<ul>';
        res.forEach(assignment => {
          assignmentList += `<li><strong>${assignment.class_type} - ${assignment.section_name}</strong>: ${assignment.subject_name}</li>`;
        });
        assignmentList += '</ul>';

        Swal.fire({
          title: `Assignments for ${teacherName}`,
          html: `Teacher is currently assigned to the following subjects in ${academicYear}:<br>${assignmentList}`,
          icon: 'info'
        });
      }
    }, 'json').fail(function() {
      Swal.fire('Error', 'Failed to fetch teacher assignments. Please try again.', 'error');
    });
  };

  // Initialize Select2 within modal
  $('.select2').each(function(){
    const $sel = $(this);
    $sel.select2({
      placeholder: 'Select teacher...',
      width: '100%',
      dropdownParent: $('#subjectsModal'),
      allowClear: true
    });

    // Show teacher assignments when a teacher is selected
    $sel.on('select2:select', function(e) {
      const teacherId = $(this).val();
      const teacherName = $(this).find('option:selected').text();
      const subjectName = $(this).data('subject');
      if (teacherId) {
        showTeacherAssignments(teacherId, teacherName, subjectName);
      }
      updateTeacherDropdowns(); // Existing function to handle duplicates within section
    });

    // Existing unselect handler
    $sel.on('select2:unselect', function() {
      updateTeacherDropdowns();
    });
  });

  // Submit assigned teachers
  $('#assignTeachersForm').submit(function(e){
    e.preventDefault();
    const sectionId = $('#sectionSelect').val();
    const year = $('#academicYear').val();

    let unassignedCount = 0;
    const selectedTeachers = new Map();
    let hasDuplicates = false;
    let duplicateDetails = '';
    const $selects = $('#subjectsTableBody select');

    if ($selects.length === 0) {
      Swal.fire('Info', 'No subjects available to assign.', 'info');
      return;
    }

    // Check for duplicates within the current section
    $selects.each(function(){
      const $select = $(this);
      const val = $select.val();
      const subjectName = $select.data('subject');
      const teacherName = $select.find('option:selected').text();

      if (!val) {
        unassignedCount++;
      } else if (selectedTeachers.has(val)) {
        hasDuplicates = true;
        const prevSubject = selectedTeachers.get(val).subject;
        duplicateDetails += `Teacher "${teacherName}" is assigned to both "${prevSubject}" and "${subjectName}" in this section.<br>`;
      } else {
        selectedTeachers.set(val, { subject: subjectName, teacher: teacherName });
      }
    });

    if (hasDuplicates) {
      Swal.fire({
        title: 'Error',
        html: `A teacher cannot be assigned to multiple subjects in the same section:<br>${duplicateDetails}`,
        icon: 'error'
      });
      return;
    }

    // Check for existing assignments across all sections
    const checkExistingAssignments = function(callback) {
      const teacherIds = Array.from(selectedTeachers.keys());
      if (teacherIds.length === 0) {
        callback([]);
        return;
      }

      $.post('fetch_teacher_assignments.php', { teacher_id: teacherIds.join(','), academic_year: year }, function(res) {
        let conflicts = '';
        res.forEach(assignment => {
          const teacherId = assignment.teacher_id; // Assume backend includes teacher_id in response
          const teacherName = selectedTeachers.get(teacherId)?.teacher || 'Unknown';
          conflicts += `Teacher "${teacherName}" is already assigned to "${assignment.subject_name}" in ${assignment.class_type} - ${assignment.section_name}.<br>`;
        });

        callback(conflicts);
      }, 'json').fail(function() {
        Swal.fire('Error', 'Failed to check existing assignments. Please try again.', 'error');
      });
    };

    const submitForm = function() {
      let formData = '';
      $('#subjectsTableBody select').each(function(){
        const val = $(this).val();
        if (val) formData += encodeURIComponent($(this).attr('name')) + '=' + encodeURIComponent(val) + '&';
      });
      formData += `section_id=${sectionId}&academic_year=${year}`;

      $.post('assign_teachers_action.php', formData, function(res){
        Swal.fire({
          title: 'Success!',
          text: res.message || 'Selected teachers assigned successfully.',
          icon: 'success'
        }).then(()=>{
          $('#subjectsModal').modal('hide');
          $('#showSubjectsBtn').trigger('click'); // Refresh modal
        });
      }, 'json').fail(function(){
        Swal.fire({
          title: 'Error',
          text: 'Failed to assign teachers. Please try again.',
          icon: 'error'
        });
      });
    };

    checkExistingAssignments(function(conflicts) {
      if (conflicts) {
        Swal.fire({
          title: 'Warning: Existing Assignments',
          html: `Some teachers are already assigned to other subjects:<br>${conflicts}<br>Do you want to proceed with these assignments?`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed',
          cancelButtonText: 'No, go back'
        }).then((result) => {
          if (result.isConfirmed) {
            if (unassignedCount > 0) {
              Swal.fire({
                title: 'Some subjects are not assigned!',
                text: `You have ${unassignedCount} subjects without a teacher. Do you want to assign only the selected ones?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, assign selected',
                cancelButtonText: 'No, go back'
              }).then((result) => {
                if (result.isConfirmed) {
                  submitForm();
                }
              });
            } else {
              submitForm();
            }
          }
        });
      } else {
        if (unassignedCount > 0) {
          Swal.fire({
            title: 'Some subjects are not assigned!',
            text: `You have ${unassignedCount} subjects without a teacher. Do you want to assign only the selected ones?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, assign selected',
            cancelButtonText: 'No, go back'
          }).then((result) => {
            if (result.isConfirmed) {
              submitForm();
            }
          });
        } else {
          submitForm();
        }
      }
    });
  });
});
</script>

<?php include('../Admin/footer.php'); ?>

<!-- Additional Backend File: fetch_teacher_assignments.php -->
<!-- Save this as a separate file named fetch_teacher_assignments.php in the same directory -->
<?php
// fetch_teacher_assignments.php
include('db_connection.php'); // Your database connection file

header('Content-Type: application/json');

$teacher_id_input = isset($_POST['teacher_id']) ? $_POST['teacher_id'] : 0;
$academic_year = isset($_POST['academic_year']) ? mysqli_real_escape_string($conn, $_POST['academic_year']) : '';

if (!$teacher_id_input || !$academic_year) {
    echo json_encode(['error' => 'Invalid teacher ID or academic year']);
    exit;
}

// Handle multiple teacher IDs (comma-separated)
$teacher_ids = array_map('intval', explode(',', $teacher_id_input));
$teacher_ids_str = implode(',', $teacher_ids);

// Fetch all subjects assigned to the teacher(s) for the academic year
$query = "
    SELECT at.uid AS teacher_id, s.subject_name, sec.section_name, sec.class_type
    FROM assign_teacher at
    JOIN subjects s ON at.suid = s.suid
    JOIN sections sec ON at.cid = sec.cid
    WHERE at.uid IN ($teacher_ids_str) AND at.academic_year = '$academic_year'
    ORDER BY at.uid, sec.class_type, sec.section_name, s.subject_name
";

$result = mysqli_query($conn, $query);
$assignments = [];

while ($row = mysqli_fetch_assoc($result)) {
    $assignments[] = [
        'teacher_id' => (int)$row['teacher_id'],
        'subject_name' => htmlspecialchars($row['subject_name']),
        'section_name' => htmlspecialchars($row['section_name']),
        'class_type' => htmlspecialchars($row['class_type'])
    ];
}

echo json_encode($assignments);
exit;
?>