$(document).ready(function(){
	if($(".field-properties-parent_id select").val() != "0"){
		$(".field-properties-application_type_id").addClass("hide");
	}
	
	$(".field-properties-parent_id select").change(function(e){
		($(this).val() == "0") ? $(".field-properties-application_type_id").removeClass("hide") : $(".field-properties-application_type_id").addClass("hide");
	});
});