<?php

namespace console\websockets\Applications\controllers;

use common\models\Applications;
use yii\helpers\ArrayHelper;

class SupportController
{
	private static $instance;

	public static function Instance(){
		if(self::$instance == null)
			self::$instance = new self();
			
		return self::$instance;
	}

    public function Revision($data, $user){
        $messages = [];
        $variables = [];

        if(empty($data["comment"])){
            unset($data["comment"]);
        }

        foreach($data as $name => $value){
            $validate = Applications::socketValidation($name, $value);

            $messages = ArrayHelper::merge($messages, $validate["messages"]);
            $variables = ArrayHelper::merge($variables, $validate["variables"]);
        }

        if(!empty($messages)){
            return [ "status" => "dialog-error", "messages" => $messages ];
        }

        $revision = $variables["application"]->findForRevision();
        $event = $variables["application"]->revision($revision['department_id'], $revision['group_id'], $user->id);

        if(isset($variables['application_properties'])){
            $variables['application_properties']->application_event_id = $event->id;
            $variables['application_properties']->save();
        }

        if(isset($variables['application_comment'])){
            $variables['application_comment']->application_event_id = $event->id;
            $variables['application_comment']->save();
        }

        return [ 'status' => "success", 'message' => 'Заявка отправлена на доработку' ];
    }

    public function Close($data, $user){
        $messages = [];
        $variables = [];

        if(empty($data["comment"])){
            unset($data["comment"]);
        }

        foreach($data as $name => $value){
            $validate = Applications::socketValidation($name, $value);

            $messages = ArrayHelper::merge($messages, $validate["messages"]);
            $variables = ArrayHelper::merge($variables, $validate["variables"]);
        }

        if(!empty($messages)){
            return [ "status" => "dialog-error", "messages" => $messages ];
        }

        $event = $variables["application"]->close($user->id);

        if(isset($variables['application_properties'])){
            $variables['application_properties']->application_event_id = $event->id;
            $variables['application_properties']->save();
        }

        if(isset($variables['application_comment'])){
            $variables['application_comment']->application_event_id = $event->id;
            $variables['application_comment']->save();
        }

        return [ 'status' => "success", 'message' => 'Заявка закрыта' ];
    }
}