<?php
include "directorHeader.php"; 

// --- Fetch dynamic counts ---
$totalStudents    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM students"))['total'];
$totalTeachers    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE user_type = 1"))['total'];
$totalClasses     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM sections"))['total'];
$totalAnnouncements = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM announcements"))['total'];
$totalMarks       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM marks"))['total'];

// --- Fetch data for chart: Students per Class ---
$classDataRes = mysqli_query($conn, "SELECT s.section_name, COUNT(c.student_id) AS student_count 
                                    FROM assign_student c
                                    LEFT JOIN sections s ON s.cid = c.section_id
                                    GROUP BY s.cid");
$classNames = $studentCounts = [];
while($row = mysqli_fetch_assoc($classDataRes)){
    $classNames[] = $row['section_name'];
    $studentCounts[] = $row['student_count'];
}

// --- Fetch data for chart: Marks Distribution ---
$marksDataRes = mysqli_query($conn, "SELECT result, COUNT(*) as count FROM marks GROUP BY result ORDER BY result");
$grades = $marksCount = [];
while($row = mysqli_fetch_assoc($marksDataRes)){
    $grades[] = $row['result'];
    $marksCount[] = $row['count'];
}
?>

<div class="container">
<div class="page-inner">
  <div class="row g-4"> 

<!-- Total Students -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100 shadow-sm hover-shadow">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                        <i class="fas fa-user-graduate fa-3x"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Total Students</p>
                        <h4 class="card-title"><?= $totalStudents ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Total Teachers -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100 shadow-sm hover-shadow">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-info bubble-shadow-small">
                        <i class="fas fa-chalkboard-teacher fa-3x"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Total Teachers</p>
                        <h4 class="card-title"><?= $totalTeachers ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Total Classes -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100 shadow-sm hover-shadow">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-success bubble-shadow-small">
                        <i class="fas fa-school fa-3x"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Total Classes</p>
                        <h4 class="card-title"><?= $totalClasses ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Total Announcements -->
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round h-100 shadow-sm hover-shadow">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                        <i class="fas fa-bullhorn fa-3x"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3">
                    <div class="numbers">
                        <p class="card-category">Total Announcements</p>
                        <h4 class="card-title"><?= $totalAnnouncements ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

  </div>		
</div>
</div>

<!-- Two Side by Side Charts -->
<div class="row mt-5">
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Students per Class</h5>
            </div>
            <div class="card-body">
                <canvas id="studentsChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Marks Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="marksChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<?php include('../admin/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Students per Class - Bar Chart
const ctx1 = document.getElementById('studentsChart').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: <?= json_encode($classNames) ?>,
        datasets: [{
            label: 'Students',
            data: <?= json_encode($studentCounts) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            y: { beginAtZero: true, title: { display: true, text: 'Students' } },
            x: { title: { display: true, text: 'Classes' } }
        }
    }
});

// Marks Distribution - Pie Chart
const ctx2 = document.getElementById('marksChart').getContext('2d');
new Chart(ctx2, {
    type: 'pie',
    data: {
        labels: <?= json_encode($grades) ?>,
        datasets: [{
            data: <?= json_encode($marksCount) ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)'
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>

<style>
.card-stats:hover {
    transform: translateY(-5px);
    transition: all 0.3s ease;
}
.icon-big i {
    font-size: 3rem; /* Increased size for all icons */
}
</style>
