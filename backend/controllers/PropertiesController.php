<?php

namespace backend\controllers;

use Yii;
use common\models\Properties;
use common\models\Fields;
use common\models\Departments;
use yii\data\ActiveDataProvider;
use backend\components\BackendComponent;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\helpers\Json;

class PropertiesController extends BackendComponent
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
                case 'save-sort':
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

    /*
    public function actionView($id){
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }
    */

    public function actionCreate(){
        $model = new Properties();
        
        $department = Departments::findOne(1);

        if($model->load(Yii::$app->request->post())){
            if($model->parent_id != 0){
                $model->application_type_id = 0;
            }
            else{
                $model->scenario = "main_attribute";
            }

            if($model->save()){
                return $this->redirect(['/departments/update', 'id' => $department->id, 'tab' => 'properties']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'department' => $department,
        ]);
    }

    public function actionUpdate($id){
        $model = $this->findModel($id);

        $department = Departments::findOne(1);

        if($model->load(Yii::$app->request->post())){
            if($model->parent_id != 0){
                $model->application_type_id = 0;
            }
            else{
                $model->scenario = "main_attribute";
            }

            if($model->save()){
                return $this->redirect(['/departments/update', 'id' => $department->id, 'tab' => 'properties']);
            }
        }
        
        return $this->render('update', [
            'model' => $model,
            'department' => $department,
        ]);
    }

    /*public function actionSaveSort(){
        if(!Yii::$app->request->isAjax){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $arraySort = Yii::$app->request->post("arraySort");
        Attributes::saveSort($arraySort);
        return Json::encode(['status' => 'success']);
    }*/

    protected function findModel($id){
        if (($model = Properties::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
