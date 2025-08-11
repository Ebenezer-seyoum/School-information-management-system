<?php
include('adminHeader.php');
// Logged-in user id
$user_id = $_SESSION['uid'];

// 1️⃣ Mark all as read
$sql = "UPDATE notifications SET is_read = 1 WHERE user_id = '$user_id' AND is_read = 0";
mysqli_query($conn, $sql);

// Fetch all notifications (latest first)
$sql_all = "SELECT * FROM notifications WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result_all = mysqli_query($conn, $sql_all);
// Handle clear all notifications request
if (isset($_POST['clear_all'])) {
    $sql_delete = "DELETE FROM notifications WHERE user_id = '$user_id'";
    mysqli_query($conn, $sql_delete);
    // Redirect back with success param
    header("Location: ".$_SERVER['PHP_SELF']."?cleared=1");
    exit();
}
?>
<!-- Page Header -->
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3"> ALL notifications</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#"> </a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
            </ul>
        </div>
<!-- Container with Full-Width Layout -->
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fa fa-bell me-2"></i> My Notifications</h4>
        </div>
<div class="card-body">
  <?php if (mysqli_num_rows($result_all) > 0) { ?>
    <div class="list-group">
        <?php while ($notif = mysqli_fetch_assoc($result_all)) { ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div class="fw-semibold"><?= htmlspecialchars($notif['message']) ?></div>
                <div class="text-muted" id="notif-time-<?= $notif['id'] ?>" data-time="<?= $notif['created_at'] ?>">
                    <?= timeAgo($notif['created_at']) ?>
                </div>
            </div>
        <?php } ?>
    </div>
                    <!-- Clear all button -->
    <form method="POST" id="clearAllForm">
        <button type="submit" name="clear_all" class="btn btn-danger mt-3 w-100">
            <i class="fa fa-trash me-2"></i> Clear All Notifications
        </button>
    </form>

            <?php } else { ?>
                <div class="alert alert-info mb-0">You have no notifications.</div>
            <?php } ?>
        </div>
    </div>
</div>
</div>
</div>
<!-- Add JavaScript to update the time dynamically -->
<script>
// Function to calculate time ago
function timeAgo(datetime) {
    const timestamp = new Date(datetime).getTime();
    const diff = Math.floor((new Date().getTime() - timestamp) / 1000);
    
    if (diff < 60) {
        return 'Just now';
    } else if (diff < 3600) {
        return Math.floor(diff / 60) + ' minute' + (Math.floor(diff / 60) > 1 ? 's' : '') + ' ago';
    } else if (diff < 86400) {
        return Math.floor(diff / 3600) + ' hour' + (Math.floor(diff / 3600) > 1 ? 's' : '') + ' ago';
    } else if (diff < 604800) {
        return Math.floor(diff / 86400) + ' day' + (Math.floor(diff / 86400) > 1 ? 's' : '') + ' ago';
    } else if (diff < 2592000) {
        return Math.floor(diff / 604800) + ' week' + (Math.floor(diff / 604800) > 1 ? 's' : '') + ' ago';
    } else {
        const date = new Date(timestamp);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }
}

// Function to update all notification times
function updateNotificationTimes() {
    const notificationElements = document.querySelectorAll('.list-group-item .text-muted');
    
    notificationElements.forEach((element) => {
        const notifTime = element.getAttribute('data-time');
        element.textContent = timeAgo(notifTime);
    });
}

// Update the time every 1 minute
setInterval(updateNotificationTimes, 60000);

// Initial update on page load
updateNotificationTimes();
</script>

<?php
// Helper function to show time ago in a more user-friendly format
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    // If the difference is negative (in the future), return a message like 'Future notification'
    if ($diff < 0) {
        return 'Future notification';
    }

    // Time calculation logic
    if ($diff < 60) {
        return 'Just now';
    }
    elseif ($diff < 3600) {
        return floor($diff / 60) . ' minute' . (floor($diff / 60) > 1 ? 's' : '') . ' ago';
    }
    elseif ($diff < 86400) {
        return floor($diff / 3600) . ' hour' . (floor($diff / 3600) > 1 ? 's' : '') . ' ago';
    }
    elseif ($diff < 604800) {
        return floor($diff / 86400) . ' day' . (floor($diff / 86400) > 1 ? 's' : '') . ' ago';
    }
    elseif ($diff < 2592000) {
        return floor($diff / 604800) . ' week' . (floor($diff / 604800) > 1 ? 's' : '') . ' ago';
    }
    else {
        return date("M d, Y h:i A", $timestamp); // If more than a month, show full date
    }
}
?>
<!-- alert when delete -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('clearAllForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent immediate form submit

    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete all your notifications permanently.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, clear all!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Ensure 'clear_all' is posted
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'clear_all';
            input.value = '1';
            e.target.appendChild(input);

            e.target.submit();
        }
    });
});
</script>



<?php
include('../admin/footer.php');
?>
