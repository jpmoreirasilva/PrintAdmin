//Centraliza na tela
function scrollTo(id, time){
	var elemento = $('#'+id);
	if (elemento.length > 0) {
		$('html, body').animate({
			scrollTop: elemento.offset().top
		},time);
	}
}

//Tabela - Recolher/Expandir Resultados
function tgTableView(table, btn){
	$.ajax({
		url: "pages/tabelas/"+table+".php?aninhar=1",
		dataType: "html",
		processData: false,
		type: "GET",
		async: false,
		cache: false,
		success: function(response){
			var dados = JSON.parse(response);
			if(dados['modo'] == 'E'){
				btn.target.innerHTML = '<i class="mdi mdi-arrow-collapse-vertical"></i> Recolher';
			} else{
				btn.target.innerHTML = '<i class="mdi mdi-arrow-expand-vertical"></i> Expandir';
			}
			$('#'+table).DataTable().ajax.reload();
		},
		error: function(){
			alert("Ocorreu um erro na solicitação AJAX.");
		}
	});
}