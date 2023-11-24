<script>
$(document).ready(function() {
	//Tabelas
	var table  = "<?php echo $tabela; ?>";
	var table2 = "<?php echo $tabela2; ?>";
	var table3 = "<?php echo $tabela3; ?>";
	
	//Tabela - Impressoras - Bt. Aninhar Resultados
	<?php if(isset($_SESSION['tabela_imp_modo']) && $_SESSION['tabela_imp_modo'] == 'R'){ ?>
	var bt_imp_aninhar = '<i class="mdi mdi-arrow-expand-vertical"></i> Expandir';
	<?php } else{ ?>
	var bt_imp_aninhar = '<i class="mdi mdi-arrow-collapse-vertical"></i> Recolher';
	<?php } ?>
	
	//Tabela - Trabalhos - Resumo
	$('#'+table).DataTable({
		dom: 'Bfrtip',
		buttons: [
			{ extend: 'excel', className: 'btn btn-primary mb-2 mr-2', text: '<i class="mdi mdi-file-excel-outline"></i> Excel' },
			{ extend: 'pdf', className: 'btn btn-primary mb-2 mr-2', text: '<i class="mdi mdi-file-document-outline"></i> PDF' },
			{ extend: 'print', className: 'btn btn-primary mb-2 mr-2', text: '<i class="mdi mdi-printer-outline"></i> Imprimir' },
		],
		"processing": true,
		"serverSide": true,
		"stateSave": false,
		"pageLength": 10,
		"lengthMenu": [[10, 25, 50, 99999], [10, 25, 50, "Tudo"]],
		"ajax":{
			url :"pages/tabelas/imp_metricas_table.php",
			type: "GET",
			cache: false
		},
		"deferRender": true,
		"autoWidth": true,
		"responsive": true,
		"language": {
		"sEmptyTable": "Nenhum registro encontrado",
		"sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
		"sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
		"sInfoFiltered": "(Filtrados de _MAX_ registros)",
		"sInfoPostFix": "",
		"sInfoThousands": ".",
		"sLengthMenu": "_MENU_ resultados por página",
		"sLoadingRecords": "Carregando...",
		"sProcessing": "Processando...",
		"sZeroRecords": "Nenhum registro encontrado",
		"sSearch": "Pesquisar",
		"oPaginate": {
			"sNext": "Próximo",
			"sPrevious": "Anterior",
			"sFirst": "Primeiro",
			"sLast": "Último"
		},
		"oAria": {
			"sSortAscending": ": Ordenar colunas de forma ascendente",
			"sSortDescending": ": Ordenar colunas de forma descendente"
			}
		}
	});
	
	//Tabela - Trabalhos - Resumo
	$('#'+table2).DataTable({
		dom: 'Bfrtip',
		buttons: [
			{ extend: 'excel', className: 'btn btn-primary mb-2 mr-2', text: '<i class="mdi mdi-file-excel-outline"></i> Excel' },
			{ extend: 'pdf', className: 'btn btn-primary mb-2 mr-2', text: '<i class="mdi mdi-file-document-outline"></i> PDF' },
			{ extend: 'print', className: 'btn btn-primary mb-2 mr-2', text: '<i class="mdi mdi-printer-outline"></i> Imprimir' },
		],
		"processing": true,
		"serverSide": true,
		"stateSave": false,
		"pageLength": 10,
		"lengthMenu": [[10, 25, 50, 99999], [10, 25, 50, "Tudo"]],
		"ajax":{
			url :"pages/tabelas/imp_metricas_info_table.php",
			type: "GET",
			cache: false
		},
		"deferRender": true,
		"autoWidth": true,
		"responsive": true,
		"language": {
		"sEmptyTable": "Nenhum registro encontrado",
		"sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
		"sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
		"sInfoFiltered": "(Filtrados de _MAX_ registros)",
		"sInfoPostFix": "",
		"sInfoThousands": ".",
		"sLengthMenu": "_MENU_ resultados por página",
		"sLoadingRecords": "Carregando...",
		"sProcessing": "Processando...",
		"sZeroRecords": "Nenhum registro encontrado",
		"sSearch": "Pesquisar",
		"oPaginate": {
			"sNext": "Próximo",
			"sPrevious": "Anterior",
			"sFirst": "Primeiro",
			"sLast": "Último"
		},
		"oAria": {
			"sSortAscending": ": Ordenar colunas de forma ascendente",
			"sSortDescending": ": Ordenar colunas de forma descendente"
			}
		}
	});
	
	//Tabela - Trabalhos - Dispositivos
	$('#'+table3).DataTable({
		dom: 'Bfrtip',
		buttons: [
			{ extend: 'excel', className: 'btn btn-primary mb-2 mr-2', text: '<i class="mdi mdi-file-excel-outline"></i> Excel' },
			{ extend: 'pdf', className: 'btn btn-primary mb-2 mr-2', text: '<i class="mdi mdi-file-document-outline"></i> PDF' },
			{ extend: 'print', className: 'btn btn-primary mb-2 mr-2', text: '<i class="mdi mdi-printer-outline"></i> Imprimir' },
			{ className: 'btn btn-primary mb-2', text: bt_imp_aninhar, action: function(bt){ tgTableView(table3, bt); } },
		],
		"processing": true,
		"serverSide": true,
		"stateSave": false,
		"pageLength": 10,
		"lengthMenu": [[10, 25, 50, 99999], [10, 25, 50, "Tudo"]],
		"ajax":{
			url :"pages/tabelas/imp_metricas_info2_table.php",
			type: "GET",
			cache: false
		},
		"deferRender": true,
		"autoWidth": true,
		"responsive": true,
		"language": {
		"sEmptyTable": "Nenhum registro encontrado",
		"sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
		"sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
		"sInfoFiltered": "(Filtrados de _MAX_ registros)",
		"sInfoPostFix": "",
		"sInfoThousands": ".",
		"sLengthMenu": "_MENU_ resultados por página",
		"sLoadingRecords": "Carregando...",
		"sProcessing": "Processando...",
		"sZeroRecords": "Nenhum registro encontrado",
		"sSearch": "Pesquisar",
		"oPaginate": {
			"sNext": "Próximo",
			"sPrevious": "Anterior",
			"sFirst": "Primeiro",
			"sLast": "Último"
		},
		"oAria": {
			"sSortAscending": ": Ordenar colunas de forma ascendente",
			"sSortDescending": ": Ordenar colunas de forma descendente"
			}
		}
	});
});
</script>