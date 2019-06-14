<?php

namespace console\websockets\Applications\controllers;

use Yii;
use yii\helpers\Json;
use common\models\Applications;

class ListenerController
{
	private static $instance;
    private $db;
    private $connections = [];
    private $users = [];

	public static function Instance(){
		if(self::$instance == null)
			self::$instance = new self();
			
		return self::$instance;
	}

    public function __construct(){
        $dsn = Yii::$app->db->dsn;
        $user = Yii::$app->db->username;
        $password = Yii::$app->db->password;

        $params = mb_substr($dsn, 6);
        $params = explode(";", $params);
        $params[] = "user=" . $user;
        $params[] = "password=" . $password;
        $params = implode(" ", $params);

        $this->db = pg_pconnect($params);
        pg_query($this->db, 'LISTEN "applications_events_insert"');
    }

    public function run($connections, $users){
        $notifications = [];
        while($notification = pg_get_notify($this->db)){
            $notifications[] = $notification;
        }

        if($notifications){
            $this->synchronization($connections, $users);

            $applications = [];

            foreach($notifications as $notification){
                $notification["payload"] = Json::decode($notification["payload"]);
                $id = [
                    $notification["payload"]["application_stack_id"],
                    $notification["payload"]["application_id_spec"],
                ];

                if(($notification['message'] == "applications_events_insert") && !in_array($id, $applications)){
                    $applications[] = $id;
                }
            }

            if(!empty($applications) && !empty($this->connections)){
                $this->notifyClients($applications);
            }
        }
    }

    private function synchronization($connections, $users){
        $this->connections = $connections;
        $this->users = $users;
    }

    private function notifyClients($applications){
        $where = [];

        foreach($applications as $application){
            foreach($application as $column){
                $where[] = $column;
            }
        }

        $applications = Applications::findAll($where);

        foreach($this->connections as $connection){
            $user = $this->users[$connection->id];
            $response = [
                'status' => "update",
                "applications" => [],
            ];
            
            foreach($applications as $application){
                $result = "remove";

                if($application->application_status_id != 8){
                    if($application->responsible == $user->id){
                        $result = "update";
                    }
                    elseif($application->department_id == $user->department_id){
                        switch($user->department_id){
                            case 3:
                                // Инженеры сетевых технологий
                                $result = "update";
                                break;
                            case 2:
                                if($application->group_id == $user->group_id){
                                    // Бригадир
                                    if($user->usersGroup->head_id == $user->id){
                                        $result = "update";
                                    }
                                    // Член бригады
                                    if($user->id == $application->responsible){
                                        $result = "update";
                                    }
                                }
                                break;
                            default:
                        }
                    }
                    // elseif(отслеживает заявку)
                }

                $response["applications"][$application->id] = $result;
            }

            $response = Json::encode($response);
            $connection->send($response);
        }
    }
}