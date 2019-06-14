#!/usr/bin/env node

var debug = require('debug')('auth');
var debug1 = require('debug')('accounting');
var debug2 = require('debug')('pg-notify');

var dbuser = "web:webparol";
// var dbpass = "webparol";

// var mng_config = {
//     "host":"10.60.248.7",
//     "db":"mng72",
//     "userpass":"monitor:Kernelp@n1c"
// };

var db_config = [
    {
        "ip":"10.60.249.8",
        "db_name":"radiusmain"
    },
    {
        "ip":"10.60.249.11",
        "db_name":"radius"
    },
    {
        "ip":"10.60.249.12",
        "db_name":"radius"
    },
    {
        "ip":"10.60.249.13",
        "db_name":"radius"
    },
];
var servers = {

    "10.60.249.8":"radiusmain",
    "10.60.249.11":"radius",
    "10.60.249.12":"radius",
    "10.60.249.13":"radius",
};

var pgp = [];
var db = [];
var dbData = [];
var i=0;
for(var k=0; k<db_config.length;k++){
    pgp[k] = require("pg-promise")(/*options*/);
    db[k] = pgp[k]("postgres://" + dbuser + "@" + db_config[k].ip + ":5432/" + db_config[k].db_name);
    // console.log("postgres://" + dbuser + "@" + key + ":5432/" + servers[key]);
    debug2("postgres://%s@%s:5432/%s", dbuser, db_config[k].ip,  db_config[k].db_name);
}

//Подключение к mng базе
// var pgp_mng = require("pg-promise")(/*options*/);
// var mng_ConnectStr = "postgres://" + mng_config.userpass + "@" + mng_config.host + ":5432/" + mng_config.db;
// debug(mng_ConnectStr);
// var db_mng = pgp_mng(mng_ConnectStr) ;


// var pgp = require("pg-promise")(/*options*/);
// var db = pgp("postgres://monitor:Kernelp@n1c@10.60.248.7:5432/mng72");

var WebSocketClient = require('websocket').client;

var client = new WebSocketClient();

client.on('connectFailed', function(error) {
    debug2('Connect Error: ' + error.toString());
});

client.connect('ws://localhost:1337/');

console.log('connected');

client.on('connect', function(connection) {
    debug2('WebSocket Client Connected');
    connection.on('error', function(error) {
        debug2("Connection Error: " + error.toString());
    });
    connection.on('close', function() {
        debug2('echo-protocol Connection Closed');
    });

    //init connection
    connection.sendUTF(JSON.stringify({
        type:'authorize',
        username: "radius",
        userid: "0",
        user_type: "server",
    }));


    connection.on('message', function(message) {
        var event = JSON.parse(message.utf8Data);
        //debug(event.type);
        //debug(event.success);
        if((event.type==="authorize")&&(event.success===true)){
            debug2("Access granted!");
            debug2("************************************************\n");


            for(var key=0;key<db_config.length;key++){
                debug2('Listner %s started:', key);
                db[key].connect({direct: true})
                    .then(function (sco) {
                        // sco.query("SELECT * from subscriber_auth ORDER by ts DESC LIMIT 1") //простой запрос к БД
                        //     .then(function (data) {
                        //         debug(data);
                        //     });

                        sco.query('LISTEN subscriber_auth');
                        sco.query('LISTEN accounting');
                        sco.client.on('notification', function (data) {
                            var payload = JSON.parse(data.payload);
                            switch (data.channel){
                                case "subscriber_auth":
                                    var send_data = {
                                        id: payload.id,
                                        login: payload.supplied_login,
                                        ts: payload.ts,
                                        auth_res: payload.authresult,
                                        authwhy: payload.authresult_why,
                                        macaddr: payload.calling_station,
                                        circuit_id: payload.circuit_id,
                                        port_id: payload.portid,
                                        nas: payload.nas
                                    };
                                    connection.sendUTF(
                                        JSON.stringify(
                                            {
                                                type:'subscriber_auth',
                                                user_name: "pg_listner",
                                                user_type: "server",
                                                data: send_data
                                            }
                                        ));
                                    debug('Received: ', data);

                                    break;

                                case "accounting":
                                    send_data = {
                                        login: payload.f3,
                                        status: payload.f1,
                                        ts: payload.f2,
                                        mac: payload.f4,
                                        f5: payload.f5
                                    };
                                    connection.sendUTF(
                                        JSON.stringify(
                                            {
                                                type:'accounting',
                                                user_name: "pg_listner",
                                                user_type: "server",
                                                data: send_data
                                            }
                                        ));
                                    debug1('Received: ', data);
                                    break;
                            }
                            // var number = {
                            //     id: data.id,
                            //     login: data.supplied_login,
                            //     ts: data.ts,
                            //     auth_res: data.authresult,
                            //     authwhy: data.authresult_why,
                            //     macaddr: data.calling_station
                            // };
                            //
                            /*connection.sendUTF(
                             JSON.stringify(
                             {
                             type:'server_msg',
                             user_name: 'radius',
                             user_type: "server",
                             data: number
                             }
                             ));*/

                        });
                        // return sco.none('LISTEN subscriber_auth').none('LISTEN accounting_');
                    })
                    .catch(function (error) {
                        debug2('Error:', error);
                    });
            }



            /* setInterval(function () {
             // for(var t = 0; t<servers.length;t++){
             var promise = new Promise((resolve,reject) => {
             if(t==db_config.length){
             t=0;
             debug("t is zeroed " + t);
             };
             resolve(t)
             });

             promise
             .then(
             t => {
             GetDBdata(t)
             .then(
             number => {  //данные из БД в переменной numbers
             debug("current counter number %s, max=%s", t,  db_config.length-1);
             data = number;
             // t++;
             // if(t===servers.length){t=0};

             if(dbData[t]!==data.id){
             dbData[t]=data.id;
             return data;
             }else{
             debug("There is no changes " + t);
             reject();
             }

             },
             error => {
             debug("current counter number %s, max=%s", t,  db_config.length);
             debug("error gde to");
             reject();
             })
             .then( //Отправка слушателям
             data => {
             // debug("Data from DB recived:", data);
             connection.sendUTF(
             JSON.stringify(
             {
             type:'server_msg',
             user_name: 'radius',
             user_type: "server",
             data: data
             }));
             debug("Sended data: ", data);

             resolve("success");
             // return t;
             });
             })
             .then(
             success => {
             t++;
             });
             // }
             },1000);*/

            // sendNumber();
        }else{
            debug("Authorization failed!");
            connection.close();
        }

        //     if (message.type === 'utf8') {
        //        debug("Received: '" + message.utf8Data + "'");
        //     }

    });
});


