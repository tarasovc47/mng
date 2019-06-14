<?php

namespace frontend\modules\techsup\controllers;

use Yii;
use common\models\ClientSearch;
use common\models\Attributes;
use common\models\Fields;
use common\models\FieldsData;
use common\models\ConnectionTechnologies;
use common\models\Departments;
use common\models\Applications;
use common\models\ApplicationsStacks;
use common\models\ApplicationsEvents;
use common\models\ApplicationsAttributes;
use common\models\UsersGroups;
use frontend\components\FrontendComponent;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class ApplicationsController extends FrontendComponent
{
    public function beforeAction($action){
        $this->view->title = "Техподдержка | Заявки";
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate(){
        $this->view->title .= " | Создание";

        $modelClientSearch = new ClientSearch();

        $abonent_id = Yii::$app->request->get('abonent');
        $client_id = Yii::$app->request->get('client');
        $abonentData = [];
        $clientData = [];

        if($abonent_id){
            $abonentData = $modelClientSearch->searchOneAbonent($abonent_id);
        }
        if($client_id){
            $clientData = $modelClientSearch->searchOneClient($client_id);
        }
        
        return $this->render("create", [
            "abonentData" => $abonentData, 
            "clientData" => $clientData,
        ]);
    }

    public function actionGetAttributesByTech(){
        if(!Yii::$app->request->isAjax){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $billing_id = Yii::$app->request->get('billing_id');
        $tech_id = Yii::$app->request->get('tech_id');
        $loki_basic_service_id = Yii::$app->request->get('loki_basic_service_id');
        $login = Yii::$app->request->get('login');
        if((($billing_id === null) && ($tech_id === null)) || ($loki_basic_service_id === null)){
            throw new NotAcceptableHttpException('Не хватает данных');
        }

        $connection_technology = false;
        if($billing_id){
            $connection_technology = ConnectionTechnologies::find()->where(["billing_id" => $billing_id])->one();
        }
        else if($tech_id){
            $connection_technology = ConnectionTechnologies::find()->where(["id" => $tech_id])->one();
        }

        if(!$connection_technology){
            return Json::encode(['status' => 'error', 'message' => 'Технология подключения не найдена']);
        }

        $attributes = Attributes::loadTreeByTechnologyId($connection_technology->id, $department_id = 1);
        $scenarios = $attributes['scenarios'];

        $html = $this->renderPartial("_attributes", [
            'attrs' => $attributes['attrs'],
            'fields' => $attributes['fields'],
            'children' => false, 
            'padding' => 0,
            'loki_basic_service_id' => $loki_basic_service_id,
            'level' => 1,
        ]);

        if($login && $billing_id){
            $html = $this->renderPartial("_create_section", [
                'loki_basic_service_id' => $loki_basic_service_id,
                'connection_technology_id' => $connection_technology->id,
                'login' => $login,
                'html' => $html,
                'departments' => Departments::loadList(),
                'brigades' => UsersGroups::loadList([ "department_id" => 2 ]), // Служба эксплуатации
                'default_brigade' => Applications::identifyBrigade($loki_basic_service_id)
            ]);
        }

        return Json::encode(['status' => 'success', 'html' => $html, 'scenarios' => $scenarios]);
    }

    public function actionGetConntechsByService(){
        if(!Yii::$app->request->isAjax){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $service_billing_id = Yii::$app->request->get('service_billing_id');
        $login = Yii::$app->request->get('login');
        $loki_basic_service_id = Yii::$app->request->get('loki_basic_service_id');
        if(($service_billing_id === null) || ($loki_basic_service_id === null) || ($login === null)){
            throw new NotAcceptableHttpException('Не хватает данных');
        }

        if((int)$service_billing_id < 0){
            return Json::encode(['status' => 'error', 'message' => 'Service billing id меньше 0']);
        }

        $conn_techs = ConnectionTechnologies::getConntechsByServiceBillingId($service_billing_id);
        $conn_techs = $this->renderPartial("_connection_technologies", [
            'conn_techs' => $conn_techs,
        ]);

        $html = $this->renderPartial("_create_section", [
            'loki_basic_service_id' => $loki_basic_service_id,
            'connection_technology_id' => 0,
            'login' => $login,
            'html' => $conn_techs,
            'departments' => Departments::loadList(),
            'brigades' => UsersGroups::loadList([ "department_id" => 2 ]), // Служба эксплуатации
            'default_brigade' => Applications::identifyBrigade($loki_basic_service_id)
        ]);

        return Json::encode(['status' => 'success', 'html' => $html]);
    }

    public function actionValidateAndCreate(){
        if(!Yii::$app->request->isAjax){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $data = Yii::$app->request->post('data');
        $models = [];
        $status = true;
        $i = 1;
        $created_at = time();
        $applications_attributes = [];

        foreach($data as $loki_basic_service_id => $info){
            $data[$loki_basic_service_id]["errors"] = [];

            $models[$i] = new Applications();

            $models[$i]->application_stack_id = "temp_cap";
            $models[$i]->id_spec = $i;
            $models[$i]->loki_basic_service_id = $loki_basic_service_id;
            $models[$i]->application_type_id = 0;
            $models[$i]->group_id = 0;
            
            $models[$i]->application_status_id = 1;
            $models[$i]->responsible = 0;

            $applications_attributes[$i] = new ApplicationsAttributes();
            $applications_attributes[$i]->application_event_id = 0;
            $applications_attributes[$i]->attributes = isset($info["attributes"]) ? $info["attributes"] : "";
            if(!$applications_attributes[$i]->validate()){
                $status = false;
                $errors = $applications_attributes[$i]->prepareErrors();
                $data[$loki_basic_service_id]["errors"] = ArrayHelper::merge($data[$loki_basic_service_id]["errors"], $errors);
            }

            if(isset($info["connection_technology"])){
                $models[$i]->connection_technology_id = $info["connection_technology"];
            }

            if(isset($info["department"])){
                $models[$i]->department_id = $info["department"];

                if(($models[$i]->department_id == 2) && isset($info["group_id"]) && !empty($info["group_id"])){
                    $models[$i]->group_id = $info["group_id"];
                }
            }

            if(!$models[$i]->validate()){
                $status = false;
                $errors = $models[$i]->prepareErrors();
                $data[$loki_basic_service_id]["errors"] = ArrayHelper::merge($data[$loki_basic_service_id]["errors"], $errors);
            }
            else{
                if(!isset($info["fields"])){
                    $info["fields"] = [];
                }
                
                $validate_fields = FieldsData::validateFields($applications_attributes[$i]->attributes, $info["fields"]);

                if($validate_fields["status"] === "too_little_fields"){
                    $status = false;
                    $data[$loki_basic_service_id]["errors"]['fields'][] = $validate_fields["error"];
                }
                else if($validate_fields["status"] === false){
                    $status = false;
                    unset($validate_fields["status"]);
                    $data[$loki_basic_service_id]["fields"] = $validate_fields;
                }
            }

            $i++;
        }

        if(!$status){
            return Json::encode(['status' => "error", 'data' => $data]);
        }

        $application_stacks = new ApplicationsStacks();
        $application_stacks->created_at = $created_at;

        while(!$application_stacks->save()){
            $application_stacks->generateId($created_at);
        }

        foreach($models as $id_spec => $model){
            $model->application_stack_id = $application_stacks->id;
            $model->application_type_id = Attributes::returnType($applications_attributes[$id_spec]->attributes);
            $model->save();

            $application_event = new ApplicationsEvents();
            $application_event->application_stack_id = $application_stacks->id;
            $application_event->application_id_spec = $model->id_spec;
            $application_event->created_at = $created_at;
            $application_event->type = 1;
            $application_event->cas_user_id = $this->cas_user->id;
            $application_event->scenario_id = isset($data[$model->loki_basic_service_id]['scenario']) ? $data[$model->loki_basic_service_id]['scenario'] : 0;
            $application_event->save();

            $applications_attributes[$id_spec]->application_event_id = $application_event->id;
            $applications_attributes[$id_spec]->save();

            if(isset($data[$model->loki_basic_service_id]["fields"])){
                FieldsData::createData($data[$model->loki_basic_service_id]["fields"], $application_event->id, $created_at, $this->cas_user->id);
            }
        }

        return Json::encode(['status' => "success"]);
    }
}
