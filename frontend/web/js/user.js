(function($){	
	$(document).ready(function(){
		$('.search-settings__checkbox').change(function(){
			$('#search-settings__danger').hide();
			$('#search-settings__success').hide();
			$('#search-settings__save').slideDown(200);
		});

		$('#search-settings__save').click(function(){
			loader(true);

			var settings = {}; 
			$('.search-settings__checkbox').each(function(){
				var field_id = $(this).data('field-id');
				var value = $(this).prop('checked');
				settings[field_id] = value ? 1 : 0;
			});

			$.post("/user/search-settings-update",
				{
					settings : settings,
				}, 
				function(data){
					if (!data.error) {
						$('#search-settings__success').show();
					} else {
						$('#search-settings__danger').show();

					}
					$('#search-settings__save').hide();
					loader(false);
				}, 
				'json'
			);
		});
	});
}(jQuery));