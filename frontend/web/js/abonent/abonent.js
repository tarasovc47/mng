(function($){
	$(document).ready(function(){
		// Переключение табов в общей инфе
		$('.abonent__tab-client-id').click(function(e){
			e.preventDefault();
			$('.abonent__tab-client-id.active').removeClass('active');
			$(this).addClass('active');
			$('.abonent__tab-content-client-id .tab-pane.active.client-id-tab').removeClass('active');
			var id = $(this).find('a').data('clientId');
			$('.abonent__tab-content-client-id .tab-pane[data-client-id = "'+id+'"]').addClass('active');
		});
	});
}(jQuery));