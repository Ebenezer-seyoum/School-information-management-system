<?php
include('directorHeader.php');

// --- Login & Role Check ---
if (!isset($_SESSION['uid'])) die("You must be logged in to view this page.");
$user = getUserByID($_SESSION['uid']);
if (!$user || getRoleNameById($user['user_type']) !== "Director") die("Not authorized.");

$success = $error = "";

// --- Handle Edit Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = (int)$_POST['edit_id'];
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    if (!$title || !$description || !$start_date || !$end_date) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE announcements SET title=?, description=?, start_date=?, end_date=? WHERE eid=?");
        $stmt->bind_param("ssssi", $title, $description, $start_date, $end_date, $id);
        if ($stmt->execute()) $success = "Announcement updated successfully.";
        else $error = "Error: " . $stmt->error;
        $stmt->close();
    }
}

// --- Fetch Announcements ---
$announcements_res = mysqli_query($conn, "SELECT a.*, u.first_name, u.father_name,
    CASE 
        WHEN CURDATE() < a.start_date THEN 'Upcoming'
        WHEN CURDATE() > a.end_date THEN 'Expired'
        ELSE 'Active'
    END AS status
    FROM announcements a 
    JOIN users u ON a.created_by=u.uid 
    ORDER BY a.start_date DESC");
?>
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Announcements</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Announcements</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">View Announcements</a></li>
            </ul>
        </div>
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-3">
            <h3 class="fw-bold">Announcements</h3>
        </div>

        <!-- Alerts -->
        <?php if($success): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Announcements Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; while($row=mysqli_fetch_assoc($announcements_res)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= htmlspecialchars($row['start_date']) ?></td>
                                <td><?= htmlspecialchars($row['end_date']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars($row['first_name'].' '.$row['father_name']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-btn" 
                                            data-id="<?= $row['eid'] ?>"
                                            data-title="<?= htmlspecialchars($row['title']) ?>"
                                            data-description="<?= htmlspecialchars($row['description']) ?>"
                                            data-start="<?= htmlspecialchars($row['start_date']) ?>"
                                            data-end="<?= htmlspecialchars($row['end_date']) ?>"
                                            data-status="<?= htmlspecialchars($row['status']) ?>"
                                            data-bs-toggle="modal" data-bs-target="#editAnnouncementModal">
                                        View/Edit
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Viewing/Editing Announcement -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" id="editForm">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="editAnnouncementLabel">Edit Announcement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="edit_id" id="editId">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" id="editTitle" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" id="editDescription" class="form-control" rows="5" required></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="editStart" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" id="editEnd" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <input type="text" id="editStatus" class="form-control" readonly>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include('../Admin/footer.php'); ?>

<script>
    // Populate modal with selected announcement data
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function(){
            document.getElementById('editId').value = this.dataset.id;
            document.getElementById('editTitle').value = this.dataset.title;
            document.getElementById('editDescription').value = this.dataset.description;
            document.getElementById('editStart').value = this.dataset.start;
            document.getElementById('editEnd').value = this.dataset.end;
            document.getElementById('editStatus').value = this.dataset.status;
        });
    });
</script>
