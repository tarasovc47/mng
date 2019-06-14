<?php

namespace frontend\modules\tariffs\controllers;

use Yii;
use common\models\TariffsGroups;
use common\models\search\TariffsGroupsSearch;
use frontend\components\FrontendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Access;
use yii\web\ForbiddenHttpException;
use common\models\Tariffs;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class TariffsGroupsController extends FrontendComponent
{
	public $permission;
    
    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }

        $this->permission = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 26); // 26 - id доступа к тарифным планам

        if(!$this->permission){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        return true;
    }

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
        $searchModel = new TariffsGroupsSearch();
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
    	if($this->permission != 2){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = new TariffsGroups();

        if ($model->load(Yii::$app->request->post())) {
            $model->publication_status = 1;
            $model->created_at = time();
            $model->cas_user_id = $this->cas_user->id;

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } 

        $tariffs_list = [];
        if ($model->abonent_type != '') {
            $tariffs_list = Tariffs::find()->where("for_abonent_type = ".$model->abonent_type." AND (closed_at IS NULL OR closed_at > ".time().")")->asArray()->all();
            $tariffs_list = ArrayHelper::map($tariffs_list, 'id', 'name');
        }

        return $this->render('create', [
            'model' => $model,
            'tariffs_list' => $tariffs_list,
        ]);
    }

    public function actionUpdate($id)
    {
    	if($this->permission != 2){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = $this->findModel($id);
        $model->loadRelatedValues();

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = time();
            $model->updater = $this->cas_user->id;

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        if ($model->abonent_type != '') {
            $tariffs_list = Tariffs::find()->where("for_abonent_type = ".$model->abonent_type." AND (closed_at IS NULL OR closed_at > ".time().")")->asArray()->all();
            $tariffs_list = ArrayHelper::map($tariffs_list, 'id', 'name');
        }

        return $this->render('update', [
            'model' => $model,
            'tariffs_list' => $tariffs_list,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = TariffsGroups::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionLoadTariffsList(){
        $abonent_type = Yii::$app->request->post('abonent_type');

        $data = Tariffs::find()->where("for_abonent_type = ".$abonent_type." AND (closed_at IS NULL OR closed_at > ".time().")")->asArray()->all();
        $data = ArrayHelper::map($data, 'id', 'name');
        $data = Html::renderSelectOptions(null, $data);

        return Json::encode($data);
    }
}
