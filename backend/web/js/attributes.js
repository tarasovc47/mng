$(document).ready(function(){
	if($(".field-attributes-parent_id select").val() != "0"){
		$(".connection-technology-id").addClass("hide");
	}

	$(".field-attributes-parent_id select").change(function(e){
		($(this).val() == "0") ? $(".connection-technology-id").removeClass("hide") : $(".connection-technology-id").addClass("hide");
	});
});