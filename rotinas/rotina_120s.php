<?php
//Armazena timestamp antes da execucao do script
$tm_inicio = microtime(true);

//Valida se conexao com BD existe
if(!isset($con)){
	//Abre conexao com BD
	require '../inc/config.php';
}

require '../vendor/autoload.php';

//Active Directory - Usuários
require "scripts/up_ldap_users.php";
echo "Active Directory - Usuários - OK\n";

//Active Directory - Grupos
require "scripts/up_ldap_grupos.php";
echo "Active Directory - Grupos - OK\n";

//Active Directory - Impressoras
require "scripts/up_ldap_imp.php";
echo "Active Directory - Impressoras - OK\n";

//Pega Informações das Impressoras
require "scripts/up_imp_info.php";
echo "Pega Informacoes das Impressoras - OK\n\n";

//Armazena timestamp depois da execucao do script
$tm_fim = microtime(true);

//Calcula o tempo de execucao do script
$tempo_execucao = $tm_fim - $tm_inicio;
$tp_exec = number_format($tempo_execucao, 3);

echo "------------------------------- \n";
echo "Tempo - Total - Execucao: $tp_exec seg \n";

//Fecha conexao com BD
mysqli_close($con);