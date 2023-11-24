<?php
//Valida se conexao com BD existe
if(!isset($con)){
	//Abre conexao com BD
	require 'inc/config.php';
}

//Config. e CSS da página
include_once 'cima.php';

//Filtro - Graf. Timeline
if(isset($_GET['f1'])){
	$_SESSION['f1'] = mysqli_real_escape_string($con, $_GET['f1']);
	echo '<script>setTimeout(function(){ scrollTo("timeline-tab",1000); },1000);</script>';
} else{
	$_SESSION['f1'] = 'D';
}

//Filtro - Graf. Custos Setores
if(isset($_GET['f2'])){
	$_SESSION['f2'] = mysqli_real_escape_string($con, $_GET['f2']);
} else{
	$_SESSION['f2'] = '-1';
}

//Filtro - Graf. Rede
if(isset($_GET['f3'])){
	$_SESSION['f3'] = mysqli_real_escape_string($con, $_GET['f3']);
	echo '<script>setTimeout(function(){ scrollTo("rede-tab",1000); $("#rede-tab").click(); },1000);</script>';
} else{
	$_SESSION['f3'] = '-1';
}

//Filtro - Tabela - Dispositivos
if(isset($_POST['filtro_imp'])){
	//Salva dados na sessao
	$_SESSION['filtro_imp']      = mysqli_real_escape_string($con, $_POST['dispositivo']);
	$_SESSION['filtro_imp_md']   = mysqli_real_escape_string($con, $_POST['modelo']);
	$_SESSION['filtro_imp_data'] = mysqli_real_escape_string($con, $_POST['data']);
	
	//Apresenta sucesso
	echo '<script>setTimeout(function(){ scrollTo("tabela-trab-imp",1000); swal.fire("Sucesso!", "Filtro aplicado com sucesso!", "success"); },1000);</script>';
}

//Filtro - Tabela - Operações
if(isset($_POST['filtro_users'])){
	//Salva dados na sessao
	$_SESSION['filtro_usuario']    = mysqli_real_escape_string($con, $_POST['usuario']);
	$_SESSION['filtro_setor'] 	   = mysqli_real_escape_string($con, $_POST['setor']);
	$_SESSION['filtro_users_data'] = mysqli_real_escape_string($con, $_POST['data']);
	
	//Apresenta sucesso
	echo '<script>setTimeout(function(){ scrollTo("tabela-ope-info",1000); swal.fire("Sucesso!", "Filtro aplicado com sucesso!", "success"); },1000);</script>';
}

//Exclui Filtros - Tabela - Dispositivos
if(isset($_POST['limpar_filtro_imp'])){
	//Exclui variaveis de sessao
	unset($_SESSION['filtro_imp']);
	unset($_SESSION['filtro_imp_md']);
	unset($_SESSION['filtro_imp_data']);
	
	//Apresenta sucesso
	echo '<script>setTimeout(function(){ scrollTo("tabela-trab-imp",1000); swal.fire("Sucesso!", "Filtros removidos com sucesso!", "success"); },1000);</script>';
}

//Exclui Filtros - Tabela - Operações
if(isset($_POST['limpar_filtro_users'])){
	//Salva dados na sessao
	$_SESSION['filtro_usuario']    = "-1";
	$_SESSION['filtro_setor']      = "-1";
	$_SESSION['filtro_users_data'] = "";
	
	//Apresenta sucesso
	echo '<script>setTimeout(function(){ scrollTo("tabela-ope-info",1000); swal.fire("Sucesso!", "Filtros removidos com sucesso!", "success"); },1000);</script>';
}

//Tabela
$tabela  = 'imp_metricas_table';
$tabela2 = 'imp_metricas_info_table';
$tabela3 = 'imp_metricas_info2_table';

//Janela Ativa
if(isset($_GET['p'])){
	$_SESSION['aba'] = $_GET['p'];
} else{
	if(!isset($_SESSION['aba'])){
		$_SESSION['aba'] = 'dash_print';
	}
}
?>
<!-- Page Wrapper -->
<div id="wrapper">
	<!-- Content Wrapper -->
	<div id="content-wrapper" class="d-flex flex-column">
		<!-- Main Content -->
		<div id="content">
			<!-- Page Heading -->
			<div class="container-fluid p-0">
				<div class="row">
					<div class="col-12">
						<div class="card bg-gradient-warning">
							<div class="card-body">
								<div class="d-flex align-items-center">
									<img src="dist/img/av_chapeco_logo.png" alt="Av. Chapecó - Logomarca" width="70" height="70">
									<div class="ml-2 pl-2" style="border-left: 1px solid #0E8E0E;">
										<h4 class="mb-0" style="color: #0E8E0E;"><strong>AUTO VIAÇÃO CHAPECÓ</strong></h4>
										<h5 class="text-white"><span class="badge bg-primary"><i class="mdi mdi-printer-pos-cog-outline"></i> PrintAdmin</span> / Dashboard</h5>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Begin Page Content -->
			<div class="container-fluid mt-3">
				<!-- Content Row -->
				<div class="row">
					<!-- Total - Páginas -->
					<div class="col-xl-3 col-md-6 mb-4">
						<div class="card border-left-primary shadow h-100 py-2">
							<div class="card-body">
								<div class="row no-gutters align-items-center">
									<div class="col mr-2">
										<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
											Páginas</div>
										<div class="h5 mb-0 font-weight-bold text-gray-800" id="info-geral-pag">0</div>
									</div>
									<div class="col-auto">
										<i class="fas fa-clone fa-2x text-primary"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Total - Dispositivos -->
					<div class="col-xl-3 col-md-6 mb-4">
						<div class="card border-left-success shadow h-100 py-2">
							<div class="card-body">
								<div class="row no-gutters align-items-center">
									<div class="col mr-2">
										<div class="text-xs font-weight-bold text-success text-uppercase mb-1">
											Dispositivos</div>
										<div class="h5 mb-0 font-weight-bold text-gray-800" id="info-geral-imp">0</div>
									</div>
									<div class="col-auto">
										<i class="fas fa-print fa-2x text-success"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Total - Usuários -->
					<div class="col-xl-3 col-md-6 mb-4">
						<div class="card border-left-info shadow h-100 py-2">
							<div class="card-body">
								<div class="row no-gutters align-items-center">
									<div class="col mr-2">
										<div class="text-xs font-weight-bold text-info text-uppercase mb-1">Usuários
										</div>
										<div class="h5 mb-0 font-weight-bold text-gray-800" id="info-geral-usr">0</div>
									</div>
									<div class="col-auto">
										<i class="fas fa-user fa-2x text-info"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Total - Grupos -->
					<div class="col-xl-3 col-md-6 mb-4">
						<div class="card border-left-warning shadow h-100 py-2">
							<div class="card-body">
								<div class="row no-gutters align-items-center">
									<div class="col mr-2">
										<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
											Grupos</div>
										<div class="h5 mb-0 font-weight-bold text-gray-800" id="info-geral-gp">0</div>
									</div>
									<div class="col-auto">
										<i class="fas fa-users fa-2x text-warning"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-12">
						<ul class="nav nav-pills">
						  <li class="nav-item">
							<a class="nav-link <?php if($_SESSION['aba'] == 'dash_print'){ echo 'active bg-warning'; } else{ echo 'text-warning'; } ?>" href="<?php echo $_SERVER['PHP_SELF'].'?p=dash_print'; ?>"><i class="fas fa-print"></i> Dispositivos</a>
						  </li>
						  <li class="nav-item">
							<a class="nav-link <?php if($_SESSION['aba'] == 'dash_users'){ echo 'active bg-warning'; } else{ echo 'text-warning'; } ?>" href="<?php echo $_SERVER['PHP_SELF'].'?p=dash_users'; ?>"><i class="fas fa-users"></i> Usuários/Setores</a>
						  </li>
						</ul>
					</div>
				</div>
				<?php
				//Carrega página de acordo com a janela aberta
				include_once 'pages/dashboard/'.$_SESSION['aba'].'.php';
				?>
			</div>
			<!-- /.container-fluid -->

		</div>
		<!-- End of Main Content -->

		<!-- Footer -->
		<footer class="sticky-footer bg-white">
			<div class="container my-auto">
				<div class="copyright text-center my-auto">
					<span>Copyright &copy; AVCHAPECO 2023</span>
				</div>
			</div>
		</footer>
		<!-- End of Footer -->

	</div>
	<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
	<i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
				<button class="close" type="button" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
			<div class="modal-footer">
				<button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
				<a class="btn btn-primary" href="login.html">Logout</a>
			</div>
		</div>
	</div>
</div>

<?php
//Javascript da página
include_once 'baixo.php';
?>