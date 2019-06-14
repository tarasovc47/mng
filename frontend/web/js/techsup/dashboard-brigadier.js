(function($){
	$(document).ready(function(){
		var Dashboard = new DashboardBrigadier();

		Dashboard.init();
	});

	function DashboardBrigadier(){
		// Класс рабочего стола
		Applications.apply(this, arguments);
	}

	// Наследуется от класс заявок
	DashboardBrigadier.prototype = Object.create(Applications.prototype);
	DashboardBrigadier.prototype.constructor = DashboardBrigadier;

	DashboardBrigadier.prototype.init = function(){
		Applications.prototype.init.apply(this, arguments);

		this.socket.parseResponse = function(data, Applications){
			switch(data.status){
				case "error":
					Applications.message("danger", data.message);
					break;
				case "success":
					Applications.message("success", data.message);
					break;
				default:
					console.log(data);
			}
		}
	}

	DashboardBrigadier.prototype.init_handlers = function(){
		Applications.prototype.init_handlers.apply(this, arguments);
		this.init_new_applications_handler();
	}

	DashboardBrigadier.prototype.init_new_applications_handler = function(){
		var that = this;

		$(".application-stack .application-stack-id a").unbind();
		$(".application-stack").each(function(e){
			if($(this).find(".application.new").length > 0){
				var open_elem = $(this).find(".application-stack-id a"),
					app_stack = $(this);

				$(open_elem).one("click", function(e){
					var ids = [],
						new_apps = app_stack.find(".application.new");

					new_apps.each(function(){
						ids.push($(this).data("id"));
					});

					var data = {
			    		"controller" : "brigadier",
			    		"action" : "took",
			    		"post" : {
			    			"ids" : ids
			    		}
			    	};

			    	that.socket.letsSend(data);
				});
			}
		});
	}

	DashboardBrigadier.prototype.confirm_responsible = function(html){
		Applications.prototype.confirm_responsible.apply(this, arguments);

		var that = this;

		$("#A-dialog .a-dialog__footer .ok").click(function(e){
			loader(true);

			var responsible = $("#choice-of-responsible").val(),
				application_id = $("#A-dialog").attr("data-id"),
				attributes = "",
				comment = $("#A-dialog #comment").val();

			$("#A-dialog .attribute_chx:checked").each(function(){
				attributes += (attributes == "") ? "{" : ",";
				attributes += $(this).attr("data-id");
			});
			attributes += (attributes == "") ? "" : "}";

			var data = {
	    		"controller" : "brigadier",
	    		"action" : "setResponsible",
	    		"post" : {
	    			"responsible" : responsible,
					"application_id" : application_id,
					"attributes" : attributes,
					"comment" : comment,
	    		}
	    	};

	    	that.socket.letsSend(data);
		});
	}

	DashboardBrigadier.prototype.confirm_department = function(html){
		Applications.prototype.confirm_department.apply(this, arguments);

		var that = this;

		$("#A-dialog .a-dialog__footer .ok").click(function(e){
			loader(true);

			var department_id = $("#choice-of-department").val(),
				application_id = $("#A-dialog").attr("data-id"),
				attributes = "",
				comment = $("#A-dialog #comment").val();

			$("#A-dialog .attribute_chx:checked").each(function(){
				attributes += (attributes == "") ? "{" : ",";
				attributes += $(this).attr("data-id");
			});
			attributes += (attributes == "") ? "" : "}";

			var data = {
	    		"controller" : "brigadier",
	    		"action" : "setDepartment",
	    		"post" : {
	    			"department_id" : department_id,
					"application_id" : application_id,
					"attributes" : attributes,
					"comment" : comment,
	    		}
	    	};

	    	that.socket.letsSend(data);
		});
	}
}(jQuery));