<?php
//Desativa erros na tela
error_reporting(0);

//Impressoras - EPSON
$sql_epson_imp = mysqli_query($con,"SELECT * FROM ad_impressoras WHERE marca = '3' ORDER BY up_imp_info ASC LIMIT 0,10");
while($epson_imp = mysqli_fetch_array($sql_epson_imp)){
	//Dados
	$ep_imp_id = $epson_imp['id'];
	$ep_imp_ip = $epson_imp['ip'];
	
	//Servidor
	$ep_imp_uri = "http://$ep_imp_ip:631/ipp/print";
	
	//Valida se Impressora está Online
	if(!file_get_contents("http://$ep_imp_ip")){
		mysqli_query($con,"UPDATE ad_impressoras SET status = 'OFF', up_imp_info = NOW() WHERE id = '$ep_imp_id'");
		continue;
	}
	
	try{
		//Classe - IPP
		$ipp = new \obray\ipp\Printer($ep_imp_uri);
		
		//Impressora - Atributos
		$attr = $ipp->getPrinterAttributes();
	} catch(\Exception $e){
		error_log(__FILE__.'->Erro: '.$e->getMessage(),0);
		mysqli_query($con,"UPDATE ad_impressoras SET up_imp_info = NOW() WHERE id = '$ep_imp_id'");
		continue;
	}

	//Transforma objeto em JSON
	$json_attr = json_encode($attr, JSON_PRETTY_PRINT);
	
	//Transforma JSON em Array
	$list_attr = json_decode($json_attr, true);
	
	//Atributos
	$ep_imp_md	  = $list_attr['printerAttributes'][0]['printer-make-and-model'];
	$ep_cart_desc = $list_attr['printerAttributes'][0]['marker-names'];
	$ep_cart_cor  = $list_attr['printerAttributes'][0]['marker-colors'];
	$ep_cart_tipo = $list_attr['printerAttributes'][0]['marker-types'];
	$ep_cart_lvl  = $list_attr['printerAttributes'][0]['marker-levels'];
	
	//Apaga dados do BD
	mysqli_query($con,"DELETE FROM imp_suprimentos WHERE dispositivo = '$ep_imp_id'");
	
	for($i=0; $i < sizeof($ep_cart_desc); $i++){
		//Dados
		$cart_desc = $ep_cart_desc[$i];
		$cart_cor  = $ep_cart_cor[$i];
		$cart_tipo = $ep_cart_tipo[$i];
		$cart_lvl  = $ep_cart_lvl[$i];
		
		//Insere dados no BD
		mysqli_query($con,"INSERT INTO imp_suprimentos (dispositivo, descricao, cor, tipo, level) VALUES ('$ep_imp_id', '$cart_desc', '$cart_cor', '$cart_tipo', '$cart_lvl')");
	}
	
	//Atualiza dados no BD
	mysqli_query($con,"UPDATE ad_impressoras SET status = 'ON', produto = '$ep_imp_md', up_imp_info = NOW() WHERE id = '$ep_imp_id'");
}

//Ativa erros na tela
error_reporting(E_ALL);