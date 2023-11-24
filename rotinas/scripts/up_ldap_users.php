<?php
//Pega parametros de execucao
$rotinas = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM rotinas WHERE id = '1'"));

//Valida se rotina está em execucao
if($rotinas['ldap_users'] == 'S'){
	return;
} else{
	//Atualiza status de execucao da rotina
	mysqli_query($con, "UPDATE rotinas SET ldap_users = 'S' WHERE id = '1'");
}

// ---------------- INICIO ---------------- //

//Parametros LDAP
$servidor = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'ldap_host'"));
$login	  = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'ldap_user'"));
$senha	  = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'ldap_pass'"));
$dominio  = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'ldap_srv'"));
$floresta = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'ldap_ou'"));
$base_dn  = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'ldap_dn'"));

//Conexao - LDAP
$ldap_con = ldap_connect($servidor['valor']);
ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0);

//Valida se conexao falhou
if(!$ldap_con){
	//Atualiza status de execucao da rotina
	mysqli_query($con, "UPDATE rotinas SET ldap_users = 'N', dt_ldap_users = now() WHERE id = '1'");
	return;
}

//Valida se autenticacao teve sucesso
if(ldap_bind($ldap_con, $login['valor'].'@'.$dominio['valor'], $senha['valor'])){
	//Parametros - Filtros
	$ldap_basedn = $floresta['valor'].','.$base_dn['valor'];
	$ldap_filter = '(objectClass=user)';
	
	//Pega info do AD
	$ldap_search = ldap_search($ldap_con, $ldap_basedn, $ldap_filter);
	$ldap_result = ldap_get_entries($ldap_con, $ldap_search);
	
	for($i = 0; $i < $ldap_result['count']; $i++){
		//Dados
		$nome       = str_replace("'", "", $ldap_result[$i]['cn'][0]);
		$usuario    = $ldap_result[$i]['samaccountname'][0];
		
		//E-mail
		if(isset($ldap_result[$i]['mail'][0])){
			//Endereco
			$endereco = $ldap_result[$i]['mail'][0];
			
			//Valida se E-mail é válido
			if(!filter_var($endereco, FILTER_VALIDATE_EMAIL)){
				$email = 'NULL';
			} else{
				$email = "'".$endereco."'";
			}
		} else{
			$email = 'NULL';
		}
		
		//Telefone
		if(isset($ldap_result[$i]['telephonenumber'][0])){
			//Numero
			$numero = $ldap_result[$i]['telephonenumber'][0];
			
			//Valida se número é válido
			if(!formatarTelefone($numero)){
				$telefone = 'NULL';
			} else{
				$telefone = "'".formatarTelefone($numero)."'";
			}
		} else{
			$telefone = 'NULL';
		}
		
		//Descrição
		if(isset($ldap_result[$i]['description'][0])){
			$descricao  = "'".str_replace("'", "", $ldap_result[$i]['description'][0])."'";
		} else{
			$descricao = 'NULL';
		}
		
		//Último Acesso
		if(isset($ldap_result[$i]['lastlogontimestamp'][0])){
			$ult_acesso = $ldap_result[$i]['lastlogontimestamp'][0];
		} else{
			$ult_acesso = 0;
		}
		
		//Valida se usuario fez login ao menos 1 vez
		if($ult_acesso > '0'){
			$dt_ult_acesso = "'".date('Y-m-d H:i:s', ($ult_acesso / 10000000 - 11644473600))."'";
		} else{
			$dt_ult_acesso = 'NULL';
		}
		
		//Valida se usuário existe no BD
		$result_user = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS qtd FROM ad_usuarios WHERE usuario = '$usuario'"));
		if($result_user['qtd'] > '0'){
			//Atualiza dados no BD
			mysqli_query($con, "UPDATE ad_usuarios SET nome='$nome', descricao=$descricao, email=$email, telefone=$telefone, ult_acesso=$dt_ult_acesso WHERE usuario='$usuario'");
		} else{
			//Insere dados no BD
			mysqli_query($con, "INSERT INTO ad_usuarios (nome, usuario, descricao, email, telefone, ult_acesso) VALUES ('$nome', '$usuario', $descricao, $email, $telefone, $dt_ult_acesso)");
		}
	}
	
	//Fecha conexao LDAP
	ldap_unbind($ldap_con);
}

// ---------------- FIM ---------------- //

//Atualiza status de execucao da rotina
mysqli_query($con, "UPDATE rotinas SET ldap_users = 'N', dt_ldap_users = now() WHERE id = '1'");