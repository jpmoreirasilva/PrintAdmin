<?php
//Valida se conexao com BD existe
if(!isset($con)){
	//Abre conexao com BD
	require '../../inc/config.php';
}

//Monta parametros
$params = $columns = $totalRecords = $data = array();

//Pega parametros enviados da tabela
$params = $_REQUEST;

//Monta colunas
$columns = array(
	0 => 'ad_usuarios.nome',
	1 => 'ad_impressoras.nome',
	2 => 'ad_usuarios.id',
	3 => 'ad_usuarios.id',
	4 => 'data'
);

//Limpa condicoes
$where_contidion = $filtro = $sqlTot = $sqlRec = "";

//Filtros
if(!empty($params['search']['value'])){
	$where_contidion .= " AND ";
	$where_contidion .= " (ad_usuarios.nome LIKE '%".$params['search']['value']."%' OR ";
	$where_contidion .= " ad_impressoras.nome LIKE '%".$params['search']['value']."%') ";
}
if(!empty($_SESSION['filtro_usuario']) && $_SESSION['filtro_usuario'] != '-1'){
	$filtro .= " AND ad_usuarios.id = '".$_SESSION['filtro_usuario']."' ";
}
if(!empty($_SESSION['filtro_setor']) && $_SESSION['filtro_setor'] != '-1'){
	$filtro .= " AND ad_usuarios.id IN (SELECT id_usuario FROM ad_grupos WHERE id_grupo = '".$_SESSION['filtro_setor']."') ";
}
if(!empty($_SESSION['filtro_users_data'])){
	//Divide em duas datas
	$exp = explode(' até ', $_SESSION['filtro_users_data']);
	
	//Formata datas para AAAA-MM-DD
	$exp_inicio = explode('/', $exp[0]);
	$dt_inicio	= $exp_inicio[2].'-'.$exp_inicio[1].'-'.$exp_inicio[0];
	
	$exp_fim = explode('/', $exp[1]);
	$dt_fim	 = $exp_fim[2].'-'.$exp_fim[1].'-'.$exp_fim[0];
	
	//Filtra resultados
	$filtro .= " AND ((DATE_FORMAT(log_metricas.criacao, '%Y-%m-%d') > '$dt_inicio' OR DATE_FORMAT(log_metricas.criacao, '%Y-%m-%d') = '$dt_inicio') AND (DATE_FORMAT(log_metricas.criacao, '%Y-%m-%d') < '$dt_fim' OR DATE_FORMAT(log_metricas.criacao, '%Y-%m-%d') = '$dt_fim')) ";
}

//Select
$sql_query = "SELECT ad_usuarios.id AS usuario, ad_impressoras.id AS dispositivo, DATE_FORMAT(log_metricas.criacao, '%Y-%m-%d') AS data FROM log_metricas, ad_usuarios, ad_impressoras WHERE (log_metricas.dono = ad_usuarios.id AND log_metricas.dispositivo = ad_impressoras.id) $filtro";

//Monta filtro de pesquisa
$sql_query .= $where_contidion;

//Agrupa resultados
$sql_query .= " GROUP BY ad_usuarios.id, ad_impressoras.id, data ";

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
	$id_user = $result['usuario'];
	$id_imp  = $result['dispositivo'];
	$criacao = $result['data'];
	
	//Pega dados do Usuário
	$result_user = mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM ad_usuarios WHERE id = '$id_user'"));
	$usuario = $result_user['nome'];
	
	//Pega dados do Dispositivo
	$result_imp  = mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM ad_impressoras WHERE id = '$id_imp'"));
	$dispositivo = $result_imp['nome'];
	
	//Total - Páginas
	$result_pag = mysqli_fetch_assoc(mysqli_query($con,"SELECT SUM(paginas) AS qtd FROM log_metricas WHERE dispositivo = '$id_imp' AND DATE_FORMAT(criacao, '%Y-%m-%d') = '$criacao'"));
	$pag = $result_pag['qtd'];
	
	//Total - MB
	$result_mb = mysqli_fetch_assoc(mysqli_query($con,"SELECT SUM(tamanho) AS qtd FROM log_metricas WHERE dispositivo = '$id_imp' AND DATE_FORMAT(criacao, '%Y-%m-%d') = '$criacao'"));
	$bytes = $result_mb['qtd'];
	
	//Transforma bytes em MB
	$mb	= bytesToMegabytes($bytes).' MB';
	
	//Formata data para DD/MM/AAAA
	$dt_trab = date('d/m/y', strtotime($criacao));
	
	//Colunas
	$col_user 	 = "<h6 class=\"text-primary\"><i class=\"fas fa-user\"></i> $usuario</h6>";
	$col_imp  	 = "<h6 class=\"text-danger\"><i class=\"fas fa-print\"></i> $dispositivo</h6>";
	$col_tra_pag = "<h6 class=\"text-info\"><i class=\"fas fa-clone\"></i> $pag</h6>";
	$col_tra_mb  = "<h6>$mb</h6>";
	$col_data	 = "<h6><i class=\"mdi mdi-calendar-clock-outline\"></i> $dt_trab</h6>";
	
	//Formata dados para exibição na tabela
	$data[] = array(
		0 => "$col_user",
		1 => "$col_imp",
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