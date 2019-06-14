function Applications(){
	this.socket;
}

Applications.prototype.init_attributes_short = function(){
	$(".application-short__last-attributes:not(.processed)").each(function(e){
		$(this).addClass("processed");

		var level = 1;
		$(this).children(".attribute").each(function(e){
			level = (level < customParseInt($(this).data("level"))) ? customParseInt($(this).data("level")) : level;
		});

		$(this).children(".attribute[data-level='" + level + "']").removeClass("hide");
		$(this).children(".attribute[data-level='" + (level - 1) + "']").removeClass("hide").addClass("without-arrow");
	});
}

Applications.prototype.init_history = function(){
	$(".application-history .caption:not(.processed)").each(function(e){
		$(this).addClass("processed");

		var container = $(this).parents(".application-history");

		$(this).click(function(e){
			if($(this).hasClass("pale")){
				container
					.find(".caption").toggleClass("pale");
				container
					.find(".application-history__element").toggleClass("hide");

				if($(this).hasClass("is-history")){
					container
						.find(".switch").removeClass("fa-toggle-on").addClass("fa-toggle-off");
				}

				if($(this).hasClass("is-comment")){
					container
						.find(".switch").removeClass("fa-toggle-off").addClass("fa-toggle-on");
				}
			}
		});
	});
}

Applications.prototype.init = function(){
	var that = this;

	this.socket = new WebSocket("ws://mng.t72.ru:8004/");

	this.socket.onopen = function(){
	    var sid = $("#Applications").data("sid"),
	    	data = {
	    		"controller" : "auth",
	    		"action" : "login",
	    		"post" : {
	    			"sid" : sid
	    		}
	    	};
	    this.letsSend(data);
	};

	this.socket.onmessage = function(e){
		var data = JSON.parse(e.data);
		if(data != ''){
			switch(data.status){
				case "update":
					that.update_applications(data.applications);
					break;
				case "render":
					that.render_applications(data.applications);
					break;
				case "responsible":
					that.confirm_responsible(data.data);
					break;
				case "department":
					that.confirm_department(data.data);
					break;
				case "refuse":
					that.confirm_refuse(data.data);
					break;
				case "complete":
					that.confirm_complete(data.data);
					break;
				case "dialog-error":
					that.dialog_error(data.messages);
					break;
				default:
					this.parseResponse(data, that);
			}
		}
	};

	this.socket.onclose = function(e){
		$("#socket-unavaliable").removeClass("hide");
	};

	this.socket.onerror = function(e){
		// that.message("danger", "Ошибка сокета");
	};

	this.socket.letsSend = function(data){
		data = JSON.stringify(data);
	    this.send(data);
	}

	this.socket.parseResponse = function(data, Applications){
		console.log(data);
	}

	this.init_handlers();
};

Applications.prototype.init_handlers = function(){
	this.init_attributes_short();
	this.init_history();
	this.init_set_responsible();
	this.init_set_department();
	this.init_refuse();
	this.init_complete();
}

Applications.prototype.update_applications = function(applications){
	var that = this,
		ids = {};

	for(var id in applications){
		if($('#A-dialog').attr("data-id") == id){
			$("#A-dialog .a-dialog__footer .cancel").click();
		}

		if(applications[id] == "remove"){
			this.remove_application(id);
		}

		if(applications[id] == "update"){
			var split = id.split("-");

			if($(".application-stack[data-id='" + split[0] + "']").length > 0){
				ids[id] = "partial";
			}
			else{
				ids[id] = "full";
			}
		}
	}

	if(!$.isEmptyObject(ids)){
		var data = {
    		"controller" : "viewer",
    		"action" : "render",
    		"post" : {
    			"ids" : ids
    		}
    	};

    	that.socket.letsSend(data);
	}
}

Applications.prototype.render_applications = function(applications){
	var that = this;

	for(var key in applications){
		var is_full = applications[key].is_full,
			stack_id = applications[key].stack_id;

		if(is_full){
			$("#Applications .applications-stacks")
				.append('<div class="application-stack" data-id="' + stack_id + '">' + applications[key].stack + '</div>');
		}
		else{
			var apps = applications[key].apps;

			for(var id in apps){
				if($("#Applications .application-container[data-id='" + id + "']").length < 1){
					$("#Applications .application-stack[data-id='" + stack_id + "'] .panel-body")
						.append("<div class='application-container' data-id='"  + id + "'></div>");
				}
				
				$("#Applications .application-container[data-id='" + id + "']").html(apps[id].full);
				$("#Applications .application-short[data-id='" + id + "']").html(apps[id].short);

				// Если с заявкой работают в данный момент
				if($("#confirm").hasClass("in") && $("#confirm").data("app") == id){
					$('#confirm .cancel').click();
				}
			}
		}
	}

	that.init_handlers();
}

Applications.prototype.remove_application = function(id){
	if($(".application-container[data-id='" + id + "']").siblings(".application-container").length > 0){
		$(".application[data-id='" + id + "']").fadeOut(400, function(){ $(this).remove(); });
		$(".application-short[data-id='" + id + "']").fadeOut(400, function(){ $(this).remove(); });
	}
	else{
		$(".application-container[data-id='" + id + "']").parents(".application-stack").fadeOut(400, function(){ $(this).remove(); });
	}
}

Applications.prototype.message = function(cls, message){
	loader(false);

	$("#applications-messages")
		.addClass("show")
		.append("<div class='alert alert-" + cls + "'>" + message + "</div>");

	$("#applications-messages .alert:not(.processed)").each(function(e){
		$(this).addClass("processed").fadeOut(4000, function(){ $(this).remove(); });

		setTimeout(function(){
			$(this).remove();

			if($("#applications-messages .alert").length == 0){
				$("#applications-messages").removeClass("show");
			}
		}, 4100);
	});
}

Applications.prototype.dialog_error = function(messages){
	loader(false);

	var html = '';
	for(var i in messages){
		html += "<div>" + messages[i] + "</div>";
	}

	$("#A-dialog .errors")
		.html(html)
		.removeClass("hide");
}

Applications.prototype.init_set_responsible = function(){
	var that = this;

	$('.application-actions .responsible:not(.processed)').each(function(){
		$(this)
			.addClass("processed")
			.click(function(e){
				loader(true);

				var id = $(this).parents(".application").attr("data-id");

				var data = {
		    		"controller" : "viewer",
		    		"action" : "responsible",
		    		"post" : {
		    			"application_id" : id
		    		}
		    	};
		    	
		    	that.socket.letsSend(data);
		    });
	});
}

Applications.prototype.confirm_responsible = function(data){
	$("#A-dialog .a-dialog__wrap").html(data.html);
	
	this.attributes_dialog_handler();
	this.comment_dialog_handler();
	this.dialog_cancel();

	$('#A-dialog')
		.attr("data-id", data.application_id)
		.modal('show');

	loader(false);
}

Applications.prototype.init_set_department = function(){
	var that = this;

	$('.application-actions .department:not(.processed)').each(function(){
		$(this)
			.addClass("processed")
			.click(function(e){
				loader(true);

				var id = $(this).parents(".application").attr("data-id");

				var data = {
		    		"controller" : "viewer",
		    		"action" : "department",
		    		"post" : {
		    			"application_id" : id
		    		}
		    	};
		    	
		    	that.socket.letsSend(data);
		    });
	});
}

Applications.prototype.confirm_department = function(data){
	$("#A-dialog .a-dialog__wrap").html(data.html);

	this.attributes_dialog_handler();
	this.comment_dialog_handler();
	this.dialog_cancel();
	this.department_dialog_handler();

	$('#A-dialog')
		.attr("data-id", data.application_id)
		.modal('show');

	loader(false);
}

Applications.prototype.init_refuse = function(){
	var that = this;

	$('.application-actions .refuse:not(.processed)').each(function(){
		$(this)
			.addClass("processed")
			.click(function(e){
				loader(true);

				var id = $(this).parents(".application").attr("data-id");

				var data = {
		    		"controller" : "viewer",
		    		"action" : "refuse",
		    		"post" : {
		    			"application_id" : id
		    		}
		    	};
		    	
		    	that.socket.letsSend(data);
		    });
	});
}

Applications.prototype.confirm_refuse = function(data){
	$("#A-dialog .a-dialog__wrap").html(data.html);

	this.dialog_cancel();

	$('#A-dialog')
		.attr("data-id", data.application_id)
		.modal('show');

	loader(false);
}

Applications.prototype.init_complete = function(){
	var that = this;

	$('.application-actions .complete:not(.processed)').each(function(){
		$(this)
			.addClass("processed")
			.click(function(e){
				loader(true);

				var id = $(this).parents(".application").attr("data-id");

				var data = {
		    		"controller" : "viewer",
		    		"action" : "complete",
		    		"post" : {
		    			"application_id" : id
		    		}
		    	};
		    	
		    	that.socket.letsSend(data);
		    });
	});
}

Applications.prototype.confirm_complete = function(data){
	$("#A-dialog .a-dialog__wrap").html(data.html);

	this.attributes_dialog_handler();
	this.comment_dialog_handler();
	this.dialog_cancel();

	$('#A-dialog')
		.attr("data-id", data.application_id)
		.modal('show');

	loader(false);
}

Applications.prototype.attributes_dialog_handler = function(){
	if($("#A-dialog .attributes").length > 0){
		$("#A-dialog .attribute_chx").change(function(e){
			var value = $(this).prop("checked"),
				attr_id = $(this).attr("data-id"),
				parent_all = $(this).parents(".attributes"),
				parent_this = $(this).parents(".checkbox[data-attr='" + attr_id + "']");
				
			if(value){
				parent_this.children(".attribute.child").addClass("show");
				// parent_this.children(".attribute-field").addClass("show");

				if(parent_this.hasClass("start")){
					parent_all.find(".attribute.start:not([data-attr='" + attr_id + "']) .attribute_chx").prop("disabled", true);
				}
			}
			else{
				parent_this.find(".attribute.child").removeClass("show");
				// parent_this.find(".attribute-field").removeClass("show");
				parent_this.find(".attribute_chx").prop("checked", false);

				if(parent_this.hasClass("start")){
					parent_all.find(".attribute.start .attribute_chx").prop("disabled", false);
				}
			}
		});
	}
}

Applications.prototype.comment_dialog_handler = function(){
	$("#A-dialog .show-comment").one('click', function(e){
		$(this).hide();
		$("#A-dialog .comment-form").removeClass("hide");
	});
}

Applications.prototype.dialog_cancel = function(){
	$("#A-dialog .a-dialog__footer .cancel").one('click', function(e){
		$("#A-dialog .a-dialog__wrap").empty();
		$('#A-dialog')
			.attr("data-id", "")
			.modal('hide');
	});
}

Applications.prototype.department_dialog_handler = function(){
	$("#A-dialog #choice-of-department").change(function(e){
		var value = $(this).val();

		if(value == '2'){
			$("#A-dialog .brigades-nod").removeClass("hide");
		}
		else{
			$("#A-dialog .brigades-nod").addClass("hide");
		}
	});
}