<?php

namespace frontend\modules\zones\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use common\models\ZonesAddresses;
use common\models\search\ZonesAddressesSearch;
use common\models\ZonesAccessAgreements;
use common\models\ZonesDistrictsAndAreas;
use common\models\ConnectionTechnologies;
use common\models\ZonesPorches;
use common\models\ZonesFloors;
use common\models\ZonesFlats;
use common\models\Services;
use common\models\Operators;
use common\models\Tariffs;
use common\models\ManagCompaniesBranches;
use common\models\ManagCompaniesToContacts;
use common\models\TariffsGroups;
use frontend\components\FrontendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\SiteHelper;
use yii\data\Pagination;
use common\models\Access;
use yii\web\ForbiddenHttpException;


class ZonesAddressesController extends FrontendComponent
{
    public $permission;
    
    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }

        $this->permission = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 23); // 23 - id доступа к адресам

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
        $searchModel = new ZonesAddressesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->post());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'abonent_types' => Yii::$app->params['abonent_types'],
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->loadRelatedValues();
        $extra_data = $model->loadExtraDataForView();

        $object_scheme = array();
        $object_scheme['porches'] = ZonesPorches::getPorchesForAddress($model->id);
        foreach ($object_scheme['porches'] as $key_porch => $porch) {
            $object_scheme[$key_porch]['porch_name'] = $porch;
            $object_scheme[$key_porch]['flats_item'] = ZonesFlats::getFlatsForPorch($key_porch, 1);
            $object_scheme[$key_porch]['offices_item'] = ZonesFlats::getFlatsForPorch($key_porch, 2);
            $object_scheme[$key_porch]['floors_item'] = ZonesFloors::getFloorsForPorches($key_porch);

        }
        unset($object_scheme['porches']);

        return $this->render('view', [
            'model' => $model,
            'object_scheme' => $object_scheme,
            'extra_data' => $extra_data,
        ]);
    }

    public function actionCreate()
    {
        if($this->permission != 2){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = new ZonesAddresses();
        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->cas_user_id = $this->cas_user->id;
            $model->publication_status = 1;
            if ($model->save()) {
                return $this->redirect(['update', 'id' => $model->id, 'scheme' => true]);
            }
        } 
        
        $extra_data = $model->loadExtraDataForForm();

        $tariffs['individual'] = Json::decode($model->tariffs_individual, true);
        $tariffs['entity'] = Json::decode($model->tariffs_entity, true);

        return $this->render('create', [
            'model' => $model,
            'extra_data' => $extra_data,
            'tariffs' => $tariffs,
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
        $modelPorches = new ZonesPorches;
        $modelFloors = new ZonesFloors;
        $modelFlats = new ZonesFlats;
        
        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = time();
            $model->updater = $this->cas_user->id;
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } 

        $extra_data = $model->loadExtraDataForForm();

        $tariffs['individual'] = Json::decode($model->tariffs_individual, true);
        $tariffs['entity'] = Json::decode($model->tariffs_entity, true);

        return $this->render('update', [
            'model' => $model,
            'extra_data' => $extra_data,
            'modelPorches' => $modelPorches,
            'modelFloors' => $modelFloors,
            'modelFlats' => $modelFlats,
            'tariffs' => $tariffs,
        ]);
        
    }

    protected function findModel($id)
    {
        if (($model = ZonesAddresses::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetExtraDataForCompany(){
        if(!Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $company_id = Yii::$app->request->get('companyId');
        
        $data['agreements'] = ZonesAccessAgreements::getAccessAgreementsByCompany($company_id);
        $data['agreements'] = Html::renderSelectOptions(null, $data['agreements']);
        $data['branches'] = ManagCompaniesBranches::getBranchesList($company_id);

        $options = ['prompt' => ''];
        $data['branches'] = Html::renderSelectOptions(null, $data['branches'], $options);
        $contacts['УК'] = ManagCompaniesToContacts::getContactsForKeyKeeperList(0, $company_id);

        $options = ['prompt' => ''];
        $data['contacts'] = Html::renderSelectOptions(null, $contacts, $options);

        echo Json::encode($data);
        die();
    }

    public function actionGetContactsForKeyKeeper(){
        if(!Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $branch_id = Yii::$app->request->get('branch_id');
        $company_id = Yii::$app->request->get('company_id');
        $data['Участок'] = ManagCompaniesToContacts::getContactsForKeyKeeperList($branch_id);
        $data['УК'] = ManagCompaniesToContacts::getContactsForKeyKeeperList(0, $company_id);

        $options = ['prompt' => ''];
        $html = Html::renderSelectOptions(null, $data, $options);

        echo Json::encode($html);
        die();
    }

    public function actionGetAreasListByDistrict(){
        if(!Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $districtId = \Yii::$app->request->get('districtId');
        $areas = ZonesDistrictsAndAreas::getAreasListByDistrict($districtId);

        $options = ['prompt' => ''];
        $html = Html::renderSelectOptions(null, $areas, $options);

        echo Json::encode($html);
        die();
    }

    public function actionGetTechnologiesList(){
        if(!Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $without_html = Yii::$app->request->get('without_html');
        $services = Yii::$app->request->get('services');
        $selected_techs = Yii::$app->request->get('selected_techs');
        $techs = ConnectionTechnologies::getTechnologiesList($services, $without_html);
        if (!$without_html) {
            if (isset($techs) && !empty($techs)) {
                $html = Html::renderSelectOptions($selected_techs, $techs);
            } else {
                $html = '<option value=""></option>';
            }
            echo Json::encode($html);
        } else {
            echo Json::encode($techs);
        }
        die();
    }

    public function actionLoadTariffsGroupsList(){
        if(!Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $abonent_type = Yii::$app->request->post('abonent_type');
        $groups = TariffsGroups::find()->where(['abonent_type' => $abonent_type, 'publication_status' => 1])->all();

        $html = Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/__group_panel', 
            [
                'checked_list' => [],
                'groups_list' => $groups,
                'abonent_type' => $abonent_type,
            ]);
        return Json::encode($html);
    }

    public function actionCreatePorch()
    {
        if($this->permission != 2 || !Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $parent_div = Yii::$app->request->get('parent_div');
        $model = new ZonesPorches();
        $model->porch_name = Yii::$app->request->get('porch');
        $model->address_id = Yii::$app->request->get('addressId');
        $model->publication_status = 1;
        $model->created_at = time();
        $model->cas_user_id = $this->cas_user->id;

        if ($model->save()) {
            $html = Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/__porch', 
            [
                'porch_id' => $model->id,
                'porch_name' => $model->porch_name,
                'parent_div' => $parent_div,
            ]);
            echo Json::encode(['errors' => false, 'html' => $html]);
        } else {
            echo Json::encode(['errors' => $model->getErrors()]);
        }
   
        die();
    }

    public function actionCreateFloor()
    {
        if($this->permission != 2 || !Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = new ZonesFloors();
        $model->porch_id = Yii::$app->request->get('porchId');
        $model->floor_name = Yii::$app->request->get('floor');
        $model->publication_status = 1;
        $model->created_at = time();
        $model->cas_user_id = $this->cas_user->id;

        if ($model->save()) {
            echo Json::encode([
                'errors' => false, 
                'porch_id' => $model->porch_id, 
                'html' => Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/___floors', 
                [
                    'porch_id' => $model->porch_id,
                    'floors_item' => 
                    [
                        $model->id => $model->floor_name,
                    ]
                ])
            ]);
        } else {
            echo Json::encode(['errors' => $model->getErrors()]);
        }
   
        die();
    }

    public function actionCreateFloors()
    {
        if($this->permission != 2 || !Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = new ZonesFloors();
        $porch_id = Yii::$app->request->get('porchId');
        $floor_begin = Yii::$app->request->get('floorBegin');
        $floor_end = Yii::$app->request->get('floorEnd');
        $result = array();

        $error = false;
        $model->floor_name = $floor_begin;
        if (!($model->validate(['floor_name']))) {
            $error = true;
            $result['floor_begin_error'] = ['errors' => $model->getErrors()];
        }
        $model->floor_name = $floor_end;
        if (!($model->validate(['floor_name']))) {
            $error = true;
             $result['floor_end_error'] = ['errors' => $model->getErrors()];
        }

        if (!$error) {
            $floor_items = array();
            $time = time();
            for ($i = $floor_begin; $i <= $floor_end; $i++) { 
                $model = new ZonesFloors();
                $model->floor_name = $i;
                $model->porch_id = $porch_id;
                $model->publication_status = 1;
                $model->created_at = $time;
                $model->cas_user_id = $this->cas_user->id;
                if ($model->save()) {
                    $floor_items[$model->id] = $model->floor_name;
                } else {
                    $result[] = ['errors' => $model->getErrors()];
                }
            }
            if (!empty($floor_items)) {
                $result[] = 
                [
                    'errors' => false, 
                    'porch_id' => $model->porch_id, 
                    'html' => Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/___floors', 
                    [
                        'porch_id' => $model->porch_id,
                        'floors_item' => $floor_items,
                    ])
                ];
            }
        }

        echo Json::encode(array_reverse($result));
        die();
    }

    public function actionCreateFlat()
    {
        if($this->permission != 2 || !Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = new ZonesFlats();
        $model->floor_id = Yii::$app->request->get('floorId');
        $model->flat_name = Yii::$app->request->get('flat');
        $model->room_type = Yii::$app->request->get('roomType');
        $model->publication_status = 1;
        $model->created_at = time();
        $model->cas_user_id = $this->cas_user->id;

        if ($model->save()) {
            echo Json::encode([
                                'errors' => false, 
                                'floor_id' => $model->floor_id, 
                                'html' => Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/____flats_offices', 
                                [
                                    'flat' => $model->flat_name,
                                    'flat_id' => $model->id,
                                    'room_type' => $model->room_type,
                                ])
                            ]);
        } else {
            echo Json::encode(['errors' => $model->getErrors()]);
        }
   
        die();
    }

    public function actionCreateFlats()
    {
        if($this->permission != 2 || !Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = new ZonesFlats();
        $floor_id = Yii::$app->request->get('floorId');
        $room_type = Yii::$app->request->get('roomType');
        $flat_begin = Yii::$app->request->get('flatBegin');
        $flat_end = Yii::$app->request->get('flatEnd');
        $result = array();

        $error = false;
        $model->scenario = 'flat_range';
        $model->flat_name = $flat_begin;
        if (!($model->validate(['flat_name']))) {
            $error = true;
            $result['flat_begin_error'] = ['errors' => $model->getErrors()];
        }
        $model->flat_name = $flat_end;
        if (!($model->validate(['flat_name']))) {
            $error = true;
             $result['flat_end_error'] = ['errors' => $model->getErrors()];
        }

        if (!$error) {
            $time = time();
            while ($flat_begin <= $flat_end) {
                $model = new ZonesFlats();
                $model->flat_name = (string)$flat_begin;
                $model->floor_id = $floor_id;
                $model->room_type = $room_type;
                $model->publication_status = 1;
                $model->created_at = $time;
                $model->cas_user_id = $this->cas_user->id;
                if ($model->save()) {
                    $result[] = [
                                    'errors' => false, 
                                    'floor_id' => $model->floor_id, 
                                    'html' => Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/____flats_offices', 
                                    [
                                        'flat' => $model->flat_name,
                                        'flat_id' => $model->id,
                                        'room_type' => $model->room_type,
                                    ])
                                ];
                } else {
                    $result[] = ['errors' => $model->getErrors()];
                }
                $flat_begin++;
            }
        }

        echo Json::encode($result);
        die();
    }

    public function actionUpdateFlat()
    {
        if($this->permission != 2 || !Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $flat_id = Yii::$app->request->get('flat_id');
        $flat_name = Yii::$app->request->get('flat_name');

        $model = ZonesFlats::findOne($flat_id);
        $model->flat_name = $flat_name;
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;

        if ($model->save()) {
            echo Json::encode([
                                'errors' => false, 
                                'flat_id' => $model->id,
                                'html' => Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/____flats_offices', 
                                [
                                    'flat' => $model->flat_name,
                                    'flat_id' => $model->id,
                                    'room_type' => $model->room_type,
                                ])
                            ]);
        } else {
            echo Json::encode(['errors' => $model->getErrors()]);
        }
   
        die();
    }

    public function actionUpdateFloor()
    {
        if($this->permission != 2 || !Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $floor_id = Yii::$app->request->get('floor_id');
        $floor_name = Yii::$app->request->get('floor_name');

        $model = ZonesFloors::findOne($floor_id);
        $model->floor_name = $floor_name;
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;

        if ($model->save()) {
            echo Json::encode([
                                'errors' => false, 
                                'floor_id' => $model->id,
                                'floor_name' => $floor_name,
                            ]);
        } else {
            echo Json::encode(['errors' => $model->getErrors()]);
        }
   
        die();
    }

    public function actionRemoveFloor()
    {
        if($this->permission != 2 || !Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $floor_id = Yii::$app->request->get('floor_id');

        $model = ZonesFloors::findOne($floor_id);
        $model->publication_status = 0;
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;

        if ($model->save()) {
            echo Json::encode([
                                'errors' => false
                            ]);
        } else {
            echo Json::encode(['errors' => $model->getErrors()]);
        }
   
        die();
    }

    public function actionUpdatePorch()
    {
        if($this->permission != 2 || !Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $porch_id = Yii::$app->request->get('porch_id');
        $porch_name = Yii::$app->request->get('porch_name');

        $model = ZonesPorches::findOne($porch_id);
        $model->porch_name = $porch_name;
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;

        if ($model->save()) {
            echo Json::encode([
                                'errors' => false, 
                                'porch_id' => $model->id,
                                'porch_name' => $porch_name,
                            ]);
        } else {
            echo Json::encode(['errors' => $model->getErrors()]);
        }
   
        die();
    }

    public function actionRemovePorch()
    {
        if($this->permission != 2 || !Yii::$app->request->isAjax){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $porch_id = Yii::$app->request->get('porch_id');

        $model = ZonesPorches::findOne($porch_id);
        $model->publication_status = 0;
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;

        if ($model->save()) {
            echo Json::encode([
                                'errors' => false,
                            ]);
        } else {
            echo Json::encode(['errors' => $model->getErrors()]);
        }
   
        die();
    }

    public function actionCreateTariffItem()
    {
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $conn_tech = Yii::$app->request->get('conn_tech');
        $abonent_type = Yii::$app->request->get('abonent_type');
        $except_tariffs = Yii::$app->request->get('except_tariffs');
        $public_tariff = (int)Yii::$app->request->get('public_tariff');

        $tariffs_list = Tariffs::getTariffsListByTechnologies($conn_tech, $abonent_type, $public_tariff, $except_tariffs);

        $html = Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/___tariff_panel', [
                'tariffs_list' => $tariffs_list,
                'abonent_type' => $abonent_type,
                'checked_list' => [],
            ]);

        echo Json::encode($html);
        die();
    } 

    public function actionCreateConnTechToggleItem()
    {
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $conn_tech = Yii::$app->request->get('conn_tech');
        $abonent_type = Yii::$app->request->get('abonent_type');
        $checked = Yii::$app->request->get('checked');
        $except_tariffs = Yii::$app->request->get('except_tariffs');
        $conn_tech = ConnectionTechnologies::findOne($conn_tech);
        $html['toggles'] = Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/___conn_tech_checkbox', [
                'conn_tech' => $conn_tech,
                'abonent_type' => $abonent_type,
                'checked' => $checked,
            ]);
        $tariffs_list = Tariffs::getTariffsListByTechnologies($conn_tech->id, $abonent_type, 0, $except_tariffs);

        $html['not_public_tariffs'] = Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/___tariff_panel', [
                'tariffs_list' => $tariffs_list,
                'abonent_type' => $abonent_type,
                'checked_list' => [],
            ]);

        echo Json::encode($html);
        die();
    }

    public function actionGetTariffsId()
    {
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $conn_techs = Yii::$app->request->get('conn_techs');
        $abonent_type_id = Yii::$app->request->get('abonent_type_id');
        $public_tariff = (int)Yii::$app->request->get('public_tariff');

        if ($public_tariff == 2) {
            $tariffs_id['public'] = Tariffs::getTariffsListByTechnologies($conn_techs, $abonent_type_id, 1);
            $tariffs_id['not_public'] = Tariffs::getTariffsListByTechnologies($conn_techs, $abonent_type_id, 0);
            $tariffs_id['public'] = ArrayHelper::getColumn($tariffs_id['public'], 'id');
            $tariffs_id['not_public'] = ArrayHelper::getColumn($tariffs_id['not_public'], 'id');
        } else {
            $tariffs_id = Tariffs::getTariffsListByTechnologies($conn_techs, $abonent_type_id, $public_tariff);
            $tariffs_id = ArrayHelper::getColumn($tariffs_id, 'id');
        }

        echo Json::encode($tariffs_id);
        die();
    } 

    public function actionMassCreate()
    {
        if($this->permission != 2){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = new ZonesAddresses();
        $model->scenario = 'mass_create';

        $addresses_stack = array();
        $addresses_stack = $model->addresses_stack;
        if (isset($addresses_stack) && !empty($addresses_stack)) {
            $model->addresses_stack = implode(';', $addresses_stack);
        }

        if ($model->load(Yii::$app->request->post())) {
            $addresses = $model;
            $addresses->created_at = time();
            $addresses->cas_user_id = $this->cas_user->id;
            $addresses->publication_status = 1;  

            if ($addresses->validate()) {
                $addresses->addresses_stack = trim($addresses->addresses_stack);
                $addresses->addresses_stack = explode(';', $addresses->addresses_stack);
                foreach ($addresses->addresses_stack as $uuid) {
                    if (!empty($uuid)) {
                        $address = ZonesAddresses::findOne(['address_uuid' => $uuid]);
                        if (!$address) {
                            $address = new ZonesAddresses();
                            $address->setAttributes($addresses->getAttributes());
                            $address->setAttributes($addresses->getAttributes(['access_agreements', 'opers', 'services_individual', 'services_entity', 'conn_techs_individual', 'conn_techs_entity', 'tariffs_individual', 'tariffs_entity']));
                            $address->address_uuid = $uuid;
                            $address->save();
                        }
                    }
                }
                $model->addresses_stack = '';
                $extra_data = $model->loadExtraDataForForm();
                return $this->render('mass_create', [
                    'model' => $model,
                    'extra_data' => $extra_data,
                    'addresses_stack' => $addresses_stack,
                    'message' => 'Предыдущий список адресов успешно сохранён.',
                ]);
            }
        }

        $extra_data = $model->loadExtraDataForForm();

        return $this->render('mass_create', [
            'model' => $model,
            'extra_data' => $extra_data,
            'addresses_stack' => $addresses_stack,
        ]);
        
    }

    public function actionLoadTariffModalBody(){
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $tariff_id = Yii::$app->request->get('tariff_id');
        $model = Tariffs::findOne($tariff_id);
        $extra_data = $model->loadExtraDataForView();
        $html = Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/_tariffs_view_modal_content', [
                'model' => $model,
                'extra_data' => $extra_data,
            ]);

        echo Json::encode($html);
        die();
    }

    public function actionLoadAddressesListTable(){
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $uuids = Yii::$app->request->post('uuid_list');
        $uuids = str_replace(['{', '}'], '', $uuids);
        $uuids = explode(',', $uuids);
        $count_uuid = count($uuids);
        $page = Yii::$app->request->post('page');
        $checked_list = Yii::$app->request->post('checked_list');
        $checked_list = explode(';', $checked_list, -1);

        $uuids = array_chunk($uuids, 30);
        $uuid_list = $uuids[$page-1];
        

        if (isset($uuid_list) && !empty($uuid_list)) {
            $html = Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/__addresses_list_table', [
                'uuid_list' => $uuid_list,
                'count_uuid' => $count_uuid,
                'page' => $page,
                'checked_list' => $checked_list,
            ]);
            echo Json::encode($html);
        } else {
            echo Json::encode('error');
        }
        
        die();
    }

    public function actionLoadAddressesListChosenRow(){
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $uuid = Yii::$app->request->post('uuid');        

        if (isset($uuid) && !empty($uuid)) {
            $html = Yii::$app->controller->renderPartial('@frontend/modules/zones/views/zones-addresses/___addresses_list_chosen_row', [
                'uuid' => $uuid,
            ]);
            echo Json::encode($html);
        } else {
            echo Json::encode('error');
        }
        
        die();
    }
}
