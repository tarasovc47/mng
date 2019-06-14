(function($){
	if(typeof window.obUnloader != 'object'){
	   //window.obUnloader = new Unloader();
	}

	$(document).ready(function(){
		// Переключение табов в создании заявки
		$('.application-create__tab-client-id').click(function(e){
			e.preventDefault();

			$('.application-create__tab-client-id').removeClass('active');
			$('.application-create__tab-content-client-id .tab-pane.client-id-tab').removeClass('active');

			var id = $(this).find('a').attr('data-client-id');
			$(this).addClass('active');
			$('.application-create__tab-content-client-id .tab-pane[data-client-id = "' + id + '"]').addClass('active');
		});

		// Создать заявку
		$(".create-app").click(function(e){
			e.preventDefault();

			dataСollection();
		});

		var scenarios = [];

		$(".create_application").change(function(e){
			var value = $(this).prop("checked"),
				service_row = $(this).parents(".service"),
				billing_id = service_row.find(".conn_tech").attr("data-connection-technology-billing-id"),
				loki_basic_service_id = service_row.find(".login").attr("data-loki-basic-service-id"),
				service_billing_id = service_row.parents(".panel-service").attr("data-service-billing-id"),
				login = service_row.find(".login").text();

			if(!value){
				$(".create-section[data-loki-basic-service-id='" + loki_basic_service_id + "']").remove();
				delete scenarios[loki_basic_service_id];
				stateCreateBtn();
				return;
			}

			if(billing_id != ""){
				getAttributesByTechBillingId(billing_id, loki_basic_service_id, login);
			}
			else{
				getConnTechsByService(service_billing_id, loki_basic_service_id, login);
			}
		});

		/** Запускает/перезапускает весь JS интерактив */
		function init(){
			stateCreateBtn();
			attributesCheckboxHandler();
			conntechsCheckboxHandler();
			departmentsHandler();
		}

		/** Получает дерево атрибутов для технологии подключения по ее billing_id */
		function getAttributesByTechBillingId(billing_id, loki_basic_service_id, login){
			loader(true);
			$.get(
				"/techsup/applications/get-attributes-by-tech",
				{
					billing_id : billing_id,
					loki_basic_service_id : loki_basic_service_id,
					login : login,
				},
				function(data){
					loader(false);
					switch(data.status){
						case "error":
							console.log(data.message);
							break;
						case "success":
							$(".create-area").append(data.html);
							scenarios[loki_basic_service_id] = data.scenarios;
							init();
							break;
						default:
					}
				},
				'json'
			);
		}

		/** Получает дерево атрибутов для технологии подключения по ее id */
		function getAttributesByTechId(tech_id, loki_basic_service_id){
			loader(true);
			$.get(
				"/techsup/applications/get-attributes-by-tech",
				{
					tech_id : tech_id,
					loki_basic_service_id : loki_basic_service_id,
				},
				function(data){
					loader(false);
					switch(data.status){
						case "error":
							console.log(data.message);
							break;
						case "success":
							$(".create-section[data-loki-basic-service-id='" + loki_basic_service_id + "'] .connection_technology.checked").append(data.html);
							scenarios[loki_basic_service_id] = data.scenarios;
							init();
							break;
						default:
					}
				},
				'json'
			);
		}

		/** Получает технологии подключения для сервиса по его billing_id */
		function getConnTechsByService(service_billing_id, loki_basic_service_id, login){
			loader(true);
			$.get(
				"/techsup/applications/get-conntechs-by-service",
				{
					service_billing_id : service_billing_id,
					loki_basic_service_id : loki_basic_service_id,
					login : login,
				},
				function(data){
					loader(false);
					switch(data.status){
						case "error":
							console.log(data.message);
							break;
						case "success":
							$(".create-area").append(data.html);
							init();
							break;
						default:
					}
				},
				'json'
			);
		}

		/** Обработчик, который инициализирует всю работу чекбоксов атрибутов */
		function attributesCheckboxHandler(){
			initTechFields();
			$(".attribute_chx:not(.processed)").each(function(){
				$(this).addClass("processed");

				$(this).click(function(e){
					var value = $(this).prop("checked"),
						attr_id = $(this).attr("data-id"),
						parent_all = $(this).parents(".attributes"),
						parent_this = $(this).parents(".checkbox[data-attr='" + attr_id + "']"),
						loki_basic_service_id = $(this).parents(".create-section").attr("data-loki-basic-service-id");
						
					if(value){
						parent_this.children(".attribute.child").addClass("show");
						parent_this.children(".attribute-field").addClass("show");

						if(parent_this.hasClass("start")){
							parent_all.find(".attribute.start:not([data-attr='" + attr_id + "']) .attribute_chx").prop("disabled", true);
						}
					}
					else{
						parent_this.find(".attribute.child").removeClass("show");
						parent_this.find(".attribute-field").removeClass("show");
						parent_this.find(".attribute_chx").prop("checked", false);

						if(parent_this.hasClass("start")){
							parent_all.find(".attribute.start .attribute_chx").prop("disabled", false);
						}
					}

					findCorrectDepartments(loki_basic_service_id);
				});
			});
		}

		/** Обработчик, который инициализирует всю работу чекбоксов технологий подключения */
		function conntechsCheckboxHandler(){
			$(".connection_technology_chx:not(.processed)").each(function(){
				$(this).addClass("processed");

				$(this).click(function(e){
					var value = $(this).prop("checked"),
						tech_id = $(this).attr("data-id"),
						billing_id = $(this).attr("data-billing-id"),
						parent_all = $(this).parents(".connection_technologies"),
						parent_this = $(this).parents(".connection_technology"),
						section = $(this).parents(".create-section"),
						loki_basic_service_id = section.attr("data-loki-basic-service-id");

					if(value){
						parent_this.addClass("checked");
						parent_all.find(".connection_technology_chx:not([data-id='" + tech_id +"'])").prop("disabled", true);

						section.attr("data-connection-technology-id", tech_id);

						getAttributesByTechId(tech_id, loki_basic_service_id);
					}
					else{
						parent_this.removeClass("checked");
						parent_all.find(".connection_technology_chx").prop("disabled", false);

						section.attr("data-connection-technology-id", 0);

						parent_this.find(".attributes").remove();

						delete scenarios[loki_basic_service_id];
					}
				});
			});
		}

		/** Обработчик, который инициализирует всю работу выбора отделов */
		function departmentsHandler(){
			$(".departments-show-all:not(.processed)").each(function(){
				$(this).addClass("processed");

				$(this).click(function(e){
					e.preventDefault();

					$(this).prev(".departments").find(".department").addClass("show");
				});
			})

			$(".department:not(.processed)").each(function(){
				$(this).addClass("processed");

				$(this).click(function(){
					if(!$(this).hasClass("checked")){
						$(this).parents(".departments").find(".department").removeClass("checked");

						$(this).addClass("checked");

						brigadesNodAnalyze();
					}
				});
			});
		}

		/** Обработчик, который инициализирует всю работу полей атрибутов */
		function initTechFields(){
			$(".attribute-field").each(function(){
				if(!$(this).hasClass("processed")){
					$(this).addClass("processed");
					var type = $(this).attr("data-field");

					if(type == "list_checkbox"){
						var field = $(this),
							cardinality = customParseInt($(this).attr("data-cardinality"));

						if(cardinality > 1){
							$(this).find(".control").click(function(e){
								var bool = field.find(".control:checked").length >= cardinality;
								
								field.find(".control:not(:checked)").prop("disabled", bool);
							});
						}
					}
				}
			});
		}

		/** Обработчик, который анализирует атрибуты, затем настраивает и подсвечивает отделы */
		function findCorrectDepartments(loki_basic_service_id){
			var departments = {},
				workspace = $(".create-section[data-loki-basic-service-id='" + loki_basic_service_id + "']"),
				attributes = workspace.find(".attribute_chx:checked"),
				attr_id,
				attr_lvl,
				department_id,
				scenario_id,
				max_lvl = 0,
				checked = false;

			attributes.each(function(){
				attr_id = $(this).attr("data-id");
				attr_lvl = $(this).attr("data-level");

				if(scenarios[loki_basic_service_id][attr_id] != undefined){
					department_id = scenarios[loki_basic_service_id][attr_id][0]["department_id"];

					if((departments[department_id] == undefined) || (departments[department_id]["attr_lvl"] < attr_lvl)){
						if(max_lvl < attr_lvl){
							max_lvl = attr_lvl;
							checked = department_id;
						}

						scenario_id = scenarios[loki_basic_service_id][attr_id][0]["scenario_id"];

						departments[department_id] = {
							"attr_id" : attr_id,
							"attr_lvl" : attr_lvl,
							"scenario_id" : scenario_id,
						};
					}				
				}
			});

			workspace
				.find(".department")
				.removeClass("maybe checked has-hint")
				.attr("data-scenario", "0")
					.children(".department-hint")
					.empty();

			for(var department_id in departments){
				var title = $(".attribute[data-attr='" + departments[department_id]["attr_id"] + "'] > label").text().trim(),
					classes = "show maybe has-hint";

				title = "Возможный вариант, т.к. выбран атрибут: " + title;

				if(customParseInt(department_id) == checked){
					classes = "show maybe checked has-hint";
				}

				workspace
					.find(".department[data-id='" + department_id + "']")
					.addClass(classes)
					.attr("data-scenario", departments[department_id]["scenario_id"])
						.children(".department-hint")
						.append(title);
			}

			brigadesNodAnalyze();
		}

		/** Показывает или скрывает кнопку создания заявки */
		function stateCreateBtn(){
			$(".create-section").length > 0 ? $(".create-app").addClass("show") : $(".create-app").removeClass("show");
		}

		/** Сбор и подготовка данных для создания заявки */
		function dataСollection($fields){
			$(".create-section__error").empty();
			$(".attribute-field__error").empty();
			$(".create-section").removeClass("error");
			$(".attribute-field").removeClass("error");

			var data = {},
				loki_basic_service_id,
				connection_technology,
				attributes,
				department,
				group_id,
				scenario,
				attribute_container,
				field_type,
				field_id,
				field_value;

			$(".create-section").each(function(){
				loki_basic_service_id = customParseInt($(this).attr("data-loki-basic-service-id"));
				connection_technology = customParseInt($(this).attr("data-connection-technology-id"));
				attributes = "";
				department = $(this).find(".department.checked").attr("data-id");
				group_id = $(this).find("select[name='brigades-nod']").val();
				scenario = $(this).find(".department.checked").attr("data-scenario");

				if(scenario === undefined){
					scenario = 0;
				}

				data[loki_basic_service_id] = {
					"connection_technology" : connection_technology,
					"attributes" : "",
					"department" : department,
					"group_id" : group_id,
					"scenario" : scenario,
					"fields" : {},
				};

				$(this).find(".attribute_chx:checked").each(function(){
					attributes += (attributes == "") ? "{" : ",";
					attributes += $(this).attr("data-id");

					attribute_container = $(this).closest(".attribute");

					attribute_container.children(".attribute-field").each(function(){
						field_type = $(this).attr("data-field"),
						field_id = customParseInt($(this).attr("data-id")),
						field_value;

						data[loki_basic_service_id]["fields"][field_id] = {
							value : "",
							bung : "just_in_case",
						};

						if(field_type == "list_checkbox"){
							field_value = [];
							var value;

							$(this).find(".control").each(function(){
								if($(this).prop("checked")){
									value = customParseInt($(this).val());
									field_value.push(value);
								}
							});

							data[loki_basic_service_id]["fields"][field_id].value = field_value;
						}
					});
				});
				attributes += (attributes == "") ? "" : "}";
				data[loki_basic_service_id]["attributes"] = attributes;
			});

			loader(true);
			$.post(
				"/techsup/applications/validate-and-create",
				{
					data : data,
				},
				function(response){
					loader(false);
					switch(response.status){
						case "error":
							showErrors(response.data);
							break;
						case "success":
							console.log("success");
							break;
						default:
					}
				},
				"json"
			);
		}

		/** Рисует все возникшие ошибки */
		function showErrors(data){
			var workspace,
				message,
				field;

			for(var loki_basic_service_id in data){
				workspace = $(".create-section[data-loki-basic-service-id='" + loki_basic_service_id + "']");

				if("errors" in data[loki_basic_service_id]){
					workspace.addClass("error");
					for(var key in data[loki_basic_service_id]["errors"]){
						message = data[loki_basic_service_id]["errors"][key] + "<br>";
						$(".create-section__error").append(message);
					}
				}

				if("fields" in data[loki_basic_service_id]){
					for(var field_id in data[loki_basic_service_id]["fields"]){
						if("error" in data[loki_basic_service_id]["fields"][field_id]){
							var field = workspace.find(".attribute-field[data-id='" + field_id + "']");
							field.addClass("error");
							field.children(".attribute-field__error").html(data[loki_basic_service_id]["fields"][field_id]["error"]);
						}
					}
				}
			}
		}

		/** Пробегает по выбранным отделам и показывает/скрывает настройку бригады */
		function brigadesNodAnalyze(){
			$(".create-section").each(function(){
				if($(this).find(".department.checked").attr('data-id') == '2'){
					$(this).find(".brigades-nod").removeClass("hide");
				}
				else{
					$(this).find(".brigades-nod").addClass("hide");
				}
			});
		}
	});
}(jQuery));