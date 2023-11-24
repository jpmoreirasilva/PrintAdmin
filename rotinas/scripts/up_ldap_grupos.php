<?php
//Pega parametros de execucao
$rotinas = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM rotinas WHERE id = '1'"));

//Valida se rotina está em execucao
if($rotinas['ldap_grupos'] == 'S'){
	return;
} else{
	//Atualiza status de execucao da rotina
	mysqli_query($con, "UPDATE rotinas SET ldap_grupos = 'S' WHERE id = '1'");
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
	mysqli_query($con, "UPDATE rotinas SET ldap_grupos = 'N', dt_ldap_grupos = now() WHERE id = '1'");
	return;
}

//Valida se autenticacao teve sucesso
if(ldap_bind($ldap_con, $login['valor'].'@'.$dominio['valor'], $senha['valor'])){
	//Parametros - Filtros
	$ldap_basedn = $floresta['valor'].','.$base_dn['valor'];
	$ldap_filter = '(objectClass=group)';
	
	//Pega info do AD
	$ldap_search = ldap_search($ldap_con, $ldap_basedn, $ldap_filter);
	$ldap_result = ldap_get_entries($ldap_con, $ldap_search);
	
	for($i = 0; $i < $ldap_result['count']; $i++){
		//Grupo
		$grupo = str_replace("'", "", $ldap_result[$i]['cn'][0]);
		
		//Descrição
		if(isset($ldap_result[$i]['description'][0])){
			$descricao  = "'".str_replace("'", "", $ldap_result[$i]['description'][0])."'";
		} else{
			$descricao = 'NULL';
		}
		
		//Valida se grupo existe no BD
		$result_gp = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS qtd FROM ad_grupos_info WHERE grupo = '$grupo'"));
		if($result_gp['qtd'] > '0'){
			//Atualiza dados no BD
			mysqli_query($con, "UPDATE ad_grupos_info SET descricao = $descricao WHERE grupo = '$grupo'");
		} else{
			//Insere dados no BD
			mysqli_query($con, "INSERT INTO ad_grupos_info (grupo, descricao) VALUES ('$grupo', $descricao)");
		}
		
		//Pega dados do Grupo
		$result_gp_nv = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM ad_grupos_info WHERE grupo = '$grupo' ORDER BY criacao DESC LIMIT 0,1"));
		$id_grupo = $result_gp_nv['id'];
		
		//Desvincula usuarios do grupo para evitar repeticao
		mysqli_query($con, "DELETE FROM ad_grupos WHERE id_grupo = '$id_grupo'");
		
		//Membros
		if(isset($ldap_result[$i]['member'])){
			for($f = 0; $f < $ldap_result[$i]['member']['count']; $f++){
				//Pega texto bruto
				$texto = $ldap_result[$i]['member'][$f];
				
				//Quebra string em pedaços
				$exp  = explode(',', $texto);
				$exp2 = explode('=', $exp[0]);
				
				//Usuário
				$usuario = mysqli_real_escape_string($con, $exp2[1]);
				
				//Pega dados do Usuário
				$result_user = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM ad_usuarios WHERE nome = '$usuario' ORDER BY criacao DESC LIMIT 0,1"));
				$id_usuario  = $result_user['id'];
				
				//Vincula usuário ao grupo
				mysqli_query($con, "INSERT INTO ad_grupos (id_usuario, id_grupo, criacao) VALUES ('$id_usuario', '$id_grupo', now())");
			}
		}
	}
	
	//Fecha conexao LDAP
	ldap_unbind($ldap_con);
}

// ---------------- FIM ---------------- //

//Atualiza status de execucao da rotina
mysqli_query($con, "UPDATE rotinas SET ldap_grupos = 'N', dt_ldap_grupos = now() WHERE id = '1'");