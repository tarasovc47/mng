"use strict";

var debug = require('debug')('server');
var debug_users = require('debug')('ws_user');
// создаем сервер
var WebSocketServer = require('ws').Server,
    wss = new WebSocketServer({port: 1337});

//написать функцию проверки клиента по базе и по списку серверов

var mng_config = {
    "host":"10.60.248.7",
    "db":"mng_main",
    "userpass":"monitor:Kernelp@n1c"
};



var pgp_mng = require("pg-promise")(/*options*/);
var mng_ConnectStr = "postgres://" + mng_config.userpass + "@" + mng_config.host + ":5432/" + mng_config.db;
debug(mng_ConnectStr);
var db_mng = pgp_mng(mng_ConnectStr) ;


var servers_peers = [];
var peers = {};
var servers_lpeers = [];
var lpeers = [];
var filter = {};
var DefaultFilter = {
    login:"",
    login_strong: false,
    macaddr:"",
    macaddr_strong: false,
    nas: "",
    status:"" // NA - без фильтра по типу статуса авторизации,  "OK" - по ОК, "PAS" - по FAIL (bad pass), "MAC" - по FAIL (bad mac), "NOU" - по FAIL (no such user)

    // "strong":false,
    // "value":'',
    // "type": "login", // login - по логину,  macaddr - по МАКу
    // "auth_res": 'N/A', // NA - без фильтра по типу статуса авторизации,  "OK" - по ОК, "PAS" - по FAIL (bad pass), "MAC" - по FAIL (bad mac), "NOU" - по FAIL (no such user)
    // "nas":-1
};

// соединение с БД

// подсоединяемся к БД

// функция отправки сообщения всем
// Отправка сообщения всем участникам чата
// Для работы этой функции ссылки на соединения с каждым участником лежат в массиве peers
function broadcast (by, message) {

    // запишем в переменную, чтоб не расходилось время
    var time = new Date().getTime();

    // отправляем по каждому соединению
    peers.forEach (function (ws) {
        ws.send (JSON.stringify ({
            type: 'message',
            message: message,
            from: by,
            time: time
        }));
    });

    // сохраняем сообщение в истории
    // chatDB.insert ({message: message, from: by, time: time}, {w:1}, function (err) {
    //     if (err) {throw err}
    // });
}


var statuses = {
    ok:"OK",
    bp:"pass",
    nu:"user",
    bm:"MAC"
    };

// убрать из массива элемент по его значению
Array.prototype.exterminate = function (value) {
    this.splice(this.indexOf(value), 1);
};

function existUser (user) {
    return new Promise(function (resolve, reject) {
        debug(user);
        db_mng.connect({direct: true})
            .then(function (sco) {
                // sco.query("SELECT * from cas_user where login='" + user + "';") //простой запрос к БД
                sco.query("SELECT * from sessions where sid='" + user + "';") //простой запрос к БД
                    .then(function (data) {
                        // debug(data);
                        if(data.length>0){
                            // debug(data.length);
                            // registered=true;
                            resolve();
                        }else{
                            // registered=false;
                            reject();
                        }
                    })
            })
    });
    // userListDB.find({login: user}).toArray(function (error, list) {
    //     callback (list.length !== 0);
    // });
}



function CheckToStong(origin,input,stronger) {
    var send = false;
    if(stronger){
        if(origin===input) {
            send = true;
        }
    }else{
        if((origin).indexOf(input) + 1) {
            send = true;
        }
    }
    return send;
}



// при новом соединении
wss.on('connection', function (ws) {
    console.log('wss on');

    // проинициализируем переменные
    var login = '';
    var registered = false;
    debug_users(lpeers);
    // при входящем сообщении
    debug('[][] connection established');
    ws.on('message', function (message) {
        // получаем событие в пригодном виде
        debug('[][] message recieved');
        var event = JSON.parse(message);

        if(event.type === 'authorize'){
            debug('[][] authorization');
            switch (event.user_type){
                case "server":
                    debug('[][] server');
                    if(servers_lpeers.indexOf(event.username)!==-1){
                        debug("Server " + event.username  + " already connected...");
                        servers_peers.exterminate(ws);
                        servers_lpeers.exterminate(login);
                    }

                    // добавим самого человека в список людей онлайн
                    servers_lpeers.push (event.username);

                    // добавим ссылку на сокет в список соединений
                    servers_peers.push (ws);
                    debug("Server " + event.username  + " connected");
                    registered = true;
                    // подготовка ответного события
                    var returning = {type:'authorize', success: true};

                    // ну и, наконец, отправим ответ
                    ws.send (JSON.stringify(returning));

                    ws.on ('close', function () {
                        debug("Server " + event.username  + " close connection");
                        servers_peers.exterminate(ws);
                        servers_lpeers.exterminate(login);
                    });
                    break;

                case "user":
                    debug('[][] user');
                    // if(lpeers.indexOf(event.username)!==-1){
                    //     debug("User " + event.username  + " already connected... Closing old connection and start new...");
                    //     peers.exterminate(ws);
                    //     lpeers.exterminate(login);
                    // }

                    // добавим самого человека в список людей онлайн
                    lpeers.push(event.username); //sid сессиии

                    // добавим ссылку на сокет в список соединений
                    // peers.push = ws;
                    peers[event.userid] = ws;
                    filter[event.userid]=DefaultFilter;


                    // проверяем, есть ли такой пользователь
                    existUser(event.username)
                        .then(function () {
                            debug_users("User " + event.userid  + " connected...");
                            // подготовка ответного события
                            returning = {type:'authorize', success: true};
                            registered = true;
                            // ну и, наконец, отправим ответ
                        })
                        .catch(function (err) {
                            console.log(event);
                            debug_users("User " + event.userid  + " not found. Access restricted...");
                            // подготовка ответного события
                            returning = {type:'authorize', success: false};
                            registered = false;

                            // ну и, наконец, отправим ответ
                        })
                        .then(function () {
                            debug(JSON.stringify(returning));
                            ws.send (JSON.stringify(returning));
                        });


                    ws.on ('close', function () {
                        // peers.exterminate(ws);
                        delete peers[event.userid];
                        delete filter[event.userid];
                        lpeers.exterminate(login);
                    });
                    // registered = true;
                    // debug("User " + event.username  + " connected...");

                    // подготовка ответного события
                    // returning = {type:'authorize', success: true};
                    // ну и, наконец, отправим ответ
                    // ws.send (JSON.stringify(returning));

                    // ws.on ('close', function () {
                    //     peers.exterminate(ws);
                    //     lpeers.exterminate(login);
                    // });

                    break;

                default:
                    debug("Type '" + event.user_type  + "' undefined");
                    returning = {type:'authorize', success: false};
                    ws.send (JSON.stringify(returning));
                    break;
            }
        }else{
            debug_users("key 1");
            if(registered){
                switch (event.user_type){
                    case "server":
                        debug('[][] Message from server');
                        //сообщеине всем подключенным юзерам
                        debug(message);

                        for(var user_id in peers){
                            switch (event.type){
                                case "accounting":
                                    debug('[][] Accounting');
                                    peers[user_id].send(message);
                                    break;

                                case "subscriber_auth":
                                    debug('[][] Subscriber log');
                                    // {
                                    //     "login": filter.login,
                                    //     "login_strong":
                                    //     "macaddr":
                                    //     "macaddr_strong":
                                    //     "nas":
                                    //     "status":
                                    // }

                                    var filter_data = filter[user_id];
                                    debug('[][] Subscriber log %s', filter_data);
                                    var data = event.data;
                                    var send = false;
                                    if(//without filters
                                        (
                                            (
                                                (filter_data.login==='')
                                                ||
                                                (filter_data.login===undefined)
                                            )
                                            &&
                                            (
                                                (filter_data.macaddr==='')
                                                ||
                                                (filter_data.macaddr===undefined)
                                            )
                                            &&
                                            (
                                                (filter_data.nas==='')
                                            )
                                            &&
                                            (
                                                (filter_data.status==='')
                                            )
                                        )
                                        ||
                                        (filter_data===undefined)
                                    ){
                                        peers[user_id].send(message);//вывод сообщения
                                    }else{
                                        var logic_macaddr = true;
                                        var logic_login = true;
                                        var logic_nas = true;
                                        var logic_status = true;


                                        if(filter_data.status!==''){
                                            debug_users("Status: %s, Filter: %s",data.authwhy,statuses[filter_data.status]);
                                            if(data.auth_res===statuses[filter_data.status]){
                                                logic_status = true;
                                            }else{
                                                logic_status = false;
                                                if(data.authwhy!==null) {
                                                    if ((data.authwhy).indexOf(statuses[filter_data.status]) + 1) {
                                                        logic_status = true;
                                                    }else{
                                                        logic_status = false
                                                    }
                                                }
                                            }
                                        }

                                        if(filter_data.nas!==''){
                                            logic_nas=true;
                                        }

                                        if(filter_data.login!==''){
                                            logic_login=CheckToStong(data.login,filter_data.login,filter_data.login_strong);
                                                //peers[user_id].send(message);
                                        }

                                        if(filter_data.macaddr!==''){
                                            logic_macaddr=CheckToStong(data.macaddr,filter_data.macaddr,filter_data.macaddr_strong);
                                                //peers[user_id].send(message);
                                        }

                                        if(logic_login && logic_macaddr && logic_status){
                                            peers[user_id].send(message);
                                        }
                                    }

                                    /*
                                    if((filter_data.value==='')||(filter_data.value===undefined)||(filter_data===undefined)){
                                        peers[user_id].send(message);

                                        // var tmp = JSON.parse(ws);
                                        // tmp =  JSON.parse(tmp.user_name);
                                        // debug_users(tmp);
                                    }else{
                                        var data = event.data;
                                        var filterBy = filter_data.type;
                                        var value = filter_data.value;
                                        debug_users("DATA: %s",data);

                                        if (filter_data.strong){
                                            if(data[filterBy] === value){   ///Строгий фильтр
                                                peers[user_id].send(message);
                                                debug_users("User %s set filter = %s",event.userid,value);
                                            }
                                        }else{
                                            if((data[filterBy]).indexOf(value) + 1) {
                                                peers[user_id].send(message);
                                            }
                                            //Нестрогий фильтр
                                        }
                                    }*/
                                    break;
                            }


                            //{
                            // "strong":false,
                            // "value":'',
                            // "type": 0, //0 - по логину,  1 - по МАКу
                            // "auth_res": 'N/A' // NA - без фильтра по типу статуса авторизации,  "OK" - по ОК, "PAS" - по FAIL (bad pass), "MAC" - по FAIL (bad mac), "NOU" - по FAIL (no such user)
                            // };

                        }
                        /*peers.forEach(function (ws) {
                           //применять фильтр здесь
                           // debug_users(filter[ws]);
                            if((filter[event.userid]==='')||(filter[event.userid]===undefined)){
                                ws.send(message);
                                // var tmp = JSON.parse(ws);
                                // tmp =  JSON.parse(tmp.user_name);
                                // debug_users(tmp);
                            }else{
                                var FilteredData = event.data;
                                if(FilteredData.login === filter[event.userid]){   ///ФИЛЬТР разграничить по пользователям, должно фильтроваться на сервере.
                                    ws.send(message);
                                }
                            }
                        });*/
                        // broadcast('server',message);
                        break;

                    case "user":
                           // debug(message);
                        // {"type":"message","message_type":"filterset","username":31,"user_type":"user","filter":"test"}
                        debug_users(event.userid);

                        if(event.message_type==='filterset'){  //заначка
                            var RecievedFilter = event.filter;
                            if(RecievedFilter.login_strong===undefined){ RecievedFilter.login_strong=false }
                            if(RecievedFilter.login_strong==="true"){ RecievedFilter.login_strong=true }
                            if(RecievedFilter.macaddr_strong==="true"){ RecievedFilter.macaddr_strong=true }
                            if(RecievedFilter.macaddr_strong===undefined){ RecievedFilter.macaddr_strong=false }
                            // {
                            //     "login": filter.login,
                            //     "login_strong":
                            //     "macaddr":
                            //     "macaddr_strong":
                            //     "nas":
                            //     "status":
                            // }
                            filter[event.userid]=RecievedFilter;
                            debug_users("User %s sent filter = %s",event.username,JSON.stringify(RecievedFilter));
                        }
                        break;
                }
            }
        }

        // если человек хочет авторизироваться, проверим его данные
        /*if (event.type === 'authorize') {
         // проверяем данные
         checkUser(event.user, event.password, function (success) {
         // чтоб было видно в другой области видимости
         registered = success;

         // подготовка ответного события
         var returning = {type:'authorize', success: success};

         // если успех, то
         if (success) {
         // добавим к ответному событию список людей онлайн
         returning.online = lpeers;

         // добавим самого человека в список людей онлайн
         lpeers.push (event.user);

         // добавим ссылку на сокет в список соединений
         peers.push (ws);

         // чтобы было видно в другой области видимости
         login = event.user;

         //  если человек вышел
         ws.on ('close', function () {
         peers.exterminate(ws);
         lpeers.exterminate(login);
         });
         }

         // ну и, наконец, отправим ответ
         ws.send (JSON.stringify(returning));

         // отправим старые сообщения новому участнику
         if (success) {
         sendNewMessages(ws);
         }
         });
         } else {
         // если человек не авторизирован, то игнорим его
         if (1) {
         // проверяем тип события
         switch (event.type) {
         // если просто сообщение
         case 'message':
         // рассылаем его всем
         broadcast (login, event.message)
         break;
         // если сообщение о том, что он печатает сообщение
         case 'type':
         // то пока я не решил, что делать в таких ситуациях
         break;
         }
         }
         }*/
    });
});
