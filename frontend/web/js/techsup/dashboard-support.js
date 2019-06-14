(function($){
	$(document).ready(function(){
		var Dashboard = new DashboardSupport();

		Dashboard.init();
	});
	
	function DashboardSupport(){
		// Класс рабочего стола тех поддержки
		Applications.apply(this, arguments);
	}

	// Наследуется от класс заявок
	DashboardSupport.prototype = Object.create(Applications.prototype);
	DashboardSupport.prototype.constructor = DashboardSupport;

	DashboardSupport.prototype.init = function(){
		Applications.prototype.init.apply(this, arguments);

		var that = this;

		this.socket.parseResponse = function(data, Applications){
			switch(data.status){
				case "error":
					Applications.message("danger", data.message);
					break;
				case "success":
					Applications.message("success", data.message);
					break;
				case "handle":
					that.confirm_handle(data.data);
					break;
				default:
					console.log(data);
			}
		}
	}

	DashboardSupport.prototype.init_handlers = function(){
		Applications.prototype.init_handlers.apply(this, arguments);

		this.init_handle();
	}

	DashboardSupport.prototype.init_handle = function(){
		var that = this;

		$('.application-actions .handle:not(.processed)').each(function(){
			$(this)
				.addClass("processed")
				.click(function(e){
					loader(true);

					var id = $(this).parents(".application").attr("data-id");

					var data = {
			    		"controller" : "viewer",
			    		"action" : "handle",
			    		"post" : {
			    			"application_id" : id
			    		}
			    	};
			    	
			    	that.socket.letsSend(data);
			    });
		});
	}

	DashboardSupport.prototype.confirm_handle = function(data){
		var that = this;

		$("#A-dialog .a-dialog__wrap").html(data.html);

		this.dialog_cancel();
		this.comment_dialog_handler();

		$('#A-dialog')
			.attr("data-id", data.application_id)
			.modal('show');

		$("#A-dialog .a-dialog__footer .ok").click(function(e){
			loader(true);

			var action = $("#A-dialog #choice-of-action").val(),
				application_id = $("#A-dialog").attr("data-id"),
				properties = "",
				comment = $("#A-dialog #comment").val();

			if(action == ""){
				var messages = [ "Необходимо выбрать действие." ];
				that.dialog_error(messages);
				return;
			}

			$("#A-dialog .property_chx:checked").each(function(){
				properties += (properties == "") ? "{" : ",";
				properties += $(this).attr("data-id");
			});
			properties += (properties == "") ? "" : "}";

			var data = {
	    		"controller" : "support",
	    		"action" : action,
	    		"post" : {
					"application_id" : application_id,
					"properties" : properties,
					"comment" : comment,
	    		}
	    	};

	    	that.socket.letsSend(data);
		});

		loader(false);
	}
}(jQuery));