$(document).ready(function(){
	$(".field-docstypes-name input").change(function(e){
		var value = translit($(this).val());
		$(".field-docstypes-folder input").val(value);
	});

	$(".field-docstypes-folder input").keydown(function(e){
		$(this).unbind();
		$(".field-docstypes-name input").unbind();
	});
});