(function($){
	$(document).ready(function(){
		chooseServicesHandler();
		tariffPlansHandler();
		addLevelButtonHandler();
		searchCriterionHandler();
		deleteLevelButtonHandler();
		searchResultPaginationHandler();
		keyupHandler();

		//поиск по нажатию ENTER и сброс формы по нажатию ESCAPE, + добавление уровня, - удаление уровня
		$(document).keyup(function(e){ 
			//enter
	        if(e.keyCode == 13){
				$('.search__area .search__search-button').click();
			}
			// escape
			if (e.keyCode == 27) {
				$('.search__area .search__clear-button').click();
			}
			// +
			if (e.keyCode == 107 || e.keyCode == 187) {
				if (!($(".search__levels input ").is( ":focus" ))) {
					$('.search__area .search__level .search__add-level-button').last().click();
				}
			}
			// -
			if (e.keyCode == 109 || e.keyCode == 189) {
				if (!($(".search__levels input ").is( ":focus" ))) {
					$('.search__area .search__level .search__delete-level-button').last().click();
				}
			}
	    });

		//ВЫБОР СЕРВИСОВ
		function chooseServicesHandler(){
			$('#search__choose-services').each(function(){
				if (!($(this).hasClass("processed"))) {
					$(this).addClass('processed');

					$(this).dropdown();

					$('.search__services .dropdown-menu li a').click(function(e){
						e.stopPropagation();
					});
				}
				
			});
			
			$(".search__services input[type='checkbox'].search__service-check").each(function(){
				if (!($(this).hasClass("processed"))) {
					$(this).addClass('processed');

					$(this).click(function(e){
						check_state();
						check_state_all();
					});
				}
			});
			
			$(".search__all-checked").not('processed').each(function(){
				if (!($(this).hasClass("processed"))) {
					$(this).addClass('processed')
					$(this).click(function(e){
						var state = $(this).prop("checked");

						$(".search__services input[type='checkbox'].search__service-check").prop("checked", state);
						check_state();
					});
				}
			});

			function check_state(){
				var state = false;

				$(".search__services input[type='checkbox'].search__service-check").each(function(){
					if($(this).prop("checked") == true){      
						state = true;
					}				
				});
			}

			function check_state_all(){
				var state = true;

				$(".search__services input[type='checkbox'].search__service-check").each(function(){
					if($(this).prop("checked") != true){      
						state = false;
					}
				});
				$(".search__all-checked").prop("checked", state);
			}
		}

		// Сброс формы поиска к исходному состоянию
	    $('.search__area .search__clear-button').click(function(){
	    	$('.search__level').each(function(){
	    		if (!($(this).hasClass('search__search-level-first'))) {
	    			$(this).remove();
	    		} else {
	    			$('.search__area .search__search-level-first').empty().html(searchLevelFirst);
	    			chooseServicesHandler();
					tariffPlansHandler();
					addLevelButtonHandler();
					searchCriterionHandler();
					$('.search__search-level-first .search__default').focus();
	    		}
	    	});
	    });


		// ПОИСК
	    function searchClient(dataForSearch, page, tab = 'both', onlyActive){
	    	$.post("/client-search/client-search/",
				{
					dataForSearch : dataForSearch,
					page : page,
					tab : tab,
					onlyActive : onlyActive		
				},
				function(data){
					$('.search__area .search__search-button').removeClass('disabled');
					$('.search__area .search__search-button').prop('disabled', false);

					switch (tab){
						case 'both':
							$('.search__result').empty().html(data);
							break;

						case 'abonents':
							$('.search__result div.tab-pane[data-tab = "abonents"]').empty().html(data);
							break;

						case 'clients':
							$('.search__result div.tab-pane[data-tab = "clients"]').empty().html(data);
							break;

						default:
							break;
					}

					searchResultPaginationHandler();
					loginsHiddingHandler();
				},
				'json'
			);
	    }

	    function getDataForSearch(){
	    	var dataForSearch = {}; // объект с данными для поиска
			var i = 1; //счетчик уровней поиска, служит также свойством в объекте
			var a=1; //счетчик для адресов
			var t=1;//счетчик для тарифов
			var s=1; //счетчик для сервисов
			var u=1; //счетчик для логинов

			$('.search__level').each(function(){

				dataForSearch[i] = {};
				dataForSearch[i].request = {};
				dataForSearch[i].criterion = $(this).find('.search__criterion option:checked').val();
				dataForSearch[i].criterionSource = $(this).find('.search__criterion option:checked').data('source');
				dataForSearch[i].condition = $(this).find('.search__criterion option:checked').data('condition');
				dataForSearch[i].criterionForSwitch = $(this).find('.search__criterion option:checked').data('criterion');
				
				if (!($(this).hasClass('search__search-level-first'))) {
					dataForSearch[i].searchClause = {};
					dataForSearch[i].searchClause = $(this).find('.search__clause').val();
				}

				switch(dataForSearch[i].criterionForSwitch){
				    		case 'tariff':
				    			dataForSearch[i].request.tariff_plan = {};
				    			$(this).find('.search__tariff-plans .chosen-choices li.search-choice a.search-choice-close').each(function(){
				    				dataForSearch[i].request.tariff_plan[t] = {};
				    				var index = $(this).data('optionArrayIndex');
			    					dataForSearch[i].request.tariff_plan[t].tariff = $('.search__tariff-plans .chosen-drop .result-selected[data-option-array-index = "'+index+'"').data('tariff');
			    					dataForSearch[i].request.tariff_plan[t].oper_id = $('.search__tariff-plans .chosen-drop .result-selected[data-option-array-index = "'+index+'"').data('operId');
				    				t++;
				    			});
				    			break;

				    		case 'client_type':
				    			dataForSearch[i].request = {};
				    			dataForSearch[i].request.client_type = $(this).find('.search__search-client-types option:checked').data('clientType');
				    			dataForSearch[i].request.oper_id = $(this).find('.search__search-client-types option:checked').data('operId');
				    			break;

				    		case 'oper_id':
				    			dataForSearch[i].request = {};
				    			dataForSearch[i].request = $(this).find('.search__search-providers option:checked').val();
				    			break;

				    		case 'suboper_id':
				    			dataForSearch[i].request = {};
				    			dataForSearch[i].request.suboper_id = $(this).find('.search__search-subproviders option:checked').data('suboperId');
				    			dataForSearch[i].request.oper_id = $(this).find('.search__search-subproviders option:checked').data('operId');
				    			break;

				    		case 'address_jur':
				    		case 'address_post':
				    		case 'address_kladr':
				    			dataForSearch[i].request = {};
				    			$(this).find('.search__address input').each(function(){
				    				if ($(this).val().trim() != '') {
				    					dataForSearch[i].request[a] = {};
				    					dataForSearch[i].request[a] = $(this).val();
				    					a++;
				    				}
				    					
				    			});
				    			break;

				    		case 'service_type':
				    			dataForSearch[i].request = {};
				    			$(this).find('.search__services .search__choose-services input:checkbox:checked').each(function(){
				    				if (!($(this).hasClass('search__all-checked'))) {
				    					dataForSearch[i].request[s] = {};
				    					dataForSearch[i].request[s] = $(this).attr('name');
				    					s++;
				    				}
				    			});
				    			break;

				    		case 'user_id':
				    			dataForSearch[i].request = {};
				    			dataForSearch[i].request.services = {};
				    			$(this).find('.search__services .search__choose-services input:checkbox:checked').each(function(){
				    				if (!($(this).hasClass('search__all-checked'))) {
				    					dataForSearch[i].request.services[u] = {};
				    					dataForSearch[i].request.services[u] = $(this).attr('name');
				    					u++;
				    				}
				    			});
				    			dataForSearch[i].request.user_id = {};
				    			dataForSearch[i].request.user_id = $(this).find('.search__search-user-id').val();
				    			break;

				    		default:
				    			dataForSearch[i].request = {};
				    			dataForSearch[i].request = $(this).find('.search__default').val();
				    			break;
				   }
				  i++;
			});
			return dataForSearch;
	    }

	    function searchResultPaginationHandler(){
			$('#search__results-panel .pagination a').each(function(){
				if (!($(this).hasClass('processed'))){
					$(this).addClass('processed');
					$(this).click(function(e){
						e.preventDefault();

						if (!($('.search__area .search__search-button').hasClass('disabled'))) {
							var destPage = $(this).data('destPage');
							var dataForSearch = getDataForSearch();
							var tab = $(this).parents('div.tab-pane').data('tab');
							var onlyActive = $('.search__area .search__only-active-logins input[type = "checkbox"]').prop('checked');

							if (!($('.search__area .search__search-button').hasClass('disabled'))) {
								$('.search__area .search__search-button').addClass('disabled');
								$('.search__area .search__search-button').prop('disabled', true);
								var spinner = "<div class='search__spinner'><i class='fa fa-spinner fa-spin fa-3x'></i></div>";

								$('.search__result .tab-content div.tab-pane.active').empty().html(spinner);
							}

							searchClient(dataForSearch, destPage, tab, onlyActive);
						}
					});
				}
			});
	    }
	    
		$('.search__area .search__search-button').click(function(e){

			var dataForSearch = getDataForSearch();
			
			if (!($(this).hasClass('disabled'))) {
				$(this).addClass('disabled');
				$(this).prop('disabled', true);

				var onlyActive = $('.search__area .search__only-active-logins input[type = "checkbox"]').prop('checked');
				var spinner = "<div class='search__spinner'><i class='fa fa-spinner fa-spin fa-3x'></i></div>";

				$('.search__result').empty().html(spinner);

				searchClient(dataForSearch, 1, 'both', onlyActive);
			}
		});

		// Автофокус на строке поиска при загрузке страницы
	    $('.search__default').focus();

	    // поиск по селекту для тарифов
	    function tariffPlansHandler(){
	    	$('.search__tariff-plans').each(function(){
	    		if (!($(this).hasClass("processed"))) {
					$(this).addClass('processed');
					$(this).chosen({
						parser_config : { copy_data_attributes : true },
						inherit_select_classes : true,
						no_results_text : 'Нет совпадений',
						placeholder_text_multiple : 'Выберите тарифный план'
					});
				}
	    	});
		}

	    // Подмена поисковой строки в заивисмости от условий поиска
	    

	    // Группа переменных с HTML кодом для вставки и подмены поисковой строки

	    // Кнопки добавления уровня и удаления уровня
	    var buttons = '	<button class="btn btn-default search__delete-level-button hidden" type="button"><i class="fa fa-close"></i></button>'
	    					+ '	<button type="button" class="btn btn-default search__add-level-button"><i class="fa fa-plus"></i></button>';
	    // поисковая строка по умолчанию
	    var defaultSearch = '<div class="form-group"><input type="text" name="request" class="form-control search__default" placeholder="Введите запрос..."></div>'
	   						+ buttons;

	    // селектор тарифных планов
	    var tariffPlansSelect = '<div class="search__tariff-plans-container"><select name="search__tariff-plans" class="search__tariff-plans" multiple>';

	    for (var service in tariffPlans){
	    	for (var oper in tariffPlans[service].tariffs){
	    		tariffPlansSelect += '<optgroup label="'+tariffPlans[service].service_name+': '+tariffPlans[service].tariffs[oper].name+'">';
	    			for (var tariff in tariffPlans[service].tariffs[oper].tariffs){
	    				tariffPlansSelect += "<option data-tariff='"+tariffPlans[service].tariffs[oper].tariffs[tariff].f1+"' data-oper-id='"+tariffPlans[service].tariffs[oper].operid+"'>"+tariffPlans[service].tariffs[oper].tariffs[tariff].f3+"</option>";
	    			}	
	    		tariffPlansSelect += "</optgroup>";
	    	}
	    }
	    tariffPlansSelect += '</select>'
	    					+ buttons;

	    // селектор типов клиентов
	    var clientTypesSelect = '<div class="form-group"><select name="search__search-client-types" class="form-control search__search-client-types">'
	    						+'<option data-client-type="person_use_srv_as_org">Физическое лицо в коммерческих целях</option>';
	    for (var oper in clientTypes){
	    	clientTypesSelect += "<optgroup label='"+clientTypes[oper].name+"''>";
	    	for (var clientType in clientTypes[oper].json_agg){
	    		clientTypesSelect += "<option data-client-type="+clientTypes[oper].json_agg[clientType].f1+" data-oper-id="+clientTypes[oper].oper_id+">"+clientTypes[oper].json_agg[clientType].f2+"</option>";
	    	}
	    	clientTypesSelect += "</optgroup>";
	    }
	    clientTypesSelect += "</select></div>"
	    						+ buttons;

		// селектор провайдеров
	    var providersSelect = '<div class="form-group"><select name="search__search-providers" class="form-control search__search-providers">';
	    for (var provider in providers){
	    	providersSelect += "<option value="+providers[provider].oper_id+">"+providers[provider].name+"</option>";
	    }
	    providersSelect += "</select></div>"
	    					+ buttons;

	    // селектор субпровайдеров
	    var subprovidersSelect = '<div class="form-group"><select name="search__search-subproviders" class="form-control search__search-subproviders">';
	    for (var provider in subproviders){
	    	subprovidersSelect += "<optgroup label='"+subproviders[provider].name+"''>";
	    	for (var subprovider in subproviders[provider].json_agg){
	    		subprovidersSelect += "<option data-suboper-id="+subproviders[provider].json_agg[subprovider].f1+" data-oper-id="+subproviders[provider].oper_id+">"+subproviders[provider].json_agg[subprovider].f2+"</option>";
	    	}
	    	subprovidersSelect += "</optgroup>";
	    }
	    subprovidersSelect += "</select></div>"
	    						+ buttons;

	    // Поля для адреса
	    var addressSearch = '<div class="search__address">'
	    					+'<div class="form-group"><input type="text" data-address="city" value="Тюмень" class="form-control" placeholder="Город"></div>'
	    					+'<div class="form-group"><input type="text" data-address="avenue" class="form-control search__focus-element" placeholder="Улица"></div>'
	    					+'<div class="form-group"><input type="text" data-address="building" class="form-control" placeholder="Дом"></div>'
	    					+'<div class="form-group"><input type="text" data-address="housing" class="form-control" placeholder="Корпус"></div>'
	    					+'<div class="form-group"><input type="text" data-address="apartment" class="form-control" placeholder="Квартира/офис"></div>'
	    					+ buttons
	    					+'</div>';

	    // чекбоксы сервисов
	    var serviceSearch = '<div class="dropdown search__services">'
							+'<button type="button" id="search__choose-services" class="btn btn-default dropdown-toggle search__choose-services" data-toggle="dropdown">Сервис <span class="caret"></span></button>'
				   			+'<ul class="dropdown-menu search__choose-services-list search__choose-services" aria-labelledby="search__choose-services">'
							+'<li><a data-target="#"><div class="checkbox"><label><input type="checkbox" name="allServices" class="search__all-checked"> Выделить всё/Снять выделение</label></div></a></li>'

							+'<li class="divider"></li>';


		for (var service in services){
			serviceSearch += '<li><a data-target="#"><div class="checkbox"><label><input type="checkbox" name="'+service+'" class="search__service-check"> '+services[service]+'</label></div></a></li>';
		}

		serviceSearch += '</ul></div>';
						
		// Поле под логин с чекбоксами сервисов
		var loginSearh = serviceSearch+'<div class="form-group"><input type="text" name="request" class="form-control search__search-user-id" placeholder="Введите запрос..."></div>'
	   						+ buttons;

	    serviceSearch += buttons;
	    // непосредственно подмена
	    var criterion;
	    function searchCriterionHandler(){
	    	$('.search__area .search__level .search__criterion').each(function(){
	    		if (!($(this).hasClass("processed"))) {
		    		$(this).addClass('processed');

				    $(this).change(function(){
				    	console.log('hurray!!!')
				    	criterion = $(this).find('option:checked').data('criterion');
	    	
				    	switch(criterion){
				    		case 'tariff':
				    			if ($(this).parents('.search__level').find('.search__query-zone .search__tariff-plans').length == 0) {
				    				$(this).parents('.search__level').find('.search__query-zone').empty().html(tariffPlansSelect);
				    			}

				    			$(this).parents('.search__level').find('.search__query-zone .search__tariff-plans').focus();
				    			tariffPlansHandler();
				    			break;

				    		case 'client_type':
				    			if ($(this).parents('.search__level').find('.search__query-zone .search__search-client-types').length == 0) {
				    				$(this).parents('.search__level').find('.search__query-zone').empty().html(clientTypesSelect);
				    			}

				    			$(this).parents('.search__level').find('.search__query-zone .search__search-client-types').focus();
				    			break;

				    		case 'oper_id':
				    			if ($(this).parents('.search__level').find('.search__query-zone .search__search-providers').length == 0) {
				    				$(this).parents('.search__level').find('.search__query-zone').empty().html(providersSelect);
				    			}

				    			$(this).parents('.search__level').find('.search__query-zone .search__search-providers').focus();
				    			break;

				    		case 'suboper_id':
				    			if ($(this).parents('.search__level').find('.search__query-zone .search__search-subproviders').length == 0) {
				    				$(this).parents('.search__level').find('.search__query-zone').empty().html(subprovidersSelect);
				    			}

				    			$(this).parents('.search__level').find('.search__query-zone .search__search-subproviders').focus();
				    			break;

				    		case 'address_jur':
				    		case 'address_post':
				    		/*case 'address_kladr':*/
				    			if ($(this).parents('.search__level').find('.search__query-zone .search__address').length == 0) {
				    				var value = {};
				    				$(this).parents('.search__level').find('.search__address-helper input').each(function(){
				    					var key = $(this).data('address');
				    					value[key] = {};
				    					value[key] = $(this).val();
				    				});

				    				$(this).parents('.search__level').find('.search__query-zone').empty().html(addressSearch);
				    				$(this).parents('.search__level').find('.search__query-zone .search__address input').each(function(){
				    					var key = $(this).data('address');
				    					$(this).val(value[key]);
				    				});
				    			}
				    			$(this).parents('.search__level').find('.search__query-zone .search__focus-element').focus();
				    			break;

				    		case 'service_type':
				    			if ($(this).parents('.search__level').find('.search__query-zone .searchСhooseServices').length == 0) {
				    				$(this).parents('.search__level').find('.search__query-zone').empty().html(serviceSearch);
				    			}

				    			$(this).parents('.search__level').find('.search__query-zone .searchСhooseServices').focus();
				    			chooseServicesHandler();
				    			break;

				    		case 'user_id':
				    			if ($(this).parents('.search__level').find('.search__query-zone .search__search-user-id').length == 0) {
				    				var value = $(this).parents('.search__level').find('.search__hidden-helper-fields input.search__default-helper').val();
				    				$(this).parents('.search__level').find('.search__query-zone').empty().html(loginSearh);
				    				$(this).parents('.search__level').find('.search__query-zone .search__search-user-id').val(value);

				    			}

				    			$(this).parents('.search__level').find('.search__query-zone .search__search-user-id').focus();
				    			chooseServicesHandler();
				    			/*$(this).parents('.search__level').find('.search__query-zone .search__choose-services .search__all-checked').click();*/
				    			break;

				    		default:
				    			if ($(this).parents('.search__level').find('.search__query-zone .search__default').length == 0) {
				    				var value = $(this).parents('.search__level').find('.search__hidden-helper-fields input.search__default-helper').val();    				
				    				$(this).parents('.search__level').find('.search__query-zone').empty().html(defaultSearch);
				    				$(this).parents('.search__level').find('.search__query-zone .search__default').val(value);
				    			}
				    			
				    			$(this).parents('.search__level').find('.search__query-zone .search__default').focus();
				    			break;
				    	}

				    	if (!($(this).parents('.search__level').hasClass('search__search-level-first'))) {
				    		$(this).parents('.search__level').find('.search__delete-level-button').removeClass('hidden');
				    	}
				    	
				    	if ($(this).parents('.search__level').find('.search__query-zone').hasClass('search__inline-block') && (criterion == 'address_jur' || criterion == 'address_post' || criterion == 'address_kladr' || criterion == 'tariff_plan')) {
				    		$(this).parents('.search__level').find('.search__query-zone').removeClass('search__inline-block');
				    	} else if ((criterion == 'address_jur' || criterion == 'address_post' || criterion == 'address_kladr' || criterion == 'tariff_plan') && !($(this).parents('.search__level').find('.search__query-zone').hasClass('search__inline-block'))){
				    	} else {
				    		$(this).parents('.search__level').find('.search__query-zone').addClass('search__inline-block');
				    	}

				       	addLevelButtonHandler();
						deleteLevelButtonHandler();
						keyupHandler();
			    	});
				}
		    });
		}

		// Запись вводимых данных в скрытые формы, чтоб подставлять при перестроении поиска
		function keyupHandler(){
			$('.search__level input.search__default').each(function(){
				if (!($(this).hasClass('processed'))) { 
					$(this).addClass('processed');

					var writing;
					$(this).keyup(function(){
						clearTimeout(writing);

						var level = $(this);

						writing = setTimeout(function(){
								var value = level.val();
								level.parents('.search__level').find('.search__hidden-helper-fields .search__default-helper').val(value);
							},
							500);
					});
				}
			});

			$('.search__level input.search__search-user-id').each(function(){
				if (!($(this).hasClass('processed'))) { 
					$(this).addClass('processed');

					var writing;
					$(this).keyup(function(){
						clearTimeout(writing);

						var level = $(this);

						writing = setTimeout(function(){
								var value = level.val();
								level.parents('.search__level').find('.search__hidden-helper-fields .search__default-helper').val(value);
							},
							500);
					});
				}
			});

			$('.search__level .search__address input').each(function(){
				if (!($(this).hasClass('processed'))) { 
					$(this).addClass('processed');

					var writing;
					$(this).keyup(function(){
						clearTimeout(writing);

						var level = $(this);

						writing = setTimeout(function(){
								var value = level.val();
								var data = level.data('address');
								level.parents('.search__level').find('.search__hidden-helper-fields .search__address-helper input[data-address = "'+data+'"]').val(value);
							},
							500);
					});
				}
			});
		}
		


	    //Добавление нового уровня поиска

	    //Переменная с кодом уровня поиска
	    var searchLevel = '<div class="search__level">'
	    					+ '<div class="form-group"><select name="search__clause" class="form-control search__clause"><option value="AND">И</option><option value="OR">Или</option><option value="NOT">Исключить</option></select></div>'
	    					+ '<div class="form-group"><select name="search__criterion" class="form-control search__criterion">';
	    for (var criterion in criterions){
	    	searchLevel += '<option value="'+criterions[criterion].source+'.'+criterions[criterion].criterion+'" data-criterion = "'+criterions[criterion].criterion+'" data-condition="'+criterions[criterion].condition+'" data-source="'+criterions[criterion].source+'">'+criterions[criterion].descr+'</option>';
	    }
	    searchLevel += '</select></div>'
	    				+ '	<div class="search__query-zone">'
	    				+ '<div class="form-group"><input type="text" name="request" class="form-control search__default" placeholder="Введите запрос..."></div>'
	    				+ '	<button class="btn btn-default search__delete-level-button" type="button"><i class="fa fa-close"></i></button>'
	    				+ '	<button type="button" class="btn btn-default search__add-level-button"><i class="fa fa-plus"></i></button>'
	    				+ '</div>'
	    				+ '<div class="search__hidden-helper-fields"><input type="text" name="hidden-helper" class="search__default-helper hidden"><div class="search__address-helper"><input type="text" data-address="city" class="hidden" value="Тюмень"><input type="text" data-address="avenue" class="hidden"><input type="text" data-address="building" class="hidden"><input type="text" data-address="housing" class="hidden"><input type="text" data-address="apartment" class="hidden"></div></div>'
	    				+ '</div>';

	    function addLevelButtonHandler(){
	    	$('.search__area .search__level .search__add-level-button').each(function(){
	    		if (!($(this).hasClass("processed"))) {
		    		$(this).addClass('processed');
		    		$(this).click(function(){
		    			if ($('.search__area .search__level').length < 5) {
			    			$(this).parents('.search__level').after(searchLevel);
			    			addLevelButtonHandler();
			    			searchCriterionHandler();
			    			deleteLevelButtonHandler();
			    			keyupHandler();
			    			$(this).parents('.search__level').find('search__delete-level-button').removeClass('hidden');
		    			}
		    		});
	    		}
	    	});
	    };

	    //Удаление уровня поиска
	    function deleteLevelButtonHandler(){
	    	$('.search__area .search__level .search__delete-level-button').each(function(){
	    		if (!($(this).hasClass("processed"))) {
		    		$(this).addClass('processed');

		    		$(this).click(function(){
		    			$(this).parents('.search__level').remove();

		    		});
		    	}
	    	});
	    };

	     //Переменная с кодом первого уровня поиска
	    var searchLevelFirst = '<div class="form-group"><select name="search__criterion" class="form-control search__criterion">';
	    for (var criterion in criterions){
	    	searchLevelFirst += '<option value="'+criterions[criterion].source+'.'+criterions[criterion].criterion+'" data-criterion = "'+criterions[criterion].criterion+'" data-condition="'+criterions[criterion].condition+'" data-source="'+criterions[criterion].source+'">'+criterions[criterion].descr+'</option>';
	    }
	    searchLevelFirst += '</select></div>'
	    				+ '	<div class="search__query-zone">'
	    				+ '<div class="form-group"><input type="text" name="request" class="form-control search__default" placeholder="Введите запрос..."></div>'
	    				+ '	<button class="btn btn-default search__delete-level-button hidden" type="button"><i class="fa fa-close"></i></button>'
	    				+ '	<button type="button" class="btn btn-default search__add-level-button"><i class="fa fa-plus"></i></button>'
	    				+ '</div>'
	    				+ '<div class="search__hidden-helper-fields"><input type="text" name="hidden-helper" class="search__default-helper hidden"><div class="search__address-helper"><input type="text" data-address="city" class="hidden" value="Тюмень"><input type="text" data-address="avenue" class="hidden"><input type="text" data-address="building" class="hidden"><input type="text" data-address="housing" class="hidden"><input type="text" data-address="apartment" class="hidden"></div></div>';

	  	

	  	// Скрывашка логинов в результатах поиска

	  	function loginsHiddingHandler(lengthHidding = 5){
	  		$('.search__results__services-list').each(function(){
	  			var count = $(this).find('.search__results__services-login').length;
	  			
	  			if (count > lengthHidding) {
	  				$(this).append('<span class="search__result__show-more text-primary">Показать ещё...</span>');
	  				$(this).append('<span class="search__result__hide-more text-primary hidden">Скрыть</span>');
	  				$(this).find('.search__results__services-login').slice(lengthHidding).addClass('hidden');
	  			}
	  		});

	  		$('.search__results__services-list .search__result__show-more').each(function(){
	  			if (!($(this).hasClass('processed'))) {
	  				$(this).addClass('processed')

	  				$(this).click(function(){
	  					$(this).toggleClass('hidden');
	  					$(this).siblings('.search__result__hide-more').toggleClass('hidden');
	  					$(this).siblings('.search__results__services-login').removeClass('hidden')

	  				});
	  			}
	  		});

	  		$('.search__results__services-list .search__result__hide-more').each(function(){
	  			if (!($(this).hasClass('processed'))) {
	  				$(this).addClass('processed')

	  				$(this).click(function(){
	  					$(this).toggleClass('hidden');
	  					$(this).siblings('.search__result__show-more').toggleClass('hidden');
	  					$(this).siblings('.search__results__services-login').slice(lengthHidding).addClass('hidden');
	  				});
	  			}
	  		});

	  		

	  	}
	});
}(jQuery));