<?php

namespace backend\controllers;

use Yii;
use common\models\ContactsOffices;
use common\models\search\ContactsOfficesSearch;
use common\models\history\ContactsOfficesHistory;
use backend\components\BackendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

class ContactsOfficesController extends BackendComponent
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
        $publication_status = 0;
        if (isset($_GET["ContactsOfficesSearch"]['publication_status'])) {
            $publication_status = $_GET["ContactsOfficesSearch"]['publication_status'];
        }
        $searchModel = new ContactsOfficesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'publication_status' => $publication_status,
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
        $model = new ContactsOffices();

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
        if (($model = ContactsOffices::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionRemove(){
        $id = Yii::$app->request->get('id');
        $status = (int)!Yii::$app->request->get('current_status');

        $model = $this->findModel($id);
        $model->publication_status = $status;
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;
        $model->save();
        
        echo Json::encode('success');
        die();
    }
}
