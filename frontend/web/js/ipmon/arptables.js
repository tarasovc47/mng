'use strict';
function LoadArps(router,net) {
    console.log(router, net);

    $.ajax({
        type: "post",
        url: "/ipmon/arptables/arps",
        data:{'net':net,'router':router },
        dataType:'json',
        // async:false,
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
        },
        success: function(result){
            $('#body' + net).html(result.html);
            $('#counts' + net).removeAttr('hidden');
            $('#count_free_' + net).html("Free: " + result.counts.free);
            $('#count_static_' + net).html("Static: " + result.counts.static);
            $('#count_dynamic_' + net).html("Dynamic: " + result.counts.dynamic);
            loader(false);
            $(".breadcrumb").removeClass("busy");
        }
    });
}

function LoadCurrentComment(router_id,ip,iface) {
    $('#CommentChange').modal('show');
    $("#ip").val(ip);
    console.log(ip);
    //
    $.ajax({
        type: "post",
        url: "/ipmon/arptables/comment",
        dataType:"json",
        data: {
            "request":0,
            "ip": ip,
            "router_id": router_id
        },
        before: function () {
            console.log('dd');
        },
        async:false,
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus,errorThrown);
        },
        success: function(result){
            console.log(result);
            $("#comment").val(result.comment);
            $("#rid").val(result.rid);
            $("#iface").val(iface);
            // console.log('sdsds');
        }
    });
    console.log(rid,ip);
}

function ArpStatus(status,ip,ss,inc) {
    var colapsedIn = $('.collapse.in');
    var iface = colapsedIn.attr('data-network');
    var router_ip = colapsedIn.attr('data-router');
    var icon = $("#icon_" + inc);
    icon.removeClass('fa-lock fa-unlock').addClass('fa-spin fa-cog');
    console.log(inc);

    $.ajax({
        type: "post",
        url: "/ipmon/arptables/status",
        data: {
            "status":status,
            "ip":ip,
            "iface":iface,
            "router_ip":router_ip
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
        },
        success: function(result){
            console.log(result);
            LoadArps(router_ip,iface);
            switch(status){
                case true:
                    icon.addClass('fa-lock text-danger').removeClass('fa-unlock fa-spin fa-cog');
                    break;

                case false:
                    icon.removeClass('fa-lock fa-spin fa-cog').addClass('fa-unlock text-danger');
                    break;
            }

            // ShowNetworks();
        }
    });
    console.log('make dynamic ');
    /*
    switch (status){
        case false:
            // loader(true);
            $.ajax({
                type: "post",
                url: "/ipmon/arptables/status",
                data: {
                    "status":status,
                    "ip":ip,
                    "iface":iface,
                    "router_id":router_id
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                },
                success: function(result){
                    console.log(result);
                    // $("icon_" + inc).removeClass('fa-lock').addClass('fa-unlock');
                    // GetArps(net,rid);
                    // ShowNetworks();
                }
            });
            console.log('make dynamic ');
            break;

        case true:
            loader(true);

            $.ajax({
                type: "post",
                url: "/ipmon/arps/ajax",
                data: {
                    "play":"Static",
                    "ip":ip,
                    "eth":eth,
                    "rid":rid
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                },
                success: function(result){
                    console.log(result);
                    $("icon_" + inc).addClass('fa-lock').removeClass('fa-unlock');
                    GetArps(net,rid);
                    // ShowNetworks();
                }
            });
            console.log('make static ');
            break;
    }*/
    // console.log(ip,eth);
    // ShowNetworks();
}




$(document).ready(function (e) {
    $('.tabtoggle').on('click',function () {
        loader(true);
        $(".breadcrumb").addClass("busy");
        var id = 't' + $(this).attr("id");
        console.log(id);
        $('#' + id).addClass('busy');
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

    $('.networks').on('hide.bs.collapse', function () {
        var colapsedIn = $('.collapse.in');
        var net = colapsedIn.attr('data-network');
        $('#counts' + net).attr('hidden','hidden');
        loader(false);
        $(".breadcrumb").removeClass("busy");
    });
    $('.networks').on('shown.bs.collapse', function () {
        $(".breadcrumb").addClass("busy");
        var colapsedIn = $('.collapse.in');
        var net = colapsedIn.attr('data-network');
        var router = colapsedIn.attr('data-router');
        console.log('collapsed out' + net);

        LoadArps(router,net);
    }).on('show.bs.collapse', function () {
        $('.list-group').html('<li class="list-group-item text-center" ><i class="fa fa-spin fa-spinner fa-pulse fa-fw fa-2x text-info"></i></li>');
    });

    var form = $("#CommentForm");
    form.submit(function (e) {
        e.preventDefault();

        var post = "&request=1&" + form.serialize();
        loader(true);
        console.log(form.serialize());
        $.ajax({
            type: "post",
            url: "/ipmon/arptables/comment",
            data: post,
            dataType: "json",
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            },
            success: function(result){
                console.log(result);
                LoadArps(result.router_id,result.iface);
                $('#CommentChange').modal('hide');
                loader(false);
            }
        });

    });
});
