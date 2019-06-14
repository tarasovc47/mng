<?php

namespace backend\modules\techsup\controllers;

use Yii;
use common\models\ApplicationsStatuses;
use common\models\Fields;
use common\models\search\ApplicationsStatusesSearch;
use backend\components\BackendComponent;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class ApplicationsStatusesController extends BackendComponent
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
        $searchModel = new ApplicationsStatusesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        $fieldsDataProvider = new ActiveDataProvider([
            'query' => Fields::find()->where(["target_id" => $model->id, "target_table" => ApplicationsStatuses::tableName()]),
        ]);
        $fields = $this->renderPartial('@backend/modules/techsup/views/fields/_tab-content', [
            'dataProvider' => $fieldsDataProvider,
            'target' => $model,
            'table' => ApplicationsStatuses::tableName(),
        ]);

        return $this->render('view', [
            'model' => $model,
            'fields' => $fields,
        ]);
    }

    public function actionCreate()
    {
        $model = new ApplicationsStatuses();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    protected function findModel($id)
    {
        if (($model = ApplicationsStatuses::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
