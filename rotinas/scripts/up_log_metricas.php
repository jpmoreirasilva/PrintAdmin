<?php
//Pega parametros de execucao
$rotinas = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM rotinas WHERE id = '1'"));

//Valida se rotina está em execucao
if($rotinas['log_metricas'] == 'S'){
	$tp_exec = 0;
	return;
} else{
	//Atualiza status de execucao da rotina
	mysqli_query($con, "UPDATE rotinas SET log_metricas = 'S' WHERE id = '1'");
}

// ---------------- INICIO ---------------- //

//Pasta de logs
$dir = '../../logs/';

//Escaneia pasta de logs
$logs = scandir($dir);

//Remove pastas criadas pelo Windows
unset($logs[0]);
unset($logs[1]);

//Valida se nenhum log foi criado
if(count($logs) == '0'){
	//Atualiza status de execucao da rotina
	mysqli_query($con, "UPDATE rotinas SET log_metricas = 'N', dt_log_metricas = now() WHERE id = '1'");
	return;
}

$i = 0;
foreach($logs as $val){
	$i++;
	
	//Log
	$log  = file_get_contents($dir.$val);
	$data = date('Y-m-d H:i:s', filectime($dir.$val));
	
	//Valida se log trouxe informacoes
	if(preg_match_all('/"[^"]+"/', $log, $matches) !== false){
		//Pega texto bruto
		$message = str_replace('"', '', $matches[0][27]);
		
		//Quebra texto em pedaços
		$exp = explode('. ', $message);
		
		//Informacoes
		$log_info = $exp[0];
		
		//Dados
		$bytes	  	= preg_replace('/[^0-9]+/', '', $exp[1]);
		$paginas  	= preg_replace('/[^0-9]+/', '', $exp[2]);
		
		//Documento
		if(preg_match('/[^,]+, (.+) pertencente/', $log_info, $mt_doc) === 1){
			$documento = "'".mysqli_real_escape_string($con, $mt_doc[1])."'";
		} else{
			$documento = 'NULL';
		}
		
		//Usuário
		if(preg_match('/pertencente a ([^\s]+)/', $log_info, $mt_user) === 1){
			$usuario = "'".mysqli_real_escape_string($con, $mt_user[1])."'";
		} else{
			$usuario = 'NULL';
		}
		
		//Impressora
		if(preg_match('/impresso em ([^\s]+)/', $log_info, $mt_imp) === 1){
			$impressora = "'".mysqli_real_escape_string($con, $mt_imp[1])."'";
		} else{
			$impressora = 'NULL';
		}
		
		//Se dados estiverem vazios, não insere no BD
		if($usuario == 'NULL' || $impressora == 'NULL'){
			continue;
		}
		
		//Pega dados do Usuário
		$result_user = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM ad_usuarios WHERE usuario = $usuario ORDER BY criacao DESC LIMIT 0,1"));
		$id_user = $result_user['id'];
		
		//Pega dados da Impressora
		$result_imp = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM ad_impressoras WHERE nome = $impressora ORDER BY criacao DESC LIMIT 0,1"));
		$id_imp = $result_imp['id'];
		
		//Insere dados no BD
		mysqli_query($con, "INSERT INTO log_metricas (documento, dispositivo, dono, tamanho, paginas, criacao) VALUES ($documento, '$id_imp', '$id_user', '$bytes', '$paginas', '$data')");
	}
	
	//Apaga log
	unlink($dir.$val);
	
	//Limite de 10 logs por execucao
	if($i >= '10'){
		break;
	}
}

// ---------------- FIM ---------------- //

//Atualiza status de execucao da rotina
mysqli_query($con, "UPDATE rotinas SET log_metricas = 'N', dt_log_metricas = now() WHERE id = '1'");