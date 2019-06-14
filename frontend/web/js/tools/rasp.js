function EditRPi(id) {
    $('#myModal').modal('show');
    var rpidata = RPiInfo(id);
    console.log(rpidata);
    $("#mod_user").html(rpidata.users);
    $("#mod_ip").text(rpidata.ip);
    $("#input_ip").val(rpidata.ip);
    $("#input_mac").val(rpidata.mac);
    $("#mod_mac").text(rpidata.mac);
    $("#mod_config").val(rpidata.config);
}
function ShowTerminals() {
    $.ajax({
        type: "post",
        url: "/tools/raspberry/ajax",
        data: {
            'action': 'show_terms',
        },
        dataType: "json",
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
        },
        success: function (json) {
            // console.log(json);
            $('#debug').text(json);
            $('#rpisList').html(json.html);
            loader(false);

        }
    });
}

function RPiInfo(id) {
    var data;
    $.ajax({
        type: "post",
        url: "/tools/raspberry/ajax",
        data: {
            'action': 'show_conf',
            'id': id,
        },
        async:false,
        dataType: "json",
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
        },
        success: function (json) {
            // console.log(json);
            $('#RpiInfo').html(json.html);
            loader(false);
            data = json;
        }
    });
    return data;
}




$(document).ready(function (e) {
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    loader(true);
    ShowTerminals();

    $("#refresh").on("click",function () {
        loader(true);
        ShowTerminals();
    });


    var rasp_form = $("#rasp_form");
    rasp_form.submit(function (e) {
        e.preventDefault();
        console.log($(this).serialize());
        var post = "&action=update&" + $(this).serialize();
        $.ajax({
            type: "post",
            url: "/tools/raspberry/ajax",
            data: post,
            // async:false,
            // dataType: "json",
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            },
            success: function (json) {
                console.log(json);
                // $('#RpiInfo').html(json.html);
                // loader(false);
                // data = json;
                $('#myModal').modal('hide');
                rasp_form.trigger('reset');
                ShowTerminals();
            }
        });
    })




});

