(function($){	
	/*$(document).on('ready pjax:end', function(){
		pageHandler();
		function pageHandler(){
			$('.rewrite_checkbox').each(function(){
				$(this).bootstrapToggle();
			});

			$('#addresses-recycle__save').click(function(){
				$('#confirm').modal('show');
				$("#confirm .confirm-content").html("Вы уверены?");
				$('#confirm .btn-success').one('click', function(e){
					$('#confirm').modal('hide');
					loader(true);
					var companies = {};
					$('.rewrite_checkbox').each(function(){
						var company_id = $(this).data('model-id');
						var value = $(this).prop('checked');
						companies[company_id] = (value) ? 3 : 4;
					});
					$.get(
						'/addresses-recycle/rewrite',
						{
							companies : companies,
						},
						function (data)
						{
							$.pjax.reload({container: '#addresses-recycle-pjax'});
							loader(false);
						},
						'json'
					)
				});
			});
		}
	});*/
}(jQuery));