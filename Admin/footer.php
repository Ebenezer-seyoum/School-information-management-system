<footer class="footer">
  <div class="container-fluid">
        <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> 
          Yeki wereda court - All rights reserved |</p>				
  			</div>
  		</footer>
  	</div>
  </div>
	<!--   Core JS Files   -->
	<script src="../assets/js/core/jquery-3.7.1.min.js"></script>
	<script src="../assets/js/core/popper.min.js"></script>
	<script src="../assets/js/core/bootstrap.min.js"></script>
	<!-- jQuery Scrollbar -->
	<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
	<!-- Chart JS -->
	<script src="../assets/js/plugin/chart.js/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<!-- jQuery Sparkline -->
	<script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>
	<!-- Chart Circle -->
	<script src="../assets/js/plugin/chart-circle/circles.min.js"></script>
	<!-- Datatables -->
	<script src="../assets/js/plugin/datatables/datatables.min.js"></script>
	<!-- Bootstrap Notify -->
	<script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
	<!-- jQuery Vector Maps -->
	<script src="../assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
	<script src="../assets/js/plugin/jsvectormap/world.js"></script>
	<!-- Sweet Alert -->
	<script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
	<!-- Kaiadmin JS -->
	<script src="../assets/js/kaiadmin.min.js"></script>
	<script src="../assets/js/profile.js"></script>
	<script src="../assets/js/main.js"></script>
	<script>
		$('#lineChart').sparkline([102,109,120,99,110,105,115], {
			type: 'line',
			height: '70',
			width: '100%',
			lineWidth: '2',
			lineColor: '#177dff',
			fillColor: 'rgba(23, 125, 255, 0.14)'
		});
		$('#lineChart2').sparkline([99,125,122,105,110,124,115], {
			type: 'line',
			height: '70',
			width: '100%',
			lineWidth: '2',
			lineColor: '#f3545d',
			fillColor: 'rgba(243, 84, 93, .14)'
		});
		$('#lineChart3').sparkline([105,103,123,100,95,105,115], {
			type: 'line',
			height: '70',
			width: '100%',
			lineWidth: '2',
			lineColor: '#ffa534',
			fillColor: 'rgba(255, 165, 52, .14)'
		});
	</script>
<!-- for judge_type -->
<script>
 document.querySelectorAll('.judge-type-btn').forEach(function(button) {
 button.addEventListener('click', function(event) {
   event.preventDefault();
   const judgeId = this.dataset.judgeId;
   const judgeType = this.dataset.judgeType;
   const buttonGroup = this.parentElement;
   const buttons = buttonGroup.querySelectorAll('.judge-type-btn');
   const hiddenInput = document.getElementById(`judge_type_${judgeId}`); 
   buttons.forEach(btn => btn.classList.remove('btn-success', 'btn-secondary', 'btn-primary', 'btn-info'));
   if (judgeType === 'primary') {
       this.classList.add('btn-success');
   } else if (judgeType === 'second') {
       this.classList.add('btn-primary');
   } else if (judgeType === 'Third') {
       this.classList.add('btn-info');
   }
   hiddenInput.value = judgeType;
 });
});
</script>
<!-- script for blocking and unblocking users -->
<script>
    setTimeout(function () {
        var successMsg = document.getElementById('successMessage');
        if (successMsg) {
            successMsg.style.display = 'none';
        }
        var errorMsg = document.getElementById('errorMessage');
        if (errorMsg) {
            errorMsg.style.display = 'none';
        }
    }, 2000); 
</script>
<!-- script for case_id automatically generated -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const caseTypeSelect = document.getElementById('case_type');
    const caseIdInput = document.getElementById('case_id');
    caseTypeSelect.addEventListener('change', function () {
        const caseType = this.value;
        if (caseType) {
            fetch('getCaseId.php?case_type=' + caseType)
                .then(response => response.text())
                .then(data => {
                    caseIdInput.value = data;
                });
        } else {
            caseIdInput.value = '';
        }
    });
});
</script>
<!-- select dynamically address -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    // When Region changes → load Zones
    $('select[name="region"]').change(function(){
        var region_id = $(this).val();
        $.post("get_zones.php", {region_id: region_id}, function(data){
            $('#zone').html(data);
            $('#woreda').html('<option value="">Select Woreda</option>'); 
        });
    });
    // When Zone changes → load Woredas
    $('#zone').change(function(){
        var zone_id = $(this).val();
        $.post("get_woredas.php", {zone_id: zone_id}, function(data){
            $('#woreda').html(data);
        });
    });

});
</script>
<!--For Notifications -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function loadNotifications() {
 $.ajax({
   url: 'fetch_notifications.php',
   method: 'GET',
   dataType: 'json',
   success: function(data) {
    $('#notifCount').text(data.length);
    $('#notifTitle').text('You have ' + data.length + ' new notification');
    $('#notifList').empty();
    if(data.length > 0) {
        data.forEach(function(notif) {
            var notifHTML = `
            <a href="#">
                <div class="notif-icon notif-primary"><i class="fa fa-user-plus"></i></div>
                <div class="notif-content">
                    <span class="block">${notif.message}</span>
                    <span class="time">${timeAgo(notif.created_at)}</span>
                </div>
            </a>`;
            $('#notifList').append(notifHTML);
        });
     } else {
        $('#notifList').append('<div class="text-center p-2">No new notifications</div>');
    }
   }
 });
}
function timeAgo(dateStr) {
    var date = new Date(dateStr);
    var now = new Date();
    var diff = Math.floor((now - date) / 1000); 
    if (diff < 60) return diff + ' seconds ago';
    diff = Math.floor(diff / 60); 
    if (diff < 60) return diff + ' minutes ago';
    diff = Math.floor(diff / 60); 
    if (diff < 24) return diff + ' hours ago';
    diff = Math.floor(diff / 24); 
    return diff + ' days ago';
}
setInterval(loadNotifications, 10000);
loadNotifications();
</script>
<!-- case distributer select judge type -->
<script>
document.querySelectorAll('.judge-type-btn').forEach(button => {
    if (!button.dataset.originalClass) {
        button.dataset.originalClass = Array.from(button.classList).find(cls =>
            cls.startsWith('btn-outline-')
        );
    }
});
const roleColorClass = {
    primary: 'btn-success',
    second: 'btn-primary',
    third: 'btn-danger'
};
document.querySelectorAll('.judge-type-btn').forEach(button => {
 button.addEventListener('click', function (e) {
   e.preventDefault();
   const judgeId = this.dataset.judgeId;
   const selectedType = this.dataset.judgeType;
   document.querySelectorAll(`.judge-type-btn[data-judge-type="${selectedType}"]`).forEach(btn => {
       btn.classList.remove('btn-success', 'btn-primary', 'btn-danger');
       btn.classList.remove('disabled');
       if (btn.dataset.originalClass) {
           btn.classList.remove('btn-outline-secondary', 'btn-outline-primary', 'btn-outline-info');
           btn.classList.add(btn.dataset.originalClass);
       }
   });
   document.querySelectorAll(`.judge-type-btn[data-judge-id="${judgeId}"]`).forEach(btn => {
       btn.classList.remove('btn-success', 'btn-primary', 'btn-danger');
       if (btn.dataset.originalClass) {
           btn.classList.remove('btn-outline-secondary', 'btn-outline-primary', 'btn-outline-info');
           btn.classList.add(btn.dataset.originalClass);
       }
       btn.classList.remove('disabled');
   });
   const roleClass = roleColorClass[selectedType];
   this.classList.remove(this.dataset.originalClass);
   this.classList.add(roleClass);
   document.getElementById('judge_type_' + judgeId).value = selectedType;
 });
});
</script>
<!-- Toggle Password Script -->
<script>
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function () {
            const target = document.getElementById(this.getAttribute('data-target'));
            if (target.type === "password") {
                target.type = "text";
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                target.type = "password";
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
</script>
<!-- For language selection -->
 <script>
  function changeLanguage(lang) {
    document.getElementById('selectedLang').textContent = lang;
  }
</script>
<!-- not inner link collapse -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const collapses = document.querySelectorAll('[data-bs-toggle="collapse"]');
    // Restore previous menu
    const openMenuId = localStorage.getItem("openMenuId");
    if (openMenuId) {
        const targetCollapse = document.getElementById(openMenuId);
        if (targetCollapse) {
            const bsCollapse = new bootstrap.Collapse(targetCollapse, {
                toggle: true
            });
        }
    }
    // Save clicked menu ID
    collapses.forEach(function (toggle) {
        toggle.addEventListener("click", function () {
            const targetId = toggle.getAttribute("href").replace("#", "");
            localStorage.setItem("openMenuId", targetId);
        });
    });
});
</script>
<!-- graphically summary -->
<script>
  const labels = <?= json_encode($labels) ?>;
  const statusCounts = <?= json_encode($statusCounts) ?>;
  const typeCounts = <?= json_encode($typeCounts) ?>;
  const ctxCombined = document.getElementById('combinedChart').getContext('2d');
  new Chart(ctxCombined, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Case Status',
          data: statusCounts,
          backgroundColor: '#4e73df'
        },
        {
          label: 'Case Type',
          data: typeCounts,
          backgroundColor: '#1cc88a'
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Case Summary',
          font: { 
            size: 22, 
            weight: 'bold', 
            family: 'Times New Roman' 
          },
          color: '#000'
        },
        tooltip: {
          callbacks: {
            label: function(ctx) {
              return `${ctx.dataset.label}: ${ctx.raw}`;
            }
          },
          titleFont: { weight: 'bold', family: 'Times New Roman' },
          bodyFont: { weight: 'bold', family: 'Times New Roman' },
          titleColor: '#000',
          bodyColor: '#000'
        },
        legend: {
          position: 'top',
          labels: {
            padding: 10,
            font: {
              weight: 'bold',
              family: 'Times New Roman'
            },
            color: '#000'
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Number of Cases',
            font: {
              weight: 'bold',
              family: 'Times New Roman'
            },
            color: '#000'
          },
          ticks: {
            font: {
              weight: 'bold',
              family: 'Times New Roman'
            },
            color: '#000'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Status / Type',
            font: {
              weight: 'bold',
              family: 'Times New Roman'
            },
            color: '#000'
          },
          ticks: {
            font: {
              weight: 'bold',
              family: 'Times New Roman'
            },
            color: '#000'
          }
        }
      }
    }
  });
</script>
<!-- search automatically -->
 
<!-- for users -->
 <script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.querySelector('input[name="search"]');
  const tableBody = document.querySelector('table.table-hover tbody');
  const rows = tableBody.getElementsByTagName('tr');

  const noResultRow = document.createElement('tr');
  noResultRow.innerHTML = `
    <td colspan="8" class="text-center text-danger" style="border: 2px solid black;">
      No user found.
    </td>`;
  noResultRow.style.display = 'none';
  tableBody.appendChild(noResultRow);

  searchInput.addEventListener('keyup', function () {
    const filter = searchInput.value.toLowerCase();
    let visibleRows = 0;

    for (let i = 0; i < rows.length; i++) {
      const row = rows[i];
      if (row === noResultRow) continue;

      const idNumber = row.cells[1]?.textContent.toLowerCase() || '';
      const firstName = row.cells[3]?.textContent.toLowerCase() || '';
      const fatherName = row.cells[4]?.textContent.toLowerCase() || '';
      const userType = row.cells[5]?.textContent.toLowerCase() || '';

      if (
        idNumber.includes(filter) ||
        firstName.includes(filter) ||
        fatherName.includes(filter) ||
        userType.includes(filter)
      ) {
        row.style.display = '';
        visibleRows++;
      } else {
        row.style.display = 'none';
      }
    }

    noResultRow.style.display = visibleRows === 0 ? '' : 'none';
  });
});
</script>
<!-- list user -->
 <script>
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.querySelector('#userSearch');
  const userCards = document.querySelectorAll('.user-card');

  searchInput.addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    let hasVisible = false;

    userCards.forEach(card => {
      const idNumber = card.dataset.idnumber;
      const username = card.dataset.username;
      const userType = card.dataset.usertype;
      const firstName = card.dataset.firstname;

      const match =
        idNumber.includes(filter) ||
        username.includes(filter) ||
        userType.includes(filter) ||
        firstName.includes(filter);

      card.style.display = match ? '' : 'none';
      if (match) hasVisible = true;
    });

    if (!hasVisible) {
      if (!document.querySelector('#noResultMessage')) {
        const noResult = document.createElement('div');
        noResult.id = 'noResultMessage';
        noResult.className = 'text-danger mt-3';
        noResult.innerText = 'No users found.';
        document.querySelector('.row').appendChild(noResult);
      }
    } else {
      const noResultMsg = document.querySelector('#noResultMessage');
      if (noResultMsg) noResultMsg.remove();
    }
  });
});
</script>
<!-- case search -->
 <script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('caseSearch');
  const tableBody = document.querySelector('table.table-hover tbody');
  const rows = tableBody.getElementsByTagName('tr');

  const noResultRow = document.createElement('tr');
  noResultRow.innerHTML = `
    <td colspan="6" class="text-center text-danger" style="border: 2px solid black;">
      No cases found.
    </td>`;
  noResultRow.style.display = 'none';
  tableBody.appendChild(noResultRow);

  searchInput.addEventListener('keyup', function () {
    const filter = searchInput.value.toLowerCase().trim();
    let visibleRows = 0;

    for (let i = 0; i < rows.length; i++) {
      const row = rows[i];
      if (row === noResultRow) continue;

      const caseId = row.cells[1]?.textContent.toLowerCase().trim() || '';
      const plaintiff = row.cells[2]?.textContent.toLowerCase().trim() || '';
      const defendant = row.cells[3]?.textContent.toLowerCase().trim() || '';
      const caseStatus = row.cells[4]?.textContent.toLowerCase().trim() || '';

      const match = caseId.includes(filter) ||
                    plaintiff.includes(filter) ||
                    defendant.includes(filter) ||
                    caseStatus.includes(filter);

      row.style.display = match ? '' : 'none';
      if (match) visibleRows++;
    }

    noResultRow.style.display = visibleRows === 0 ? '' : 'none';
  });
});
</script>
<!-- for select Plantiff and Defendent for file upload visble -->
 <script>
document.addEventListener("DOMContentLoaded", function () {
    const litigantType = document.querySelector("select[name='litigant_type']");
    const plaintiffSection = document.getElementById("plaintiff-section");

    function togglePlaintiffSection() {
        if (litigantType.value === "plaintiff") {
            plaintiffSection.style.display = "flex";
        } else {
            plaintiffSection.style.display = "none";
        }
    }

    litigantType.addEventListener("change", togglePlaintiffSection);
    togglePlaintiffSection(); // Call on page load in case of refresh
});
</script>


<script>
function checkADDPassword() {
    const password = document.getElementById("password").value;
    // Show checklist only when password has content
    const checklist = document.getElementById("password-checklist");
    if (password.length > 0) {
        checklist.style.display = "block";
    } else {
        checklist.style.display = "none";
    }
    // Validate each condition
    document.getElementById("lower").style.color = /[a-z]/.test(password) ? "green" : "red";
    document.getElementById("lower").innerText = /[a-z]/.test(password) ? "✅ One lowercase letter" : "❌ One lowercase letter";
    document.getElementById("upper").style.color = /[A-Z]/.test(password) ? "green" : "red";
    document.getElementById("upper").innerText = /[A-Z]/.test(password) ? "✅ One uppercase letter" : "❌ One uppercase letter";
    document.getElementById("special").style.color = /[@#$%^&+=!]/.test(password) ? "green" : "red";
    document.getElementById("special").innerText = /[@#$%^&+=!]/.test(password) ? "✅ One special character (@#$%^&+=!)" : "❌ One special character (@#$%^&+=!)";
    document.getElementById("length").style.color = password.length >= 8 ? "green" : "red";
    document.getElementById("length").innerText = password.length >= 8 ? "✅ At least 8 characters" : "❌ At least 8 characters";
}
</script>
<script>
function checkPassword() {
    const password = document.getElementById("new_password").value;
    const checklist = document.getElementById("password-checklist");
    // Show checklist if user types something, hide otherwise
    checklist.style.display = password.length > 0 ? "block" : "none";
    // Check lowercase
    const hasLower = /[a-z]/.test(password);
    document.getElementById("lower").style.color = hasLower ? "green" : "red";
    document.getElementById("lower").innerText = hasLower ? "✅ One lowercase letter" : "❌ One lowercase letter";
    // Check uppercase
    const hasUpper = /[A-Z]/.test(password);
    document.getElementById("upper").style.color = hasUpper ? "green" : "red";
    document.getElementById("upper").innerText = hasUpper ? "✅ One uppercase letter" : "❌ One uppercase letter";
    // Check special character
    const hasSpecial = /[@#$%^&+=!]/.test(password);
    document.getElementById("special").style.color = hasSpecial ? "green" : "red";
    document.getElementById("special").innerText = hasSpecial ? "✅ One special character (@#$%^&+=!)" : "❌ One special character (@#$%^&+=!)";
    // Check length
    const hasLength = password.length >= 8;
    document.getElementById("length").style.color = hasLength ? "green" : "red";
    document.getElementById("length").innerText = hasLength ? "✅ At least 8 characters" : "❌ At least 8 characters";
}
</script>

  </body>
</html>
<?php ob_end_flush(); ?>