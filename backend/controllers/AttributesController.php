<?php

namespace backend\controllers;

use Yii;
use common\models\Attributes;
use common\models\Fields;
use common\models\Departments;
use yii\data\ActiveDataProvider;
use backend\components\BackendComponent;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\helpers\Json;

class AttributesController extends BackendComponent
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

    public function actionView($id){
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate(){
        $model = new Attributes();

        $department = false;
        if(Yii::$app->request->get("department")){
            $department = Departments::findOne(Yii::$app->request->get("department"));
        }

        if(!$department){
            throw new NotAcceptableHttpException('Не удалось определить отдел.');
        }

        $model->department_id = $department->id;
        
        if($model->load(Yii::$app->request->post())){
            if($model->parent_id != 0){
                $model->connection_technology_id = 0;
            }
            else{
                $model->scenario = "main_attribute";
            }

            if($model->save()){
                return $this->redirect(['/departments/update', 'id' => $model->department_id, 'tab' => 'attributes']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'department' => $department,
        ]);
    }

    public function actionUpdate($id){
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post())){
            if($model->parent_id != 0){
                $model->connection_technology_id = 0;
            }
            else{
                $model->scenario = 'main_attribute';
            }

            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSaveSort(){
        if(!Yii::$app->request->isAjax){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $arraySort = Yii::$app->request->post("arraySort");
        Attributes::saveSort($arraySort);
        return Json::encode(['status' => 'success']);
    }

    protected function findModel($id){
        if (($model = Attributes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
