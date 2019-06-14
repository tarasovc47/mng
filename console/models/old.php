<?php
/*
namespace console\models;

use Yii;
use yii\db\Query;
use yii\helpers\Json;
use common\models\CasUser;

class ApplicationsDaemon extends \morozovsk\websocket\Daemon
{
    public $users = [];
    public $db;
    public $timer = 5; // 1 секунда

    protected function onStart(){
        $dsn = Yii::$app->db->dsn;
        $user = Yii::$app->db->username;
        $password = Yii::$app->db->password;

        $params = mb_substr($dsn, 6);
        $params = explode(";", $params);
        $params[] = "user=".$user;
        $params[] = "password=".$password;
        $params = implode(" ", $params);

        $this->db = pg_pconnect($params);
        pg_query($this->db, 'LISTEN "myEvent"');
    }

    // it is called when the connection is open
    protected function onOpen($connectionId, $info){
        parse_str(substr($info['GET'], 1), $_GET);

        $user_id = $this->loadBySid($_GET["sid"]);
        $this->users[$connectionId] = CasUser::findOne($user_id);
    }

    // it is called when existed connection is closed
    protected function onClose($connectionId){
        unset($this->users[$connectionId]);
    }

    // it is called when received a message from client
    protected function onMessage($connectionId, $data, $type){
        
        $notifies = [];
        $notify = pg_get_notify($this->db);

        while($notify){
            $notifies[] = $notify;
            $notify = pg_get_notify($this->db);
        }

        if($notifies){
            $message = json_encode($notifies);
        }
        else{
            $message = json_encode("Ничего нет");
        }
        $message = json_encode("Ответ");

        foreach($this->clients as $connectionId => $client){
            $this->sendToClient($connectionId, $message);
        }
        // echo "ID пользователя: " . $this->users[$connectionId] . "\n";
    }

    protected function onTimer(){
        print_r($this->users) . "\n";
        
        /*$notifies = [];

        while($notify = pg_get_notify($this->db)){
            $notifies[] = $notify;
        }

        if($notifies){
            $message = json_encode($notifies);

            foreach($this->clients as $connectionId => $client){
                $this->sendToClient($connectionId, $message);
            }
        }
    }

    private function sendToUser($user_id, $data){
        $data = Json::encode($data);

        foreach($this->users as $connectionId => $user){
            if($connectionId == $user_id){
                $this->sendToClient($connectionId, $data);
            }
        }
    }

    private function loadBySid($sid){
        $user = NULL;

        $session = (new Query())
            ->select(['id_user'])
            ->from('sessions')
            ->where(['sid' => $sid])
            ->one();

        if($session){
            $user = $session['id_user'];
        }

        return $user;
    }
}*/