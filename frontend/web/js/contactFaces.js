(function($){
	$(document).ready(function(){
		phonesMaskHandler();
		removePhoneButtonHandler();
		removeEmailButtonHandler();

		// маска телефона для создания контактных лиц
		function phonesMaskHandler(){
			$('.contactfaces-phones').each(function(){
				if (!($(this).hasClass('processed'))) {
					$(this).addClass('processed');
					$(this).mask("(999) 999-9999");
				}
				
			});
			
		}

		// подтверждение удаления контакта у укашки
		//$("a.manag_companies__contact_delete, a.manag_companies__branch_contact_delete").click(function(e){
		//	return confirm("Вы уверены, что хотите удалить контактное лицо?");
		//});

		// ВНИМАНИЕ дальше говнокод, писала под наркозом. Надо переделать.

		$('.contact-faces__add-another-phone').click(function(){
			var key = $('.contactfaces-phones').length;
			var html = '<div class="row">'
						+'<div class="col-md-4">'
						+'<div class="form-group field-contactfaces-phones--'+key+' required">'
						+'<div class="input-group">'
						+'<span class="input-group-addon">+7</span>'
						+'<input type="text" class="contactfaces-phones form-control" name="ContactFaces[phones][-'+key+']" data-phone-id="">'
						+'<span class="input-group-btn">'
						+'<button class="btn btn-default contact-faces__remove-phone-button" type="button"><i class="fa fa-times" aria-hidden="true"></i></button></span>'
						+'</div><div class="help-block"></div>'
						+'</div></div>'
						+'<div class="col-md-8"><div class="form-group field-contactfaces-phones_comments--'+key+'">'
						+'<input type="text" class="contactfaces-phones_comments form-control" name="ContactFaces[phones_comments][-'+key+']">'
						+'<div class="help-block"></div>'
						+'</div></div></div>';

			$(this).parents('.form-group').before(html);
			phonesMaskHandler();
			removePhoneButtonHandler();
		});
		$('.contact-faces__add-another-email').click(function(){
			var key = $('.contactfaces-emails').length;

			var html = '<div class="row">'
						+'<div class="col-md-4">'
						+'<div class="form-group field-contactfaces-emails--'+key+' required">'
						+'<div class="input-group">'
						+'<input type="text" class="contactfaces-emails form-control" name="ContactFaces[emails][-'+key+']" data-email-id="">'
						+'<span class="input-group-btn">'
						+'<button class="btn btn-default contact-faces__remove-email-button" type="button"><i class="fa fa-times" aria-hidden="true"></i></button></span>'
						+'</div><div class="help-block"></div>'
						+'</div></div>'
						+'<div class="col-md-8"><div class="form-group field-contactfaces-emails_comments--'+key+'">'
						+'<input type="text" class="contactfaces-emails_comments form-control" name="ContactFaces[emails_comments][-'+key+']">'
						+'<div class="help-block"></div>'
						+'</div></div></div>';

			$(this).parents('.form-group').before(html);
			removeEmailButtonHandler();
		});

		function removePhoneButtonHandler(){
			$('.contact-faces__remove-phone-button').each(function(){
				if (!($(this).hasClass('processed'))) {
					$(this).addClass('processed');

					$(this).click(function(){
						var index = $(this).parents('.row').index();

						var phone_id = $(this).parents('.input-group-btn').siblings('input').data('phone-id');
						if (index != 0) {
							$(this).parents('.row').remove();
						} else {
							$(this).parents('.input-group-btn').siblings('input').val('')
							.parents('.row').find('.contactfaces-phones_comments').val('');
						}
						if (phone_id != '' && phone_id > 0) {
							loader(true);
							$.get('/contact-faces/remove-phone/',
								{
									phone_id : phone_id,
								},
								function(data)
								{
									loader(false);
								},
								'json'
							)
						}
					});
				}
			});
		}
		function removeEmailButtonHandler(){
			$('.contact-faces__remove-email-button').each(function(){
				if (!($(this).hasClass('processed'))) {
					$(this).addClass('processed');

					$(this).click(function(){
						var index = $(this).parents('.row').index();
						var email_id = $(this).parents('.input-group-btn').siblings('input').data('email-id');

						if (index != 0) {
							$(this).parents('.row').remove();
						} else {
							$(this).parents('.input-group-btn').siblings('input').val('')
							.parents('.row').find('.contactfaces-emails_comments').val('');
						}
						if (email_id != '' && email_id > 0) {
							loader(true);
							$.get('/contact-faces/remove-email/',
								{
									email_id : email_id,
								},
								function(data)
								{
									loader(false);
								},
								'json'
							)
						}
					});
				}
			});
		}
	});
}(jQuery));