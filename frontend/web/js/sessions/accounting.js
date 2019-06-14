'use strict';
var SessionModal = $('#session');

function disconnect(id,login){
    $('#confirm').modal('show');
    $("#confirm .confirm-content").html("Подтвердите завершение сессии для логина <b>" + login + "</b>");
    $('#confirm_btn').one('click', function(e){
        $('#confirm').modal('hide');
        console.log("confirmed ");
        $.ajax({
            type: "get",
            url: "/sessions/disconnect",
            data: {
                'login': login,
                'session_name': id
            },
            dataType: "json",
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            },
            success: function (json) {
                // InputFormState(json.full.auth_reason,json.full.auth_msg_descr);
                // if(json.full.auth_reason=="-1"){

                // }
                console.log(json);
                console.log("task created");
                loader(false);
                SessionModal.modal("hide");
                $('#notice').modal('show');
                $("#notice .notice-content").html("<div class='text-center'>" + json.notifyText + " (<i>" + json.notifyTS + "</i>) для логина <b>"  + login + "</b></div>");
                // if (collapse_flag){
                //     $('#SessionHistory').addClass('in');
                // }
            }
        });
    });
}

