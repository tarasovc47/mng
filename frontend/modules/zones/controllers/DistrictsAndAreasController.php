<?php

namespace frontend\modules\zones\controllers;

use Yii;
use common\models\ZonesDistrictsAndAreas;
use common\models\ZonesAddresses;
use common\models\search\ZonesDistrictsAndAreasSearch;
use frontend\components\FrontendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
use common\models\Access;
use yii\web\ForbiddenHttpException;

class DistrictsAndAreasController extends FrontendComponent
{
    public $permission;
    
    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }

        $this->permission = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 24); // 24 - id доступа к округам и районам

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
        $searchModel = new ZonesDistrictsAndAreasSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new ZonesDistrictsAndAreas();
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'types' => $model->types,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $addresses = array();
        if ($model->type == 2) {
            $addresses = ZonesAddresses::find()->where(['area_id' => $model->id])->asArray()->all();
            $addresses = ArrayHelper::map($addresses, 'id', 'address_uuid');
        }
        return $this->render('view', [
            'model' => $model,
            'types' => $model->types,
            'addresses' => $addresses,
        ]);
    }

    public function actionAddingAddresses($id)
    {
        if($this->permission != 2){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $modelArea = $this->findModel($id);
        $dynModel = new DynamicModel(['area_id', 'district_id', 'addresses']);

        $dynModel->addRule(['area_id', 'district_id'], 'required') 
                    ->addRule(['addresses'], 'required', ['message' => 'Необходимо выбрать хотя бы один адрес.']) 
                    ->addRule(['area_id', 'district_id'], 'integer')
                    ->addRule(['addresses'], 'safe');

        if ($dynModel->load(Yii::$app->request->post()) && $dynModel->validate()) {
            foreach ($dynModel->addresses as $key => $address) {
                $address = (int)$address;
                $addressModel = ZonesAddresses::findOne($address);
                if ($addressModel) {
                    $addressModel->district_id = $dynModel->district_id;
                    $addressModel->area_id = $dynModel->area_id;
                    $addressModel->scenario = 'without_related_values';
                    $addressModel->save();
                }
            }

            return $this->redirect(['view', 
                'id' => $modelArea->id,
            ]);
        }

        return $this->render(
                                'adding_addresses', 
                                [
                                    'modelArea' => $modelArea,
                                    'dynModel' => $dynModel,
                                ]
                            );
    }

    public function actionCreate()
    {
        if($this->permission != 2){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = new ZonesDistrictsAndAreas();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->type == 2) {
                $model->scenario = 'type_is_area';
            } else {
               $model->parent_id = -1; 
            }

            $model->created_at = time();
            $model->cas_user_id = $this->cas_user->id;
            $model->publication_status = 1;

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
        return $this->render('create', [
            'model' => $model,
            'types' => $model->types,
        ]);
    }

    public function actionUpdate($id)
    {
        if($this->permission != 2){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->type == 2) {
                $model->scenario = 'type_is_area';
            } else {
               $model->parent_id = -1; 
            }
            $model->updated_at = time();
            $model->updater = $this->cas_user->id;

            if ($model->save()) {   
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } 
        
        return $this->render('update', [
            'model' => $model,
            'types' => $model->types,
        ]);
        
    }

    protected function findModel($id)
    {
        if (($model = ZonesDistrictsAndAreas::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
