(function($){
	$(document).ready(function(){

		$('.tariffs #tariffs-billing_id').chosen({
			placeholder_text_multiple : ' ',
			placeholder_text_single : ' ',
			no_results_text : 'Ничего не найдено!',
			width: '100%',
			allow_single_deselect: true,
		});

		$('.tariffs #tariffssearch-opers').chosen({
			placeholder_text_multiple : ' ',
			no_results_text : 'Ничего не найдено!',
			width: "100%"
		});

		$('.tariffs #tariffssearch-services').chosen({
			width : '100%',
		    placeholder_text_multiple : ' ',
		    no_results_text : 'Ничего не найдено!'
		});

		$('.tariffs #tariffssearch-connection_technologies').chosen({
			width : '100%',
		    placeholder_text_multiple : ' ',
		    no_results_text : 'Ничего не найдено!'
		});

		$('.tariffs #tariffs-opers').chosen({
			placeholder_text_multiple : ' ',
			no_results_text : 'Ничего не найдено!',
			width: '100%',
		});

		$('.tariffs #tariffs-services[data-package=1]').chosen({
			placeholder_text_multiple : ' ',
			no_results_text : 'Ничего не найдено!',
			width: '100%',
		});
		serviceCloseHandler();

		$('.tariffs #tariffs-connection_technologies').chosen({
			placeholder_text_multiple : ' ',
			no_results_text : 'Ничего не найдено!',
			width: '100%',
		});

		$('#tariffs-services, #tariffs_services_chosen .chosen-drop').click(function(){
			loader(true);
			var services = $('#tariffs-services').val();
			var techs = $('#tariffs-connection_technologies').val();
			var tariffs = $('#tariffs-billing_id').val();

			if (services) {
				$.get(
					'/tariffs/get-extra-data-for-form',
					{
						services : services,
						techs : techs,
						tariffs : tariffs,
					},
					function (data)
					{
						$('#tariffs-connection_technologies').html(data.techs).prop('disabled', false).trigger('chosen:updated');
						$('#tariffs-billing_id').html(data.tariffs).prop('disabled', false).trigger('chosen:updated');
						loader(false);
					},
					'json'
				)
			} else {
				var html = '<option value=""></option>';
				$('#tariffs-connection_technologies').html(html).prop('disabled', true).trigger('chosen:updated');
				$('#tariffs-billing_id').html(html).prop('disabled', true).trigger('chosen:updated');
				loader(false);
			}

			serviceCloseHandler();

		});

		function serviceCloseHandler(){
			$('#tariffs_services_chosen a.search-choice-close').each(function(){
				if (!($(this).hasClass("processed"))) {
					$(this).addClass('processed');
					$(this).click(function(){
						$('#tariffs_services_chosen .chosen-drop').click();
					});
				}
			});
		}

		// датапикер 
		$('#tariffs-opened_at').datetimepicker({
			ignoreReadonly : true,
			showClose : true,
			useCurrent: false,
			format : "D-MM-YYYY",
		});

		$('#tariffs-closed_at').datetimepicker({
			ignoreReadonly : true,
			showClose : true,
			showClear : true,
			useCurrent: false,
			format : "D-MM-YYYY",
		});

		$('#tariffssearch-opened_at, #tariffssearch-closed_at').datetimepicker({
			ignoreReadonly : true,
			showClose : true,
			showClear : true,
			useCurrent: false,
			format : "D-MM-YYYY",
		});

		$('.tariffs__open-search').click(function(){
			$('.zones-tariffs-search').stop().slideToggle(300);
			if ($(this).html() == 'Показать поиск') {
				$(this).html('Скрыть поиск');
			} else {
				$(this).html('Показать поиск');
			}
		});

		// автоматическое именование тарифа если выбран тариф из биллинга
		$('#tariffs_billing_id_chosen .chosen-drop').click(function(){
			var tariff_name = $('#tariffs-billing_id option:selected').html();
			if ($('#tariffs-name').val() == '') {
				$('#tariffs-name').val(tariff_name);
			}
		});


	});
}(jQuery));