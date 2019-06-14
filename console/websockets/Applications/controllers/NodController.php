<?php

namespace console\websockets\Applications\controllers;

use common\models\CasUser;
use common\models\Access;
use common\models\Applications;
use common\models\Departments;
use common\models\ApplicationsAttributes;
use common\models\ApplicationsComments;
use yii\helpers\ArrayHelper;

class NodController
{
	private static $instance;

	public static function Instance(){
		if(self::$instance == null)
			self::$instance = new self();
			
		return self::$instance;
	}

    public function Refuse($data, $user){
        // 21 - возможность переназначить в другой отдел
        if(!Access::hasAccess($user->id, $user->roles, 21)){
            return ['status' => "dialog-error", 'messages' => [ 'Нет доступа для отказа от заявок' ]];
        }

        $messages = [];
        $variables = [];

        foreach($data as $name => $value){
            $validate = Applications::socketValidation($name, $value);

            $messages = ArrayHelper::merge($messages, $validate["messages"]);
            $variables = ArrayHelper::merge($variables, $validate["variables"]);
        }

        if(!empty($messages)){
            return [ "status" => "dialog-error", "messages" => $messages ];
        }

        $event = $variables['application']->refuse($user->id);

        $variables['application_comment']->application_event_id = $event->id;
        $variables['application_comment']->save();
        
        return [ 'status' => "success", 'message' => 'Заявка передана' ];
    }

    public function Complete($data, $user){
        $messages = [];
        $variables = [];

        if(empty($data["attributes"]) && empty($data["comment"])){
            $messages[] = "Необходимо отметить атрибуты или написать комментарий.";
            unset($data["attributes"]);
            unset($data["comment"]);
        }

        foreach($data as $name => $value){
            if((($name == "attributes") || ($name == "comment")) && empty($value)){
                continue;
            }

            $validate = Applications::socketValidation($name, $value);

            $messages = ArrayHelper::merge($messages, $validate["messages"]);
            $variables = ArrayHelper::merge($variables, $validate["variables"]);
        }

        if(!empty($messages)){
            return [ "status" => "dialog-error", "messages" => $messages ];
        }

        if($variables["status"]->id == 7){
            $event = $variables['application']->complete($user);
            $m = "Заявка завершена.";
        }
        else{
            $event = $variables['application']->setStatus($variables["status"]->id, $user->id);
            $m = "Статус обновлен.";
        }

        if(isset($variables['application_attributes'])){
            $variables['application_attributes']->application_event_id = $event->id;
            $variables['application_attributes']->save();
        }

        if(isset($variables['application_comment'])){
            $variables['application_comment']->application_event_id = $event->id;
            $variables['application_comment']->save();
        }
        
        return [ 'status' => "success", 'message' => $m ];
    }
}