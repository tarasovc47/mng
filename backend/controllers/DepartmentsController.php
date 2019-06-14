<?php

namespace backend\controllers;

use Yii;
use common\models\Departments;
use common\models\Access;
use common\models\Services;
use common\models\Attributes;
use common\models\search\DepartmentsSearch;
use yii\data\ActiveDataProvider;
use backend\components\BackendComponent;
use yii\web\NotFoundHttpException;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use common\models\SearchFields;
use common\models\SearchFieldsSettings;
use common\models\UsersGroups;
use common\models\CasUser;
use common\models\Properties;

class DepartmentsController extends BackendComponent
{
    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }

        $noAccess = false;
        if($this->permission == 1){
            switch(Yii::$app->controller->action->id){
                case 'create':
                    $noAccess = true;
                    break;
                case 'update':
                    $noAccess = true;
                    break;
                case 'access-update':
                    $noAccess = true;
                    break;
                default:
            }
        }

        if($noAccess){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        return true;
    }

    public function actionIndex()
    {
        $searchModel = new DepartmentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Departments();

        if($model->load(Yii::$app->request->post())){
            $model->cas_name = mb_strtolower($model->cas_name);

            if($model->save()){
                $group = new UsersGroups();
                $group->department_id = $model->id;
                $group->name = $model->name;
                $group->save();
                
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post())){
            $model->cas_name = mb_strtolower($model->cas_name);

            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $fields = SearchFields::getFieldsList();
        $department_fields_values = SearchFieldsSettings::getValuesList($id, 0);
        $accessSettings = Access::getAllModulesSettingsForDepartment($id);
        $services = Services::find()->all();
        $attributes = Attributes::loadAttributes($id, true, true, true);
        $groups = UsersGroups::find()
            ->with("casUsers")
            ->where([ "department_id" => $model->id ])
            ->orderBy([ "id" => SORT_ASC ])
            ->all();
        $undefined_users = CasUser::find()
            ->where([ "department_id" => $model->id, "group_id" => [ NULL, 0 ] ])
            ->all();

        $properties = false;
        if($model->id === 1){
            $properties = Properties::loadProperties(true);
        }

        return $this->render('update', [
            'model' => $model,
            'accessSettings' => $accessSettings,
            'department_fields_values' => $department_fields_values,
            'fields' => $fields,
            'attributes' => $attributes,
            'groups' => $groups,
            'undefined_users' => $undefined_users,
            'properties' => $properties,
        ]);
    }

    public function actionAccessUpdate()
    {
        if(!Yii::$app->request->isAjax){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = Access::findOne(Yii::$app->request->post("id"));

        if($model !== null){
            $model->value = Yii::$app->request->post("value");

            if($model->save()){
                return Json::encode(['status' => 'success']);
            }
        }
        
        return Json::encode(['status' => 'error']);
    }

    protected function findModel($id)
    {
        if (($model = Departments::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSearchSettingsUpdate()
    {
        if(!Yii::$app->request->isAjax){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $settings = Yii::$app->request->post("settings");
        $department_id = Yii::$app->request->post("department_id");
        $error = false;
        $settings_list = SearchFieldsSettings::getValuesList($department_id, 0);
        
        if(isset($settings) && !empty($settings)){
            foreach ($settings as $field_id => $setting) {
                if (isset($settings_list[$field_id])) {
                    if ($settings_list[$field_id] == $setting) {
                        continue;
                    }
                    $model_id = SearchFieldsSettings::getModelId($field_id, $department_id, 0);
                    $model = SearchFieldsSettings::findOne($model_id);
                } else {
                    $model = new SearchFieldsSettings();
                    $model->department_id = $department_id;
                    $model->field_id = $field_id;
                    $model->cas_user_id = 0;
                }

                $model->value = $setting;

                if(!$model->save()){
                    $error = true;
                }
            }
        } else {
            $error = true;
        }
        
        return Json::encode(['error' => $error]);
    }

    // После прочтения - сжечь. В смысле после применения - удалить
    // Метод для первичного автосоздания групп пользователей
    public function actionCreateGroups(){
        $groups = UsersGroups::find()->all();

        if(empty($groups)){
            $departments = Departments::find()->orderBy([ "id" => SORT_ASC ])->all();

            foreach($departments as $d){
                $group = new UsersGroups();
                $group->department_id = $d->id;
                $group->name = $d->name;
                $group->save();
            }
        }
        else{
            $count = count($groups);
            echo "Группы уже существуют, целых {$count} штук. Метод нужно удалить.";
            die();
        }
    }
}
