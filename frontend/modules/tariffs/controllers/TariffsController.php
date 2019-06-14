<?php

namespace frontend\modules\tariffs\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use common\models\Tariffs;
use common\models\TariffsToBillingTariffs;
use common\models\ConnectionTechnologies;
use common\models\ZonesAddresses;
use common\models\search\TariffsSearch;
use frontend\components\FrontendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Access;
use yii\web\ForbiddenHttpException;

class TariffsController extends FrontendComponent
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

    public function actionIndex()
    {
        $searchModel = new TariffsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->post());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $extra_data = $model->loadExtraDataForView();
        if (isset($model->billingtariffs) && !empty($model->billingtariffs)) {
            $model->billing_id = TariffsToBillingTariffs::getBillingIdsListById($model->id, 1);
        }
        $abon_types = Yii::$app->params['abonent_types'];
        
        return $this->render('view', [
            'model' => $model,
            'abon_types' => $abon_types,
            'extra_data' => $extra_data,
        ]);
    }

    public function actionCreate()
    {
    	if($this->permission != 2){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = new Tariffs();        
        $package = Yii::$app->request->get('package');
        $model->package = (int)$package;
        $model->scenario = $model->package ? 'package_tariff' : 'non_package_tariff';

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->cas_user_id = $this->cas_user->id;
            $model->rewriteDates();
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $extra_data = $model->getExtraDataForForm();

        return $this->render('create', [
            'model' => $model,
            'extra_data' => $extra_data,
            'package' => $package,
        ]);
    }

    public function actionUpdate($id)
    {
    	if($this->permission != 2){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = $this->findModel($id);
        $package = Yii::$app->request->get('package');
        $model->package = (int)$package;
        $model->scenario = $model->package ? 'package_tariff' : 'non_package_tariff';
        $model->getRelatedValues();

        if ($model->load(Yii::$app->request->post())) {
            $model->rewriteDates();
            $model->updated_at = time();
            $model->updater = $this->cas_user->id;
            if ($model->save()) { 
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } 

        $extra_data = $model->getExtraDataForForm();

        return $this->render('update', [
            'model' => $model,
            'extra_data' => $extra_data,
            'package' => $package,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Tariffs::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetExtraDataForForm(){
    	if($this->permission != 2 || !Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $services = Yii::$app->request->get('services');
        $techs = Yii::$app->request->get('techs');
        $tariffs = Yii::$app->request->get('tariffs');
        $data['tariffs'] = TariffsToBillingTariffs::getTariffsFromBillingByServices($services);
        $data['techs'] = ConnectionTechnologies::getTechnologiesList($services);

        $options = ['prompt' => ''];
        $data['tariffs'] = Html::renderSelectOptions($tariffs, $data['tariffs'], $options);
        $data['techs'] = Html::renderSelectOptions($techs, $data['techs']);

        echo Json::encode($data);
        die();
    }

    public function actionRemove($id)
    {
    	if($this->permission != 2){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = $this->findModel($id);
        $model->getRelatedValues();
        $model->scenario = 'remove';
        $model->closed_at = time();
        $model->updated_at = $model->closed_at;
        $model->updater = $this->cas_user->id;
        $model->save();

        return $this->redirect(['index']);
    }
}
