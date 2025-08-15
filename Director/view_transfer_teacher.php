<?php
include('directorHeader.php');

$profile = getUserByID($_SESSION["uid"]);
$roleName = getRoleNameById($profile["user_type"]);

if (!isset($_SESSION["uid"]) || $roleName != "Director") {
    echo "You are not authorized to view this page.";
    exit;
}

// --- Helper PHP functions ---
function fetchAllAssignTeacher($conn){
    $res = mysqli_query($conn, "SELECT at.atid, at.teacher_id, at.academic_year, at.section_id, at.subject_id,
                                        s.section_name, s.class_type, sub.subject_name
                                  FROM assign_teacher at
                                  LEFT JOIN sections s ON at.section_id = s.cid
                                  LEFT JOIN subjects sub ON at.subject_id = sub.suid
                                  ORDER BY at.teacher_id, at.academic_year");
    $tmp = [];
    while($a = mysqli_fetch_assoc($res)){
        $tmp[$a['teacher_id']][] = $a;
    }
    return $tmp;
}

function fetchAllSections($conn){
    $res = mysqli_query($conn, "SELECT cid, section_name, class_type FROM sections ORDER BY section_name ASC");
    $tmp = [];
    while($r = mysqli_fetch_assoc($res)) $tmp[] = $r;
    return $tmp;
}

function fetchAllSubjects($conn){
    $res = mysqli_query($conn, "SELECT suid, subject_name FROM subjects ORDER BY subject_name ASC");
    $tmp = [];
    while($r = mysqli_fetch_assoc($res)) $tmp[] = $r;
    return $tmp;
}

// --- Handle single class/subject transfer ---
if(isset($_POST['single_transfer'])){
    $atid = intval($_POST['atid']);
    $new_section = intval($_POST['new_section']);
    $new_subject = intval($_POST['new_subject']);

    if($atid && $new_section && $new_subject){
        mysqli_query($conn, "UPDATE assign_teacher SET section_id=$new_section, subject_id=$new_subject WHERE atid=$atid");
        $_SESSION['transfer_success'] = true;
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

// --- Fetch data for JS ---
$assignData = fetchAllAssignTeacher($conn);
$allSections = fetchAllSections($conn);
$allSubjects = fetchAllSubjects($conn);
?>

<style>
  .profile-img { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; }
  .modal-body { max-height: 400px; overflow-y: auto; }
  .table td, .table th { vertical-align: middle; }
</style>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Manage Teacher Transfer</h3>
    </div>
    <div class="main-content">
      <section class="section">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">All Teachers</h4>
                <form method="GET" class="d-flex">
                  <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control" placeholder="Search by name or ID">
                </form>
              </div>
              <div class="card-body table-responsive">
                <table class="table table-hover align-middle text-center">
                  <thead class="table-secondary">
                    <tr>
                      <th>#</th>
                      <th>Teacher Name</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                    $search = mysqli_real_escape_string($conn, trim($_GET['search'] ?? ''));
                    $res = mysqli_query($conn, "SELECT uid, first_name, father_name
                                                FROM users
                                                WHERE user_type = 1
                                                AND (first_name LIKE '%$search%' OR father_name LIKE '%$search%' OR uid LIKE '%$search%')
                                                ORDER BY first_name ASC");
                    $no = 1;
                    if(mysqli_num_rows($res) > 0){
                        while($row = mysqli_fetch_assoc($res)){
                            echo "<tr>
                                <td>$no</td>
                                <td>".htmlspecialchars($row['first_name'] . ' ' . $row['father_name'])."</td>
                                <td>
                                  <button type='button' class='btn btn-warning btn-sm transfer-btn'
                                    data-teacher-id='{$row['uid']}'
                                    data-teacher-name='".htmlspecialchars($row['first_name'] . ' ' . $row['father_name'], ENT_QUOTES)."' >
                                    Transfer
                                  </button>
                                </td>
                              </tr>";
                            $no++;
                        }
                    } else {
                        echo '<tr><td colspan="3" class="text-danger">No teachers found.</td></tr>';
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

<!-- Modal: Teacher Classes -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="transferModalLabel">Transfer Teacher</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="modal_teacher_id">
        <div class="mb-3">
          <label class="form-label">Select Academic Year</label>
          <select id="modal_academic_year" class="form-control"></select>
        </div>
        <div id="classes_table_container" class="table-responsive"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Single Class/Subject Transfer -->
<div class="modal fade" id="singleTransferModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" id="singleTransferForm">
        <div class="modal-header">
          <h5 class="modal-title" id="singleTransferModalLabel">Transfer Class/Subject</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="atid" id="modal_atid">
          <div class="mb-3">
            <label class="form-label">Current Class</label>
            <input type="text" id="current_class" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Current Subject</label>
            <input type="text" id="current_subject" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">New Section</label>
            <select name="new_section" id="new_section_select" class="form-control"></select>
          </div>
          <div class="mb-3">
            <label class="form-label">New Subject</label>
            <select name="new_subject" id="new_subject_select" class="form-control"></select>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const assignData = <?= json_encode($assignData, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>;
const allSections = <?= json_encode($allSections); ?>;
const allSubjects = <?= json_encode($allSubjects); ?>;

function populateSelect(selectElem, data, textKey, valueKey){
    selectElem.innerHTML = '';
    data.forEach(d=>{
        const opt = document.createElement('option');
        opt.value = d[valueKey];
        opt.textContent = d[textKey] + (d.class_type ? ' - '+d.class_type : '');
        selectElem.appendChild(opt);
    });
}

// Event delegation for transfer buttons
document.addEventListener('click', function(e){
    // Teacher Transfer Modal
    if(e.target && e.target.classList.contains('transfer-btn')){
        const btn = e.target;
        const teacherId = btn.dataset.teacherId;
        const teacherName = btn.dataset.teacherName;

        document.getElementById('modal_teacher_id').value = teacherId;
        document.getElementById('transferModalLabel').textContent = `Transfer: ${teacherName}`;

        const yearSelect = document.getElementById('modal_academic_year');
        const container = document.getElementById('classes_table_container');
        yearSelect.innerHTML = '';
        container.innerHTML = '';

        if(assignData[teacherId]){
            const years = [...new Set(assignData[teacherId].map(a=>a.academic_year))];
            years.forEach(y=>{
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                yearSelect.appendChild(opt);
            });

            function renderTable(year){
                container.innerHTML = `<table class="table table-bordered text-center"><thead><tr>
                    <th>#</th><th>Class</th><th>Subject</th><th>Academic Year</th><th>Action</th></tr></thead><tbody></tbody></table>`;
                const tbody = container.querySelector('tbody');
                let no=1;
                assignData[teacherId].filter(a=>a.academic_year==year).forEach(c=>{
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>${no}</td><td>${c.section_name} - ${c.class_type}</td><td>${c.subject_name}</td><td>${c.academic_year}</td>
                        <td><button type="button" class="btn btn-sm btn-primary single-transfer-btn" data-atid="${c.atid}" data-class="${c.section_name} - ${c.class_type}" data-subject="${c.subject_name}">Transfer</button></td>`;
                    tbody.appendChild(tr);
                    no++;
                });

                // Single Transfer Delegation
                tbody.addEventListener('click', function(e2){
                    if(e2.target && e2.target.classList.contains('single-transfer-btn')){
                        const atid = e2.target.dataset.atid;
                        document.getElementById('modal_atid').value = atid;
                        document.getElementById('current_class').value = e2.target.dataset.class;
                        document.getElementById('current_subject').value = e2.target.dataset.subject;
                        populateSelect(document.getElementById('new_section_select'), allSections, 'section_name', 'cid');
                        populateSelect(document.getElementById('new_subject_select'), allSubjects, 'subject_name', 'suid');
                        new bootstrap.Modal(document.getElementById('singleTransferModal')).show();
                    }
                });
            }

            renderTable(years[0]);
            yearSelect.onchange = ()=> renderTable(yearSelect.value);
        }

        new bootstrap.Modal(document.getElementById('transferModal')).show();
    }
});

// SweetAlert for success
<?php if(!empty($_SESSION['transfer_success'])): ?>
Swal.fire({icon:'success',title:'Success!',text:'Teacher transferred successfully.'});
<?php unset($_SESSION['transfer_success']); endif; ?>
</script>

<?php include('../Admin/footer.php'); ?>
