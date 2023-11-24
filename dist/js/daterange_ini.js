$(document).ready(function(){
	//Intervalo entre duas datas sem precisao da hora
	$('.date-range').dateRangePicker({
		startOfWeek: 'monday',
    	separator : ' até ',
    	format: 'DD/MM/YYYY',
    	autoClose: false,
		language: 'pt',
		monthSelect: true,
		yearSelect: true,
		time: {
			enabled: false
		}
	});
});