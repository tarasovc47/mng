<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\TariffsToOpers;
use common\models\TariffsToBillingTariffs;
use common\models\TariffsToServices;
use common\models\TariffsToConnectionTechnologies;
use common\models\ConnectionTechnologies;
use yii\db\Query;
use common\models\history\TariffsHistory;

class Tariffs extends \yii\db\ActiveRecord
{
    public $updater;
    public $updated_at;
    public $old_attributes = array();
    public $opers;
    public $services;
    public $connection_technologies;
    public $billing_id;
    public $package_tariff = [
        '0' => 'Нет',
        '1' => 'Да',
    ];
    public $priority_tariff = [
        '0' => 'Нет',
        '1' => 'Да',
    ];
    public $public_tariff = [
        '0' => 'Нет',
        '1' => 'Да',
    ];
    public static $billing_tariff_state = [
        '1' => 'Активные для общего пользования',
        '0' => 'Активные для служебного пользования',
        '-1' => 'Неактивные',
    ];

    public static function tableName()
    {
        return 'tariffs';
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->processingRelations($this->opers, TariffsToOpers::className(), 'getOpersForTariff', 'oper_id');
        $this->processingRelations($this->billing_id, TariffsToBillingTariffs::className(), 'getBillingTariffsForTariff', 'billing_id');
        $this->processingRelations($this->services, TariffsToServices::className(), 'getServicesForTariff', 'service_id');
        $this->processingRelations($this->connection_technologies, TariffsToConnectionTechnologies::className(), 'getConnTechsForTariff', 'connection_technology_id');

        $history = new TariffsHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();

        parent::afterSave($insert, $changedAttributes);
    }

    public function rules()
    {
        return [
            [['old_attributes'], 'safe'],
            [['billing_id'], 'integer', 'on' => 'default'],
            [['name', 'for_abonent_type', 'created_at', 'package', 'opened_at', 'priority', 'price', 'opers', 'services', 'connection_technologies', 'cas_user_id'], 'required'],
            [['name', 'comment'], 'string'],
            [['name', 'comment'], 'trim'],
            [['for_abonent_type', 'created_at', 'closed_at', 'package', 'opened_at', 'priority', 'public', 'price', 'updater', 'updated_at', 'cas_user_id', 'speed', 'channels'], 'integer'],
            [['services', 'billing_id'], 'integer', 'on' => 'non_package_tariff'],
            [['services', 'billing_id'], 'each', 'rule' => ['integer'], 'on' => 'package_tariff'],
            [['opers', 'connection_technologies'], 'each', 'rule' => ['integer']],
            ['closed_at', 'compare', 'compareAttribute' => 'opened_at', 'operator' => '>'],
            [['services', 'connection_technologies'], 'safe', 'on' => 'remove'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'billing_id' => 'Тариф в биллинге',
            'comment' => 'Примечание',
            'for_abonent_type' => 'Тип абонента',
            'opers' => 'Операторы',
            'services' => 'Сервисы',
            'connection_technologies' => 'Технологии подключения',
            'created_at' => 'Дата создания',
            'opened_at' => 'Дата открытия',
            'closed_at' => 'Дата закрытия',
            'package' => 'Пакетный тариф',
            'priority' => 'Приоритетный тариф',
            'public' => 'Общедоступный тариф',
            'price' => 'Стоимость, руб.',
            'speed' => 'Скорость, мб/с',
            'channels' => 'Количество каналов',
        ];
    }

    public function getRelatedValues()
    {
        $this->opers = TariffsToOpers::getOpersForTariff($this->id, 1);
        $this->services = TariffsToServices::getServicesForTariff($this->id, 1);
        $this->connection_technologies = TariffsToConnectionTechnologies::getConnTechsForTariff($this->id, 1);
        $this->billing_id = TariffsToBillingTariffs::getBillingIdsForTariff($this->id, 1);

        return true;
    }

    public function getExtraDataForForm(){
        $extra_data = array();
        $extra_data['abon_types'] = Yii::$app->params['abonent_types'];
        $extra_data['opers_list'] = Operators::loadList();
        $extra_data['services_list'] = Services::loadList();
        
        $extra_data['tariffs_list'] = array();
        $extra_data['conn_tech_list'] = array();
        if (!empty($this->services)) {
            $extra_data['tariffs_list'] = TariffsToBillingTariffs::getTariffsFromBillingByServices($this->services);
            $extra_data['conn_tech_list'] = ConnectionTechnologies::getTechnologiesList($this->services);
        }
        return $extra_data;
    }

    public function loadExtraDataForView(){
        $extra_data = array();
        $extra_data['services_and_techs_list'] = TariffsToServices::loadServicesListForTariffView($this->id);
        return $extra_data;
    }

    public function rewriteDates(){
        if($this->opened_at != '' && !is_numeric($this->opened_at)) {
            $this->opened_at = strtotime($this->opened_at);
        }
        if ($this->closed_at != '') {
            $this->closed_at = strtotime($this->closed_at);
        }

        return true;
    }

    public static function find()
    {
        return new \common\models\query\TariffsQuery(get_called_class());
    }

    // Настройка связей с другими таблицами
    public function getTariffsToOpers()
    {
        return $this->hasMany(TariffsToOpers::className(), ['tariff_id' => 'id'])->
       andWhere(['publication_status' => 1]);
    }

    public function getBillingtariffs()
    {
        return $this->hasMany(TariffsToBillingTariffs::className(), ['tariff_id' => 'id'])->
       andWhere(['publication_status' => 1]);
    }

    public function getTariffsToServices()
    {
        return $this->hasMany(TariffsToServices::className(), ['tariff_id' => 'id'])->
       andWhere(['publication_status' => 1]);
    }

    public function getTariffsToConnTechs()
    {
        return $this->hasMany(TariffsToConnectionTechnologies::className(), ['tariff_id' => 'id'])->
       andWhere(['publication_status' => 1]);
    }

    protected function processingRelations($data, $model_name, $method_name, $column_name)
    {
        $old_data = $model_name::$method_name($this->id);

        if (!empty($data)) {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (in_array($value, $old_data)) {
                        unset($old_data[array_search($value, $old_data)]);
                        $model = $model_name::findOne([$column_name => $value, 'tariff_id' => $this->id]);
                        $model->publication_status = 1;
                        $model->updated_at = $this->updated_at;
                        $model->updater = $this->updater;
                        $model->save();
                    } else {
                        $model = new $model_name();
                        $model->tariff_id = $this->id;
                        $model->$column_name = $value;
                        $model->publication_status = 1;
                        $model->created_at = $this->created_at;
                        $model->cas_user_id = $this->cas_user_id;
                        $model->save();
                    }
                }
            } else {
                if (in_array($data, $old_data)) {
                    unset($old_data[array_search($data, $old_data)]);
                    $model = $model_name::findOne([$column_name => $data, 'tariff_id' => $this->id]);
                    $model->publication_status = 1;
                    $model->updated_at = $this->updated_at;
                    $model->updater = $this->updater;
                    $model->save();
                } else {
                    $model = new $model_name();
                    $model->tariff_id = $this->id;
                    $model->$column_name = $data;
                    $model->publication_status = 1;
                    $model->created_at = $this->created_at;
                    $model->cas_user_id = $this->cas_user_id;
                    $model->save();
                }
            }
        }

        if (!empty($old_data)) {
            foreach ($old_data as $key => $value) {
                $model = $model_name::findOne([$column_name => $value, 'tariff_id' => $this->id]);
                $model->publication_status = 0;
                $model->updated_at = $this->updated_at;
                $model->updater = $this->updater;
                $model->save();
            }
        }
    }

    // Получение списка тарифов для создания адреса
    public static function getTariffsList($services, $abonent_type){
        /* не уверена что этот метод где-то используется, но оставлю дабы не ломать ничего перед увольнением */
        $services = implode(', ', $services);
        $abonent_type = implode(', ', $abonent_type);
        $connection = Yii::$app->db;
        $result = $connection
                            ->createCommand("
                                SELECT t.id, t.name, s.name as service, o.name as oper
                                FROM tariffs t
                                LEFT JOIN tariffs_to_services tts ON t.id = tts.tariff_id
                                LEFT JOIN services s ON tts.service_id = s.id
                                LEFT JOIN tariffs_to_opers tto ON t.id = tto.tariff_id
                                LEFT JOIN operators o ON o.id = tto.oper_id
                                WHERE  tts.service_id IN ({$services}) AND t.for_abonent_type IN ({$abonent_type})
                                GROUP BY s.name, o.name
                            ")
                            ->queryAll();

        $for_map = array();
        $i = 0;
        foreach ($result as $key_tariff => $tariff) {
            $for_map[$i]['optgroup'] = $service_type['service'].': '.$tariffs[$key_service_type]['oper'];
            $for_map[$i]['tariff_id'] = $tariffs[$key_service_type]['tariffs'][$key_tariff]['f1'];
            $for_map[$i]['tariff_name'] = $tariffs[$key_service_type]['tariffs'][$key_tariff]['f2'];
            $i++;
        }
    

        return $result;
    }

    public static function getTariffsListByTechnologies($technologies, $abonent_type, $public_tariff, $except_tariffs = array()){
        $where = ['and', 
                    ['tariffs.for_abonent_type' => $abonent_type], 
                    ['tariffs_to_connection_technologies.connection_technology_id' => $technologies],
                    [
                        'or', ['>', 'closed_at', time()], ['closed_at' => null]
                    ],
                    ['tariffs.public' => $public_tariff],
                ];

        if (!empty($except_tariffs)) {
            $where[] = ['not in', 'tariffs.id', $except_tariffs];
        }

        $tariffs = self::find()->joinWith('tariffsToConnTechs')
                                ->where($where)
                                ->asArray()
                                ->all();

        foreach ($tariffs as $key => $tariff) {
            $tariffs[$key]['services_and_techs_list'] = TariffsToServices::loadServicesListForTariffView($tariff['id']);
        }

        return ArrayHelper::index($tariffs, 'id');
    }
}
