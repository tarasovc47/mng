(function($){
    $(document).ready(function(e){
    	$(".toggleLeftMenu").click(function(e){
    		e.preventDefault();
    		if($(".collapsing").length == 0){
    			$(this).toggleClass("opened");
	    		var opened = $(this).hasClass("opened"),
	    			collapse;

				$(".nav .has-list").each(function(){
					collapse = $(this).siblings("ul").hasClass("in");

					if((opened && !collapse) || (!opened && collapse)){
						$(this).click();
					}
				});
    		}
    	});

        $(".has-list").click(function(e){
            e.preventDefault();
        });


    });
}(jQuery));