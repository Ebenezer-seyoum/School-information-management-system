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

        <!-- Filters Form -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label>Filter Type</label>
                            <select id="filter_type" name="filter_type" class="form-select form-select-sm">
                                <option value="">Select Filter</option>
                                <option value="weekly" <?= $filter_type=='weekly'?'selected':'' ?>>This Week</option>
                                <option value="monthly" <?= $filter_type=='monthly'?'selected':'' ?>>This Month</option>
                                <option value="yearly" <?= $filter_type=='yearly'?'selected':'' ?>>This Year</option>
                                <option value="range" <?= $filter_type=='range'?'selected':'' ?>>Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3 range-date" style="display: <?= $filter_type=='range'?'block':'none' ?>;">
                            <label>From Date</label>
                            <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3 range-date" style="display: <?= $filter_type=='range'?'block':'none' ?>;">
                            <label>To Date</label>
                            <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3 align-self-end">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Announcement Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="announcementTable" class="table table-hover table-bordered text-center">
                        <thead class="table-secondary">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while($row = mysqli_fetch_assoc($announcements)){
                                echo "<tr>
                                        <td>".$no++."</td>
                                        <td>".htmlspecialchars($row['title'])."</td>
                                        <td>".nl2br(htmlspecialchars($row['description']))."</td>
                                        <td>".htmlspecialchars($row['start_date'])."</td>
                                        <td>".htmlspecialchars($row['end_date'])."</td>
                                        <td>".htmlspecialchars($row['first_name'].' '.$row['father_name'])."</td>
                                        <td>".htmlspecialchars($row['created_at'])."</td>
                                        <td>".htmlspecialchars($row['status'])."</td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../Admin/footer.php'); ?>

<script>
$(function(){
    // Show/hide date inputs based on filter type
    $('#filter_type').change(function(){
        if($(this).val() == 'range'){
            $('.range-date').show();
        } else {
            $('.range-date').hide();
        }
    });

    // DataTable with mismatch row cleanup and robust exports
    if ($.fn.DataTable && $.fn.dataTable && $.fn.dataTable.Buttons) {
        var $table = $('#announcementTable');
        var headerCount = $table.find('thead th').length;
        $table.find('tbody tr').each(function(){
            var cells = $(this).children('td,th').length;
            if (cells !== headerCount) { $(this).remove(); }
        });

        $table.DataTable({
            dom: 'Bfrtip',
            pageLength: 25,
            language: { emptyTable: 'No announcements found' },
            columns: [null, null, null, null, null, null, null, null],
            buttons: [
                { extend: 'copyHtml5', text: 'Copy', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { search: 'applied', order: 'applied', page: 'all' } } },
                { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { search: 'applied', order: 'applied', page: 'all' } } },
                { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { search: 'applied', order: 'applied', page: 'all' } } },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    className: 'btn btn-sm btn-secondary',
                    orientation: 'landscape',
                    pageSize: 'A3',
                    exportOptions: { columns: ':visible', modifier: { search: 'applied', order: 'applied', page: 'all' } },
                    customize: function(doc){
                        var tableNode = doc.content && doc.content[1] && doc.content[1].table ? doc.content[1].table : null;
                        if (tableNode && tableNode.body && tableNode.body[0]) {
                            tableNode.widths = new Array(headerCount).fill('*');
                        }
                        doc.defaultStyle = doc.defaultStyle || {};
                        doc.defaultStyle.fontSize = 8;
                        doc.styles = doc.styles || {};
                        doc.styles.tableHeader = doc.styles.tableHeader || {};
                        doc.styles.tableHeader.fontSize = 9;
                        doc.styles.tableHeader.alignment = 'center';
                    }
                },
                { extend: 'print', text: 'Print', className: 'btn btn-sm btn-secondary', exportOptions: { columns: ':visible', modifier: { search: 'applied', order: 'applied', page: 'all' } } }
            ]
        });
    }
});
</script>
