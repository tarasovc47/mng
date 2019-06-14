<?php

namespace frontend\controllers;

use Yii;
use common\models\ContactFaces;
use common\models\ContactFacesPhones;
use common\models\ContactFacesEmails;
use common\models\search\ContactFacesSearch;
use frontend\components\FrontendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ContactFacesController extends FrontendComponent
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
        $publication_status = 0;
        if (isset($_GET["ContactFacesSearch"]['publication_status'])) {
            $publication_status = $_GET["ContactFacesSearch"]['publication_status'];
        }
        $searchModel = new ContactFacesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'publication_status' => $publication_status,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $model = new ContactFaces();

        if ($model->load(Yii::$app->request->post())) {
            $model->cas_user_id = $this->cas_user->id;
            $model->created_at = time();
            $model->publication_status = 1;
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
        $model->getRelatedValues();

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = time();
            $model->updater = $this->cas_user->id;
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } 
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionRemove($id)
    {
        $model = $this->findModel($id);
        $model->getRelatedValues();
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;
        $model->publication_status = 0;
        $model->save();

        return $this->redirect(['index', 'ContactFacesSearch[publication_status]' => 1]);
    }

    public function actionRecover($id)
    {
        $model = $this->findModel($id);
        $model->getRelatedValues();
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;
        $model->publication_status = 1;
        $model->save();

        return $this->redirect(['index', 'ContactFacesSearch[publication_status]' => 1]);
    }

    public function actionRemovePhone()
    {
        $id = Yii::$app->request->get('phone_id');
        $model_phones = $this->findModelPhones($id);
        if ($model_phones != '') {
            $model_phones->publication_status = 0;
            $model_phones->updated_at = time();
            $model_phones->updater = $this->cas_user->id;
            $model_phones->save();
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
        
        die();
    }

    public function actionRemoveEmail()
    {
        $id = Yii::$app->request->get('email_id');
        $model_emails = $this->findModelEmails($id);
        if ($model_emails != '') {
            $model_emails->publication_status = 0;
            $model_emails->updated_at = time();
            $model_emails->updater = $this->cas_user->id;
            $model_emails->save();
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
        
        die();
    }

    protected function findModel($id)
    {
        if (($model = ContactFaces::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelPhones($id)
    {
        if (($model = ContactFacesPhones::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findModelEmails($id)
    {
        if (($model = ContactFacesEmails::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
