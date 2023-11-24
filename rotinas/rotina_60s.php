<?php
//Armazena timestamp antes da execucao do script
$tm_inicio = microtime(true);

//Valida se conexao com BD existe
if(!isset($con)){
	//Abre conexao com BD
	require '../inc/config.php';
}

//LOG - Métricas
require "scripts/up_log_metricas.php";
echo "LOG - Metricas \n\n";

//Armazena timestamp depois da execucao do script
$tm_fim = microtime(true);

//Calcula o tempo de execucao do script
$tempo_execucao = $tm_fim - $tm_inicio;
$tp_exec = number_format($tempo_execucao, 3);

echo "------------------------------- \n";
echo "Tempo - Total - Execucao: $tp_exec seg \n";

//Fecha conexao com BD
mysqli_close($con);