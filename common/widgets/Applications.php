<?php
namespace common\widgets;

use Yii;
use common\components\SiteHelper;
use common\models\Attributes;
use common\models\Properties;
use common\models\ClientSearch;
use yii\helpers\ArrayHelper;
use common\assets\Applications as ApplicationsAssets;
use common\assets\FieldsData as FieldsDataAssets;

class Applications extends \yii\bootstrap\Widget
{
	public $applications;
    public $applications_stacks = [];
    public $attributes = [];
    public $properties = [];
    public $clients = [];
    public $user;
    public $template;

    public function init(){
        if($this->applications === null){
            throw new InvalidConfigException('Атрибут "applications" обязательно должен быть указан.');
        }

        if($this->user === null){
            throw new InvalidConfigException('Атрибут "user" обязательно должен быть указан.');
        }

        if($this->template === null){
            throw new InvalidConfigException('Атрибут "template" обязательно должен быть указан.');
        }

        foreach($this->applications as $key => $application){
            foreach($application->applicationsEvents as $n => $event){
                if($event->applicationAttributes){
                    $attributes = SiteHelper::to_php_array($event->applicationAttributes->attributes);

                    foreach($attributes as $attr){
                        if(!in_array($attr, $this->attributes)){
                            $this->attributes[] = $attr;
                        }
                    }
                }

                if($event->applicationProperties){
                    $properties = SiteHelper::to_php_array($event->applicationProperties->properties);

                    foreach($properties as $prop){
                        if(!in_array($prop, $this->properties)){
                            $this->properties[] = $prop;
                        }
                    }
                }
            }

            if(!in_array($application->application_stack_id, $this->applications_stacks)){
                $this->applications_stacks[] = $application->application_stack_id;
            }

            if(!in_array($application->loki_basic_service_id, $this->clients)){
                $this->clients[] = $application->loki_basic_service_id;
            }
        }

        $this->attributes = Attributes::findAll($this->attributes);
        $this->attributes = ArrayHelper::index($this->attributes, "id");

        $this->properties = Properties::findAll($this->properties);
        $this->properties = ArrayHelper::index($this->properties, "id");

        if(!empty($this->clients)){
            $clientSearch = new ClientSearch();
            $this->clients = $clientSearch->searchByLokiBasicService($this->clients);
        }

        ApplicationsAssets::register($this->view);
        FieldsDataAssets::register($this->view);
    }

	public function run(){
        return $this->render("applications/dashboard", [
            "applications" => $this->applications,
            "applications_stacks" => $this->applications_stacks,
            "attributes_repository" => $this->attributes,
            "properties_repository" => $this->properties,
            "clients" => $this->clients,
            "template" => $this->template,
            "user" => $this->user,
        ]);
	}

    public static function stack($stack_id, $applications, $attributes_repository, $properties_repository, $clients, $template, $user){
        return Yii::$app->view->render("@common/widgets/views/applications/_stack", [
            "stack_id" => $stack_id,
            "applications" => $applications, 
            "attributes_repository" => $attributes_repository, 
            "properties_repository" => $properties_repository,
            "clients" => $clients, 
            "template" => $template, 
            "user" => $user
        ]);
    }

    public static function short($application, $template, $clients, $attributes_repository){
        return Yii::$app->view->render("@common/widgets/views/applications/__short", [
            "application" => $application,
            "template" => $template,
            "clients" => $clients,
            "attributes_repository" => $attributes_repository,
        ]);
    }

    public static function application($application, $template, $user, $clients, $attributes_repository, $properties_repository){
        return Yii::$app->view->render("@common/widgets/views/applications/__application", [
            "application" => $application,
            "template" => $template,
            "user" => $user,
            "clients" => $clients,
            'attributes_repository' => $attributes_repository,
            'properties_repository' => $properties_repository,
        ]);
    }

    public static function confirm_responsible($users, $attributes, $application){
        return Yii::$app->view->render("@common/widgets/views/applications/confirm_responsible", [
            'users' => $users,
            'attributes' => $attributes,
            'application' => $application,
        ]);
    }

    public static function confirm_department($departments, $attributes, $application, $brigades, $default_brigade){
        return Yii::$app->view->render("@common/widgets/views/applications/confirm_department", [
            'departments' => $departments,
            'attributes' => $attributes,
            'application' => $application,
            'brigades' => $brigades,
            'default_brigade' => $default_brigade,
        ]);
    }

    public static function confirm_refuse($application){
        return Yii::$app->view->render("@common/widgets/views/applications/confirm_refuse", [
            'application' => $application,
        ]);
    }

    public static function confirm_complete($attributes, $application, $statuses){
        return Yii::$app->view->render("@common/widgets/views/applications/confirm_complete", [
            'attributes' => $attributes,
            'application' => $application,
            'statuses' => $statuses,
        ]);
    }

    public static function confirm_handle($properties, $application){
        return Yii::$app->view->render("@common/widgets/views/applications/confirm_handle", [
            'properties' => $properties,
            'application' => $application,
        ]);
    }
}