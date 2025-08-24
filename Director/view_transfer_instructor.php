<?php
include('directorHeader.php');
$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);

if (!isset($_SESSION["uid"]) || $roleName != "Director") {
    echo "You are not authorized to view this page.";
    exit;
}
?>

<style>
  .profile-img { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; }
  .modal-body { max-height: 400px; overflow-y: auto; }
  .table td, .table th { vertical-align: middle; }
</style>
<div class="container">
  <div class="page-inner">
    <div class="page-header">
     <h3 class="fw-bold mb-3">view Instructor</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Manage Instructor</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">View Instructor</a></li>
      </ul>
   </div>
</div>
<div class="container">
  <div class="page-inner">
    <div class="main-content">
      <section class="section">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">All Instructors</h4>
                <form method="GET">
                  <div class="search-box">
                    <div class="input-group">
                      <span class="input-group-text bg-primary text-white">
                        <i class="fas fa-search"></i>
                      </span>
                      <input type="text" name="search" id="userSearch"
                             class="form-control search-input"
                             value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                             placeholder="Search by ID, Name, or Role...">
                      <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                  </div>
                </form>
              </div>
              <div class="card-body table-responsive">
                <table class="table table-hover align-middle text-center">
                  <thead class="table-secondary">
                    <tr>
                      <th>#</th>
                      <th>Instructor Name</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                    $search = trim($_GET['search'] ?? '');
                    $res = mysqli_query($conn, "SELECT u.uid, u.first_name, u.father_name
                                                FROM users u
                                                WHERE u.user_type = 1
                                                AND (u.first_name LIKE '%$search%' OR u.father_name LIKE '%$search%' OR u.uid LIKE '%$search%')
                                                ORDER BY u.first_name ASC");
                    $no = 1;
                    if(mysqli_num_rows($res) > 0){
                        while($row = mysqli_fetch_assoc($res)){
                            ?>
                            <tr>
                              <td><?= $no ?></td>
                              <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['father_name']) ?></td>
                              <td>
                                <button type="button" class="btn btn-warning btn-sm transfer-btn"
                                  data-instructor-id="<?= $row['uid'] ?>"
                                  data-instructor-name="<?= htmlspecialchars($row['first_name'] . ' ' . $row['father_name']) ?>">
                                  Transfer
                                </button>
                              </td>
                            </tr>
                            <?php
                            $no++;
                        }
                    } else {
                        echo '<tr><td colspan="3" class="text-danger">No instructors found.</td></tr>';
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

<!-- Step 1 Modal: Instructor Classes -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="transferModalLabel">Transfer Instructor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="modal_instructor_id">
        <div class="mb-3">
          <label class="form-label">Select Academic Year</label>
          <select id="modal_academic_year" class="form-control"></select>
        </div>
        <div id="classes_table_container" class="table-responsive"></div>
      </div>
    </div>
  </div>
</div>

<!-- Step 2 Modal: Single Class Transfer -->
<div class="modal fade" id="singleTransferModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" id="singleTransferForm">
        <div class="modal-header">
          <h5 class="modal-title" id="singleTransferModalLabel">Transfer Class</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="hid" id="modal_hid">
          <div class="mb-3">
            <label class="form-label">Current Class</label>
            <input type="text" id="current_class" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">New Section</label>
            <select name="new_section" id="new_section_select" class="form-control"></select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="single_transfer" class="btn btn-primary">Transfer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Fetch all assign_instructor data with hid
const assignData = <?php
$assign_res = mysqli_query($conn, "SELECT ai.hid, ai.instructor_id, ai.academic_year, s.cid as section_id, s.section_name, s.class_type
                                  FROM assign_instructor ai
                                  LEFT JOIN sections s ON ai.section_id = s.cid
                                  ORDER BY ai.instructor_id, ai.academic_year");
$tmp = [];
while($a = mysqli_fetch_assoc($assign_res)){
    $tmp[$a['instructor_id']][] = $a;
}
echo json_encode($tmp);
?>;

// All sections for new selection
const allSections = <?php
$sec_res = mysqli_query($conn, "SELECT cid, section_name, class_type FROM sections ORDER BY section_name ASC");
$sections = [];
while($s = mysqli_fetch_assoc($sec_res)) $sections[] = $s;
echo json_encode($sections);
?>;

function populateNewSectionSelect(selectElem) {
    selectElem.innerHTML = '';
    allSections.forEach(sec=>{
        const opt = document.createElement('option');
        opt.value = sec.cid;
        opt.textContent = sec.section_name + ' - ' + sec.class_type;
        selectElem.appendChild(opt);
    });
}

document.querySelectorAll('.transfer-btn').forEach(btn=>{
    btn.addEventListener('click', function(){
        const instructorId = this.dataset.instructorId;
        const instructorName = this.dataset.instructorName;
        document.getElementById('modal_instructor_id').value = instructorId;
        document.getElementById('transferModalLabel').textContent = `Transfer: ${instructorName}`;

        const yearSelect = document.getElementById('modal_academic_year');
        const container = document.getElementById('classes_table_container');
        yearSelect.innerHTML = '';
        container.innerHTML = '';

        if(assignData[instructorId]){
            // Populate year options
            const years = [...new Set(assignData[instructorId].map(a=>a.academic_year))];
            years.forEach(y=>{
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                yearSelect.appendChild(opt);
            });

            function renderTable(year){
                container.innerHTML = `<table class="table table-bordered text-center"><thead><tr>
                    <th>#</th><th>Class</th><th>Academic Year</th><th>Action</th></tr></thead><tbody></tbody></table>`;
                const tbody = container.querySelector('tbody');
                let no=1;
                assignData[instructorId].filter(a=>a.academic_year==year).forEach(c=>{
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>${no}</td><td>${c.section_name} - ${c.class_type}</td><td>${c.academic_year}</td>
                        <td><button type="button" class="btn btn-sm btn-primary single-transfer-btn" data-hid="${c.hid}" data-class="${c.section_name} - ${c.class_type}">Transfer</button></td>`;
                    tbody.appendChild(tr);
                    no++;
                });
            }

            renderTable(years[0]);

            yearSelect.onchange = function(){
                renderTable(this.value);
            };

            // Delegate event for dynamically created buttons
            container.addEventListener('click', e=>{
                if(e.target.classList.contains('single-transfer-btn')){
                    const hid = e.target.dataset.hid;
                    const currentClass = e.target.dataset.class;
                    document.getElementById('modal_hid').value = hid;
                    document.getElementById('current_class').value = currentClass;
                    populateNewSectionSelect(document.getElementById('new_section_select'));
                    new bootstrap.Modal(document.getElementById('singleTransferModal')).show();
                }
            });
        }

        new bootstrap.Modal(document.getElementById('transferModal')).show();
    });
});
</script>

<?php
// Handle single class transfer
if(isset($_POST['single_transfer'])){
    $hid = intval($_POST['hid']);
    $new_section = intval($_POST['new_section']);

    if($hid && $new_section){
        mysqli_query($conn, "UPDATE assign_instructor SET section_id=$new_section WHERE hid=$hid");
        $_SESSION['transfer_success'] = true;
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

// SweetAlert success only once
if(!empty($_SESSION['transfer_success'])){
    echo "<script>Swal.fire({icon:'success',title:'Success!',text:'Class transferred successfully.'})</script>";
    unset($_SESSION['transfer_success']);
}

include('../Admin/footer.php');
?>
