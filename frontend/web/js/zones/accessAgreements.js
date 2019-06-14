(function($){
	$(document).ready(function(){

		// chosen для договоров доступа
		$('#zonesaccessagreements-manag_company_id').chosen({
			no_results_text : 'Ничего не найдено!'
		});

		// Подключение датапикеров
		$('#zonesaccessagreements-opened_at').datetimepicker({
			ignoreReadonly : true,
			showClose : true,
			useCurrent: false,
			format : "D-MM-YYYY",
		});

		$('#zonesaccessagreements-closed_at').datetimepicker({
			ignoreReadonly : true,
			showClose : true,
			showClear : true,
			useCurrent: false,
			format : "D-MM-YYYY",
		});

		$('.zones__agreements-index__closed-at, .zones__agreements-index__opened-at')
		.datetimepicker({
			ignoreReadonly : true,
			showClose : true,
			showClear : true,
			useCurrent: false,
			format : "D-MM-YYYY",
		})
		.on("dp.change", function(e){
			var data = $("#w0-filters *").serialize(),
				url = window.location.pathname;

			$('#w0-filters').yiiGridView({ "filterUrl": url + "?" + data });
			$('#w0-filters').yiiGridView('applyFilter');
		});	

		$('#zonesaccessagreements-auto_prolongation').click(function(){
			console.log($(this).prop('checked'));
			if ($(this).prop('checked')) {
				$('.zones__agreements__closed_at').val('').addClass('hidden');
			} else {
				$('.zones__agreements__closed_at').removeClass('hidden');
			}
			
		});
	});
}(jQuery));