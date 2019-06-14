<?php

namespace common\models;

use Yii;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use common\components\SiteHelper;

use common\models\ZonesDistrictsAndAreas;
use common\models\ZonesAddressStatuses;
use common\models\ManagCompanies;
use common\models\ZonesAccessAgreements;
use common\models\Operators;
use common\models\Services;
use common\models\Tariffs;
use common\models\ConnectionTechnologies;
use common\models\ZonesPorches;
use common\models\ZonesFloors;
use common\models\ZonesFlats;
use common\models\ZonesAddressTypes;
use common\models\ManagCompaniesToContacts;
use common\models\ZonesAddressesToTariffs;
use common\models\ZonesAddressesToConnectionTechnologies;
use common\models\ZonesAddressesToAgreements;
use common\models\ZonesAddressesToOpers;
use common\models\ZonesAddressesToServices;
use common\models\ZonesAddressesToTariffsGroups;
use common\models\history\ZonesAddressesHistory;
use yii\db\Query;

class ZonesAddresses extends \yii\db\ActiveRecord
{
    // ВНИМАНИЕ!!!!!!! При добавлении нового публичного свойства, которое необходимо при сохранении модели в бд - обязательно добавить его в actionMassUpdate контроллера в метод getAttributes()
    public $old_attributes = array();
    public $tariffs_required = array();  
    public $access_agreements;
    public $opers;
    public $services_individual;
    public $services_entity;
    public $conn_techs_individual;
    public $conn_techs_entity;
    public $tariffs_individual;
    public $tariffs_entity;
    public $cas_login;
    public $without_related_values = false;
    public $updated_at;
    public $updater;
    public $addresses_stack;

    public function beforeValidate(){
        if (empty($this->tariffs_required)) {
            $connection = Yii::$app->db;
            $this->tariffs_required = $connection
                                ->createCommand("SELECT id, tariffs_required FROM zones__address_statuses")
                                ->queryAll();

            $this->tariffs_required = ArrayHelper::map($this->tariffs_required, 'id', 'tariffs_required');
        }

        $tariffs = Json::decode($this->tariffs_individual, true);
        if (!empty($tariffs['auto_tariffs'])) {
            foreach ($tariffs['auto_tariffs'] as $key => $conn_tech) {
                $tariffs['auto_tariffs'][$key] = (int)$conn_tech;
            }
        }
        

        if (!empty($tariffs['manual_tariffs'])) {
            foreach ($tariffs['manual_tariffs'] as $key => $tariff) {
                $tariffs['manual_tariffs'][$key] = (int)$tariff;
            }
        }
        $this->tariffs_individual = Json::encode($tariffs, JSON_FORCE_OBJECT);

        return parent::beforeValidate();
    }

    public function afterSave($insert, $changedAttributes)
    {
        $tariffs_individual = Json::decode($this->tariffs_individual, true);
        $tariffs_entity = Json::decode($this->tariffs_entity, true);

        $this->processingRelations($this->access_agreements, ZonesAddressesToAgreements::className(), 'loadAgreementsListForAddress', 'agreement_id');

        $this->processingRelations($this->opers, ZonesAddressesToOpers::className(), 'loadOpersForAddress', 'oper_id');

        $this->processingRelations($this->services_individual, ZonesAddressesToServices::className(), 'loadServicesListForAddress', 'service_id', ['abonent_type' => 1]);
        $this->processingRelations($this->services_entity, ZonesAddressesToServices::className(), 'loadServicesListForAddress', 'service_id', ['abonent_type' => 2]);

        $this->processingRelations($this->conn_techs_individual, ZonesAddressesToConnectionTechnologies::className(), 'loadTechsForAddress', 'connection_technology_id', ['abonent_type' => 1]);
        $this->processingRelations($this->conn_techs_entity, ZonesAddressesToConnectionTechnologies::className(), 'loadTechsForAddress', 'connection_technology_id', ['abonent_type' => 2]);

        $this->processingRelations($tariffs_individual['manual_tariffs'], ZonesAddressesToTariffs::className(), 'loadManualTariffsForAddress', 'tariff_id', ['abonent_type' => 1]);
        $this->processingRelations($tariffs_entity['manual_tariffs'], ZonesAddressesToTariffs::className(), 'loadManualTariffsForAddress', 'tariff_id', ['abonent_type' => 2]);

        $this->processingRelations($tariffs_individual['groups'], ZonesAddressesToTariffsGroups::className(), 'loadGroupsListForAddress', 'tariffs_group_id', ['abonent_type' => 1]);
        $this->processingRelations($tariffs_entity['groups'], ZonesAddressesToTariffsGroups::className(), 'loadGroupsListForAddress', 'tariffs_group_id', ['abonent_type' => 2]);


        $history = new ZonesAddressesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
       
        parent::afterSave($insert, $changedAttributes);
    }

    public static function tableName()
    {
        return 'zones__addresses';
    }

    public function rules()
    {
        return [
            [['address_uuid'], 'unique', 'message' => 'Значение поля «Адрес» уже занято.'],
            [['address_uuid'], 'required', 'except' => ['mass_create']],
            [['addresses_stack'], 'required', 'on' => ['mass_create'], 'message' => 'Необходимо выбрать хотя бы один адрес'],
            [['contract_with_manag_company', 'cas_user_id', 'created_at', 'publication_status'], 'required'],
            [['all_flats', 'all_offices', 'address_type_id', 'district_id', 'area_id', 'build_status_individual', 'build_status_entity', 'manag_company_id', 'manag_company_branch_id', 'key_keeper', 'cas_user_id', 'created_at', 'contract_with_manag_company', 'publication_status', 'updater', 'updated_at'], 'integer'],
            [['address_uuid', 'comment', 'coordinates', 'connection_cost_individual', 'connection_cost_entity', 'addresses_stack'], 'string'],
            [['address_uuid', 'comment', 'coordinates', 'connection_cost_individual', 'connection_cost_entity', 'addresses_stack'], 'trim'],
            [['access_agreements', 'opers', 'services_individual', 'services_entity', 'conn_techs_individual', 'conn_techs_entity'], 'each', 'rule' => ['integer']],

            [['services_individual'], 'validateServicesIndividual', 'skipOnError' => false, 'skipOnEmpty' => false, 'except' => ['without_related_values']],
            [['services_entity'], 'validateServicesEntity', 'skipOnError' => false, 'skipOnEmpty' => false, 'except' => ['without_related_values']],

            [['conn_techs_individual'], 'validateConnTechsIndividual', 'skipOnError' => false, 'skipOnEmpty' => false, 'except' => ['without_related_values']],
            [['conn_techs_entity'], 'validateConnTechsEntity', 'skipOnError' => false, 'skipOnEmpty' => false, 'except' => ['without_related_values']],

            [['tariffs_individual'], 'validateTriffsIndividual', 'skipOnError' => false, 'skipOnEmpty' => false, 'except' => ['without_related_values']],
            [['tariffs_entity'], 'validateTriffsEntity', 'skipOnError' => false, 'skipOnEmpty' => false, 'except' => ['without_related_values']],

            [['build_status_individual'], 'required', 'message' => 'Необходимо заполнить «Статус объекта» для физических лиц.'],
            [['build_status_entity'], 'required', 'message' => 'Необходимо заполнить «Статус объекта» для юридических лиц.'],
            
        ];
    }

    // валидация сервисов физ лиц
    public function validateServicesIndividual($attribute, $params){
        if ($this->build_status_individual != '') {
            if ($this->tariffs_required[$this->build_status_individual] == 1 && empty($this->services_individual)) {
                if ($this->build_status_entity != '') {
                    if ($this->tariffs_required[$this->build_status_entity] == 1 && empty($this->services_entity)) {
                        $this->addError($attribute, 'Необходимо заполнить «Сервис» для физических или юридических лиц.');
                    } 
                    if ($this->tariffs_required[$this->build_status_entity] == 0) {
                        $this->addError($attribute, 'Необходимо заполнить «Сервис» для физических лиц.');
                    }
                }
            }
        }
        
    }

    // валидация сервисов юр лиц
    public function validateServicesEntity($attribute, $params){
        if ($this->build_status_entity != '') {
            if ($this->tariffs_required[$this->build_status_entity] == 1 && empty($this->services_entity)) {
                if ($this->build_status_individual != '') {
                    if ($this->tariffs_required[$this->build_status_individual] == 1 && empty($this->services_individual)) {
                        $this->addError($attribute, 'Необходимо заполнить «Сервис» для физических или юридических лиц.');
                    }
                    if ($this->tariffs_required[$this->build_status_individual] == 0) {
                        $this->addError($attribute, 'Необходимо заполнить «Сервис» для юридических лиц.');
                    }
                }
            }
        }
    }

    // валидация технологий подключения физ лиц
    public function validateConnTechsIndividual($attribute, $params){
        if ($this->build_status_individual != '') {
            if ($this->tariffs_required[$this->build_status_individual] == 1 && empty($this->conn_techs_individual)) {
                if ($this->build_status_entity != '') {
                    if ($this->tariffs_required[$this->build_status_entity] == 1 && empty($this->conn_techs_entity)) {
                        $this->addError($attribute, 'Необходимо заполнить «Технологии подключения» для физических или юридических лиц.');
                    } 
                    if ($this->tariffs_required[$this->build_status_entity] == 0) {
                        $this->addError($attribute, 'Необходимо заполнить «Технологии подключения» для физических лиц.');
                    }
                }
            }
        }
    }

    // валидация технологий подключения юр лиц
    public function validateConnTechsEntity($attribute, $params){
        if ($this->build_status_entity != '') {
            if ($this->tariffs_required[$this->build_status_entity] == 1 && empty($this->conn_techs_entity)) {
                if ($this->build_status_individual != '') {
                    if ($this->tariffs_required[$this->build_status_individual] == 1 && empty($this->conn_techs_individual)) {
                        $this->addError($attribute, 'Необходимо заполнить «Технологии подключения» для физических или юридических лиц.');
                    }
                    if ($this->tariffs_required[$this->build_status_individual] == 0) {
                        $this->addError($attribute, 'Необходимо заполнить «Технологии подключения» для юридических лиц.');
                    }
                }
            }
        }
    }

    // валидация тарифов физ лиц
    public function validateTriffsIndividual($attribute, $params){
        if ($this->build_status_individual != '') {
            $this->tariffs_individual = json_decode($this->tariffs_individual, true);

            if ($this->tariffs_required[$this->build_status_individual] == 1 && (empty($this->tariffs_individual['auto_tariffs']) && empty($this->tariffs_individual['manual_tariffs']))) {
                if ($this->build_status_entity != '') {
                    if ($this->tariffs_required[$this->build_status_entity] == 1 && empty($this->tariffs_entity)) {
                        $this->addError($attribute, 'Необходимо заполнить «Тарифные планы» для физических или юридических лиц.');
                    } 
                    if ($this->tariffs_required[$this->build_status_entity] == 0) {
                        $this->addError($attribute, 'Необходимо заполнить «Тарифные планы» для физических лиц.');
                    }
                }


            }

            if (!empty($this->tariffs_individual['auto_tariffs'])) {
                foreach ($this->tariffs_individual['auto_tariffs'] as $key => $conn_tech) {
                    if (!ConnectionTechnologies::findOne($conn_tech)) {
                        nset($this->tariffs_individual['auto_tariffs'][$key]);
                        $this->addError($attribute, 'Значение «Автоматически подключать все активные тарифные планы» для физических лиц неверно.');
                        break;
                    }
                }
            }
            
            if (!empty($this->tariffs_individual['manual_tariffs'])) {
                foreach ($this->tariffs_individual['manual_tariffs'] as $key => $tariff) {
                    if (!Tariffs::findOne($tariff)) {
                        unset($this->tariffs_individual['manual_tariffs'][$key]);
                        $this->addError($attribute, 'Значение «Выбрать тарифные планы вручную» для физических лиц неверно.');
                        break;
                    }
                }
            }
            $this->tariffs_individual = json_encode($this->tariffs_individual, JSON_FORCE_OBJECT);
        } 
    }

    // валидация тарифов юр лиц
    public function validateTriffsEntity($attribute, $params){
        if ($this->build_status_entity != '') {
            if ($this->tariffs_required[$this->build_status_entity] == 1 && (empty($this->tariffs_entity['auto_tariffs']) || empty($this->tariffs_entity['manual_tariffs']))) {
                if ($this->build_status_individual != '') {
                    if ($this->tariffs_required[$this->build_status_individual] == 1 && empty($this->tariffs_individual)) {
                        $this->addError($attribute, 'Необходимо заполнить «Тарифные планы» для физических или юридических лиц.');
                    } 
                    if ($this->tariffs_required[$this->build_status_individual] == 0) {
                        $this->addError($attribute, 'Необходимо заполнить «Тарифные планы» для физических лиц.');
                    }
                }

                $this->tariffs_entity = json_decode($this->tariffs_entity, true);
                if (!empty($this->tariffs_entity['auto_tariffs'])) {
                    foreach ($this->tariffs_entity['auto_tariffs'] as $key => $conn_tech) {
                        if (!ConnectionTechnologies::findOne($conn_tech)) {
                            unset($this->tariffs_entity['auto_tariffs'][$key]);
                            $this->addError($attribute, 'Значение «Автоматически подключать все активные тарифные планы» для юридических лиц неверно.');
                            break;
                        }
                    }
                }

                if (!empty($this->tariffs_entity['manual_tariffs'])) {
                    foreach ($this->tariffs_entity['manual_tariffs'] as $key => $tariff) {
                        if (!Tariffs::findOne($tariff)) {
                            unset($this->tariffs_entity['manual_tariffs'][$key]);
                            $this->addError($attribute, 'Значение «Выбрать тарифные планы вручную» для юридических лиц неверно.');
                            break;
                        }
                    }
                }
                $this->tariffs_entity = json_encode($this->tariffs_entity, JSON_FORCE_OBJECT);
            } 
        } 
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_uuid' => 'Адрес',
            'all_flats' => 'Общее количество квартир',
            'all_offices' => 'Общее количество офисов',
            'coordinates' => 'Координаты',
            'address_type_id' => 'Тип',
            'comment' => 'Примечания',
            'district_id' => 'Округ',
            'area_id' => 'Район',
            'build_status_individual' => 'Статус объекта',
            'build_status_entity' => 'Статус объекта',
            'manag_company_id' => 'Компания, предоставляющая доступ',
            'access_agreements' => 'Договоры доступа',
            'opers' => 'Операторы',
            'services_individual' => 'Сервисы',
            'services_entity' => 'Сервисы',
            'conn_techs_individual' => 'Технологии подключения',
            'conn_techs_entity' => 'Технологии подключения',
            'tariffs_individual' => 'Тарифные планы',
            'tariffs_entity' => 'Тарифные планы',
            'connection_cost_individual' => 'Стоимость подключения',
            'connection_cost_entity' => 'Стоимость подключения',
            'manag_company_branch_id' => 'Участок',
            'key_keeper' => 'У кого брать ключи',
            'contract_with_manag_company' => 'Заключён договор с управлющей компанией',
        ];
    }

    public function getAddressesToTariffs()
    {
        return $this->hasMany(ZonesAddressesToTariffs::className(), ['address_id' => 'id'])->
       andWhere(['publication_status' => 1]);
    }

    public function getAddressesToAgreements()
    {
        return $this->hasMany(ZonesAddressesToAgreements::className(), ['address_id' => 'id'])->
       andWhere(['publication_status' => 1]);
    }

    public function getAddressesToConnTechs()
    {
        return $this->hasMany(ZonesAddressesToConnectionTechnologies::className(), ['address_id' => 'id'])->
       andWhere(['publication_status' => 1]);
    }

    public function getAddressesToOpers()
    {
        return $this->hasMany(ZonesAddressesToOpers::className(), ['address_id' => 'id'])->
       andWhere(['publication_status' => 1]);
    }

    public function getDistrict()
    {
        return $this->hasOne(ZonesDistrictsAndAreas::className(), ['id' => 'district_id'])->
       andWhere(['publication_status' => 1, 'type' => 1]);
    }

    public function getArea()
    {
        return $this->hasOne(ZonesDistrictsAndAreas::className(), ['id' => 'area_id'])->
       andWhere(['publication_status' => 1, 'type' => 2]);
    }

    // получения значений для алреса из всех связанных таблиц при редактировании
    public function loadRelatedValues()
    {
        $this->opers = ZonesAddressesToOpers::loadOpersForAddress($this->id, 1);
        $this->access_agreements = ZonesAddressesToAgreements::loadAgreementsListForAddress($this->id, 1);
        $this->services_individual = ZonesAddressesToServices::loadServicesListForAddress($this->id, 1, 1);
        $this->services_entity = ZonesAddressesToServices::loadServicesListForAddress($this->id, 2, 1);
        $this->conn_techs_individual = ZonesAddressesToConnectionTechnologies::loadTechsForAddress($this->id, 1, 1);
        $this->conn_techs_entity = ZonesAddressesToConnectionTechnologies::loadTechsForAddress($this->id, 2, 1);
        $this->tariffs_individual = ZonesAddressesToTariffs::loadTariffsListForAddress($this->id, $this->conn_techs_individual, 1);
        $this->tariffs_entity = ZonesAddressesToTariffs::loadTariffsListForAddress($this->id, $this->conn_techs_entity, 2);

        return true;
    }

    // получение данных для форм (списки районов, компаний и т.д.)
    public function loadExtraDataForForm()
    {
        $extra_data = array();
        $extra_data['districtsList'] = ZonesDistrictsAndAreas::getDistrictList();
        unset($extra_data['districtsList'][-1]);

        $extra_data['areasList'] = array();
        if ($this->district_id != '') {
            $extra_data['areasList'] = ZonesDistrictsAndAreas::getAreasListByDistrict($this->district_id);
        }

        $extra_data['addressTypesList'] = ZonesAddressTypes::getAddressTypesList();
        $extra_data['statusesList'] = ZonesAddressStatuses::getStatusesList();
        $extra_data['companiesList'] = ManagCompanies::getActiveCompaniesList();

        $extra_data['agreementsList'] = array();
        $extra_data['companyBranchesList'] = array();
        $extra_data['keyKeeperList'] = array();
        if ($this->manag_company_id != '') {
            $extra_data['agreementsList'] = ZonesAccessAgreements::getAccessAgreementsByCompany($this->manag_company_id);
            $extra_data['companyBranchesList'] = ManagCompaniesBranches::getBranchesList($this->manag_company_id);
            $extra_data['keyKeeperList']['УК'] = ManagCompaniesToContacts::getContactsForKeyKeeperList(0, $this->manag_company_id);

            if ($this->manag_company_branch_id != '') {
                $extra_data['keyKeeperList']['Участок'] = ManagCompaniesToContacts::getContactsForKeyKeeperList($this->manag_company_branch_id);
            } 
        }

        $extra_data['connTechsIndividualList'] = array();
        $extra_data['tariffs_list_individual_public'] = array();
        $extra_data['tariffs_list_individual_not_public'] = array();
        if (isset($this->services_individual) && !empty($this->services_individual)) {
            $extra_data['connTechsIndividualList'] = ConnectionTechnologies::getTechnologiesList($this->services_individual);
            $non_auto_techs_list = array_diff($this->conn_techs_individual, Json::decode($this->tariffs_individual, true)['auto_tariffs']);
            $extra_data['tariffs_list_individual_public'] = Tariffs::getTariffsListByTechnologies($non_auto_techs_list, 1, 1);
            $extra_data['tariffs_list_individual_not_public'] = Tariffs::getTariffsListByTechnologies($this->conn_techs_individual, 1, 0);
            $extra_data['tariffs_list_individual_groups'] = TariffsGroups::find()->where(['publication_status' => 1, 'abonent_type' => 1])->all();
        }

        $extra_data['connTechsEntityList'] = array();
        $extra_data['tariffs_list_entity_public'] = array();
        $extra_data['tariffs_list_entity_not_public'] = array();
        if (isset($this->services_entity) && !empty($this->services_entity)) {
            $extra_data['connTechsEntityList'] = ConnectionTechnologies::getTechnologiesList($this->services_entity);
            $non_auto_techs_list = array_diff($this->conn_techs_entity, Json::decode($this->tariffs_entity, true)['auto_tariffs']);
            $extra_data['tariffs_list_entity_public'] = Tariffs::getTariffsListByTechnologies($non_auto_techs_list, 2, 1);
            $extra_data['tariffs_list_entity_not_public'] = Tariffs::getTariffsListByTechnologies($this->conn_techs_entity, 2, 0);
            $extra_data['tariffs_list_entity_groups'] = TariffsGroups::find()->where(['publication_status' => 1, 'abonent_type' => 2])->all();
        }

        $extra_data['opersList'] = Operators::loadList();
        $extra_data['servicesList'] = Services::loadList();

        $extra_data['errors'] = $this->getErrors();

        if (!$this->isNewRecord) {
            $extra_data['porches'] = ZonesPorches::getPorchesForAddress($this->id);
        }

        return $extra_data;
    }

    public function loadExtraDataForView()
    {
        $extra_data = array();

        $extra_data['services_and_techs_list_individual'] = ZonesAddressesToServices::loadServicesListForZonesAddressView($this->id, 1);
        $extra_data['services_and_techs_list_entity'] = ZonesAddressesToServices::loadServicesListForZonesAddressView($this->id, 2);

        $tariffs_individual = Json::decode($this->tariffs_individual, true);
        $tariffs_entity = Json::decode($this->tariffs_entity, true);

        if (empty($tariffs_individual['groups'])) {
            $extra_data['tariffs_list_individual'] = ZonesAddressesToTariffs::loadTariffsListForAddressView($this->id, 1, $this->services_individual, 
            $this->conn_techs_individual);
        } else {
            $extra_data['tariffs_list_individual'] = ZonesAddressesToTariffsGroups::loadGroupsListForAddressView($this->id, 1);
        }

        if (empty($tariffs_entity['groups'])) {
            $extra_data['tariffs_list_entity'] = ZonesAddressesToTariffs::loadTariffsListForAddressView($this->id, 2, $this->services_entity, 
            $this->conn_techs_entity);
        } else {
            $extra_data['tariffs_list_entity'] = ZonesAddressesToTariffsGroups::loadGroupsListForAddressView($this->id, 2);
        }

        $extra_data['opers_list'] = ZonesAddressesToOpers::loadOpersListForAddressView($this->id);

        return $extra_data;
    }

    public static function find()
    {
        return new \common\models\query\ZonesAddressesQuery(get_called_class());
    }

    protected function processingRelations($data, $model_name, $method_name, $column_name, $extra_data = [])
    {
        if (isset($extra_data['abonent_type'])) {
            $old_data = $model_name::$method_name($this->id, $extra_data['abonent_type']);
        } else {
            $old_data = $model_name::$method_name($this->id);
        }

        if ($column_name == 'connection_technology_id') {
            if ($extra_data['abonent_type'] == 1) {
                $tariffs = Json::decode($this->tariffs_individual, true);
            } elseif ($extra_data['abonent_type'] == 2) {
                $tariffs = Json::decode($this->tariffs_entity, true);
            }
        }

        if (!empty($data)) {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if ($column_name == 'connection_technology_id') {
                        $extra_data['auto_tariffs'] = (isset($tariffs['auto_tariffs'][$value])) ? 1 : 0;
                    }
                    if (in_array($value, $old_data)) {
                        unset($old_data[array_search($value, $old_data)]);
                        if (isset($extra_data['abonent_type'])) {
                            $model = $model_name::findOne([$column_name => $value, 'address_id' => $this->id, 'abonent_type' => $extra_data['abonent_type']]);
                        } else{
                            $model = $model_name::findOne([$column_name => $value, 'address_id' => $this->id]);
                        }
                        if (isset($extra_data['auto_tariffs'])) {
                            $model->auto_tariffs = $extra_data['auto_tariffs'];
                        }
                        $model->publication_status = 1;
                        $model->updated_at = $this->updated_at;
                        $model->updater = $this->updater;
                        $model->save();
                    } else {
                        $model = new $model_name();
                        $model->address_id = $this->id;
                        $model->$column_name = $value;
                        if (!empty($extra_data)) {
                            foreach ($extra_data as $extra_value_name => $extra_value) {
                                $model->$extra_value_name = $extra_value;
                            }
                        }
                        $model->publication_status = 1;
                        $model->created_at = $this->created_at;
                        $model->cas_user_id = $this->cas_user_id;
                        $model->save();
                    }
                }
            } else {
                if ($column_name == 'connection_technology_id') {
                        $extra_data['auto_tariffs'] = (isset($tariffs['auto_tariffs'][$data])) ? 1 : 0;
                    }
                if (in_array($data, $old_data)) {
                    unset($old_data[array_search($data, $old_data)]);
                    if (isset($extra_data['abonent_type'])) {
                        $model = $model_name::findOne([$column_name => $data, 'address_id' => $this->id, 'abonent_type' => $extra_data['abonent_type']]);
                    } else{
                        $model = $model_name::findOne([$column_name => $data, 'address_id' => $this->id]);
                    }
                    if (isset($extra_data['auto_tariffs'])) {
                        $model->auto_tariffs = $extra_data['auto_tariffs'];
                    }
                    $model->publication_status = 1;
                    $model->updated_at = $this->updated_at;
                    $model->updater = $this->updater;
                    $model->save();
                } else {
                    $model = new $model_name();
                    $model->address_id = $this->id;
                    $model->$column_name = $data;
                    if (!empty($extra_data)) {
                        foreach ($extra_data as $extra_value_name => $extra_value) {
                            $model->$extra_value_name = $extra_value;
                        }
                    }
                    $model->publication_status = 1;
                    $model->created_at = $this->created_at;
                    $model->cas_user_id = $this->cas_user_id;
                    $model->save();
                }
            }
        }

        if (!empty($old_data)) {
            foreach ($old_data as $key => $value) {
                if (isset($extra_data['abonent_type'])) {
                    $model = $model_name::findOne([$column_name => $value, 'address_id' => $this->id, 'abonent_type' => $extra_data['abonent_type']]);
                } else{
                    $model = $model_name::findOne([$column_name => $value, 'address_id' => $this->id]);
                }
                $model->publication_status = 0;
                $model->updated_at = $this->updated_at;
                $model->updater = $this->updater;
                $model->save();
            }
        }
    }

    // получения списка адресов, которые обслуживает заданная УК
    public function getAddressesForManagCompany($company_id){
        $connection = Yii::$app->db;
        $addresses = $connection
                    ->createCommand("
                        SELECT id, address_uuid, manag_company_branch_id
                        FROM zones__addresses  
                        WHERE manag_company_id = '{$company_id}' 
                    ")
                    ->queryAll();
        return ArrayHelper::map($addresses, 'id', 'address_uuid', 'manag_company_branch_id');
    }

    // получения списка адресов для виджета поиска из заданного списка
    public function addressesSearch($addresses){
        $addresses = (new Query())
            ->select(['id', 'address_uuid'])
            ->from('zones__addresses')
            ->where(["address_uuid" => $addresses])
            ->all();
        
        return ArrayHelper::map($addresses, 'id', 'address_uuid');
    }
}
