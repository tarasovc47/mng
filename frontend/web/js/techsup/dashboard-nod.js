(function($){
	$(document).ready(function(){
		var Dashboard = new DashboardNod();

		Dashboard.init();
	});

	function DashboardNod(){
		// Класс рабочего стола
		Applications.apply(this, arguments);
	}

	// Наследуется от класс заявок
	DashboardNod.prototype = Object.create(Applications.prototype);
	DashboardNod.prototype.constructor = DashboardNod;

	DashboardNod.prototype.init = function(){
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

	DashboardNod.prototype.init_handlers = function(){
		Applications.prototype.init_handlers.apply(this, arguments);
	}

	DashboardNod.prototype.confirm_refuse = function(html){
		Applications.prototype.confirm_refuse.apply(this, arguments);

		var that = this;

		$("#A-dialog .a-dialog__footer .ok").click(function(e){
			loader(true);

			var application_id = $("#A-dialog").attr("data-id"),
				comment = $("#A-dialog #comment").val();

			var data = {
	    		"controller" : "nod",
	    		"action" : "refuse",
	    		"post" : {
					"application_id" : application_id,
					"comment" : comment,
	    		}
	    	};

	    	that.socket.letsSend(data);
		});
	}

	DashboardNod.prototype.confirm_complete = function(html){
		Applications.prototype.confirm_complete.apply(this, arguments);

		var that = this;

		$("#A-dialog .a-dialog__footer .ok").click(function(e){
			loader(true);

			var application_id = $("#A-dialog").attr("data-id"),
				status_id = $("#choice-of-status").val(),
				attributes = "",
				comment = $("#A-dialog #comment").val();

			$("#A-dialog .attribute_chx:checked").each(function(){
				attributes += (attributes == "") ? "{" : ",";
				attributes += $(this).attr("data-id");
			});
			attributes += (attributes == "") ? "" : "}";

			var data = {
	    		"controller" : "nod",
	    		"action" : "complete",
	    		"post" : {
					"application_id" : application_id,
					"status_id" : status_id,
					"attributes" : attributes,
					"comment" : comment,
	    		}
	    	};

	    	that.socket.letsSend(data);
		});
	}
}(jQuery));