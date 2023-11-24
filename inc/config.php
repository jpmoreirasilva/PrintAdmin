<?php
//Parametros - Conexão BD
$servidor = 'localhost';
$usuario  = 'root';
$senha	  = 'Acessopriv@d02023';
$banco	  = 'printadmin';

//Conexão BD
$con = mysqli_connect($servidor, $usuario, $senha, $banco) or die('Falha ao conectar-se ao banco de dados!');

//Inicia ou resume sessao
session_start();

//Site
$result_site = mysqli_fetch_assoc(mysqli_query($con, "SELECT valor FROM parametros WHERE nome = 'site'"));
$par_site = $result_site['valor'];

function formatarTelefone($numero) {
    //Valida se número é inválido
	if(empty($numero) || preg_match('/[^0-9]/',$numero) === 1 || (strlen($numero) < '10' || strlen($numero) > '11')){
		return false;
	}
    
    //Verifique o comprimento do número para determinar o formato
    if(strlen($numero) == 10) {
        //Formato para números de telefone com 10 dígitos
        $numero_formatado = '(' . substr($numero, 0, 2) . ') ' . substr($numero, 2, 4) . '-' . substr($numero, 6);
    } else{
        //Formato para números de telefone com 11 dígitos
        $numero_formatado = '(' . substr($numero, 0, 2) . ') ' . substr($numero, 2, 5) . '-' . substr($numero, 7);
    }
    
    return $numero_formatado;
}

//Calcula a porcentagem e arredonda
function calcularPorcentagem($parcial, $total) {
	//Valida se total é maior que 0
	if($total > '0'){
		return round(($parcial / $total) * 100);
	} else{
		return 0;
	}
}

//Transforma bytes em MB
function bytesToMegabytes($bytes) {
    return number_format($bytes / (1024 * 1024), 2);
}