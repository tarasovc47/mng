<?php

namespace backend\modules\techsup\controllers;

use Yii;
use common\models\TechsupScenarios;
use common\models\Attributes;
use common\models\search\TechsupScenariosSearch;
use backend\components\BackendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ScenariosController extends BackendComponent
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
        $searchModel = new TechsupScenariosSearch();
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
        $model = new TechsupScenarios();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $attributes = Attributes::getAllAttributesWithServicesAndConnTechs();

        return $this->render('create', [
            'model' => $model,
            'attributes' => $attributes,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $attributes = Attributes::getAllAttributesWithServicesAndConnTechs();

        return $this->render('update', [
            'model' => $model,
            'attributes' => $attributes,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = TechsupScenarios::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
