(function($){
	$(document).ready(function(){
		$('#tariffsgroups-tariffs').chosen({
			placeholder_text_multiple : ' ',
			no_results_text : 'Ничего не найдено!',
			width: '100%',
		});

		$('#tariffsgroups-abonent_type').change(function(){
			var abonent_type = $(this).val();

			if (abonent_type != '') {
				loader(true);
				$.post('/tariffs/tariffs-groups/load-tariffs-list',
					{
						abonent_type : abonent_type,
					},
					function(data){
						$('#tariffsgroups-tariffs').html(data).prop('disabled', false).trigger('chosen:updated');
						loader(false);
					},
					'json'
				);
			} else {
				$('#tariffsgroups-tariffs').prop('disabled', true).trigger('chosen:updated');
			}
		});
	});
}(jQuery));