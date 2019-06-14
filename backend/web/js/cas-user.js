$(document).ready(function(){
	$(".cas-user-access select").change(function(e){
		var cas_user_id = customParseInt($_GET("id")),
			access_id = $(this).attr("data-access"),
			module_setting_key = $(this).attr("data-key"),
			value = customParseInt($(this).val()),
			element = $(this);

		loader(true);

		$.post("/cas-user/access-update",
			{
				cas_user_id : cas_user_id,
				access_id : access_id,
				module_setting_key : module_setting_key,
				value : value,
			}, 
			function(data){
				loader(false);

				if(data.status == "error"){
					$("#notice .notice-content").html("Возникла ошибка.");
					$("#notice").modal('show');
				}
				else{
					element.siblings(".hint-block").find(".changes-saved").show().fadeOut(3000);
					element.siblings(".hint-block").find(".about-setting").html(data.about);

					if(data.status == "success_empty"){
						element.attr("data-access", "");
					}
					else{
						element.attr("data-access", data.id);
					}
				}
			}, 
			'json'
		);
	});
});