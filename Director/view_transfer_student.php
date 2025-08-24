<?php
include('directorHeader.php');

// --- Load sections ---
$sections = mysqli_query($conn,"SELECT * FROM sections ORDER BY class_type, section_name ASC");

// --- Load academic years ---
$years = mysqli_query($conn,"SELECT DISTINCT academic_year FROM assign_student ORDER BY academic_year DESC");
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Transfer Students</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Student Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Transfer Student</a></li>
      </ul>
    </div>

    <!-- Selection Card -->
    <div class="page-inner d-flex justify-content-center align-items-center" style="min-height:300px;">
      <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:700px; width:100%;">
        <div class="text-center mb-4">
          <h4 class="fw-bold">Select Section & Academic Year</h4>
          <p class="text-muted">Choose where you want to view and transfer students</p>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Academic Year</label>
            <select id="yearSelect" class="form-select form-select-lg">
              <option value="">-- Select Academic Year --</option>
              <?php while($y=mysqli_fetch_assoc($years)): ?>
                <option value="<?= htmlspecialchars($y['academic_year']) ?>">
                  <?= htmlspecialchars($y['academic_year']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Section</label>
            <select id="sectionSelect" class="form-select form-select-lg">
              <option value="">-- Select Section --</option>
              <?php
              $grouped_sections = [];
              mysqli_data_seek($sections,0);
              while($sec=mysqli_fetch_assoc($sections)) $grouped_sections[$sec['class_type']][] = $sec;
              foreach($grouped_sections as $type => $secs):
              ?>
                <optgroup label="<?= htmlspecialchars($type) ?>">
                  <?php foreach($secs as $s): ?>
                    <option value="<?= $s['cid'] ?>">
                      <?= htmlspecialchars($s['section_name'].' ('.$s['class_type'].')') ?>
                    </option>
                  <?php endforeach; ?>
                </optgroup>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12 d-grid mt-3">
            <button type="button" id="showStudentsBtn" class="btn btn-primary btn-lg">Show Students</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="studentsModalLabel"><strong>Section:</strong> 
        <span id="modalSectionName"></span> | <strong>Year:</strong> <span id="modalYear"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <!-- Search bar FULL WIDTH -->
        <div class="mb-3">
          <div class="search-box w-100">
            <div class="input-group">
              <span class="input-group-text bg-primary text-white"><i class="fas fa-search"></i></span>
              <input type="text" id="studentSearch" class="form-control search-input" placeholder="Search by ID, Name, or Role...">
              <button class="btn btn-primary" type="button" id="searchTrigger" aria-label="Search">Search</button>
            </div>
          </div>
        </div>

        <table class="table table-bordered table-hover mt-2">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>ID</th>
              <th>Name</th>
              <th>Father Name</th>
              <th>Class Type</th>
              <th>Transfer</th>
            </tr>
          </thead>
          <tbody id="studentsTableBody"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){
    const sectionMap = {};
    <?php
    mysqli_data_seek($sections, 0);
    while($sec=mysqli_fetch_assoc($sections)):
    ?>
        sectionMap['<?= $sec['cid'] ?>'] = '<?= htmlspecialchars($sec['section_name'].' ('.$sec['class_type'].')') ?>';
    <?php endwhile; ?>

  $('#showStudentsBtn').click(function(){
        const sectionId = $('#sectionSelect').val();
        const year = $('#yearSelect').val();

        if(!sectionId || !year){ 
            Swal.fire("Error","Please select both academic year and section","error");
            return; 
        }

    $.post('fetch_section_students.php', { section_id: sectionId, academic_year: year }, function(data){
      let res = [];
      try { res = (typeof data === 'string') ? JSON.parse(data) : data; } catch(e){ res = []; }
            let html = '';

            if(res.length === 0){
                html = '<tr><td colspan="7" class="text-center">No students found.</td></tr>';
            } else {
                let counter = 1;
                res.forEach(stu => {
                    html += `<tr>
                        <td>${counter++}</td>
                        <td>${stu.student_id}</td>
                        <td>${stu.first_name}</td>
                        <td>${stu.father_name}</td>
                        <td>${stu.class_type}</td>
                        <td>
                          <button class="btn btn-sm btn-outline-success transferBtn" 
                                  data-id="${stu.sid}" 
                                  data-section="${sectionId}"
                                  title="Transfer">
                            <i class="fas fa-random"></i> Transfer
                          </button>
                        </td>
                    </tr>`;
                });
            }

            $('#studentsTableBody').html(html);
            $('#modalSectionName').text(sectionMap[sectionId] || '');
            $('#modalYear').text(year);
            $('#studentsModal').modal('show');
        });
    });

    // --- Dynamic search ---
    $('#studentSearch').on('keyup', function(){
        let value = $(this).val().toLowerCase();
        $("#studentsTableBody tr").filter(function(){
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // --- Transfer button with SweetAlert2 ---
  $(document).on('click', '.transferBtn', function(){
        const studentId = $(this).data('id');
        const currentSectionId = $(this).data('section');
        const currentSectionName = sectionMap[currentSectionId];
    const academicYear = $('#yearSelect').val();

        // Build dropdown of new sections
        let sectionOptions = `<select id="newSectionSelect" class="swal2-select">`;
        sectionOptions += `<option value="">-- Select New Section --</option>`;
        <?php
        mysqli_data_seek($sections,0);
        while($sec=mysqli_fetch_assoc($sections)): ?>
            sectionOptions += `<option value="<?= $sec['cid'] ?>"><?= htmlspecialchars($sec['section_name'].' ('.$sec['class_type'].')') ?></option>`;
        <?php endwhile; ?>
        sectionOptions += `</select>`;

    Swal.fire({
            title: "Transfer Student",
            html: `
              <div class="text-start">
                <label><strong>Current Section:</strong></label>
                <input type="text" class="form-control mb-2" value="${currentSectionName}" readonly>
                <label><strong>New Section:</strong></label>
                ${sectionOptions}
              </div>
            `,
            showCancelButton: true,
            confirmButtonText: "Confirm Transfer",
            cancelButtonText: "Cancel",
            preConfirm: () => {
                const newSection = Swal.getPopup().querySelector('#newSectionSelect').value;
                if(!newSection){
                    Swal.showValidationMessage("Please select a new section");
                }
                return { newSection: newSection };
            }
        }).then((result) => {
            if(result.isConfirmed){
        const newSectionId = result.value.newSection;
        if(String(newSectionId) === String(currentSectionId)){
          Swal.fire("Notice","Student is already in the selected section.","info");
          return;
        }
        $.post('transfer_student_action.php', { student_id: studentId, new_section: newSectionId, academic_year: academicYear }, function(resp){
          let r = {};
          try { r = (typeof resp === 'string') ? JSON.parse(resp) : resp; } catch(e){ r = {status:'error', message:'Unexpected response'}; }
          if(r.status === 'success'){
            Swal.fire("Success", r.message || 'Student transferred successfully', "success");
            $('#showStudentsBtn').click(); // reload students
          } else {
            Swal.fire("Error", r.message || 'Transfer failed', "error");
          }
        });
            }
        });
    });
});
</script>

<?php include('../Admin/footer.php'); ?>
