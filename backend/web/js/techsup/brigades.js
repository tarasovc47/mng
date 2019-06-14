$(document).ready(function(){
	$('.field-brigades-members select').chosen({
		search_contains : true,
		no_results_text : "Ничего не найдено",
		placeholder_text_multiple : "Выбрать",
		placeholder_text_single	: "Выбрать",
		width : "auto",
	});
});