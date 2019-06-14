(function($){
	$(document).ready(function(){

	/*---------------------------Create & Update--------------------------*/
		tariffsToggleHandler();
		tariffsCheckboxPanelHandler();
		groupPanelHandler();

		addFloorHandler();
		addFlatHandler();
		controlPorchHandler();
		controlFloorHandler();
		updateFlatHandler();

		// карта на создании адреса
		if ($('html.zones-addresses.create').length > 0 || $('html.zones-addresses.update').length > 0) {
			ymaps.ready(function(){
				ymaps.ready(init);

				var map,
					object,
					center,
					zoom,
					coords = $('#zonesaddresses-coordinates').val();

				center = (coords != '') ? coords.split(',').map(parseFloat) : [57.150612, 65.547308];
				zoom = (coords != '') ? 15 : 10;
				coords = center;

				function init(){
		            map = new ymaps.Map('zones__form-map', {
		                center: center,
		                zoom: zoom,
		                controls: ['zoomControl', 'typeSelector', 'fullscreenControl'],
		            });

		            object = new ymaps.Placemark(coords, {}, { preset: "islands#violetDotIconWithCaption", draggable: true });
		            map.geoObjects.add(object);

		            object.events.add("dragend", function(e){
		            	set_value(this.geometry.getCoordinates());
		            }, object);

		            map.events.add("click", function(e){
		            	object.geometry.setCoordinates(e.get('coords'));
		            	set_value(object.geometry.getCoordinates());
		            });

		            var searchControl = new ymaps.control.SearchControl({
				        options: {
				            noPlacemark: true,
				        }
				    });
					map.controls.add(searchControl);
					searchControl.events.add("resultselect", function(e){
						var index = searchControl.getSelectedIndex(),
							coords = searchControl.getResultsArray()[index].geometry.getCoordinates();

						object.geometry.setCoordinates(coords);

						set_value(coords);
					});				
					$('.place-editable').on('address:enable', function(){
						var search_value = $(this).text();
						searchControl.search(search_value);
					});

					map.events.add('boundschange', function(e){
						set_value(object.geometry.getCoordinates());
		            });
		        }

		        function set_value(coords){
		        	$('#zonesaddresses-coordinates').val(coords);
		        }
			});
		}


		// chosen для договоров доступа в форме создания адреса
		$('#zonesaddresses-access_agreements').chosen({
			width : '100%',
			placeholder_text_multiple : ' ',
			no_results_text : 'Ничего не найдено!'
		}).on('change', function(){
			$('#zonesaddresses-contract_with_manag_company').prop('checked', true);
		});

		// chosen для операторов в форме создания адреса
		$('#zonesaddresses-opers').chosen({
			width : '100%',
			placeholder_text_multiple : ' ',		
			no_results_text : 'Ничего не найдено!'
		});

		// chosen для УК в форме создания адреса
		$('#zonesaddresses-manag_company_id').chosen({
			width : '100%',
			placeholder_text_single : ' ',		
			no_results_text : 'Ничего не найдено!'
		});

		// chosen для сервисов в форме создания адреса
		var service_deselected;
		$('#zonesaddresses-services_individual, #zonesaddresses-services_entity').chosen({
			width : '100%',
			placeholder_text_multiple : ' ',		
			no_results_text : 'Ничего не найдено!'
		}).on('change', function(evt, params) {
			if (params !== undefined) {
	    		service_deselected = params.deselected;
	    	}
		});

		// chosen для технологий в форме создания адреса
		$('#zonesaddresses-conn_techs_individual, #zonesaddresses-conn_techs_entity').chosen({
			width : '100%',
			placeholder_text_multiple : ' ',		
			no_results_text : 'Ничего не найдено!'
		});

		// Подтягивание договоров доступа в форму для адресов при выборе Управляющей компании
		$('#zonesaddresses-manag_company_id').change(function(){
			var companyId = $(this).val();
			if (companyId != '') {
				loader(true);
				$.get(
					'/zones/zones-addresses/get-extra-data-for-company',
					{
						companyId : companyId,
					},
					function (data)
					{
						$('#zonesaddresses-access_agreements').prop('disabled', false).html(data.agreements).trigger("chosen:updated");
						$('#zonesaddresses-manag_company_branch_id').prop('disabled', false).html(data.branches);
						$('#zonesaddresses-key_keeper').prop('disabled', false).html(data.contacts);
						loader(false);
					},
					'json'
				)
			} else {
				var html = '<option value=""></option>';
				$('#zonesaddresses-access_agreements').prop('disabled', true).html(html).trigger("chosen:updated");
				$('#zonesaddresses-manag_company_branch_id').prop('disabled', true).html(html);
			}
			
		});

		// Подтягивание контактов в форму для адресов при выборе Участка УК
		$('#zonesaddresses-manag_company_branch_id').change(function(){
			var branch_id = $(this).val();
			var company_id = $('#zonesaddresses-manag_company_id').val();
			if (branch_id != '') {
				loader(true);
				$.get(
					'/zones/zones-addresses/get-contacts-for-key-keeper',
					{
						branch_id : branch_id,
						company_id : company_id,
					},
					function (data)
					{
						$('#zonesaddresses-key_keeper').prop('disabled', false).html(data);
						loader(false);
					},
					'json'
				)
			} else {
				loader(true);
				$('#zonesaddresses-key_keeper optgroup[label="Участок"]').remove();
				loader(false);
			}
			
		});

		// Загрузка options для селектора районов при выборе округов
		$('#zonesaddresses-district_id').change(function(){
			var district = $(this).val();
			if (district.length != 0) {
				loader(true);
				$.get(
					'/zones/zones-addresses/get-areas-list-by-district',
					{
						districtId : district,
					},
					function (data)
					{
						$('#zonesaddresses-area_id').prop('disabled', false).html(data);
						loader(false);
					},
					'json'
				)
			} else {
				var html = '<option value=""></option>';
				$('#zonesaddresses-area_id').prop('disabled', true).html(html);
			}
			
		});

		// Разблокировка поля для выбора сервиса при выборе статуса объекта
		$('.zonesaddresses-build_status').change(function(){
			var abonent_type = $(this).data('abonent-type');
			if ($(this).val() != '') {
				$('.zonesaddresses-services[data-abonent-type='+abonent_type+']').prop('disabled', false).trigger("chosen:updated");
			} else {
				//$('#zonesaddresses-services_individual').prop('disabled', true).trigger("chosen:updated");
			}
		});

		//перезаполнение скрытого поля тарифов
		function rewriteTariffsHidden(tariffs_hidden, abonent_type){
			var tariffs_json = JSON.stringify(tariffs_hidden);
			$('.zonesaddresses-tariffs[data-abonent-type='+abonent_type+']').val(tariffs_json);
		}

		// Загрузка options для селектора технологий подключения при выборе сервисов
		$('.zonesaddresses-services').change(function(){
			var abonent_type = $(this).data('abonent-type');
			var services = $(this).val();
			var selected_techs = $('.zonesaddresses-conn_techs[data-abonent-type='+abonent_type+']').val();

			if (services != null) {
				loader(true);
				$.get(
					'/zones/zones-addresses/get-technologies-list',
					{
						services : services,
						selected_techs : selected_techs,
						without_html : 0,
					},
					function (data)
					{
						serviceDesecelt(abonent_type);
						$('.zonesaddresses-conn_techs[data-abonent-type='+abonent_type+']').html(data);
						$('.zonesaddresses-conn_techs[data-abonent-type='+abonent_type+']').prop('disabled', false).trigger("chosen:updated");
						loader(false);
					},
					'json'
				)
			} else {
				serviceDesecelt(abonent_type);
				var html = '<option value=""></option>';
				$('.zonesaddresses-conn_techs[data-abonent-type='+abonent_type+']').prop('disabled', true).html(html).trigger("chosen:updated");
			}
		});

		function serviceDesecelt(abonent_type){
			if (service_deselected !== undefined) {
				loader(true);
				$.get(
					'/zones/zones-addresses/get-technologies-list',
					{
						services : service_deselected,
						selected_techs : 0, //false
						without_html : 1,
					},
					function (data)
					{
						for (var conn_tech in data) {
							$('.zonesaddresses-conn_techs[data-abonent-type='+abonent_type+'] option[value='+conn_tech+']').prop('selected' , false);
							$('.zonesaddresses-conn_techs[data-abonent-type='+abonent_type+']').change();
						}
						service_deselected = undefined;
						loader(false);
					},
					'json'
				)
			}
		}

		//var old_conn_techs_individual;
		var new_conn_techs_individual;
		//var old_conn_techs_entity;
		var new_conn_techs_entity;

		function loadConnTechsSelectValue(){
			new_conn_techs_individual = $('.zonesaddresses-conn_techs[data-abonent-type=1]').val();
			new_conn_techs_entity = $('.zonesaddresses-conn_techs[data-abonent-type=2]').val();
		}
		loadConnTechsSelectValue();
		// логика создания и удаления панелей выбора тарифных планов и таглов для автоматики
		$('.zonesaddresses-conn_techs').change(function(){
			var abonent_type = $(this).data('abonent-type');

			var old_conn_techs;
			var new_conn_techs;
			new_conn_techs = $(this).val();

			switch (abonent_type){
				case 1:
					old_conn_techs = new_conn_techs_individual;
					new_conn_techs_individual = new_conn_techs;
					break;
				case 2:
					old_conn_techs = new_conn_techs_entity;
					new_conn_techs_entity = new_conn_techs;
					break;
			}
			var selected_conn_tech;
			var deselected_conn_tech;
				
			if (new_conn_techs != null) {
				if (old_conn_techs != null) {
					for (var conn_tech in new_conn_techs){
						if ($.inArray(new_conn_techs[conn_tech], old_conn_techs) == -1) {
							selected_conn_tech = new_conn_techs[conn_tech];
						}
					}
				} else {
					selected_conn_tech = new_conn_techs[0];
				}
			}
			if (old_conn_techs != null) {
				if (new_conn_techs != null) {
					for (var conn_tech in old_conn_techs){
						if ($.inArray(old_conn_techs[conn_tech], new_conn_techs) == -1) {
							deselected_conn_tech = old_conn_techs[conn_tech];
						}
					}
				} else {
					deselected_conn_tech = old_conn_techs[0];
				}
			}

			var tariffs_hidden = $('.zonesaddresses-tariffs[data-abonent-type='+abonent_type+']').val();
			if (tariffs_hidden == '' || tariffs_hidden == null || tariffs_hidden == 'null') {
				tariffs_hidden = {};
				tariffs_hidden.auto_tariffs = {};
				tariffs_hidden.manual_tariffs = {};
				tariffs_hidden.groups = {};
			} else {
				tariffs_hidden = JSON.parse(tariffs_hidden);
			}

			if (selected_conn_tech !== undefined) {
				loader(true);
				var isset_tariffs = [];
				$('.zones__form__not-public-tariffs[data-abonent-type='+abonent_type+'] .thumbnail').each(function(){
					isset_tariffs.push($(this).data('tariff-id'));
				});
				$.get(
					'/zones/zones-addresses/create-conn-tech-toggle-item',
					{
						conn_tech : selected_conn_tech,
						abonent_type : abonent_type,
						checked : true,
						except_tariffs : isset_tariffs,
					},
					function (data)
					{
						tariffs_hidden.auto_tariffs[selected_conn_tech] = selected_conn_tech;

						rewriteTariffsHidden(tariffs_hidden, abonent_type);

						$('.zones__addresses__tariffs[data-abonent-type='+abonent_type+'] .all-active-tariffs-toggle-group').append(data.toggles);
						$('.zones__addresses__tariffs[data-abonent-type='+abonent_type+'] .zones__form__not-public-tariffs').append(data.not_public_tariffs);
						$('.zones__addresses__tariffs[data-abonent-type='+abonent_type+']').removeClass('hidden');
						if ($('.zones__form__not-public-tariffs[data-abonent-type='+abonent_type+'] .thumbnail').length != 0) {
							$('.zones__addresses__tariffs[data-abonent-type='+abonent_type+'] .all-active-not-public-tariffs-panel-group').removeClass('hidden');
						}
						tariffsToggleHandler();
						tariffsCheckboxPanelHandler();
						loader(false);
					},
					'json'
				);
			}
			if (deselected_conn_tech !== undefined) {
				var conn_techs_list = $(this).val();
				loader(true);
				if (conn_techs_list != null) {
					$.get(
						'/zones/zones-addresses/get-tariffs-id',
						{
							conn_techs : conn_techs_list,
							abonent_type_id : abonent_type,
							public_tariff : 2,
						},
						function (data)
						{
							$('.all-active-tariffs-toggle[data-abonent-type='+abonent_type+'][data-conn-tech-id='+deselected_conn_tech+']').parents('.form-group').remove();
							delete tariffs_hidden.auto_tariffs[deselected_conn_tech];
							$('.zones__form__public-tariffs[data-abonent-type='+abonent_type+'] .thumbnail').each(function(){
								var tariff_id = $(this).data('tariff-id');
								if (data.public[tariff_id] == undefined) {
									$(this).remove();
									delete tariffs_hidden.manual_tariffs[tariff_id];
									
								}
							});
							$('.zones__form__not-public-tariffs[data-abonent-type='+abonent_type+'] .thumbnail').each(function(){
								var tariff_id = $(this).data('tariff-id');
								if (data.not_public[tariff_id] == undefined) {
									$(this).remove();
									delete tariffs_hidden.manual_tariffs[tariff_id];
									
								}
							});
							rewriteTariffsHidden(tariffs_hidden, abonent_type);
							if ($('.zones__form__not-public-tariffs[data-abonent-type='+abonent_type+'] .thumbnail').length == 0) {
								$('.zones__addresses__tariffs[data-abonent-type='+abonent_type+'] .all-active-not-public-tariffs-panel-group').addClass('hidden');
							}
							if ($('.zones__form__public-tariffs[data-abonent-type='+abonent_type+'] .thumbnail').length == 0) {
								$('.zones__addresses__tariffs[data-abonent-type='+abonent_type+'] .all-active-tariffs-panel-group').addClass('hidden');
							}
							loader(false);
						},
						'json'
					)
				} else {

					tariffs_hidden.auto_tariffs = {};
					tariffs_hidden.manual_tariffs = {};
					rewriteTariffsHidden(tariffs_hidden, abonent_type);
					$('.all-active-tariffs-toggle[data-abonent-type='+abonent_type+']').parents('.form-group').each(function(){
						$(this).remove();
					});
					$('.zones__form__not-public-tariffs[data-abonent-type='+abonent_type+']').empty();
					$('.zones__form__public-tariffs[data-abonent-type='+abonent_type+']').empty();
					
					$('.zones__addresses__tariffs[data-abonent-type='+abonent_type+']').addClass('hidden');
					$('.zones__addresses__tariffs[data-abonent-type='+abonent_type+'] .all-active-not-public-tariffs-panel-group').addClass('hidden');
					loader(false);
				}	
				
			}
		});

		// логика заполнения скрытого поля в панельке тарифов определенной технологии
		function tariffsToggleHandler(){
			var abonent_type;
			$('.all-active-tariffs.all-active-tariffs-toggle').each(function(){
				if (!($(this).hasClass('processed'))) {
					$(this).addClass('processed');
					abonent_type = $(this).data('abonent-type');
					$(this).bootstrapToggle();	
				}
			});

			$('.all-active-tariffs-toggle-group .toggle.btn').each(function(){
				if (!($(this).hasClass('processed'))) {
					$(this).addClass('processed');

					$(this).click(function(){
						var tariffs_hidden = $('.zonesaddresses-tariffs[data-abonent-type='+abonent_type+']').val();
						tariffs_hidden = JSON.parse(tariffs_hidden);
						
						var conn_tech = $(this).find('.all-active-tariffs-toggle').data('conn-tech-id');
						var checked = !$(this).find('.all-active-tariffs-toggle').prop('checked');
						if (checked) {
							tariffs_hidden.auto_tariffs[conn_tech] = conn_tech;

							var non_auto_techs = [];
							$('.all-active-tariffs[data-abonent-type='+abonent_type+'].all-active-tariffs-toggle:not(:checked)[data-conn-tech-id != '+conn_tech+']').each(function(){
								non_auto_techs.push($(this).data('conn-tech-id'));
							});

							if (non_auto_techs.length != 0) {
								loader(true);
								$.get(
									'/zones/zones-addresses/get-tariffs-id',
									{
										conn_techs : non_auto_techs,
										abonent_type_id : abonent_type,
										public_tariff : 1
									},
									function (data)
									{
										$('.zones__form__public-tariffs[data-abonent-type='+abonent_type+'] .thumbnail').each(function(){
											var tariff_id = $(this).data('tariff-id');
											if (data[tariff_id] == undefined) {
												$(this).remove();
												delete tariffs_hidden.manual_tariffs[tariff_id];
												rewriteTariffsHidden(tariffs_hidden, abonent_type);
											}
										});
										loader(false);
									},
									'json'
								);
							} else {
								$('.zones__form__public-tariffs[data-abonent-type='+abonent_type+'] .thumbnail').each(function(){
									$(this).remove();
								});
								var not_public_tariffs = {};
								$('.zones__form__not-public-tariffs[data-abonent-type='+abonent_type+'] .thumbnail').each(function(){
									var tariff_id = $(this).data('tariff-id');
									not_public_tariffs[tariff_id] = tariff_id;
								});
								for (var tariff_id in tariffs_hidden.manual_tariffs) {
									if (not_public_tariffs[tariff_id] == undefined) {
										delete tariffs_hidden.manual_tariffs[tariff_id];
									}
								}
								$('.zones__addresses__tariffs[data-abonent-type='+abonent_type+'] .all-active-tariffs-panel-group').addClass('hidden');
								rewriteTariffsHidden(tariffs_hidden, abonent_type);
							}

						} else {
							delete tariffs_hidden.auto_tariffs[conn_tech];
							var isset_tariffs = [];
							$('.zones__form__public-tariffs[data-abonent-type='+abonent_type+'] .thumbnail').each(function(){
								isset_tariffs.push($(this).data('tariff-id'));
							});
							loader(true);
							$.get(
								'/zones/zones-addresses/create-tariff-item',
								{
									conn_tech : conn_tech,
									abonent_type : abonent_type,
									except_tariffs : isset_tariffs,
									public_tariff : 1
								},
								function (data)
								{
									if (data != false) {
										$('.zones__addresses__tariffs[data-abonent-type='+abonent_type+'] .all-active-tariffs-panel-group').removeClass('hidden');
										$('.zones__addresses__tariffs[data-abonent-type='+abonent_type+'] .all-active-tariffs-panel-group .zones__form__public-tariffs').append(data);
										tariffsCheckboxPanelHandler();
									}
									loader(false);
								},
								'json'
							);
							rewriteTariffsHidden(tariffs_hidden, abonent_type);
						}
					});
				}
			});
		}

		function tariffsCheckboxPanelHandler(){
			$('.zones__form__tariffs-container .thumbnail').each(function(){
				if (!($(this).hasClass('processed'))) {
					$(this).addClass('processed');

					$(this).click(function(){
						var abonent_type = $(this).data('abonent-type');
						$(this).find('.caption').toggleClass('bg-success');
						var checkBox = $(this).find('.manual-tariffs-checkboxes');
						checkBox.prop("checked", !checkBox.prop("checked"));

						var tariff_id = checkBox.data('tariff-id');

						var tariffs_hidden = $('.zonesaddresses-tariffs[data-abonent-type='+abonent_type+']').val();
						tariffs_hidden = JSON.parse(tariffs_hidden);

						if (checkBox.prop("checked")) {
							tariffs_hidden.manual_tariffs[tariff_id] = tariff_id;
						} else {
							delete tariffs_hidden.manual_tariffs[tariff_id];
						}

						rewriteTariffsHidden(tariffs_hidden, abonent_type);
					});
				}
			});
		}

		$('.zones__addresses__tariffs-choise-type').change(function(){
			var abonent_type = $(this).data('abonent-type'),
				checked = $(this).prop('checked'),
				tariffs_hidden = $('.zonesaddresses-tariffs[data-abonent-type='+abonent_type+']').val();

			tariffs_hidden = JSON.parse(tariffs_hidden);

			if (checked) {
				loader(true);
				$.post('/zones/zones-addresses/load-tariffs-groups-list',
					{
						abonent_type : abonent_type,
					},
					function(data){
						tariffs_hidden.manual_tariffs = {};
						tariffs_hidden.auto_tariffs = {};
						rewriteTariffsHidden(tariffs_hidden, abonent_type);
						$('.zones__addresses__tariffs-group-choise[data-abonent-type='+abonent_type+']').html(data);
						$('.zones__addresses__tariffs-single-choise[data-abonent-type='+abonent_type+']').addClass('hidden');
						$('.zones__addresses__tariffs-group-choise[data-abonent-type='+abonent_type+']').removeClass('hidden');
						groupPanelHandler();
						loader(false);
					},
					'json'
				);
			} else {
				tariffs_hidden.groups = {};

				$('.all-active-tariffs-toggle').each(function(){
					if ($(this).prop('checked')) {
						tariffs_hidden.auto_tariffs[$(this).data('conn-tech-id')] = $(this).data('conn-tech-id');
					}
				});

				$('.manual-tariffs-checkboxes').each(function(){
					if ($(this).prop('checked')) {
						tariffs_hidden.manual_tariffs[$(this).data('tariff-id')] = $(this).data('tariff-id');
					}
				});

				rewriteTariffsHidden(tariffs_hidden, abonent_type);
				$('.zones__addresses__tariffs-single-choise[data-abonent-type='+abonent_type+']').removeClass('hidden');
				$('.zones__addresses__tariffs-group-choise[data-abonent-type='+abonent_type+']').addClass('hidden');
			}

		});

		function groupPanelHandler(){
			$('.zones__form__group-panel').each(function(){
				if (!$(this).hasClass('processed')) {
					$(this).addClass('processed');

					$(this).click(function(){
						var abonent_type = $(this).data('abonent-type'),
							tariffs_hidden = $('.zonesaddresses-tariffs[data-abonent-type='+abonent_type+']').val()
							group_id = $(this).data('group-id');

						var checkBox = $(this).find('input');
						var checked = !$(this).find('input').prop("checked");
						checkBox.prop("checked", checked);

						tariffs_hidden = JSON.parse(tariffs_hidden);
						$(this).find('.caption').toggleClass('bg-success');

						if (checked) {console.log('checked');
							tariffs_hidden.groups[group_id] = group_id;
						} else{console.log('not checked');
							delete tariffs_hidden.groups[group_id];
						}

						rewriteTariffsHidden(tariffs_hidden, abonent_type);
					});
				}
			});
		}

	
	


		// добавление подъезда
		$('.zones__address__add-porch').click(function(){
			$('#zones__address__modal-add-porch').modal('show');

			$('.zones__address__modal-add-porch').click(function(){
				var porch = $('#zonesporches-porch_name').val();
				var addressId = $('#zonesporches-porch_name').data('addressId');
				var parentDiv = $('.zones__address__porches-collapses').find('div.panel-group').attr('id');

				$('#confirm').modal('show');
		        $("#confirm .confirm-content").html("Вы действительно хотите создать подъезд № "+porch+"?");
		        
		        $('#confirm .btn-success').one('click', function(e){
		        	loader(true);
					$.get(
						'/zones/zones-addresses/create-porch',
						{
							porch : porch,
							addressId : addressId,
							parent_div : parentDiv,
						},
						function (data)
						{
							$('#confirm').modal('hide');
							if (data.errors) {
								$('#zonesporches-porch_name').parents('.form-group').addClass('has-error').find('.help-block').html(data.errors.porch[0]);
							} else {
								$('#zones__address__modal-add-porch').modal('hide');
								$('div#'+parentDiv).append(data.html);
								addFloorHandler();
							}
							controlPorchHandler();
							loader(false);
						},
						'json'
					);
		        });	
			});

		});
		function controlPorchHandler(){
			$('.zones__address__porch-control').each(function(){
				if (!($(this).hasClass("processed"))) {
					$(this).addClass('processed');

					$(this).find('i.fa-pencil').click(function(e){
						var porch_name = $(this).data('porch-name');
						var porch_id = $(this).data('porch-id');

						$('#zonesporches-porch_name_update').val(porch_name);
						$('#zonesporches-porch_name_update').attr('data-porch-id', porch_id);
						$('#zones__address__update-porch').modal('show');

						$('.zones__address__modal__update-porch').click(function(){
							var porch_name = $('#zonesporches-porch_name_update').val();
							loader(true);
							$.get(
								'/zones/zones-addresses/update-porch',
								{
									porch_name : porch_name,
									porch_id : porch_id,
								},
								function (data)
								{
									if (data.errors) {
										$('#zonesporches-porch_name_update').parents('.form-group').addClass('has-error').find('.help-block').html(data.errors.porch_name[0]);
									} else {
										$('#zones__address__update-porch').modal('hide');
										$('span.porch-name[data-porch-id="'+data.porch_id+'"]').text(data.porch_name);
										$('.zones__address__porch-control i.fa-pencil[data-porch-id="'+data.porch_id+'"]').data('porch-name', data.porch_name);
									}
									loader(false);
								},
								'json'
							)
						});
					});

					$(this).find('i.fa-trash').click(function(){
						var porch_name = $(this).data('porch-name');
						var porch_id = $(this).data('porch-id');

						$('#confirm').modal('show');
						$("#confirm .confirm-content").html('Вы действительно хотите удалить подъезд № '+porch_name+'?');
						$('#confirm .btn-success').one('click', function(e){
	 						loader(true);
							$.get(
								'/zones/zones-addresses/remove-porch',
								{
									porch_id : porch_id,
								},
								function (data)
								{
									if (!data.errors) {
										loader(false);
										$('div[id='+porch_id+']').parents('.zones__address__porch-panel-heading').remove();
										$('#confirm').modal('hide');
									}
								},
								'json'
							);
						});	
					});
				}
			});
		}

		$('#zones__address__modal-add-porch').on('hidden.bs.modal', function(){
			$('#zonesporches-porch_name').val('').parents('.form-group').removeClass('has-error').find('.help-block').html('');
			$('.zones__address__modal-add-porch').unbind('click');
		});

		$('#zones__address__update-porch').on('hidden.bs.modal', function(){
			$('#zonesporches-porch_name_update').val('').parents('.form-group').removeClass('has-error').find('.help-block').html('');
			$('.zones__address__modal__update-porch').unbind('click');
		});
		
		// Добавление этажей
		function addFloorHandler(){
			$('.zones__address__add-floor').each(function(){
				if (!($(this).hasClass("processed"))) {
					$(this).addClass('processed');
					//открытие модали на добавление этажа
					$(this).click(function(){
						$('#zones__address__add-floor').modal('show');
						var porchId = $(this).data('porchId');

						// добавление этажа
						$('.zones__address__modal-add-floor').click(function(){
							var floor = $('#zonesfloors-floor_name').val();

							$('#confirm').modal('show');
					        $("#confirm .confirm-content").html("Вы действительно хотите создать этаж № "+floor+"?");
					        
					        $('#confirm .btn-success').one('click', function(e){
					        	loader(true);
								$.get(
									'/zones/zones-addresses/create-floor',
									{
										floor : floor,
										porchId : porchId,
									},
									function (data)
									{
	                    				$('#confirm').modal('hide');
										if (data.errors) {
											$('#zonesfloors-floor_name').parents('.form-group').addClass('has-error').find('.help-block').html(data.errors.floor_name[0]);
										} else {
											$('#zones__address__add-floor').modal('hide');

											$('div[data-porch-id="'+data.porch_id+'"]').find('.zones__address__add-floors').after(data.html);
											addFlatHandler();
										}
										controlFloorHandler();
										loader(false);
									},
									'json'
								);
					        });		
							
						});
					});
				}
			});

			$('.zones__address__add-floors').each(function(){
				if (!($(this).hasClass("processed"))) {
					$(this).addClass('processed');
					//открытие модали на добавление диапазона этажей
					$(this).click(function(){
						$('#zones__address__add-floors').modal('show');
						var porchId = $(this).data('porchId');

						// добавление этажей
						$('.zones__address__modal-add-floors').click(function(){
							var floorBegin = $('#zones__address__modal__floor-begin').val();
							var floorEnd = $('#zones__address__modal__floor-end').val();

							$('#confirm').modal('show');
					        $("#confirm .confirm-content").html("Вы действительно хотите создать этажи с "+floorBegin+" по "+floorEnd+"?");
					        
					        $('#confirm .btn-success').one('click', function(e){
					        	loader(true);
								$.get(
									'/zones/zones-addresses/create-floors',
									{
										floorBegin : floorBegin,
										floorEnd : floorEnd,
										porchId : porchId,
									},
									function (data)
									{
										$('#confirm').modal('hide');
										if (data.floor_begin_error) {
											$('#zones__address__modal__floor-begin').parents('.form-group').addClass('has-error').find('.help-block').html(data.floor_begin_error.errors.floor_name[0]);
										} 
										if (data.floor_end_error) {
											$('#zones__address__modal__floor-end').parents('.form-group').addClass('has-error').find('.help-block').html(data.floor_end_error.errors.floor_name[0]);
										}


										if (!data.floor_end_error && !data.floor_begin_error) {
											$('#zones__address__add-floors').modal('hide');
											var html = ''
											
											$('div[data-porch-id="'+data[0].porch_id+'"]').find('.zones__address__add-floors').after(data[0].html);
											addFlatHandler();
										}
										controlFloorHandler();
										loader(false);
									},
									'json'
								);
					        });	
						});
					});
				}
			});
		}

		function controlFloorHandler(){
			$('.zones__address__floor-control').each(function(){
				if (!($(this).hasClass("processed"))) {
					$(this).addClass('processed');

					//кнопка редактирования
					$(this).find('i.fa-pencil').click(function(){
						var floor_name = $(this).data('floor-name');
						var floor_id = $(this).data('floor-id');

						$('#zonesfloors-floor_name_update').val(floor_name);
						$('#zonesfloors-floor_name_update').attr('data-floor-id', floor_id);
						$('#zones__address__update-floor').modal('show');

						$('.zones__address__modal__update-floor').click(function(){
							var floor_name = $('#zonesfloors-floor_name_update').val();
							loader(true);
							$.get(
								'/zones/zones-addresses/update-floor',
								{
									floor_name : floor_name,
									floor_id : floor_id,
								},
								function (data)
								{
									if (data.errors) {
										$('#zonesfloors-floor_name_update').parents('.form-group').addClass('has-error').find('.help-block').html(data.errors.floor_name[0]);
									} else {
										$('#zones__address__update-floor').modal('hide');
										$('span.floor-name[data-floor-id="'+data.floor_id+'"]').text(data.floor_name);
										$('.zones__address__floor-control i.fa-pencil[data-floor-id="'+data.floor_id+'"]').data('floor-name', data.floor_name);
									}
									loader(false);
								},
								'json'
							);
						});
					});

					$(this).find('i.fa-trash').click(function(){
						var floor_id = $(this).data('floor-id');
						var floor_name = $(this).data('floor-name');

						$('#confirm').modal('show');
						$("#confirm .confirm-content").html('Вы действительно хотите удалить этаж № '+floor_name+'?');
						$('#confirm .btn-success').one('click', function(e){
	 						loader(true);
							$.get(
								'/zones/zones-addresses/remove-floor',
								{
									floor_id : floor_id,
								},
								function (data)
								{
									if (!data.errors) {
										loader(false);
										$('.zones__address__floor[data-floor-id='+floor_id+']').remove();
										$('#confirm').modal('hide');
									}
								},
								'json'
							);
						});	
					});
				}
			});
		}

		$('#zones__address__add-floor').on('hidden.bs.modal', function(){
			$('#zonesfloors-floor_id').val('').parents('.form-group').removeClass('has-error').find('.help-block').html('');
			$('.zones__address__modal-add-floor').unbind('click');
		});

		$('#zones__address__add-floors').on('hidden.bs.modal', function(){
			$('#zones__address__modal__floor-begin').val('').parents('.form-group').removeClass('has-error').find('.help-block').html('');
			$('#zones__address__modal__floor-end').val('').parents('.form-group').removeClass('has-error').find('.help-block').html('');
			$('.zones__address__modal-add-floors').unbind('click');
		});

		$('#zones__address__update-floor').on('hidden.bs.modal', function(){
			$('#zonesfloors-floor_name_update').val('').parents('.form-group').removeClass('has-error').find('.help-block').html('');
			$('.zones__address__modal__update-floor').unbind('click');
		});

		// Добавление квартир
		function addFlatHandler(){
			$('.zones__address__add-flat').each(function(){
				if (!($(this).hasClass("processed"))) {
					$(this).addClass('processed');
					$(this).click(function(){
						var roomType = $(this).data('roomType');
						if (roomType == 1) {
							$('#zones__address__add-flat span.zones__address__modal-header-flat').removeClass('hidden');
							$('#zones__address__add-flat span.zones__address__modal-header-office').addClass('hidden');
						} else if (roomType == 2) {
							$('#zones__address__add-flat span.zones__address__modal-header-office').removeClass('hidden');
							$('#zones__address__add-flat span.zones__address__modal-header-flat').addClass('hidden');
						}
						var floorId = $(this).data('floorId');
						$('#zones__address__add-flat').modal('show');

						$('.zones__address__modal-add-flat').click(function(){
							var flat = $('#zonesflats-flat_name').val();

					        $('#confirm').modal('show');
					        if (roomType == 1) {
					        	$("#confirm .confirm-content").html("Вы действительно хотите создать квартиру № "+flat+"?");
							} else if (roomType == 2) {
								$("#confirm .confirm-content").html("Вы действительно хотите создать офис № "+flat+"?");
							}
					        
					        $('#confirm .btn-success').one('click', function(e){
					            loader(true);
								$.get(
									'/zones/zones-addresses/create-flat',
									{
										flat : flat,
										floorId : floorId,
										roomType : roomType,
									},
									function (data)
									{
										
	                    				$('#confirm').modal('hide');
										if (data.errors) {
											$('#zonesflats-flat_name').parents('.form-group').addClass('has-error').find('.help-block').html(data.errors.flat_name[0]);
										} else {
											$('#zones__address__add-flat').modal('hide');

											if (roomType == 1) {
												$('.zones__address__floor[data-floor-id="'+data.floor_id+'"]').find('.zones__address__flats_offices .zones__address__flats').append(data.html);
											} else if (roomType == 2) {
												$('.zones__address__floor[data-floor-id="'+data.floor_id+'"]').find('.zones__address__flats_offices .zones__address__offices').append(data.html);
											}
										}
										updateFlatHandler();	
										loader(false);							
									},
									'json'
								);
					        });						
						})
					});
				}
			});

			$('.zones__address__add-flats').each(function(){
				if (!($(this).hasClass("processed"))) {
					$(this).addClass('processed');
					//открытие модали на добавление диапазона квартир
					$(this).click(function(){
						var roomType = $(this).data('roomType');
						if (roomType == 1) {
							$('#zones__address__add-flats span.zones__address__modal-header-flat').removeClass('hidden');
							$('#zones__address__add-flats span.zones__address__modal-header-office').addClass('hidden');
						} else if (roomType == 2) {
							$('#zones__address__add-flats span.zones__address__modal-header-office').removeClass('hidden');
							$('#zones__address__add-flats span.zones__address__modal-header-flat').addClass('hidden');
						}
						$('#zones__address__add-flats').modal('show');
						var floorId = $(this).data('floorId');

						// добавление квартиры
						$('.zones__address__modal-add-flats').click(function(){
							var flatBegin = $('#zones__address__modal__flat-begin').val();
							var flatEnd = $('#zones__address__modal__flat-end').val();

							$('#confirm').modal('show');
					        if (roomType == 1) {
					        	$("#confirm .confirm-content").html("Вы действительно хотите создать квартиры с "+flatBegin+" по "+flatEnd+"?");
							} else if (roomType == 2) {
								$("#confirm .confirm-content").html("Вы действительно хотите создать офисы с "+flatBegin+" по "+flatEnd+"?");
							}
					        
					        $('#confirm .btn-success').one('click', function(e){
					        	loader(true);
								$.get(
									'/zones/zones-addresses/create-flats',
									{
										flatBegin : flatBegin,
										flatEnd : flatEnd,
										floorId : floorId,
										roomType : roomType,
									},
									function (data)
									{
										$('#confirm').modal('hide');
										if (data.flat_begin_error) {
											$('#zones__address__modal__flat-begin').parents('.form-group').addClass('has-error').find('.help-block').html(data.flat_begin_error.errors.flat_name[0]);
										} 
										if (data.flat_end_error) {
											$('#zones__address__modal__flat-end').parents('.form-group').addClass('has-error').find('.help-block').html(data.flat_end_error.errors.flat_name[0]);
										}


										if (!data.flat_begin_error && !data.flat_end_error) {
											$('#zones__address__add-flats').modal('hide');
											var html = ''
											for (var flat in data){
												html += data[flat].html;
											}

											if (roomType == 1) {
												$('.zones__address__floor[data-floor-id="'+floorId+'"]').find('.zones__address__flats_offices .zones__address__flats').append(html);
											} else if (roomType == 2) {
												$('.zones__address__floor[data-floor-id="'+floorId+'"]').find('.zones__address__flats_offices .zones__address__offices').append(html);
											}
										}
										updateFlatHandler();
										loader(false);
									},
									'json'
								);
					        });							
						});
					});
				}
			});
		};

		function updateFlatHandler(){
			$('.zones__address__flat-control .fa-pencil').each(function(){
				if (!($(this).hasClass("processed"))) {
					$(this).addClass('processed');
					$(this).click(function(){
						var roomType = $(this).data('room-type');
						if (roomType == 1) {
							$('#zones__address__update-flat span.zones__address__modal-header__update-flat').removeClass('hidden');
							$('#zones__address__update-flat span.zones__address__modal-header__update-office').addClass('hidden');
						} else if (roomType == 2) {
							$('#zones__address__update-flat span.zones__address__modal-header__update-office').removeClass('hidden');
							$('#zones__address__update-flat span.zones__address__modal-header__update-flat').addClass('hidden');
						}
						var flat_name = $(this).data('flat-name');
						var flat_id = $(this).data('flat-id');

						$('#zonesflats-flat_name_update').val(flat_name);
						$('#zonesflats-flat_name_update').attr('data-flat-id', flat_id);
						$('#zones__address__update-flat').modal('show');

						$('.zones__address__modal__update-flat').click(function(){
							var flat_name = $('#zonesflats-flat_name_update').val();
							loader(true);
							$.get(
								'/zones/zones-addresses/update-flat',
								{
									flat_name : flat_name,
									flat_id : flat_id,
								},
								function (data)
								{
									if (data.errors) {
										$('#zonesflats-flat_name_update').parents('.form-group').addClass('has-error').find('.help-block').html(data.errors.flat_name[0]);
									} else {
										$('#zones__address__update-flat').modal('hide');

										if (roomType == 1) {
											$('i.fa[data-flat-id="'+data.flat_id+'"]').parents('.zones__address__flat').replaceWith(data.html);
										} else if (roomType == 2) {
											$('i.fa[data-flat-id="'+data.flat_id+'"]').parents('.zones__address__flat').replaceWith(data.html);
										}
									}
									updateFlatHandler();
									loader(false);
								},
								'json'
							)
						});
					});
				}
			});
		}

		$('#zones__address__add-flat').on('hidden.bs.modal', function(){
			$('#zonesflats-flat_name').val('').parents('.form-group').removeClass('has-error').find('.help-block').html('');
			$('.zones__address__modal-add-flat').unbind('click');
		});
		
		$('#zones__address__add-flats').on('hidden.bs.modal', function(){
			$('#zones__address__modal__flat-begin').val('').parents('.form-group').removeClass('has-error').find('.help-block').html('');
			$('#zones__address__modal__flat-end').val('').parents('.form-group').removeClass('has-error').find('.help-block').html('');
			$('.zones__address__modal-add-flats').unbind('click');
		});

		$('#zones__address__update-flat').on('hidden.bs.modal', function(){
			$('#zonesflats-flat_name_update').val('').parents('.form-group').removeClass('has-error').find('.help-block').html('');
			$('.zones__address__modal__update-flat').unbind('click');
		});

		

		/*---------------------------Mass Create--------------------------*/
		if ($('.zones-mass-create').length > 0) {
			addressRowDeleteHandler();
			addressAllRowDeleteHandler();
			addressesListPageHandler();
			addressesListCheckboxHandler();
			checkedAllAddressesHandler();

			$('a.place-find-editable').on('address:enable', function(){
				$('#zones__address__load-addresses-list').prop('disabled', false);
			});
			$('a.place-find-editable').on('address:disable', function(){
				$('#zones__address__load-addresses-list').prop('disabled', true);
			});

			$('#zones__address__load-addresses-list').click(function(){
				var uuid_list = $('a.place-find-editable input').val();
				$('#zonesaddresses-addresses_stack').val('');
				loadAddressesListTable(uuid_list, 1);
			});

			function loadAddressesListTable(uuid_list, page){
				var checked_list = $('#zonesaddresses-addresses_stack').val();
				loader(true);
				$.post(
					'/zones/zones-addresses/load-addresses-list-table',
					{
						uuid_list : uuid_list,
						page : page,
						checked_list : checked_list,
					},
					function (data)
					{
						if (data != 'error') {
							$('#zones__address__addresses-list').html(data);
							$('.zones__addresses__address-list-checkbox').each(function(){
								$(this).bootstrapToggle();
								addressesListPageHandler();
								addressesListCheckboxHandler();
								checkedAllAddressesHandler();
							});
						}
						loader(false);
					},
					'json'
				);
			}

			function loadAddressesListRow(uuid){
				loader(true);
				$.post(
					'/zones/zones-addresses/load-addresses-list-chosen-row',
					{
						uuid : uuid,
					},
					function (data)
					{
						if (data != 'error') {
							$('#addresses-list__chosen tbody').append(data);
							addressRowDeleteHandler();
							addressAllRowDeleteHandler();
						}
						loader(false);
					},
					'json'
				);
			}

			function addressesListPageHandler(){
				$('#zones__addresses__address-list-pagination a.zones__addresses__page-link').each(function(){
					if (!$(this).hasClass('processed')) {
						$(this).addClass('processed');

						$(this).click(function(){
							var uuid_list = $('a.place-find-editable input').val();
							var page = $(this).data('dest-page');
							loadAddressesListTable(uuid_list, page);
						});
					}
				});
			}

			function addressesListCheckboxHandler(){
				$('.zones__addresses__address-list-checkbox').each(function(){
					if (!$(this).hasClass('processed')) {
						$(this).addClass('processed');

						$(this).change(function(){
							var checked = $(this).prop('checked');
							var uuid = $(this).data('uuid');
							var addresses_stack = $('#zonesaddresses-addresses_stack').val();

							if (checked) {
								addresses_stack += uuid + ';';
								loadAddressesListRow(uuid);
							} else {
								addresses_stack = addresses_stack.replace(uuid + ';', '');
								$('.addresses-list__delete[data-uuid='+uuid+']').click();
							}

							$('#zonesaddresses-addresses_stack').val(addresses_stack);
						});
					}
				});
			}

			function addressRowDeleteHandler(){
				$('.addresses-list__delete').each(function(){
					if (!$(this).hasClass('processed')) {
						$(this).addClass('processed');

						$(this).click(function(){
							var uuid = $(this).data('uuid');
							$(this).parents('tr').remove();

							$('.zones__addresses__address-list-checkbox[data-uuid='+uuid+']').bootstrapToggle('off');
						});
					}
				});
			}

			function addressAllRowDeleteHandler(){
				if (!$('#addresses-list__table__uncheck-all').hasClass('processed')) {
					$('#addresses-list__table__uncheck-all').addClass('processed');

					$('#addresses-list__table__uncheck-all').click(function(){
						$('.zones__addresses__address-list-checkbox').each(function(){
							if ($(this).prop('checked')) {
								$(this).bootstrapToggle('off');
							}
						});
					});
				}
			}

			function checkedAllAddressesHandler(){
				if (!$('#addresses-list__table__check-all').hasClass('processed')) {
					$('#addresses-list__table__check-all').addClass('processed');

					$('#addresses-list__table__check-all').click(function(){
						$('.zones__addresses__address-list-checkbox').each(function(){
							var checked = $(this).prop('checked');
							if (!checked) {
								$(this).bootstrapToggle('on');
							}
						});
					});
				}
			}
		}

		/*---------------------------View--------------------------*/
		// карта на вьюшке
		if ($('html.view').length > 0) {
			ymaps.ready(function(){
				ymaps.ready(init);

				var map,
					object,
					center,
					zoom,
					clusterer,
					object,
					geoObjects = new Array();

				coords = $('#zones__address-view__map-place').data('coordinates').split(',').map(parseFloat);
				center = coords;
				zoom = 15;

				function init(){
		            map = new ymaps.Map('zones__address-view__map-place', {
		                center: center,
		                zoom: zoom,
		                controls: ['zoomControl', 'typeSelector', 'fullscreenControl'],
		            });

		            clusterer = new ymaps.Clusterer({
			            preset: 'islands#invertedVioletClusterIcons',
			        });

				    // Добавление объектов.
				    object = new ymaps.GeoObject({
	                                geometry: {
	                                    type: "Point",
	                                    coordinates: coords,
	                                },
	                            },
	                            {
	                                preset: 'islands#violetDotIconWithCaption',
	                            });

	                geoObjects.push(object);

	                clusterer.add(geoObjects);
	                map.geoObjects.add(clusterer);
		        }

			});
		}

		/*---------------------------Index--------------------------*/

		//Кнопка скрытия поиска
		$('.zones__addresses-view__open-search').click(function(){
			$('.zones-search').stop().slideToggle(300);
			if ($(this).html() == 'Показать поиск') {
				$(this).html('Скрыть поиск');
			} else {
				$(this).html('Показать поиск');
			}
		});

		//Включение/отключение кнопки поиска
		$('a.place-find-editable').on('address:enable', function(){
			$('#zones__search__search-button').prop('disabled', false);
		});
		$('a.place-find-editable').on('address:disable', function(){
			$('#zones__search__search-button').prop('disabled', true);
		});

		//показ модали тарифа во view
		$('.zones__view__tariff-panel').click(function(){
			var tariff_id = $(this).data('tariff-id');
			loader(true);
			$.get(
				'/zones/zones-addresses/load-tariff-modal-body',
				{
					tariff_id : tariff_id,
				},
				function (data)
				{
					$('.zones__view__tariff-modal[data-tariff-id = '+tariff_id+']').find('.modal-body').html(data);
					loader(false);
					$('.zones__view__tariff-modal[data-tariff-id = '+tariff_id+']').modal('show');
				},
				'json'
			)
		})
	});
}(jQuery));