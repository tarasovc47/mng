(function($){
	$(document).ready(function(){

		if ($('html.create').length > 0 || $('html.create-branch').length > 0 || $('html.update').length > 0 || $('html.update-branch').length > 0) {
			ymaps.ready(function(){
				ymaps.ready(init);

				var map,
					object,
					center,
					zoom,
					coords = $('.frontend.manag-companies #managcompanies-coordinates, .frontend.manag-companies #managcompaniesbranches-coordinates').val();

				center = (coords != '') ? coords.split(',').map(parseFloat) : [57.150612, 65.547308];
				zoom = (coords != '') ? 15 : 10;
				coords = center;

				function init(){
		            map = new ymaps.Map('manag-companies__map', {
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

					map.events.add('boundschange', function(e){
						set_value(object.geometry.getCoordinates());
		            });
		        }

		        function set_value(coords){
		        	$('.frontend.manag-companies #managcompanies-coordinates, .frontend.manag-companies #managcompaniesbranches-coordinates').val(coords);
		        }
			});
		}

		// маска телефона для создания контактных лиц
		$('#contactfaces-phone').mask("+7 (999) 999-9999");

		// подтверждение удаления контакта у укашки
		$("a.manag_companies__contact_delete, a.manag_companies__branch_contact_delete").click(function(e){
			return confirm("Вы уверены, что хотите удалить контактное лицо?");
		});


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

				center = [57.150612, 65.547308];
				zoom = 10;

				function init(){
		            map = new ymaps.Map('manag-companies__general-map', {
		                center: center,
		                zoom: zoom,
		                controls: ['zoomControl', 'typeSelector', 'fullscreenControl'],
		            });

		            clusterer = new ymaps.Clusterer({
			            preset: 'islands#invertedVioletClusterIcons',
			        });

				    // Добавление объектов.
				    coords = $('#information').data('company-coord').split(',').map(parseFloat);
				    object = new ymaps.GeoObject({
	                                geometry: {
	                                    type: "Point",
	                                    coordinates: coords,
	                                },
	                                properties: {
	                                	iconCaption: 'Фактический адрес',
	                                },
	                            },
	                            {
	                                preset: 'islands#violetDotIconWithCaption',
	                            });

	                geoObjects.push(object);

	                $('#branches td[data-branch-coordinates]').each(function(){
	                	coords = $(this).data('branchCoordinates').split(',').map(parseFloat);
	                	branch_name = $(this).data('branchName');
	                	object = new ymaps.GeoObject({
	                                geometry: {
	                                    type: "Point",
	                                    coordinates: coords,
	                                },
	                                properties: {
	                                	iconCaption: branch_name,
	                                },
	                            },
	                            {
	                                preset: 'islands#violetDotIconWithCaption',
	                            });

	                	geoObjects.push(object);
					});


	                clusterer.add(geoObjects);
	                map.geoObjects.add(clusterer);

					
		        }

			});
		}

		$('#managcompaniestocontacts-contact_face_id').chosen({
			width : '100%',
			placeholder_text : ' ',		
			no_results_text : 'Ничего не найдено!',
		});

		$('#managcompaniestocontacts-branch_id').click(function(){
			var company_id = $(this).data('company-id');
			var branch_id = $(this).val();

			if (branch_id != '') {
				loader(true);
				$.get(
					'/manag-companies/get-contacts-list-for-branch',
					{
						company_id : company_id,
						branch_id : branch_id,
					},
					function (data)
					{
						$('#managcompaniestocontacts-contact_face_id').html(data).prop('disabled', false).trigger('chosen:updated');
						$('#managcompaniestocontacts-contact_office_id').prop('disabled', false);
						loader(false);							
					},
					'json'
				)
			} else {
				$('#managcompaniestocontacts-contact_face_id').empty().prop('disabled', true).trigger('chosen:updated');
				$('#managcompaniestocontacts-contact_office_id').prop('disabled', true);
			}
			
		});

		// Подгрузка списка контактов в селект на странице массового добавления адресов к УК
		$('#dynamicmodel-branch_id').click(function(){
			var branch_id = $(this).val();

			if (branch_id != '') {
				loader(true);
				$.get(
					'/manag-companies/get-contacts-for-branch',
					{
						branch_id : branch_id,
					},
					function (data)
					{
						$('#dynamicmodel-key_keeper').append(data);
						loader(false);							
					},
					'json'
				)
			} else {
				$('#dynamicmodel-key_keeper optgroup[label="Участок"]').remove();
			}
		});	
	});
}(jQuery));