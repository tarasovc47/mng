$(document).ready(function(){
	$(".field-fields-label input").change(function(e){
		var value = translit($(this).val());
		$(".field-fields-name input").val(value);
	});

	$(".field-fields-name input").keydown(function(e){
		$(this).unbind();
		$(".field-fields-label input").unbind();
	});

	$("#fields-type").change(function(e){
		var value = $(this).val();

		$(".private-field-params .field-section").removeClass("show");
		if(value != ""){
			$(".private-field-params .field-section." + value).addClass("show");
		}
	});
});