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
<!-- report generation script -->
 <!-- General JS Scripts -->
<script src="../assets/js/app.min.js"></script>
<!-- Page Specific JS File -->
<!-- DataTables Export Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
  $('#caseInfoTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
      'copy', 'csv', 'excel', 'pdf', 'print'
    ],
    pageLength: 25
  });
});
</script>

<!-- JS Libraies -->
<script src="../assets/bundles/apexcharts/apexcharts.min.js"></script>
<!-- Page Specific JS File -->
<script src="../assets/js/page/index.js"></script>
<!-- Template JS File -->
<script src="../assets/js/scripts.js"></script>
<!-- Custom JS File -->
<script src="../assets/js/custom.js"></script>
<!-- End of report generation script -->
 <!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<!-- jQuery (required) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<!-- DataTables Buttons JS -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.flash.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<!-- Optional libraries (needed for Excel and PDF) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
</script>
<!-- language selection -->
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
<!-- view filter by date -->
 <script>
  function toggleSection(section) {
    const checkbox = document.getElementById('toggle' + capitalize(section));
    const range = document.getElementById(section + 'DateRange');

    if (checkbox.checked) {
      range.classList.remove('d-none');
    } else {
      range.classList.add('d-none');
      // Clear values if unchecked
      range.querySelectorAll('input').forEach(input => input.value = '');
    }
  }

  function autoSubmitIfFilled() {
    const form = document.querySelector('form');
    let ready = true;

    ['created', 'distributed', 'end'].forEach(section => {
      const from = form.querySelector(`input[name="from_${section}"]`);
      const to = form.querySelector(`input[name="to_${section}"]`);
      if ((from && from.value && to && to.value) || (!from || !to)) {
        // ok
      } else {
        ready = false;
      }
    });

    if (ready) form.submit();
  }

  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  // Re-show on load if checkbox is checked
  window.addEventListener('DOMContentLoaded', () => {
    ['created', 'distributed', 'end'].forEach(toggleSection);
  });
</script>
<!-- export files -->
 <script>
 var headerCount = $('#caseReportTable thead th').length;
var firstRowCount = $('#caseReportTable tbody tr:first td').length;

if (headerCount !== firstRowCount) {
  console.error('Column mismatch: Header has ' + headerCount + 
               ' columns, first row has ' + firstRowCount);
}
</script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<!-- jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    // First verify the table structure
    var $table = $('#caseReportTable');
    var headerCount = $table.find('thead th').length;
    var firstRowCount = $table.find('tbody tr:first td').length;
    
    console.log('Header columns:', headerCount);
    console.log('First row columns:', firstRowCount);
    
    if (headerCount !== firstRowCount) {
        console.error('Column count mismatch! Header:', headerCount, 'Body:', firstRowCount);
        // Fallback to basic table if mismatch occurs
        $table.addClass('table').addClass('table-striped');
        return;
    }
    
    // Only initialize DataTables if column counts match
    try {
        $('#caseReportTable').DataTable({
            scrollX: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-sm btn-primary',
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'csv',
                    className: 'btn btn-sm btn-success',
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-info',
                    exportOptions: { columns: ':visible' }
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-sm btn-danger',
                    exportOptions: { columns: ':visible' },
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-secondary',
                    exportOptions: { columns: ':visible' }
                }
            ],
            colReorder: true,
            initComplete: function() {
                console.log('DataTables initialized successfully');
            },
            error: function(e, settings, techNote, message) {
                console.error('DataTables error:', message);
                // Fallback to basic table
                $table.DataTable().destroy();
                $table.addClass('table').addClass('table-striped');
            }
        });
    } catch (e) {
        console.error('DataTables initialization failed:', e);
        // Fallback to basic table
        $table.addClass('table').addClass('table-striped');
    }
});
</script>


  </body>
</html>
<?php ob_end_flush(); ?>