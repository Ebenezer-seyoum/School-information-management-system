<?php
include('directorHeader.php');

// --- Login & Role Check ---
if (!isset($_SESSION['uid'])) {
    die("You must be logged in to view this page.");
}
$user = getUserByID($_SESSION['uid']);
if (!$user || getRoleNameById($user['user_type']) !== "Director") {
    die("Not authorized.");
}

$success = $error = "";

// --- Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    if (!$title || !$description || !$start_date || !$end_date) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO announcements (title, description, start_date, end_date, created_by, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssi", $title, $description, $start_date, $end_date, $_SESSION['uid']);
        if ($stmt->execute()) {
            $success = "Announcement created successfully.";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// --- Fetch All Announcements ---
$announcements_res = mysqli_query($conn, "SELECT a.*, u.first_name, u.father_name 
                                          FROM announcements a 
                                          JOIN users u ON a.created_by=u.uid 
                                          ORDER BY a.start_date DESC");
?>

<!-- page header -->
<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">create announcement</h3>
      <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">announcement</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">create announcement</a></li>
      </ul>
  </div>
<!-- end page header -->

    <!-- SweetAlert will show success/error messages; Bootstrap alerts removed -->

    <!-- Create Announcement Form -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">New Announcement</h5>
            </div>
            <div class="card-body">
                <form id="announceForm" method="POST" action="" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter announcement title">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Enter announcement description"></textarea>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success">Create Announcement</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

            <!-- (Announcement list removed per request) -->
    </div>
<?php include('../Admin/footer.php'); ?>
<script>
    (function(){
        const useSwal2 = (window.Swal && typeof Swal.fire === 'function');
        const showAlert = function(icon, title, message){
            // Preserve line breaks
            const textMsg = (message||'').toString();
            const htmlMsg = textMsg.replace(/\n/g,'<br>');
            if (useSwal2){
                Swal.fire({ icon: icon, title: title, html: htmlMsg, confirmButtonColor: '#0d6efd' });
            } else if (window.swal) {
                // SweetAlert (v1) fallback: supports \n in text
                window.swal(title, textMsg, icon === 'error' ? 'error' : (icon==='success'?'success':'info'));
            } else {
                // Last resort
                alert(title + "\n\n" + textMsg);
            }
        };
        <?php if(!empty($success)) { $msg = (string)$success; ?>
            showAlert('success','Success', <?= json_encode($msg) ?>);
        <?php } ?>
        <?php if(!empty($error)) { $msg = (string)$error; ?>
            showAlert('error','Error', <?= json_encode($msg) ?>);
        <?php } ?>

                // Client-side validation with SweetAlert (aggregate missing fields)
                document.addEventListener('DOMContentLoaded', function(){
                    const form = document.getElementById('announceForm');
                    if (!form) return;
                    form.addEventListener('submit', function(e){
                        const title = (form.querySelector('[name="title"]').value || '').trim();
                        const desc = (form.querySelector('[name="description"]').value || '').trim();
                        const start = (form.querySelector('[name="start_date"]').value || '').trim();
                        const end = (form.querySelector('[name="end_date"]').value || '').trim();

                        const missing = [];
                        if (!title) missing.push('• Title');
                        if (!start) missing.push('• Start Date');
                        if (!end)   missing.push('• End Date');
                        if (!desc)  missing.push('• Description');

                        const issues = [];
                        if (start && end && end < start) issues.push('• End date must be after or equal to Start date');

                        if (missing.length || issues.length){
                            e.preventDefault();
                            let msg = '';
                            if (missing.length){ msg += 'Please enter the following:\n' + missing.join('\n'); }
                            if (issues.length){ msg += (msg ? '\n\n' : '') + issues.join('\n'); }
                            return showAlert('error','Error', msg);
                        }
                    });
                });
    })();
</script>
