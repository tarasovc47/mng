$(document).ready(function(){
	$(".scenarios__attributes .attribute").click(function(e){
		if($(e.target).hasClass("choose")){
			if(!$(e.target).siblings(".text").hasClass("added")){
				var attr = customParseInt($(e.target).data("attr"));
				
				$("#techsupscenarios-techsup_attribute_id").val(attr);
				highlightAttr();
			}

			e.stopPropagation();
			return false;
		}
		$(this).toggleClass("open");

		$(this).is(".open") ? $(this).children(".attribute").addClass("show") : $(this).children(".attribute").removeClass("show");
		
		e.stopPropagation();
	});

	highlightAttr();

	function highlightAttr(){
		$(".choose")
			.removeClass("hide")
			.siblings(".text").removeClass("added")
			.parents(".attribute").removeClass("open")
			.children(".attribute").removeClass("show");

		var attr = $("#techsupscenarios-techsup_attribute_id").val();

		$(".choose[data-attr='" + attr + "']")
			.addClass("hide")
			.siblings(".text").addClass("added")
			.parents(".attribute").addClass("open")
			.children(".attribute").addClass("show");
	}
});