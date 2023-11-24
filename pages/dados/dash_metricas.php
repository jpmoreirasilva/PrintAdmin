<?php
//Valida se conexao com BD existe
if(!isset($con)){
	//Abre conexao com BD
	require '../../inc/config.php';
}

//Total de páginas impressas
$result_qtd_pag = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(paginas) AS total FROM log_metricas"));
if(!is_null($result_qtd_pag['total'])){
	$info_geral_pag = $result_qtd_pag['total'];
} else{
	$info_geral_pag = 0;
}

//Total de impressoras
$result_qtd_imp = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM ad_impressoras"));
$info_geral_imp = $result_qtd_imp['total'];

//Total de usuários
$result_qtd_usr = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM ad_usuarios"));
$info_geral_usr = $result_qtd_usr['total'];

//Total de grupos
$result_qtd_gp = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM ad_grupos"));
$info_geral_gp = $result_qtd_gp['total'];

//Dados atual
$dt_hoje = time();

//Pega dias da semana no idioma PT-BR
$dias_semana = ['Seg', 'Ter', 'Quar', 'Quin', 'Sex', 'Sab', 'Dom'];

//Pega meses do ano no idioma PT-BR
$meses = ['Jan', 'Fev', 'Mar', 'Mai', 'Abr', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

//Pega custo de cada página impressa
$result_custo = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'custo_pag'"));
$custo = $result_custo['valor'];

//Janela - Dispositivos
if($_SESSION['aba'] == 'dash_print'){
	// ---------------- Graf. Impressões ---------------- //
	
	$labels = "";
	$values = "";
	$total_pag = 0;
	
	for($i = 6; $i >= 0; $i--){
		//Valida se filtra resultados por mes
		if(isset($_SESSION['f1']) && $_SESSION['f1'] == 'M'){
			//Subtrai da data 1 mes
			$data = date("Y-m", strtotime("-$i months", $dt_hoje));
			
			//Nome do mes do ano
			$nr = date("n", strtotime("-$i months", $dt_hoje)) - 1;
			$nome = $meses[$nr];
			
			//Pega Qtd. de Impressões feitas no mes
			$result_imp = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(paginas) AS qtd FROM log_metricas WHERE DATE_FORMAT(criacao, '%Y-%m') = '$data'"));
			$result_qtd_imp = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS resultado FROM log_metricas WHERE DATE_FORMAT(criacao, '%Y-%m') = '$data'"));
		} else{
			//Subtrai da data 1 dia
			$data  = date("Y-m-d", strtotime("-$i days", $dt_hoje));
			
			//Nome do dia de ontem
			$nr = date("N", strtotime("-$i days", $dt_hoje)) - 1;
			$nome = $dias_semana[$nr];
			
			//Pega Qtd. de Impressões feitas no dia
			$result_imp = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(paginas) AS qtd FROM log_metricas WHERE DATE_FORMAT(criacao, '%Y-%m-%d') = '$data'"));
			$result_qtd_imp = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS resultado FROM log_metricas WHERE DATE_FORMAT(criacao, '%Y-%m-%d') = '$data'"));
		}
		
		//Valida se ao menos 1 impressao foi feita
		if($result_qtd_imp['resultado'] > '0'){
			$total = $result_imp['qtd'];
		} else{
			$total = 0;
		}
		
		//Adiciona ao total de páginas impressas
		$total_pag += $total;
		
		//Separa valores por virgula
		if($i < '6'){
			$labels .= ",";
			$values .= ",";
		}
		
		//Popula array com dados
		$labels .= "'$nome'";
		$values .= "$total";
	}

	//Monta arrays
	$tarjas = "[$labels]";
	$dados  = "[$values]";

	//Graf. Impressoes Diarias
	$graf_imp_dia = "
	<div class=\"w-100 d-block text-right\"><span class=\"mr-2\">Total: </span><span class=\"text-info\"><b><i class=\"fas fa-clone\"></i> $total_pag Páginas</b></span></div>
	<canvas id=\"line-chart-1\"></canvas>
	<script>
		setTimeout(function(){
			montarGraficoJS('line-chart-1', $tarjas, $dados, 'Páginas');
		},1000);
	</script>";
	
	// ---------------- Graf. Custos Impressoras ---------------- //

	//Filtro
	if(isset($_SESSION['f3']) && $_SESSION['f3'] != '-1'){
		$d = $_SESSION['f3'];
		$filtro = " AND DATE_FORMAT(criacao, '%Y-%m-%d') > '".date("Y-m-d", strtotime("-$d days"))."' ";
	} else{
		$filtro = "";
	}
	
	//Impressoras
	$sql_impressoras = mysqli_query($con, "SELECT * FROM ad_impressoras WHERE (SELECT COUNT(*) FROM log_metricas WHERE dispositivo = ad_impressoras.id $filtro) > '0'");

	$labels = "";
	$values = "";
	$custos_imp = 0;

	$i = 0;
	while($result_imp = mysqli_fetch_array($sql_impressoras)){
		//Dados
		$id_imp 	= $result_imp['id'];
		$impressora	= $result_imp['nome'];
		
		//Limita nome da impressora a 10 caracteres
		if(strlen($impressora) > '10'){
			$dispositivo = substr($impressora, 0, 10).'...';
		} else{
			$dispositivo = $impressora;
		}
		
		//Qtd. de Impressões feitas pelo dispositivo
		$result_qtd_imp = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(paginas) AS qtd FROM log_metricas WHERE dispositivo = '$id_imp' $filtro"));
		$total = $result_qtd_imp['qtd'];
		
		//Calcula valor total dos custos pelo dispositivo
		$valor	  = $custo * $total;
		$despesas = number_format($valor, 2, '.', '');
		
		//Adiciona ao custo total das impressoras
		$custos_imp += $valor;
		
		//Separa valores por virgula
		if($i > '0'){
			$labels .= ",";
			$values .= ",";
		}
		
		//Popula array com dados
		$labels .= "'$dispositivo'";
		$values .= "$despesas";
		
		$i++;
	}
	
	//Formata valor para o real
	$custos_imp_br = number_format($custos_imp, 2, ',', '.');
	
	//Monta arrays
	$tarjas = "[$labels]";
	$dados  = "[$values]";

	//Graf. Custos Impressoras
	$graf_imp_custos = "
	<div class=\"w-100 d-block text-right\"><span class=\"mr-2\">Total: </span><span class=\"text-info\"><b>$custos_imp_br R$</b></span></div>
	<canvas id=\"bar-chart-1\"></canvas>
	<script>
		setTimeout(function(){
			montarGrafSetorJS('bar-chart-1', $tarjas, $dados, 'Custos');
		},1000);
	</script>";
	
	// ---------------- Graf. Uso das Impressoras ---------------- //

	//Pega total de impressoes feitas
	$result_qtd_imp = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(paginas) AS qtd FROM log_metricas"));
	$total = $result_qtd_imp['qtd'];

	//Pega as 10 impressoras mais utilizadas
	$sql_impressoras = mysqli_query($con, "SELECT *, (SELECT SUM(paginas) FROM log_metricas WHERE dispositivo = ad_impressoras.id) AS qtd FROM ad_impressoras WHERE (SELECT COUNT(*) FROM log_metricas WHERE dispositivo = ad_impressoras.id) > '0'");

	//Gráfico - Parametros
	$labels = "";
	$values = "";

	//Tarja com info do grafico
	$trj_info = "";

	//Cores da tarja de informacoes
	$trj_cores = ['#e60049', '#0bb4ff', '#50e991', '#e6d800', '#9b19f5', '#ffa300', '#dc0ab4', '#b3d4ff', '#00bfa0'];

	$i = 0;
	while($impressora = mysqli_fetch_array($sql_impressoras)){
		//Dados
		$nome 	 = $impressora['nome'];
		$paginas = $impressora['qtd'];
		
		//Porcentagem
		$porc = calcularPorcentagem($paginas, $total);
		
		//Limita nome da impressora a 15 caracteres
		if(strlen($nome) > '15'){
			$dispositivo = substr($nome, 0, 15).'...';
		} else{
			$dispositivo = $nome;
		}
		
		//Separa valores por virgula
		if($i > '0'){
			$labels .= ",";
			$values .= ",";
		}
		
		//Popula array com dados
		$labels .= "'$dispositivo'";
		$values .= "$porc";
		
		//Monta tarja de informacoes
		$trj_info .= '<span class="mr-2"><i class="fas fa-circle" style="color:'.$trj_cores[$i].';"></i> '.$dispositivo.'</span>';
		
		$i++;
	}

	//Monta arrays
	$tarjas = "[$labels]";
	$dados  = "[$values]";

	//Graf. Uso Impressoras
	$graf_uso_imp = '
	<div class="chart-pie pt-4 pb-2">
		<canvas id="pizza-chart-1"></canvas>
	</div>
	<div class="mt-4 text-center small">'.$trj_info.'</div>
	<script>
		setTimeout(function(){
			montarGraficoPizzaJS("pizza-chart-1", '.$tarjas.', '.$dados.');
		},1000);
	</script>';
	
	// ---------------- Monitoramento ---------------- //
	
	//Impressoras
	$sql_impressoras = mysqli_query($con,"SELECT ad_impressoras.id, ad_impressoras.nome, ad_impressoras.ip, ad_impressoras.status, imp_modelos.marca FROM ad_impressoras, imp_modelos WHERE ad_impressoras.marca = imp_modelos.id");
	
	//HTML
	$html = '<div class="row">';
	
	while($result_imp = mysqli_fetch_array($sql_impressoras)){
		//Dados
		$impid 		= $result_imp['id'];
		$impressora = $result_imp['nome'];
		$ip			= $result_imp['ip'];
		$status		= $result_imp['status'];
		$marca		= $result_imp['marca'];
		
		//Pega níveis de suprimentos das impressoras
		$sql_suprimentos = mysqli_query($con,"SELECT * FROM imp_suprimentos WHERE dispositivo = '$impid'");
		
		//Dados - Gráfico
		$sup_desc = "";
		$sup_cor  = "";
		$sup_lvl  = "";
		
		$i = 0;
		while($result_sup = mysqli_fetch_array($sql_suprimentos)){
			if($i > '0'){
				$sup_desc .= ", ";
				$sup_cor  .= ", ";
				$sup_lvl  .= ", ";
			}
			//Monta dados do Gráfico
			$sup_desc .= "'".$result_sup['descricao']."'";
			$sup_cor  .= "'".$result_sup['cor']."'";
			$sup_lvl  .= $result_sup['level'];
			$i++;
		}
		
		//Envolve em colchetes
		$imp_sup_desc = "[$sup_desc]";
		$imp_sup_cor  = "[$sup_cor]";
		$imp_sup_lvl  = "[$sup_lvl]";
		
		//Imagem
		if($marca == 'Epson'){
			$ft_imp = 'imp_epson.png';
		} elseif($marca == 'Brother'){
			$ft_imp = 'imp_brother.png';
		} else{
			$ft_imp = 'imp_generic.png';
		}
		
		//Status
		if($status == 'ON'){
			$st_imp = '<span class="badge bg-success text-white">Online</span>';
		} elseif($status == 'OFF'){
			$st_imp = '<span class="badge bg-danger text-white">Offline</span>';
		} else{
			$st_imp = '<span class="badge bg-secondary text-white">Desconhecido</span>';
		}
		
		//Monta HTML
		$html .= '
		<div class="col-sm-12 col-md-6 col-lg-4 text-center">
			<img src="dist/img/'.$ft_imp.'" alt="Impressora" width="200" height="200">
			<div class="container pl-5 pr-5">
				<div class="row align-items-center">
					<div class="col-6 p-0">
						<h6 class="mb-1">'.$impressora.'</h6><span class="badge bg-dark text-white mb-1 mr-1">'.$marca.'</span>'.$st_imp.'<br><span class="badge bg-primary text-white mb-1"><i class="mdi mdi-monitor"></i> '.$ip.'</span>
					</div>
					<div class="col-6 p-0">
						<canvas id="monit-imp-'.$impid.'" height="100"></canvas>
						<script>
						setTimeout(function(){
							var desc  = '.$imp_sup_desc.';
							var cor   = '.$imp_sup_cor.';
							var dados = '.$imp_sup_lvl.';
							
							var ctx = document.getElementById("monit-imp-'.$impid.'");
							var myBarChart = new Chart(ctx, {
							  type: "bar",
							  data: {
								labels:  desc,
								datasets: [{
								  label: "Nível",
								  backgroundColor: cor,
								  hoverBackgroundColor: cor,
								  borderColor: cor,
								  data: dados,
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
									  unit: "month"
									},
									gridLines: {
									  display: false,
									  drawBorder: false
									},
									ticks: {
									  display: false,
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
										return number_format(value) + "%";
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
								  titleFontColor: "#6e707e",
								  titleFontSize: 14,
								  backgroundColor: "rgb(255,255,255)",
								  bodyFontColor: "#858796",
								  borderColor: "#dddfeb",
								  borderWidth: 1,
								  xPadding: 15,
								  yPadding: 15,
								  displayColors: false,
								  caretPadding: 10,
								  callbacks: {
									label: function(tooltipItem, chart) {
									  var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || "";
									  return datasetLabel + ": " + number_format(tooltipItem.yLabel) + "%";
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
		</div>';
	}
	
	//Fecha tags
	$html .= '</div>';
	
	//Monitoramento
	$monit_impressoras = $html;
	
	//Retorna dados
	echo json_encode([
		0 => [
			'id' => 'info-geral-pag',
			'valor' => $info_geral_pag
		],
		1 => [
			'id' => 'info-geral-imp',
			'valor' => $info_geral_imp
		],
		2 => [
			'id' => 'info-geral-usr',
			'valor' => $info_geral_usr
		],
		3 => [
			'id' => 'info-geral-gp',
			'valor' => $info_geral_gp
		],
		4 => [
			'id' => 'graf-imp-dia',
			'valor' => $graf_imp_dia
		],
		5 => [
			'id' => 'graf-uso-imp',
			'valor' => $graf_uso_imp
		],
		6 => [
			'id' => 'graf-imp-custos',
			'valor' => $graf_imp_custos
		],
		7 => [
			'id' => 'monit-impressoras',
			'valor' => $monit_impressoras
		],
	]);
}

//Janela - Usuários/Setores
if($_SESSION['aba'] == 'dash_users'){
	// ---------------- Graf. Custos Setores ---------------- //

	//Filtro
	if(isset($_SESSION['f2']) && $_SESSION['f2'] != '-1'){
		$d = $_SESSION['f2'];
		$filtro = " AND DATE_FORMAT(criacao, '%Y-%m-%d') > '".date("Y-m-d", strtotime("-$d days"))."' ";
	} else{
		$filtro = "";
	}

	//Setores
	$sql_setores = mysqli_query($con, "SELECT * FROM ad_grupos_info WHERE (SELECT COUNT(*) FROM log_metricas WHERE dono IN (SELECT id_usuario FROM ad_grupos WHERE id_grupo = ad_grupos_info.id) $filtro) > '0'");

	$labels = "";
	$values = "";

	$i = 0;
	while($result_setor = mysqli_fetch_array($sql_setores)){
		//Dados
		$id_setor = $result_setor['id'];
		$nome	  = $result_setor['grupo'];
		
		//Limita nome do grupo a 15 caracteres
		if(strlen($nome) > '15'){
			$setor = substr($nome, 0, 15).'...';
		} else{
			$setor = $nome;
		}
		
		//Qtd. de Impressões feitas pelo Setor
		$result_qtd_imp = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(paginas) AS qtd FROM log_metricas WHERE dono IN (SELECT id_usuario FROM ad_grupos WHERE id_grupo = '$id_setor') $filtro"));
		$total = $result_qtd_imp['qtd'];
		
		//Calcula valor total dos custos pelo Setor
		$valor	  = $custo * $total;
		$despesas = number_format($valor, 2, '.', '');
		
		//Separa valores por virgula
		if($i > '0'){
			$labels .= ",";
			$values .= ",";
		}
		
		//Popula array com dados
		$labels .= "'$setor'";
		$values .= "$despesas";
		
		$i++;
	}

	//Monta arrays
	$tarjas = "[$labels]";
	$dados  = "[$values]";

	//Graf. Custos Setores
	$graf_imp_setor = "
	<canvas id=\"bar-chart-1\"></canvas>
	<script>
		setTimeout(function(){
			montarGrafSetorJS('bar-chart-1', $tarjas, $dados, 'Custos');
		},1000);
	</script>";

	// ---------------- Rank - Usuários ---------------- //

	//Pega 5 usuários com maior número de impressoes
	$sql_usuarios = mysqli_query($con, "SELECT ad_usuarios.id, ad_usuarios.nome, SUM(log_metricas.paginas) AS paginas FROM ad_usuarios, log_metricas WHERE ad_usuarios.id = log_metricas.dono GROUP BY ad_usuarios.id, ad_usuarios.nome ORDER BY paginas DESC LIMIT 0,5");

	//HTML
	$html = '';

	while($result_user = mysqli_fetch_array($sql_usuarios)){
		//Dados
		$id		 = $result_user['id'];
		$nome	 = $result_user['nome'];
		$paginas = $result_user['paginas'];
		
		//Pega setor o qual usuário pertence
		$result_gp = mysqli_fetch_assoc(mysqli_query($con, "SELECT ad_grupos_info.* FROM ad_grupos_info, ad_grupos WHERE (ad_grupos_info.id = ad_grupos.id_grupo AND ad_grupos.id_usuario = '$id') ORDER BY ad_grupos_info.criacao DESC LIMIT 0,1"));
		$setor = $result_gp['grupo'];
		
		//Calcula total de custos que teve com o usuário
		$valor   = $custo * $paginas;
		$despesa = number_format($valor, 2, ',', '.').' R$';
		
		//Monta HTML
		$html .= '
		<div class="row mb-3">
			<div class="col-6">
				<h6 class="text-primary mb-0"><i class="fas fa-user"></i> '.$nome.'</h6> / <span class="badge bg-danger text-white">'.$setor.'</span>
			</div>
			<div class="col-6 text-right">
				<h6 class="text-info d-inline mb-0"><i class="fas fa-clone"></i> '.$paginas.'</span> ~ '.$despesa.'
			</div>
		</div>';
	}

	//Rank - Usuários
	$rank_usuarios = $html;

	//Retorna dados
	echo json_encode([
		0 => [
			'id' => 'info-geral-pag',
			'valor' => $info_geral_pag
		],
		1 => [
			'id' => 'info-geral-imp',
			'valor' => $info_geral_imp
		],
		2 => [
			'id' => 'info-geral-usr',
			'valor' => $info_geral_usr
		],
		3 => [
			'id' => 'info-geral-gp',
			'valor' => $info_geral_gp
		],
		4 => [
			'id' => 'graf-imp-setor',
			'valor' => $graf_imp_setor
		],
		5 => [
			'id' => 'rank-usuarios',
			'valor' => $rank_usuarios
		]
	]);
}