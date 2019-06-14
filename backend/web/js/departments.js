$(document).ready(function(){
	$(".departments-access select").change(function(e){
		var id = customParseInt($(this).data("access")),
			value = customParseInt($(this).val()),
			element = $(this);

		loader(true);
		$.post("/departments/access-update",
			{
				id : id,
				value : value,
			}, 
			function(data){
				loader(false);
				switch(data.status){
					case "success":
						element.siblings(".hint-block").find(".changes-saved").show().fadeOut(3000);
						break;
					default:
				}
			}, 
			'json'
		);
	});

	$('.search-settings__checkboxes input').change(function(){
		$('#search-settings__danger').hide();
		$('#search-settings__success').hide();
		$('#search-settings__save').slideDown(200);
	});

	$('#search-settings__save').click(function(){
		loader(true);
		var department_id = $('.departments-search-settings').data('department-id');
		var settings = {}; 
		$('.search-settings__checkbox').each(function(){
			var field_id = $(this).data('field-id');
			var value = $(this).prop('checked');
			settings[field_id] = value ? 1 : 0;
		});

		$.post("/departments/search-settings-update",
			{
				settings : settings,
				department_id : department_id,
			}, 
			function(data){
				if (!data.error) {
					$('#search-settings__success').show();
				} else {
					$('#search-settings__danger').show();

				}
				$('#search-settings__save').hide();
				loader(false);
			}, 
			'json'
		);
	});
	
	$('.groups__users-list').sortable({
		connectWith: '.groups__users-list',
		placeholder: "placeholder",
		stop: function(event, ui){
			// Сортировка в список неопределенных пользователей нас не интересует
            var container = ui.item.parent()[0],
            	container = $(container);

            if(container.hasClass("undefined")){
            	$(this).sortable('cancel');
            	return;
            }

            // Скроем, чтобы не мешалось
            if($(".groups__users-list.undefined li").length < 1){
            	$(".panel.undefined").hide();
            }

            $('.save-groups').fadeIn(500);
        },
	});

	$('.save-groups').click(function(e){
		loader(true);

		var data = {};
		$(".groups__users-list:not(.undefined)").each(function(){
			var group_id = $(this).attr("data-id");

			data[group_id] = [];
			$(this).children().each(function(){
				var user_id = customParseInt($(this).attr("data-id"));

				data[group_id].push(user_id);
			});

		});

		$.post("/users-groups/save", 
			{
				data : data,
			}, 
			function(data){
			 	loader(false);

				switch(data.status){
					case "success":
						$('.save-groups').hide();
						break;
					default:
						console.log(data);
				}
			}, 
			'json'
		);
	});

	/*
	if($(".attributes-tree").hasClass("has-access")){
		$('.attributes-tree__list').sortable({
			placeholder: "placeholder",
			start: function(event, ui){
				$('.save-sort').fadeIn(500);
			},
		});
	}

	$('.save-sort').click(function(e){
		var array_sort = {};
		$(".attributes-tree__list .first-lvl").each(function(index){
			array_sort[index] = form_array_sort($(this));
		});

		console.log(array_sort);

		$.post(
			'/attributes/save-sort',
			{
				arraySort : array_sort,
			},
			function(data){
				switch(data.status){
					case 'success':
						$('.save-sort').fadeOut(500);
						break;
					default:
				}
			},
			'json'
		);
	});

	function form_array_sort(ul){
		var response = {},
			sort = 0,
			id = 0;

		ul.children('li').each(function(){
			id = $(this).attr('data-id');
			response[id] = {};

			response[id]["sort"] = sort;
			response[id]["children"] = ($(this).children('ul').html() != '') ? form_array_sort($(this).children('ul')) : false;

			sort++;
		});

		return response;
	}
	*/
});