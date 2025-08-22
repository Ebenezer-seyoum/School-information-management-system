<?php
include "directorHeader.php"; 
$success = $error = "";
$promotedStudents = [];
$notPromotedStudents = [];

// --- Fetch all classes ---
$classesRes = mysqli_query($conn, "SELECT * FROM sections ORDER BY section_name");

// --- Handle actual database update after confirmation ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['promote_final'])) {
    $fromClass = $_POST['from_class'];
    $fromYear  = $_POST['from_year'];
    $toClass   = $_POST['to_class'];
    $toYear    = $_POST['to_year'];
    $promotedList = json_decode($_POST['promoted_list'], true);
    $notPromotedList = json_decode($_POST['not_promoted_list'], true);

    foreach ($promotedList as $student_id) {
        mysqli_query($conn, "UPDATE assign_student 
            SET promote_status = 2 
            WHERE student_id='$student_id' AND section_id='$fromClass' AND academic_year='$fromYear'");
        mysqli_query($conn, "INSERT INTO assign_student (student_id, section_id, academic_year, promote_status) 
            VALUES ('$student_id', '$toClass', '$toYear', 0)");
    }

    foreach ($notPromotedList as $student_id) {
        mysqli_query($conn, "UPDATE assign_student 
            SET promote_status = 1 
            WHERE student_id='$student_id' AND section_id='$fromClass' AND academic_year='$fromYear'");
        mysqli_query($conn, "INSERT INTO assign_student (student_id, section_id, academic_year, promote_status) 
            VALUES ('$student_id', '$fromClass', '$toYear', 0)");
    }
    echo "<script>
        Swal.fire({icon:'success', title:'Success', text:'Students promoted successfully!'}).then(()=>{window.location=''});
    </script>";
}

// --- Fetch students for preview when class/year is selected ---
$selectedClass = $_POST['from_class'] ?? '';
$selectedYear  = $_POST['from_year'] ?? '';
if ($selectedClass && $selectedYear) {
    $res = mysqli_query($conn, "
        SELECT a.student_id, s.first_name, s.father_name, s.grand_father_name ,s.student_id AS id
        FROM assign_student a 
        JOIN students s ON a.student_id=s.sid 
        WHERE a.section_id='$selectedClass' AND a.academic_year='$selectedYear'
    ");
    while ($stu = mysqli_fetch_assoc($res)) {
        $student_id = $stu['student_id'];
        $id = $stu['id'];
        // Get all marks
        $marksRes = mysqli_query($conn, "
            SELECT m.result, sub.subject_name 
            FROM marks m 
            JOIN subjects sub ON m.subject_id=sub.suid 
            WHERE m.student_id='$student_id' 
              AND m.section_id='$selectedClass' 
              AND m.academic_year='$selectedYear'
        ");
        $total = 0; $count = 0; $failCount = 0;
        $englishFail = false;
        $mathFail = false;
        while($m = mysqli_fetch_assoc($marksRes)){
            $mark = $m['result'];
            $subject = strtolower($m['subject_name']);
            $total += $mark;
            $count++;
            if($mark < 50) $failCount++;
            if($subject == "english" && $mark < 50) $englishFail = true;
            if(($subject == "mathematics" || $subject == "math") && $mark < 50) $mathFail = true;
        }
        $avgMark = ($count>0)?round($total/$count,2):0;

        // Promotion rules
        if($avgMark >= 50 && $failCount < 3 && !($englishFail && $mathFail)){
            $promotedStudents[] = [
                'student_id'=> $student_id,
                'id'=>$id,
                'first_name'=>$stu['first_name'],
                'father_name'=>$stu['father_name'],
                'grand_father_name'=>$stu['grand_father_name']
            ];
        } else {
            $notPromotedStudents[] = [
                'student_id'=> $student_id,
                'id'=>$id,
                'first_name'=>$stu['first_name'],
                'father_name'=>$stu['father_name'],
                'grand_father_name'=>$stu['grand_father_name']
            ];
        }
    }
}
?>

<div class="container">
  <div class="page-inner">
    <div class="page-header">
      <h3 class="fw-bold mb-3">Promote Student</h3>
    </div>

    <!-- Promotion Form -->
    <form method="POST" action="" id="promoteForm">
        <div class="card shadow-sm mb-4 p-3">
            <div class="row g-3">
                <h2 class="fw-bold text-center">Promote Students</h2>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Current Class</label>
                        <select name="from_class" class="form-select" required onchange="this.form.submit()">
                            <option value="">-- Select Class --</option>
                            <?php
                            mysqli_data_seek($classesRes, 0);
                            while($cls = mysqli_fetch_assoc($classesRes)): ?>
                                <option value="<?= $cls['cid'] ?>" <?= ($cls['cid']==$selectedClass)?'selected':'' ?> >
                                    <?= htmlspecialchars($cls['section_name'].' ('.$cls['class_type'].')') ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Academic Year</label>
                        <input type="number" name="from_year" class="form-control" value="<?= $selectedYear ?>" required onchange="this.form.submit()">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Promote To Class</label>
                        <select name="to_class" class="form-select" required>
                            <option value="">-- Select Target Class --</option>
                            <?php
                            mysqli_data_seek($classesRes, 0);
                            while($cls = mysqli_fetch_assoc($classesRes)):
                                if($cls['cid'] != $selectedClass): ?>
                                <option value="<?= $cls['cid'] ?>"><?= htmlspecialchars($cls['section_name'].' ('.$cls['class_type'].')') ?></option>
                            <?php endif; endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Academic Year</label>
                        <input type="number" name="to_year" class="form-control" required>
                    </div>
                </div>
            </div>
     <div class="text-end mt-3">
    <?php if($selectedClass && $selectedYear): ?>
        <button type="button" class="btn btn-success btn-lg" id="previewBtn">Promote Students</button>
    <?php endif; ?>
</div>
        </div>
    </form>
  </div>
</div>
<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Promotion Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
    <div class="modal-body">
  <div class="row">
    <!-- Promoted Students Table -->
    <div class="col-md-6">
        <h5 class="text-success">Promoted Students</h5>
        <input type="text" class="form-control mb-2" id="searchPromoted" placeholder="Search promoted student...">
        <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
            <table class="table table-bordered table-striped" id="promotedTable">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($promotedStudents as $stu): ?>
                    <tr>
                        <td><?= $stu['id'] ?></td>
                        <td><?= htmlspecialchars($stu['first_name'].' '.$stu['father_name'].' '.$stu['grand_father_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Not Promoted Students Table -->
    <div class="col-md-6">
        <h5 class="text-danger">Not Promoted Students</h5>
        <input type="text" class="form-control mb-2" id="searchNotPromoted" placeholder="Search not promoted student...">
        <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
            <table class="table table-bordered table-striped" id="notPromotedTable">
                <thead class="table-danger">
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($notPromotedStudents as $stu): ?>
                    <tr>
                        <td><?= $stu['id'] ?></td>
                        <td><?= htmlspecialchars($stu['first_name'].' '.$stu['father_name'].' '.$stu['grand_father_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-success" id="confirmPromoteBtn">Confirm Promotion</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('previewBtn').addEventListener('click', function(){
    var modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
});

document.getElementById('confirmPromoteBtn').addEventListener('click', function(){
    let form = document.getElementById('promoteForm');
    
    let inputFinal = document.createElement('input');
    inputFinal.type = 'hidden';
    inputFinal.name = 'promote_final';
    inputFinal.value = 1;
    form.appendChild(inputFinal);

    let promotedList = <?php echo json_encode(array_column($promotedStudents,'student_id')); ?>;
    let notPromotedList = <?php echo json_encode(array_column($notPromotedStudents,'student_id')); ?>;

    let inputPromoted = document.createElement('input');
    inputPromoted.type = 'hidden';
    inputPromoted.name = 'promoted_list';
    inputPromoted.value = JSON.stringify(promotedList);
    form.appendChild(inputPromoted);

    let inputNotPromoted = document.createElement('input');
    inputNotPromoted.type = 'hidden';
    inputNotPromoted.name = 'not_promoted_list';
    inputNotPromoted.value = JSON.stringify(notPromotedList);
    form.appendChild(inputNotPromoted);

    form.submit();
});
</script>
<script>
function filterTable(inputId, tableId) {
    document.getElementById(inputId).addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#" + tableId + " tbody tr");
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
}

filterTable("searchPromoted", "promotedTable");
filterTable("searchNotPromoted", "notPromotedTable");
</script>

<?php include('../Admin/footer.php'); ?>
