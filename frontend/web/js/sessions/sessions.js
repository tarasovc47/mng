'use strict';
String.prototype.replaceAll = function(search, replace){
    return this.split(search).join(replace);
};
function ConvertMAC(mac){   // to abcd.efgh.klmn  - format
    function MakeRightFormat(tmp) {
        return tmp.substring(0, 4) + "." + tmp.substring(4,8) + "." + tmp.substring(8,12);
    }
    if(mac.length<12){
        return mac;
    }

    if(mac.indexOf('.')+1) {
        var MAC = mac;
    }else{
        if (mac.indexOf(':')+1){
            MAC = MakeRightFormat(mac.replaceAll(':',''));

        } else {
            if (mac.indexOf('-')+1) {
                MAC = MakeRightFormat(mac.replaceAll('-',''));
                // MAC = mac;
            }else{
                MAC = MakeRightFormat(mac);
                // MAC = implode(":", explode("-", mac));
            }

            // MAC = implode(":", explode(".", mac));
            // MAC = substr(MAC, 0, 2).":"
            //     .substr(MAC, 2, 5).":"
            //     .substr(MAC, 7, 5).":"
            //     .substr(MAC, 12, 2);
        }
    }
    return MAC;
}

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

var auth = false;
var subscriber_auth_log_data = [];
var i = 0;
var k = 0;

var started = [];  //массив сообщении о начале сессиии
var stoped = [];    //массив сообщений о завершении сессии

function MacWODots(mac) {
    return mac.replaceAll('.','');
}

function SubscribersAuthLogTable(message){
    var html = '';
    for(var i =0;i<message.length;i++){
        var first='';
        switch (message[i].auth_res){
            case "OK":
                // var auth_color = 'success';
                var auth_result = true;
                var status = 'success';
                break;

            case "FAIL":
                // auth_color = 'danger';
                auth_result = false;
                status = 'danger';
                break;
        }
        if(i===0){
            first="active";
        }else{
            if(auth_result){
                first="success";
            }
        }
        var ts = new Date(message[i].ts).toLocaleString();
        html += "<tr class='" + first + " '>";
        if(auth_result){
            html += "<td><i class='fa fa-check text-success'></i></td>";
        }else{
            html += "<td><i class='fa fa-ban text-danger'></i></td>";
        }
        html += "<td>" + message[i].login + "<br>" + '<font size="2" color="gray">' + MacWODots(message[i].macaddr) + "</font></td>";
        html += "<td>" + ts + "</td>";
        html += "<td>" + message[i].circuit_id + "</td>";
        html += "<td>";
        if(auth_result){
            html += "";
        }else{
            html += "<span class='text-danger'>" + message[i].authwhy + "</span>";
        }
        // html += AuthMessages[i].auth_res + ": ";
        // html += "&nbsp;";
        html += "</td>";
        html += "<td class='visible-lg'>" + NasList[message[i].nas].descript + "<br><span class='text-muted'>" + NasList[message[i].nas].ipaddress + "</span>" + "</td>";
        html += "</tr>";
    }
    return html;
}
var NasList = [];

function FirstRow(row,i){
    if(i==0){
        var rrow = "<b>" + row + "</b>";
    }else{
        rrow = row;
    }
    return rrow;
}

function StopTable(message,max_length) {
    if(stoped.unshift(message)===max_length){
        stoped.pop();
    }
    var html = "";
    for(var i = 0; i<stoped.length;i++){
        var ts = new Date(stoped[i].ts).toLocaleString();
        html += "<tr class='warning'>";
        html += "<td>";
        html += FirstRow(stoped[i].login,i);
        html += "</td>";
        html += "<td>";
        html += FirstRow(ts,i);
        html += "</td>";
        html += "<td>";
        html += FirstRow(stoped[i].mac,i);
        html += "</td>";
        html += "</tr>";
    }
    return html;
}

function StartedTable(message,max_length) {
    if(started.unshift(message)===max_length){
        started.pop();
    }
// <a href="/sessions/accounting" title="Добавить в избранное" data-toggle="tooltip" data-placement="right" data-method="POST" data-params="{&quot;id&quot;:64}"><i class="fa fa-star"></i></a>
    var html = "";
    for(var i = 0; i<started.length;i++){
        var ts = new Date(started[i].ts).toLocaleString();
        var data = [];
        data['login'] = started[i].login;
        console.log(data);
        html += "<tr class='success'>";
        html += "<td>";
        html += '<a href="#" onclick="LoadAccounting(\'' + started[i].login + '\',\'\',\'\',\'\');">';
        html += FirstRow(started[i].login,i);
        html += "</a></td>";
        html += "<td>";
        html += FirstRow(ts,i);
        html += "</td>";
        html += "<td>";
        html += FirstRow(started[i].mac,i);
        html += "</td>";
        html += "</tr>";
    }
    return html;
}

function LoadAccounting(login,macaddr,ipv4,ipv6) {
    loader(true);
    // console.log(login,macaddr,ipv4,ipv6);
    var data = {};
    data.login = login;
    data.macaddr = macaddr;
    data.ipv4 = ipv4;
    data.ipv6 = ipv6;
    console.log(data);
    $.ajax({
        type: "post",
        url: "/sessions/accounting",
        data: data,
        // dataType: "json",
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
        },
        success: function (json) {
            $('#AccountingBody').html(json);
            loader(false);
        }
    });
    var title = '';
    if((login!==undefined)||(login!=='')){
        title = login;
    }else{
        if((macaddr!==undefined)||(macaddr!=='')){
            title = macaddr;
        }else{
            if((ipv4!==undefined)||(ipv4!=='')){
                title = ipv4;
            }else{
                title = ipv6;
            }
        }
    }

    $('#modalTitle').html(title);
    $('#accountingModal').modal('show');
}

function LoadHistory(data) {
    $.ajax({
        type: "post",
        url: "/sessions/archive",
        data: data,
        // dataType: "json",
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
        },
        success: function (json) {
            // console.log(json);
            $('#ArchiveCard').html(json);
            loader(false);
        }
    });
}

(function($){
    $.fn.serializeObject = function(){
        var self = this,
            json = {},
            push_counters = {},
            patterns = {
                "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
                "push":     /^$/,
                "fixed":    /^\d+$/,
                "named":    /^[a-zA-Z0-9_]+$/
            };


        this.build = function(base, key, value){
            base[key] = value;
            return base;
        };

        this.push_counter = function(key){
            if(push_counters[key] === undefined){
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };

        $.each($(this).serializeArray(), function(){

            // skip invalid keys
            if(!patterns.validate.test(this.name)){
                return;
            }

            var k,
                keys = this.name.match(patterns.key),
                merge = this.value,
                reverse_key = this.name;

            while((k = keys.pop()) !== undefined){

                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

                // push
                if(k.match(patterns.push)){
                    merge = self.build([], self.push_counter(reverse_key), merge);
                }

                // fixed
                else if(k.match(patterns.fixed)){
                    merge = self.build([], k, merge);
                }

                // named
                else if(k.match(patterns.named)){
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
        });

        return json;
    };
})(jQuery);

$(document).ready(function (e) {

    var accounting_form = $('#accounting_form');
    var archive_form = $('#archive_form');

    // archive_form.on('beforeSubmit',function (e) {
    //     e.preventDefault();
    // });
    // archive_form.on('afterSubmit',function (e) {
    //     e.preventDefault();
    // });
    // archive_form.on('pjax:start',function (e) {
    //     e.preventDefault();
    //     console.log('test');
    // });
    // archive_form.submit(function (e) {
    //     e.preventDefault();
    //     if(($('#archive-login').val()==='')&&($('#archive-macaddr').val()==='')&&($('#archive-ipv4').val()==='')&&($('#archive-ipv6').val()==='')){
    //         $('.oneof .help-block').text('Заполните хотя бы одно из полей');
    //         $('.oneof').addClass('has-error').removeClass('has-success');
    //     }else{
    //         $('.oneof .help-block').empty();
    //         $('.oneof').removeClass('has-error').addClass('has-success');
    //         LoadHistory($(this).serializeObject());
    //     }
    // });



    $("#radiusmain-macaddr").on('input',function () {
        var mac = $(this).val();
        if(mac.length===12) {
            if(mac.indexOf('.')+1){
                // console.log("MAC with dot")
            }else{
                $(this).val(mac.match(/.{2}/g).join(':'));
            }
            // var arr = mac.match(/\w{2}/);
        }
    });


    accounting_form.submit(function (e) {
       e.preventDefault();
       var tmp = $(this).serializeObject();
       var data = tmp['RadiusMain'];
       LoadAccounting(data.login,data.macaddr,data.ipv4,data.ipv6);
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $(function () {
       for(var nas in naslist){
           NasList[nas] = naslist[nas];
       }
    });
    // console.log('yo mtf');
    var subscriber_auth_filter = $('#subscriber_auth_filter');
    var max_subscriber_auth_list_length = $('#max_subscriber_auth_list_length');
    var status = $('#status');
    var pause = $('#pause');
    var pauseIcon = $('#pauseIcon');
    var subscriber_auth_list = $('#subscriber_auth_list');
    var accounting_start = $('#accounting_start');
    var accounting_stop = $('#accounting_stop');

    $('.subscriber_auth_filter').on('change',function () {
        subscriber_auth_filter.submit();
    });
    $('#subscriber_auth_filter_reset').on('click',function () {
       subscriber_auth_filter.trigger('reset').submit();

    });

    // if user is running mozilla then use it's built-in WebSocket
    window.WebSocket = window.WebSocket || window.MozWebSocket;

    var paused = false;
    pause.on('click',function (e) {
        if(pauseIcon.hasClass("fa-pause")){
            paused = true;
            pauseIcon.removeClass('fa-pause').addClass('fa-play');
        }else{
            paused=false;
            pauseIcon.removeClass('fa-play').addClass('fa-pause');
        }
    });

    // if browser doesn't support WebSocket, just show some notification and exit
    if (!window.WebSocket) {
        content.html($('<p>', { text: 'Sorry, but your browser doesn\'t '
        + 'support WebSockets.'} ));
        input.hide();
        $('span').hide();
        return;
    }

    subscriber_auth_filter.submit(function (ev) {
        ev.preventDefault();
        var tmp = $(this).serializeArray();
        var filterData = {};
        for(var i in tmp){
            if(tmp[i].name!=='_csrf-frontend'){
                filterData[((tmp[i].name).replace(/RadiusMain\[/g,'')).replace(/\]/g,'')] = tmp[i].value;
            }
            // console.log();
        }
        /*login:"4564"
         macaddr:""
         nas:"6"
         status:""*/

        filterData.macaddr = ConvertMAC(filterData.macaddr);
        // console.log($(this).serializeObject());
        // console.log(filterData);
        var sendingData = {
            type: "message",
            username: userinfo.login,
            userid: userinfo.id,
            user_type: "user",
            message_type:'filterset',
            filter: filterData
        };
        console.log(sendingData);
        connection.send( JSON.stringify(sendingData));
        // console.log( $(this).serializeObject());
    });

    // if browser doesn't support WebSocket, just show some notification and exit
    if (!window.WebSocket) {
        content.html($('<p>', { text: 'Sorry, but your browser doesn\'t '
        + 'support WebSockets.'} ));
        input.hide();
        $('span').hide();
        return;
    }

    var fails = {
        "Bad password":"Ошибка в пароле",
        "No such user":"Логин не найден",
        "null":"&nbsp;"
    }
    var input = $('#input');
    var connection = new WebSocket('ws://mng.t72.ru:1337');
    connection.onopen = function () {
        // first we want users to enter their names
        input.removeAttr('disabled');

        connection.send(JSON.stringify({
            type: "authorize",
            username: userinfo.sid,
            userid: userinfo.id,
            user_type: "user"
        }));
        //Написать обработку ответа об авторизации
        // status.text('Choose name:');
    };
    connection.onerror = function (error) {
        // just in there were some problems with conenction...
        content.html($('<p>', { text: 'Sorry, but there\'s some problem with your '
        + 'connection or the server is down.' } ));
    };

    connection.onmessage = function (message) {
        // try to parse JSON message. Because we know that the server always returns
        // JSON this should work without any problem but we should make sure that
        // the massage is not chunked or otherwise damaged.
        var json = JSON.parse(message.data);
        // console.log(json);
        if(started.length>0){
            $('#startTable').removeClass("hidden");
        }
        // else{
        //     $('#startTable').addClass("hidden");
        // }

        if(stoped.length>0){
            $('#stopTable').removeClass("hidden");
        }

        switch (json.type) {
            //авторизация
            case "authorize":
                status.removeClass('label-default');
                if (json.success === true) {
                    auth = true;
                    status.html("<i class='fa fa-check'></i> соединен ").addClass(' label-success');
                } else {
                    console.log("Authorization failed!");
                    connection.close();
                    status.html("<i class='fa fa-ban'></i>Auth fail!").addClass(' label-danger');
                }
                break;

            case 'subscriber_auth':
                if(auth){
                    subscriber_auth_log_data.unshift(json.data);
                    if(subscriber_auth_log_data.length>=max_subscriber_auth_list_length.val()){
                        while(subscriber_auth_log_data.length>=max_subscriber_auth_list_length.val()){
                            subscriber_auth_log_data.pop();
                        }
                    };
                    // console.log(subscriber_auth_log_data);
                    if(!paused) {
                        subscriber_auth_list.html(SubscribersAuthLogTable(subscriber_auth_log_data));
                    }
                }else{
                    console.log("undefined client");
                }
                break;


            case 'accounting':
                if(auth){
                    switch (json.data.status){
                        case "start":
                            accounting_start.html(StartedTable(json.data,12));
                            if(!paused){
                            }
                            // console.log("Сессия поднялась");
                            // console.log("Сессия живая");
                            break;

                        case "stop":
                            accounting_stop.html(StopTable(json.data,12));
                            if(!paused) {
                            }
                            // console.log("Сессия стопнулась");
                            break;

                        // case "alive":
                        //     console.log("Сессия живая");
                        //     break;
                    }
                    // NewEvent(json.data);
                    console.log(json);
                }else{
                    console.log("undefined client");
                    connection.close();
                }
                break;
        }
    };
});