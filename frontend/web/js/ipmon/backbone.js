var count = 0;
function CheckNode(id) {
    $.ajax({
        type: "post",
        url: "/ipmon/backbone/checknode",
        data:{
            'node_id':id,
        },
        dataType: "json",
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
        },
        success: function(result){
            // console.log(result);
            $('#icon_' + result.node_id).removeClass("fa-pulse fa-spinner text-warning").addClass(result.icon);
            $('#uptime_' + result.node_id).html(result.uptime);
        }
    });
}
$(document).ready(function (e) {
    var nodeItem = $('.nodeItem');
    nodeItem.each(function(i,elem){
        CheckNode($(this).attr('id'));
        // console.log();
    });

    $('#backbonehosts-sw_model').on('change',function () {
        var model = $("#backbonehosts-sw_model option:selected").text();
        var checkbox = $('#backbonehosts-configured');
        if(model.indexOf("Eltex")!==0){
            if(checkbox.prop('checked')==='true'){
                checkbox.click();
            };
            checkbox.attr("disabled","disabled");
            console.log("It is not Eltex");
        }else{
            checkbox.removeAttr('disabled')
        };
    });

    $("#p0").on('pjax:send', function() {
        loader(true);
        $(".breadcrumb").addClass("busy");
    });

    $("#p0").on('pjax:complete', function() {
        loader(false);
        $(".breadcrumb").removeClass("busy");
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});

