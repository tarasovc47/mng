<?php

namespace console\websockets\Applications\controllers;

use common\models\CasUser;
use common\models\Access;
use common\models\Applications;
use common\models\Departments;
use common\models\ApplicationsAttributes;
use common\models\ApplicationsComments;
use yii\helpers\ArrayHelper;

class BrigadierController
{
	private static $instance;

	public static function Instance(){
		if(self::$instance == null)
			self::$instance = new self();
			
		return self::$instance;
	}

	public function Took($data, $user){
		// 11 - возможность устанавливать статус "Принято в работу"
        if(!Access::hasAccess($user->id, $user->roles, 11)){
            return ['status' => "error", 'message' => "Нет доступа для принятия заявок в работу"];
        }

        $application_stack_id = "";
        $id_spec = [];
        foreach($data["ids"] as $id){
            $id = explode("-", $id);

            if(empty($application_stack_id)){
                $application_stack_id = $id[0];
            }

            $id_spec[] = $id[1];
        }

        $applications = Applications::find()->where(["application_stack_id" => $application_stack_id, "id_spec" => $id_spec])->all();

        if(empty($applications)){
            return ['status' => "error", 'message' => "Заявки не найдены"];
        }

        foreach($applications as $application){
            $application->setTakenStatus($user->id);
        }

        return ['status' => "success", 'message' => "Принято в работу"];
	}

    public function SetResponsible($data, $user){
        // 16 - возможность назначать ответственных за заявку
        if(!Access::hasAccess($user->id, $user->roles, 16)){
            return ['status' => "dialog-error", 'messages' => [ 'Нет доступа для установки ответственного за заявку' ]];
        }

        $messages = [];
        $variables = [];

        foreach($data as $name => $value){
            if((($name == "attributes") || ($name == "comment")) && empty($value)){
                continue;
            }

            $validate = Applications::socketValidation($name, $value);

            $messages = ArrayHelper::merge($messages, $validate["messages"]);
            $variables = ArrayHelper::merge($variables, $validate["variables"]);
        }

        if(!empty($messages)){
            return ["status" => "dialog-error", "messages" => $messages];
        }

        $event = $variables['application']->setResponsible($variables["responsible"]->id, $user->id);

        if(isset($variables['application_attributes'])){
            $variables['application_attributes']->application_event_id = $event->id;
            $variables['application_attributes']->save();
        }

        if(isset($variables['application_comment'])){
            $variables['application_comment']->application_event_id = $event->id;
            $variables['application_comment']->save();
        }

        return ["status" => "success", "message" => "Установлен ответственный"];
    }

    public function SetDepartment($data, $user){
        // 18 - возможность переназначить в другой отдел
        if(!Access::hasAccess($user->id, $user->roles, 18)){
            return ['status' => "dialog-error", 'messages' => [ 'Нет доступа для переназначения в другой отдел' ]];
        }

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
            return ["status" => "dialog-error", "messages" => $messages];
        }

        $event = $variables['application']->setDepartment($variables['department']->id, $user->id, 0);

        if(isset($variables['application_attributes'])){
            $variables['application_attributes']->application_event_id = $event->id;
            $variables['application_attributes']->save();
        }

        if(isset($variables['application_comment'])){
            $variables['application_comment']->application_event_id = $event->id;
            $variables['application_comment']->save();
        }
        
        return [ 'status' => "success", 'message' => 'Заявка передана' ];
    }
}