(function($){
	$(document).ready(function(){
		// отображение/скрытие поля для выбора округа в создании округов и районов
		$('#zonesdistrictsandareas-type').click(function(){
			var type = $(this).val();
			var field_parents = $('.field-zonesdistrictsandareas-parent_id');
			var field_brigade = $('.field-zonesdistrictsandareas-users_group_id');
			if (type == 1 && !(field_parents.hasClass('hidden'))) {
				field_parents.addClass('hidden');
			}
			if (type == 2 && field_parents.hasClass('hidden')) {
				field_parents.removeClass('hidden');
			}

			if (type == 1 && !(field_brigade.hasClass('hidden'))) {
				field_brigade.addClass('hidden');
			}
			if (type == 2 && field_brigade.hasClass('hidden')) {
				field_brigade.removeClass('hidden');
			}
		});
	});
}(jQuery));