<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="PrintAdmin - Gestão de Impressoras e Recursos">
    <meta name="author" content="avchapeco.com.br">

    <title>PrintAdmin - Dashboard</title>
	
	<!-- Icone -->
	<link href="../dist/img/printadmin_icon.png" rel="icon" type="image/x-icon">
	
    <!-- Custom fonts for this template-->
    <link href="../assets/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../dist/css/sb-admin-2.min.css" rel="stylesheet">
	<link href="../assets/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
	<link href="../assets/datatables/buttons.bootstrap4.min.css" rel="stylesheet">
	<link href="../assets/date-range-picker/daterangepicker.min.css" rel="stylesheet">
	<link href="../assets/sweetalert2/sweetalert2.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.3.67/css/materialdesignicons.min.css" rel="stylesheet">
	
	<?php
	//Substitui estilo padrão da página
	echo '<link href="../dist/css/style.css?_='.time().'" rel="stylesheet">';
	?>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col-4">
				<canvas id="bar-chart-1"></canvas>
				<script>
				setTimeout(function(){
					var ctx = document.getElementById('bar-chart-1');
					var myBarChart = new Chart(ctx, {
					  type: 'bar',
					  data: {
						labels: ['Coluna 01', 'Coluna 02', 'Coluna 03'],
						datasets: [{
						  label: 'Teste',
						  backgroundColor: "#4e73df",
						  hoverBackgroundColor: "#2e59d9",
						  borderColor: "#4e73df",
						  data: [10, 20, 30],
						}],
					  },
					  options: {
						maintainAspectRatio: false,
						layout: {
						  padding: {
							left: 10,
							right: 25,
							top: 25,
							bottom: 0
						  }
						},
						scales: {
						  xAxes: [{
							time: {
							  unit: 'month'
							},
							gridLines: {
							  display: false,
							  drawBorder: false
							},
							ticks: {
							  maxTicksLimit: 6
							},
							maxBarThickness: 25,
						  }],
						  yAxes: [{
							ticks: {
							  min: 0,
							  max: 100,
							  maxTicksLimit: 5,
							  padding: 10,
							  // Include a dollar sign in the ticks
							  callback: function(value, index, values) {
								return number_format(value) + ' R$';
							  }
							},
							gridLines: {
							  color: "rgb(234, 236, 244)",
							  zeroLineColor: "rgb(234, 236, 244)",
							  drawBorder: false,
							  borderDash: [2],
							  zeroLineBorderDash: [2]
							}
						  }],
						},
						legend: {
						  display: false
						},
						tooltips: {
						  titleMarginBottom: 10,
						  titleFontColor: '#6e707e',
						  titleFontSize: 14,
						  backgroundColor: "rgb(255,255,255)",
						  bodyFontColor: "#858796",
						  borderColor: '#dddfeb',
						  borderWidth: 1,
						  xPadding: 15,
						  yPadding: 15,
						  displayColors: false,
						  caretPadding: 10,
						  callbacks: {
							label: function(tooltipItem, chart) {
							  var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
							  return datasetLabel + ': ' + number_format(tooltipItem.yLabel) + ' R$';
							}
						  }
						},
					  }
					});
				},1000);
				</script>
			</div>
			<div class="col-4">
				<canvas id="bar-chart-2"></canvas>
				<script>
				setTimeout(function(){
					var ctx = document.getElementById('bar-chart-2');
					var myBarChart = new Chart(ctx, {
					  type: 'bar',
					  data: {
						labels: ['Coluna 01', 'Coluna 02', 'Coluna 03'],
						datasets: [{
						  label: 'Teste',
						  backgroundColor: "#4e73df",
						  hoverBackgroundColor: "#2e59d9",
						  borderColor: "#4e73df",
						  data: [10, 20, 30],
						}],
					  },
					  options: {
						maintainAspectRatio: false,
						layout: {
						  padding: {
							left: 10,
							right: 25,
							top: 25,
							bottom: 0
						  }
						},
						scales: {
						  xAxes: [{
							time: {
							  unit: 'month'
							},
							gridLines: {
							  display: false,
							  drawBorder: false
							},
							ticks: {
							  maxTicksLimit: 6
							},
							maxBarThickness: 25,
						  }],
						  yAxes: [{
							ticks: {
							  min: 0,
							  max: 100,
							  maxTicksLimit: 5,
							  padding: 10,
							  // Include a dollar sign in the ticks
							  callback: function(value, index, values) {
								return number_format(value) + ' R$';
							  }
							},
							gridLines: {
							  color: "rgb(234, 236, 244)",
							  zeroLineColor: "rgb(234, 236, 244)",
							  drawBorder: false,
							  borderDash: [2],
							  zeroLineBorderDash: [2]
							}
						  }],
						},
						legend: {
						  display: false
						},
						tooltips: {
						  titleMarginBottom: 10,
						  titleFontColor: '#6e707e',
						  titleFontSize: 14,
						  backgroundColor: "rgb(255,255,255)",
						  bodyFontColor: "#858796",
						  borderColor: '#dddfeb',
						  borderWidth: 1,
						  xPadding: 15,
						  yPadding: 15,
						  displayColors: false,
						  caretPadding: 10,
						  callbacks: {
							label: function(tooltipItem, chart) {
							  var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
							  return datasetLabel + ': ' + number_format(tooltipItem.yLabel) + ' R$';
							}
						  }
						},
					  }
					});
				},1000);
				</script>
			</div>
			<div class="col-4">
				<canvas id="bar-chart-3"></canvas>
				<script>
				setTimeout(function(){
					var ctx = document.getElementById('bar-chart-3');
					var myBarChart = new Chart(ctx, {
					  type: 'bar',
					  data: {
						labels: ['Coluna 01', 'Coluna 02', 'Coluna 03'],
						datasets: [{
						  label: 'Teste',
						  backgroundColor: "#4e73df",
						  hoverBackgroundColor: "#2e59d9",
						  borderColor: "#4e73df",
						  data: [10, 20, 30],
						}],
					  },
					  options: {
						maintainAspectRatio: false,
						layout: {
						  padding: {
							left: 10,
							right: 25,
							top: 25,
							bottom: 0
						  }
						},
						scales: {
						  xAxes: [{
							time: {
							  unit: 'month'
							},
							gridLines: {
							  display: false,
							  drawBorder: false
							},
							ticks: {
							  maxTicksLimit: 6
							},
							maxBarThickness: 25,
						  }],
						  yAxes: [{
							ticks: {
							  min: 0,
							  max: 100,
							  maxTicksLimit: 5,
							  padding: 10,
							  // Include a dollar sign in the ticks
							  callback: function(value, index, values) {
								return number_format(value) + ' R$';
							  }
							},
							gridLines: {
							  color: "rgb(234, 236, 244)",
							  zeroLineColor: "rgb(234, 236, 244)",
							  drawBorder: false,
							  borderDash: [2],
							  zeroLineBorderDash: [2]
							}
						  }],
						},
						legend: {
						  display: false
						},
						tooltips: {
						  titleMarginBottom: 10,
						  titleFontColor: '#6e707e',
						  titleFontSize: 14,
						  backgroundColor: "rgb(255,255,255)",
						  bodyFontColor: "#858796",
						  borderColor: '#dddfeb',
						  borderWidth: 1,
						  xPadding: 15,
						  yPadding: 15,
						  displayColors: false,
						  caretPadding: 10,
						  callbacks: {
							label: function(tooltipItem, chart) {
							  var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
							  return datasetLabel + ': ' + number_format(tooltipItem.yLabel) + ' R$';
							}
						  }
						},
					  }
					});
				},1000);
				</script>
			</div>
		</div>
	</div>
	
	<!-- Bootstrap core JavaScript-->
	<script src="../assets/jquery/jquery.min.js"></script>
	<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

	<!-- Core plugin JavaScript-->
	<script src="../assets/jquery-easing/jquery.easing.min.js"></script>

	<!-- Custom scripts for all pages-->
	<script src="../dist/js/sb-admin-2.min.js"></script>

	<!-- Page level plugins -->
	<script src="../assets/chart.js/Chart.min.js"></script>
	<script src="../assets/datatables/jquery.dataTables.min.js"></script>
	<script src="../assets/datatables/dataTables.bootstrap4.min.js"></script>
	<script src="../assets/datatables/dataTables.buttons.min.js"></script>
	<script src="../assets/jszip/jszip.min.js"></script>
	<script src="../assets/pdfmake/pdfmake.min.js"></script>
	<script src="../assets/pdfmake/vfs_fonts.js"></script>
	<script src="../assets/datatables/buttons.html5.min.js"></script>
	<script src="../assets/datatables/buttons.print.min.js"></script>
	<script src="../assets/moment.js/moment.min.js"></script>
	<script src="../assets/date-range-picker/jquery.daterangepicker.min.js"></script>
	<script src="../assets/sweetalert2/sweetalert2.all.min.js"></script>
	
	<!-- Page level custom scripts -->
	<script src="../dist/js/demo/chart-area-demo.js"></script>
	<script src="../dist/js/demo/chart-bar-demo.js"></script>
	<script src="../dist/js/demo/chart-pie-demo.js"></script>
	<script src="../dist/js/script.js?_=<?php echo time(); ?>"></script>
</body>
</html>