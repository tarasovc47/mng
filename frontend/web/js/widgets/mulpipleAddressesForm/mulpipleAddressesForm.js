$(document).ready(function(){
	$('a.place-find-editable').click(function(){
		$(this).one('result_ready', function() {
		    var addresses = $('.address-search input').val();
		    if (addresses != undefined) {
		    	loader(true);
				$.get(
					'/widgets-requests/multiple-addresses',
					{
						addresses : addresses,
					},
					function (data)
					{
						$('#multiple-addresses-form-table').html(data);
						allCheckedHandler();
						loader(false);							
					},
					'json'
				)
		    }
		});
	});

	function allCheckedHandler(){
		$('#all_checked input').click(function(){
			var state = $(this).prop('checked');
			$('.address-checkbox input').each(function(){
				$(this).prop('checked', state);
			});
		});
	}
});