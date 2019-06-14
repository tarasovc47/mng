<?php

namespace backend\controllers;

use Yii;
use common\models\CasUser;
use common\models\Access;
use common\models\Departments;
use common\models\search\CasUserSearch;
use backend\components\BackendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use common\components\SiteHelper;
class CasUserController extends BackendComponent
{
    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }

        $noAccess = false;
        if($this->permission == 1){
            switch(Yii::$app->controller->action->id){
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
        $searchModel = new CasUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);        

        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $groups = [];
        if(!empty($model->department->usersGroups)){
            foreach($model->department->usersGroups as $group){
                $groups[$group->id] = $group->name;
            }
        }


        $accessSettings = Access::getAllModulesSettingsForCasUser($model);

        return $this->render('update', [
            'model' => $model,
            'groups' => $groups,
            'accessSettings' => $accessSettings,
        ]);
    }

    public function actionAccessUpdate(){
        if(!Yii::$app->request->isAjax){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $post = Yii::$app->request->post();        
        $user = CasUser::findOne($post["cas_user_id"]);

        if(!$user){
            return Json::encode(['status' => 'error']);
        }

        $action = "";
        if(!$post["access_id"]){
            $action = "create";
        }
        else{
            $current_access = Access::findOne($post["access_id"]);

            if($current_access->department_id != 0){
                $action = "create";
            }

            if($current_access->cas_user_id == $user->id){


                $cas_names = SiteHelper::to_php_array($user->roles);

                $departments = Departments::getDepartmentsByCasNames($cas_names);
                $departments_ids = array_keys($departments);
                $accesses = Access::find()
                    ->where([ "cas_user_id" => 0, "department_id" => $departments_ids, "module_setting_key" => $post["module_setting_key"] ])
                    ->all();
                
                if(!empty($accesses)){
                    $max = "";
                    foreach($accesses as $access){
                        if(empty($max) || ($max->value < $access->value)){
                            $max = $access;
                        }
                    }

                    if(!empty($max) && ($max->value == $post["value"])){
                        $action = "delete_department";
                    }
                    else{
                        $action = "update";
                    }
                }
                else if(empty($accesses) && ($post["value"] == 0)){
                    $action = "delete_empty";
                }
                else{
                    $action = "update";
                }
            }
        }

        switch($action){
            case "create":
                $model = new Access();
                $model->cas_user_id = $user->id;
                $model->department_id = 0;
                $model->module_setting_key = $post["module_setting_key"];
                $model->value = $post["value"];

                if($model->save()){
                    $json = [ 'status' => 'success_private', 'id' => $model->id, 'about' => 'Индивидуальная настройка пользователя' ];
                }
                break;
            case "update":
                $current_access->value = $post["value"];

                if($current_access->save()){
                    $json = ['status' => 'success_private', 'id' => $current_access->id, 'about' => 'Индивидуальная настройка пользователя'];
                }
                break;
            case "delete_department":
                $current_access->delete();
                $about = "Настройка наследуется от отдела <a href='/departments/view?id=";
                $about .= $max->department_id . "'>" . $departments[$max->department_id]["name"] . "</a>";
                $json = [ 'status' => 'success_department', 'id' => $max->id, 'about' => $about ];
                break;
            case "delete_empty":
                $current_access->delete();
                $json = ['status' => 'success_empty', 'about' => "Настройка не пересекается с пользователем или его отделами."];
                break;
            default:
                $json = ['status' => 'error'];
        }

        return Json::encode($json);
    }

    protected function findModel($id)
    {
        if (($model = CasUser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
