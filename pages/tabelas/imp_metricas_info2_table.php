<?php
//Valida se conexao com BD existe
if(!isset($con)){
	//Abre conexao com BD
	require '../../inc/config.php';
}

//Alterna entre modos de exibição
if(isset($_GET['aninhar'])){
	//Salva modo na sessão
	if(!isset($_SESSION['tabela_imp_modo']) || $_SESSION['tabela_imp_modo'] == 'E'){
		$_SESSION['tabela_imp_modo'] = 'R';
	} else{
		$_SESSION['tabela_imp_modo'] = 'E';
	}
	
	//Informa qual modo está sendo apresentado
	echo json_encode(['modo' => $_SESSION['tabela_imp_modo']]);
	return;
}

//Monta parametros
$params = $columns = $totalRecords = $data = array();

//Pega parametros enviados da tabela
$params = $_REQUEST;

//Monta colunas
$columns = array(
	0 => 'ad_impressoras.nome',
	1 => 'imp_modelos.marca',
	2 => 'log_metricas.paginas',
	3 => 'log_metricas.tamanho',
	4 => 'data'
);

//Limpa condicoes
$where_contidion = $filtro = $sqlTot = $sqlRec = "";

//Filtros
if(!empty($params['search']['value'])){
	$where_contidion .= " AND ";
	$where_contidion .= " (ad_impressoras.nome LIKE '%".$params['search']['value']."%') ";
}
if(!empty($_SESSION['filtro_imp']) && $_SESSION['filtro_imp'] != '-1'){
	$f1 = " AND ad_impressoras.id = '".$_SESSION['filtro_imp']."' ";
	$filtro .= "$f1";
}
if(!empty($_SESSION['filtro_imp_md']) && $_SESSION['filtro_imp_md'] != '-1'){
	$f2 = " AND imp_modelos.id = '".$_SESSION['filtro_imp_md']."' ";
	$filtro .= "$f2";
}
if(!empty($_SESSION['filtro_imp_data'])){
	//Divide em duas datas
	$exp = explode(' até ', $_SESSION['filtro_imp_data']);
	
	//Formata datas para AAAA-MM-DD
	$exp_inicio = explode('/', $exp[0]);
	$dt_inicio	= $exp_inicio[2].'-'.$exp_inicio[1].'-'.$exp_inicio[0];
	
	$exp_fim = explode('/', $exp[1]);
	$dt_fim	 = $exp_fim[2].'-'.$exp_fim[1].'-'.$exp_fim[0];
	
	//Filtra resultados
	$f3 = " AND ";
	$f3 .= " (DATE_FORMAT(log_metricas.criacao,'%Y-%m-%d') > '$dt_inicio' OR DATE_FORMAT(log_metricas.criacao,'%Y-%m-%d') = '$dt_inicio') ";
	$f3 .= " AND (DATE_FORMAT(log_metricas.criacao,'%Y-%m-%d') < '$dt_fim' OR DATE_FORMAT(log_metricas.criacao,'%Y-%m-%d') = '$dt_fim') ";
	$filtro .= "$f3";
}
if(isset($_SESSION['tabela_imp_modo']) && $_SESSION['tabela_imp_modo'] == 'R'){
	$f4 = 'R';
} else{
	$f4 = 'E';
}

//Select
if($f4 == 'R'){
	if(!empty($_SESSION['filtro_imp_data'])){
		$sql_query = "SELECT ad_impressoras.id, '$dt_inicio ~ $dt_fim' AS data FROM log_metricas, ad_impressoras, imp_modelos WHERE (log_metricas.dispositivo = ad_impressoras.id AND ad_impressoras.marca = imp_modelos.id) $filtro";
	} else{
		$sql_query = "SELECT ad_impressoras.id, 'Todas' AS data FROM log_metricas, ad_impressoras, imp_modelos WHERE (log_metricas.dispositivo = ad_impressoras.id AND ad_impressoras.marca = imp_modelos.id) $filtro";
	}
} else{
	$sql_query = "SELECT ad_impressoras.id, DATE_FORMAT(log_metricas.criacao, '%Y-%m-%d') AS data FROM log_metricas, ad_impressoras, imp_modelos WHERE (log_metricas.dispositivo = ad_impressoras.id AND ad_impressoras.marca = imp_modelos.id) $filtro";
}

//Monta filtro de pesquisa
$sql_query .= $where_contidion;

//Agrupa resultados
$sql_query .= " GROUP BY ad_impressoras.id, data ";

//Concatena nos parametros
$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

//Ordenação
$sqlRec .=  " ORDER BY ". $columns[$params['order'][0]['column']]." ".$params['order'][0]['dir']."  LIMIT ".$params['start']." ,".$params['length']." ";

//Filtros
$queryTot 		= mysqli_query($con, $sqlTot);
$totalRecords 	= mysqli_num_rows($queryTot);
$queryRecords 	= mysqli_query($con, $sqlRec);

while($result = mysqli_fetch_array($queryRecords)){
	//Dados
	$id_imp  = $result['id'];
	$dt_trab = $result['data'];
	
	//Pega dados do dispositivo
	$result_imp = mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM ad_impressoras WHERE id = '$id_imp'"));
	$imp    = $result_imp['nome'];
	$id_md  = $result_imp['marca'];
	
	//Pega ID do Modelo
	$result_imp_md = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM imp_modelos WHERE id = '$id_md'"));
	$modelo = $result_imp_md['marca'];
	
	//Total - Páginas
	$result_pag = mysqli_fetch_assoc(mysqli_query($con,"SELECT SUM(paginas) AS qtd FROM log_metricas WHERE dispositivo = '$id_imp' ".($f4 == 'E' ? "AND DATE_FORMAT(criacao, '%Y-%m-%d') = '$dt_trab'" : (!empty($_SESSION['filtro_imp_data']) ? "$f3" : "")).""));
	$pag = $result_pag['qtd'];
	
	//Total - MB
	$result_mb = mysqli_fetch_assoc(mysqli_query($con,"SELECT SUM(tamanho) AS qtd FROM log_metricas WHERE dispositivo = '$id_imp' ".($f4 == 'E' ? "AND DATE_FORMAT(criacao, '%Y-%m-%d') = '$dt_trab'" : (!empty($_SESSION['filtro_imp_data']) ? "$f3" : "")).""));
	$bytes = $result_mb['qtd'];
	
	//Transforma bytes em MB
	$mb	= bytesToMegabytes($bytes).' MB';
	
	//Formata data para DD/MM/AAAA
	if($f4 == 'E'){
		$dt_trab = date('d/m/y', strtotime($dt_trab));
	} else{
		if(!empty($_SESSION['filtro_imp_data'])){
			$dt_trab = date('d/m/y', strtotime($dt_inicio)).' ~ '.date('d/m/y', strtotime($dt_fim));
		}
	}
	
	//Colunas
	$col_imp 	 = "<h6 class=\"text-primary\"><i class=\"fas fa-print\"></i> $imp</h6>";
	$col_imp_md  = "<h6><span class=\"badge bg-danger text-white\">$modelo</span></h6>";
	$col_tra_pag = "<h6 class=\"text-info\"><i class=\"fas fa-clone\"></i> $pag</h6>";
	$col_tra_mb  = "<h6>$mb</h6>";
	$col_data	 = "<h6><i class=\"mdi mdi-calendar-clock-outline\"></i> $dt_trab</h6>";
	
	//Formata dados para exibição na tabela
	$data[] = array(
		0 => "$col_imp",
		1 => "$col_imp_md",
		2 => "$col_tra_pag",
		3 => "$col_tra_mb",
		4 => "$col_data"
	);
}

//Monta Json													   
$json_data = array(
	"draw"            => intval( $params['draw'] ),   
	"recordsTotal"    => intval( $totalRecords ),  
	"recordsFiltered" => intval( $totalRecords ),
	"data"            => $data
);

//Imprime resultado
echo json_encode($json_data);