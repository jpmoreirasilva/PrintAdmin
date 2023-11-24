<?php
//Pega parametros de execucao
$rotinas = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM rotinas WHERE id = '1'"));

//Valida se rotina está em execucao
if($rotinas['ldap_imp'] == 'S'){
	return;
} else{
	//Atualiza status de execucao da rotina
	mysqli_query($con, "UPDATE rotinas SET ldap_imp = 'S' WHERE id = '1'");
}

// ---------------- INICIO ---------------- //

//Parametros LDAP
$servidor = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'ldap_host'"));
$login	  = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'ldap_user'"));
$senha	  = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'ldap_pass'"));
$dominio  = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'ldap_srv'"));
$base_dn  = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'ldap_dn'"));

//Conexao - LDAP
$ldap_con = ldap_connect($servidor['valor']);
ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);

//Valida se conexao falhou
if(!$ldap_con){
	//Apresenta erro no log
	error_log("LDAP - Status: Conexão falhou! | Servidor: $dominio",0);
	
	//Atualiza status de execucao da rotina
	mysqli_query($con, "UPDATE rotinas SET ldap_imp = 'N', dt_ldap_imp = now() WHERE id = '1'");
	return;
}

//Valida se autenticacao falhou
if(!ldap_bind($ldap_con, $login['valor'].'@'.$dominio['valor'], $senha['valor'])){
	//Apresenta erro no log
	error_log("LDAP - Status: Credenciais inválidas! | Usuário: ".$login['valor']." | Senha: ".$senha['valor']."",0);
	
	//Atualiza status de execucao da rotina
	mysqli_query($con, "UPDATE rotinas SET ldap_imp = 'N', dt_ldap_imp = now() WHERE id = '1'");
	return;
}

//Parametros - Filtros
$ldap_basedn = $base_dn['valor'];
$ldap_filter = '(&(objectClass=printQueue)(objectCategory=printQueue))';

//Pega info do AD
$ldap_search = ldap_search($ldap_con, $ldap_basedn, $ldap_filter);
$ldap_result = ldap_get_entries($ldap_con, $ldap_search);

for($i = 0; $i < $ldap_result['count']; $i++){
	//Dados
	$nome   = str_replace("'", "", $ldap_result[$i]['printername'][0]);
	$driver = str_replace("'", "", $ldap_result[$i]['drivername'][0]);
	$ip     = $ldap_result[$i]['portname'][0];
	
	//Modelo
	$exp = explode(" ", $driver);
	$modelo = $exp[0];
	
	//Valida se Modelo existe no BD
	$count_imp_md = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS qtd FROM imp_modelos WHERE marca = '$modelo'"));
	if($count_imp_md['qtd'] > '0'){
		//Pega ID do Modelo
		$result_imp_md = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM imp_modelos WHERE marca = '$modelo' ORDER BY criacao DESC LIMIT 0,1"));
		$id_md = $result_imp_md['id'];
	} else{
		//Insere modelo no BD
		mysqli_query($con, "INSERT INTO imp_modelos (marca, criacao) VALUES ('$modelo', NOW())");
		
		//Pega ID do Modelo
		$result_imp_md = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM imp_modelos WHERE marca = '$modelo' ORDER BY criacao DESC LIMIT 0,1"));
		$id_md = $result_imp_md['id'];
	}
	
	//Descrição
	if(isset($ldap_result[$i]['description'][0])){
		$descricao  = "'".str_replace("'", "", $ldap_result[$i]['description'][0])."'";
	} else{
		$descricao = 'NULL';
	}
	
	//Valida se impressora existe no BD
	$result_imp = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS qtd FROM ad_impressoras WHERE nome = '$nome'"));
	if($result_imp['qtd'] > '0'){
		//Atualiza dados no BD
		mysqli_query($con, "UPDATE ad_impressoras SET ip='$ip', marca='$id_md', driver='$driver', descricao=$descricao WHERE nome = '$nome'");
	} else{
		//Insere dados no BD
		mysqli_query($con, "INSERT INTO ad_impressoras (nome, ip, marca, driver, descricao) VALUES ('$nome', '$ip', '$id_md', '$driver', $descricao)");
	}
}

//Fecha conexao LDAP
ldap_unbind($ldap_con);

// ---------------- FIM ---------------- //

//Atualiza status de execucao da rotina
mysqli_query($con, "UPDATE rotinas SET ldap_imp = 'N', dt_ldap_imp = now() WHERE id = '1'");