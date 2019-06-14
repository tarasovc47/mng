<?php

namespace console\websockets\Applications\controllers;

use common\models\CasUser;
use common\models\Access;
use common\models\Applications;
use common\models\Departments;
use common\models\ApplicationsAttributes;
use common\models\ApplicationsComments;
use common\models\UsersGroups;
use yii\helpers\ArrayHelper;

class EngineerController
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

        // 12 - возможность назначать ответственных за заявку
        if(!Access::hasAccess($user->id, $user->roles, 12)){
            return ['status' => "error", 'message' => "Нет доступа для установки ответственного за заявку"];
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
            $application->setResponsible($user->id, $user->id);
        }

        return ["status" => "success", "message" => "Принято в работу и установлен ответственный"];
	}

    public function SetResponsible($data, $user){
        // 12 - возможность назначать ответственных за заявку
        if(!Access::hasAccess($user->id, $user->roles, 12)){
            return ['status' => "dialog-error", 'messages' => [ 'Нет доступа для установки ответственного за заявку' ]];
        }

        /*
        В будущем доделать.
            Получаем доступ к назначению ответственного (id 12).
            Получаем доступ к назначению ответственного (id ?), если заявка:
                1) Не принадлежит текущему пользователю
                2) Принадлежит к отделу инженеров СТ (id 3)
        */

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

        /*
            Если заявка не принадлежит текущему пользователю - проверяем доступ
        */
        $event = $variables['application']->setResponsible($variables['responsible']->id, $user->id);

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
        // 13 - возможность переназначить в другой отдел
        if(!Access::hasAccess($user->id, $user->roles, 13)){
            return ['status' => "dialog-error", 'messages' => [ 'Нет доступа для переназначения в другой отдел' ]];
        }

        /*
            В будущем доделать.
            Получаем доступ к смене отдела (id 13).
            Получаем доступ к смене отдела (id ?), если заявка:
                1) Не принадлежит текущему пользователю
                2) Принадлежит к отделу инженеров СТ (id 3)
        */

        $messages = [];
        $variables = [];
        $group_id = 0;

        if(empty($data["attributes"]) && empty($data["comment"])){
            $messages[] = "Необходимо отметить атрибуты или написать комментарий.";
            unset($data["attributes"]);
            unset($data["comment"]);
        }

        foreach($data as $name => $value){
            if((($name == "attributes") || ($name == "comment")) && empty($value)){
                continue;
            }

            if(($name == "group_id") && ($data["department_id"] != '2')){
                continue;
            }

            $validate = Applications::socketValidation($name, $value);

            $messages = ArrayHelper::merge($messages, $validate["messages"]);
            $variables = ArrayHelper::merge($variables, $validate["variables"]);
        }

        if(!empty($messages)){
            return ["status" => "dialog-error", "messages" => $messages];
        }

        if(isset($variables["group"])){
            $group_id = $variables["group"]->id;
        }

        /*
            Если заявка не принадлежит текущему пользователю - проверяем доступ
        */
        $event = $variables['application']->setDepartment($variables['department']->id, $user->id, $group_id);

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