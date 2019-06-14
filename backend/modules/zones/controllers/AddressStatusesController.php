<?php

namespace backend\modules\zones\controllers;

use Yii;
use common\models\ZonesAddressStatuses;
use common\models\search\ZonesAddressStatusesSearch;
use backend\components\BackendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class AddressStatusesController extends BackendComponent
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new ZonesAddressStatusesSearch();
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
        $model = new ZonesAddressStatuses();

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
        if (($model = ZonesAddressStatuses::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
