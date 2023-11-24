<!-- Bootstrap core JavaScript-->
<script src="assets/jquery/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="assets/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="dist/js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="assets/chart.js/Chart.min.js"></script>
<script src="assets/datatables/jquery.dataTables.min.js"></script>
<script src="assets/datatables/dataTables.bootstrap4.min.js"></script>
<script src="assets/datatables/dataTables.buttons.min.js"></script>
<script src="assets/jszip/jszip.min.js"></script>
<script src="assets/pdfmake/pdfmake.min.js"></script>
<script src="assets/pdfmake/vfs_fonts.js"></script>
<script src="assets/datatables/buttons.html5.min.js"></script>
<script src="assets/datatables/buttons.print.min.js"></script>
<script src="assets/moment.js/moment.min.js"></script>
<script src="assets/date-range-picker/jquery.daterangepicker.min.js"></script>
<script src="assets/sweetalert2/sweetalert2.all.min.js"></script>

<!-- Page level custom scripts -->
<script src="dist/js/demo/chart-area-demo.js"></script>
<script src="dist/js/demo/chart-bar-demo.js"></script>
<script src="dist/js/demo/chart-pie-demo.js"></script>
<script src="dist/js/script.js?_=<?php echo time(); ?>"></script>

<?php
//Carrega dados do Dashboard
echo '
<script>
$(document).ready(function(){
	$.ajax({
		url: "pages/dados/dash_metricas.php",
		type: "GET",
		dataType: "html",
		async: false,
		cache: false,
		success: function(retorno){
			var dados = JSON.parse(retorno);
			for(var i=0; i < dados.length; i++){
				var id = dados[i]["id"];
				var valor = dados[i]["valor"];
				
				$("#"+id).html(valor);
			}
		},
		fail: function(){
			alert("Erro nao esperado, por favor recarregue a pagina!");
		}
	});
});
</script>';

//Filtra resultado apresentado nos graficos
if($_SESSION['aba'] == 'dash_print'){
	echo '
	<script>
	$(document).ready(function(){
		$("#filtro-graf-imp").change(function(){
			window.location.href = "'.$par_site.'/index.php?f1=" + $("#filtro-graf-imp").val();
		});
		$("#filtro-graf-custos-imp").change(function(){
			window.location.href = "'.$par_site.'/index.php?f3=" + $("#filtro-graf-custos-imp").val();
		});
	});
	</script>';
}
if($_SESSION['aba'] == 'dash_users'){
	echo '
	<script>
	$(document).ready(function(){
		$("#filtro-graf-setor").change(function(){
			window.location.href = "'.$par_site.'/index.php?f2=" + $("#filtro-graf-setor").val();
		});
	});
	</script>';
}

//DataTables
include_once 'pages/tabelas/datatables_ini.php';

//Recarrega tabelas a cada 60s
if($_SESSION['aba'] == 'dash_print'){
	echo '
	<script>
	$(document).ready(function(){
		setInterval(function(){ $("#'.$tabela3.'").DataTable().ajax.reload(null, false); },60000);
	});
	</script>';
}
if($_SESSION['aba'] == 'dash_users'){
	echo '
	<script>
	$(document).ready(function(){
		setInterval(function(){ $("#'.$tabela.'").DataTable().ajax.reload(null, false); },60000);
		setInterval(function(){ $("#'.$tabela2.'").DataTable().ajax.reload(null, false); },60000);
	});
	</script>';
}

//Form
echo '<script src="dist/js/daterange_ini.js?_='.time().'"></script>';
?>
</body>

</html>