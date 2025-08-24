<?php
include('directorHeader.php');

// --- Load sections ---
$sections = mysqli_query($conn,"SELECT * FROM sections ORDER BY class_type, section_name ASC");
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">View Subject</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Subject Management</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">View Subject</a></li>
      </ul>
    </div>
<!-- Section Selection (Professional & Centered) -->
<div class="container">
  <div class="page-inner d-flex justify-content-center align-items-center" style="min-height:300px;">
    <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width:600px; width:100%;">
      <div class="text-center mb-4">
        <h4 class="fw-bold">Select Section to View Subjects</h4>
        <p class="text-muted">Choose a section from the dropdown below</p>
      </div>

      <div class="row g-3 align-items-center">
        <div class="col-12">
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
                  <option value="<?= $s['cid'] ?>"><?= htmlspecialchars($s['section_name'].' ('.$s['class_type'].')') ?></option>
                <?php endforeach; ?>
              </optgroup>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12 d-grid mt-3">
          <button type="button" id="showSubjectsBtn" class="btn btn-primary btn-lg">Show Subjects</button>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Bootstrap Modal -->
<div class="modal fade" id="subjectsModal" tabindex="-1" aria-labelledby="subjectsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="subjectsModalLabel">Subjects in Section</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Section:</strong> <span id="modalSectionName"></span></p>
        <table class="table table-bordered table-striped mt-2">
          <thead class="table-light">
            <tr>
              <th style="width:10%;">Role No.</th>
                <th style="width:20%;">Abbreviation</th>
              <th style="width:90%;">Subject Name</th>
            </tr>
          </thead>
          <tbody id="subjectsTableBody"></tbody>
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

<script>
$(function(){
    // Create a mapping of section ID to section name + abbreviation
    const sectionMap = {};
    <?php
    mysqli_data_seek($sections, 0); // Reset pointer
    while($sec=mysqli_fetch_assoc($sections)):
    ?>
        sectionMap['<?= $sec['cid'] ?>'] = '<?= htmlspecialchars($sec['section_name'].' ('.$sec['class_type'].')') ?>';
    <?php endwhile; ?>

    $('#showSubjectsBtn').click(function(){
        const sectionId = $('#sectionSelect').val();
        if(!sectionId){ alert('Please select a section'); return; }

        $.post('fetch_section_subjects.php', { section_id: sectionId }, function(data){
            const res = JSON.parse(data);
            let html = '';

            if(res.length === 0){
                html = '<tr><td colspan="2" class="text-center">No subjects found for this section.</td></tr>';
            } else {
                res.forEach((sub, index) => {
                    html += `<tr>
                        <td>${index + 1}</td>
                        <td>${sub.abbreviation_name}</td>
                        <td>${sub.subject_name}</td>
                        
                    </tr>`;
                });
            }

            $('#subjectsTableBody').html(html);
            $('#modalSectionName').text(sectionMap[sectionId] || '');
            $('#subjectsModal').modal('show');
        });
    });
});
</script>

<?php include('../Admin/footer.php'); ?>
