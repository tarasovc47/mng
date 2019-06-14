<?php

namespace backend\modules\zones\controllers;

use Yii;
use common\models\ZonesAddressTypes;
use common\models\search\ZonesAddressTypesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

class AddressTypesController extends \backend\components\BackendComponent
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
        $searchModel = new ZonesAddressTypesSearch();
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

    public function actionCreate()
    {
        $model = new ZonesAddressTypes();

        if ($model->load(Yii::$app->request->post())) {
            $model->publication_status = 1;
            $model->cas_user_id = $this->cas_user->id;
            $model->created_at = time();
            if ($model->save()) {
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

        if ($model->load(Yii::$app->request->post())) {
            $model->updater = $this->cas_user->id;
            $model->updated_at = time();
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = ZonesAddressTypes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
