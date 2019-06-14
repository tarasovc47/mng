/*
 *	Приведение переменной к целому числу десятичной 
 *	системы счисления (явно указан второй параметр)
*/
function customParseInt(element){
	return parseInt(element, 10);
}

/*
 *	Перевод строки в латинницу
*/
function translit(text){
	var space = '',
		transl = {
			'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'e', 'ж': 'zh', 
			'з': 'z', 'и': 'i', 'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
			'о': 'o', 'п': 'p', 'р': 'r','с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h',
			'ц': 'c', 'ч': 'ch', 'ш': 'sh', 'щ': 'sh','ъ': space, 'ы': 'y', 'ь': space, 'э': 'e', 'ю': 'yu', 'я': 'ya',
			' ': '_', '`': space, '~': space, '!': space, '@': space,
			'#': space, '$': space, '%': space, '^': space, '&': space, '*': space, 
			'(': space, ')': space,'-': space, '\=': space, '+': space, '[': space, 
			']': space, '\\': space, '|': space, '/': space,'.': space, ',': space,
			'{': space, '}': space, '\'': space, '"': space, ';': space, ':': space,
			'?': space, '<': space, '>': space, '№':space
		},
        result = '',
		curent_sim = '',
		text = text.toLowerCase();

	for(i = 0; i < text.length; i++) {
	    if(transl[text[i]] != undefined) {
	        if(curent_sim != transl[text[i]] || curent_sim != space){
	             result += transl[text[i]];
	             curent_sim = transl[text[i]];
	        }                                                                             
	    }
	    else{
	        result += text[i];
	        curent_sim = text[i];
	    }
	}

	return result;
}

/*
 *	Аналог PHP-шного $_GET['key'], только в виде функции
 *	Получает значение get параметра из адреса
*/
function $_GET(key) {
    var s = window.location.search;
    s = s.match(new RegExp(key + '=([^&=]+)'));
    return s ? s[1] : false;
}

/*
 *	Показать или скрыть индикатор загрузки
*/
function loader($state){
	var loader = document.getElementById("loader"),
		loader_content = document.getElementById("loader-content");
	loader.className = $state ? "show" : "";
	loader_content.className = $state ? "show" : "";
	return true;
}

/** Подтверждение ухода со страницы */
function Unloader(){
    var o = this;

    this.unload = function(evt){
        var message = "Вы уверены, что хотите покинуть страницу?";
        if (typeof evt == "undefined") {
            evt = window.event;
        }
        if (evt) {
            evt.returnValue = message;
        }
        return message;
    }
 
    this.resetUnload = function(){
        $(window).off('beforeunload', o.unload);
 
         setTimeout(function(){
            $(window).on('beforeunload', o.unload);
        }, 2000);
    }
 
    this.init = function(){
        $(window).on('beforeunload', o.unload);
 
        // $('a').on('click', function(){o.resetUnload});
        // $(document).on('submit', 'form', function(){o.resetUnload});
        /* $(document).on('keydown', function(event){
            if((event.ctrlKey && event.keyCode == 116) || event.keyCode == 116){
                o.resetUnload;
            }
        });*/
    }
    this.init();
}

$(document).ready(function(e){
	/** Нажатие кнопки "Отмена" в подвтерждающем окне. Снимает установленное событие с click кнопки success */
	$('#confirm .cancel').click(function(e){
    	$("#confirm .confirm-content").empty();
    	$('#confirm .btn-success').unbind('click');
    });
});