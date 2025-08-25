<?php
include('teacherHeader.php');

// --- Login & Role Check ---
if (!isset($_SESSION['uid'])) die("You must be logged in.");
$user = getUserByID($_SESSION['uid']);
if (!$user || getRoleNameById($user['user_type']) !== "Teacher") {
    die("Not authorized.");
}

// --- Filters ---
$filter_type = $_GET['filter_type'] ?? '';
$from_date   = $_GET['from_date'] ?? '';
$to_date     = $_GET['to_date'] ?? '';

// --- Build SQL ---
$sql = "SELECT a.eid, a.title, a.description, a.start_date, a.end_date, u.first_name, u.father_name, a.created_at,
               CASE 
                   WHEN CURDATE() < a.start_date THEN 'Upcoming'
                   WHEN CURDATE() > a.end_date THEN 'Expired'
                   ELSE 'Active'
               END AS status
        FROM announcements a
        JOIN users u ON a.created_by = u.uid
        WHERE 1=1";

if($filter_type){
    switch($filter_type){
        case 'weekly':
            $sql .= " AND YEARWEEK(a.start_date,1) = YEARWEEK(CURDATE(),1)";
            break;
        case 'monthly':
            $sql .= " AND MONTH(a.start_date) = MONTH(CURDATE()) AND YEAR(a.start_date) = YEAR(CURDATE())";
            break;
        case 'yearly':
            $sql .= " AND YEAR(a.start_date) = YEAR(CURDATE())";
            break;
        case 'range':
            if($from_date && $to_date){
                $sql .= " AND a.start_date >= '".mysqli_real_escape_string($conn,$from_date)."' 
                          AND a.end_date <= '".mysqli_real_escape_string($conn,$to_date)."'";
            }
            break;
    }
}
$sql .= " ORDER BY a.start_date DESC";
$announcements = mysqli_query($conn, $sql);
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
    <!-- Filters Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
             <h2 class="fw-bold text-center">📢 Announcements</h2>
        <p class="text-muted text-center">View all announcements relevant to your classes and school.</p>
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filter_type" class="form-label">Filter Type</label>
                    <select id="filter_type" name="filter_type" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="weekly" <?= $filter_type=='weekly'?'selected':'' ?>>This Week</option>
                        <option value="monthly" <?= $filter_type=='monthly'?'selected':'' ?>>This Month</option>
                        <option value="yearly" <?= $filter_type=='yearly'?'selected':'' ?>>This Year</option>
                        <option value="range" <?= $filter_type=='range'?'selected':'' ?>>Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3 range-date" style="display: <?= $filter_type=='range'?'block':'none' ?>;">
                    <label class="form-label">From</label>
                    <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 range-date" style="display: <?= $filter_type=='range'?'block':'none' ?>;">
                    <label class="form-label">To</label>
                    <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Announcement Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="announcementTable" class="table table-hover table-bordered text-center align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while($row = mysqli_fetch_assoc($announcements)){
                            $desc = strlen($row['description'])>50 ? substr($row['description'],0,50).'...' : $row['description'];
                            $statusBadge = '';
                            switch($row['status']){
                                case 'Active': $statusBadge='<span class="badge bg-success">Active</span>'; break;
                                case 'Upcoming': $statusBadge='<span class="badge bg-info">Upcoming</span>'; break;
                                case 'Expired': $statusBadge='<span class="badge bg-danger">Expired</span>'; break;
                            }
                            echo "<tr>
                                    <td>".($no++)."</td>
                                    <td>".htmlspecialchars($row['title'])."</td>
                                    <td>".htmlspecialchars($desc)."</td>
                                    <td>".htmlspecialchars($row['start_date'])."</td>
                                    <td>".htmlspecialchars($row['end_date'])."</td>
                                    <td>".htmlspecialchars($row['first_name'].' '.$row['father_name'])."</td>
                                    <td>".htmlspecialchars($row['created_at'])."</td>
                                    <td>{$statusBadge}</td>
                                    <td>
                                        <button class='btn btn-sm btn-info view-btn' 
                                            data-title='".htmlspecialchars($row['title'],ENT_QUOTES)."' 
                                            data-description='".htmlspecialchars($row['description'],ENT_QUOTES)."' 
                                            data-start='".htmlspecialchars($row['start_date'])."' 
                                            data-end='".htmlspecialchars($row['end_date'])."' 
                                            data-status='{$row['status']}'>
                                            View
                                        </button>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Start Date:</strong> <span id="modalStart"></span></p>
        <p><strong>End Date:</strong> <span id="modalEnd"></span></p>
        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
        <hr>
        <p id="modalDescription"></p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php include('../Admin/footer.php'); ?>

<script>
$(function(){
    // Show/hide date inputs for range filter
    $('#filter_type').change(function(){
        if($(this).val()=='range'){
            $('.range-date').show();
        } else {
            $('.range-date').hide();
        }
    });

    // DataTable initialization
    if($.fn.DataTable){
        var table = $('#announcementTable');
        var headerCount = table.find('thead th').length;
        table.find('tbody tr').each(function(){
            if($(this).children('td,th').length != headerCount) $(this).remove();
        });

        table.DataTable({
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copyHtml5', text: 'Copy', className:'btn btn-sm btn-secondary' },
                { extend: 'csvHtml5', text: 'CSV', className:'btn btn-sm btn-secondary' },
                { extend: 'excelHtml5', text: 'Excel', className:'btn btn-sm btn-secondary' },
                { extend: 'pdfHtml5', text: 'PDF', className:'btn btn-sm btn-secondary', orientation:'landscape', pageSize:'A4',
                    customize: function(doc){
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 9;
                        doc.styles.tableHeader.alignment = 'center';
                    }
                },
                { extend: 'print', text: 'Print', className:'btn btn-sm btn-secondary' }
            ],
            language: { emptyTable: "No announcements found" }
        });
    }

    // Modal view button
    $('.view-btn').click(function(){
        $('#modalTitle').text($(this).data('title'));
        $('#modalDescription').text($(this).data('description'));
        $('#modalStart').text($(this).data('start'));
        $('#modalEnd').text($(this).data('end'));
        $('#modalStatus').text($(this).data('status'));
        var modal = new bootstrap.Modal(document.getElementById('announcementModal'));
        modal.show();
    });
});
</script>
