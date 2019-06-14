<?php

namespace common\models;

use Yii;
use common\models\history\ZonesAddressesToTariffsHistory;
use common\models\ZonesAddressesToConnectionTechnologies as ZCT;
use common\models\ZonesAddressesToServices as ZS;
use common\models\Tariffs;
use common\models\TariffsToServices;
use yii\helpers\ArrayHelper;
use common\components\SiteHelper;
use yii\db\Query;

class ZonesAddressesToTariffs extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;

    public static function tableName()
    {
        return 'zones__addresses_to_tariffs';
    }

    public function rules()
    {
        return [
            [['address_id', 'tariff_id', 'abonent_type', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['address_id', 'tariff_id', 'abonent_type', 'publication_status', 'created_at', 'cas_user_id', 'updated_at', 'updater'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_id' => 'Address ID',
            'tariff_id' => 'Tariff ID',
            'abonent_type' => 'Abonent Type',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesAddressesToTariffsQuery(get_called_class());
    }

    public function getTariff()
    {
        return $this->hasOne(Tariffs::className(), ['id' => 'tariff_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $history = new ZonesAddressesToTariffsHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();

        parent::afterSave($insert, $changedAttributes);
    }

    public static function loadTariffsListForAddressView($address_id, $abonent_type, $address_services, $address_techs){
        $connection = Yii::$app->db;
        $auto_tariffs_techs = array();
        $auto_tariffs_techs = ZCT::getAutoTariffsTechs($address_id, $abonent_type);
        $auto_tariffs_list = array();
        $auto_package = array();

        if (!empty($auto_tariffs_techs)) {
            $servcices_list = ZS::loadServicesListForAddress($address_id, $abonent_type);
            $servcices_list = implode(', ', $servcices_list);

            $auto_tariffs_techs = implode(', ', $auto_tariffs_techs);
            $auto_tariffs_list = $connection
                        ->createCommand("
                                SELECT t.id
                                FROM tariffs t
                                LEFT JOIN tariffs_to_connection_technologies ttct ON ttct.tariff_id = t.id
                                WHERE ttct.connection_technology_id IN ({$auto_tariffs_techs}) AND t.for_abonent_type = {$abonent_type}
                                AND t.public = 1 AND ttct.publication_status = 1
                                ")
                        ->queryAll();
                        
            if (isset($auto_tariffs_list) && !empty($auto_tariffs_list)) {
                $auto_tariffs_list = ArrayHelper::index($auto_tariffs_list, 'id');         
                $auto_tariffs_list = ArrayHelper::getColumn($auto_tariffs_list, 'id');     
                $auto_package = $connection
                        ->createCommand("
                                SELECT t.id, array_agg(tts.service_id) as services, array_agg(ttct.connection_technology_id) as conn_techs
                                FROM tariffs t
                                LEFT JOIN tariffs_to_connection_technologies ttct ON ttct.tariff_id = t.id
                                LEFT JOIN tariffs_to_services tts ON tts.tariff_id = t.id
                                WHERE 
                                    t.id IN (".implode(', ', $auto_tariffs_list).") AND 
                                    t.for_abonent_type = {$abonent_type} AND 
                                    t.public = 1 AND 
                                    t.package = 1 AND 
                                    ttct.publication_status = 1 AND 
                                    tts.publication_status = 1
                                GROUP BY t.id
                                ")
                        ->queryAll();    

                foreach ($auto_package as $key => $tariff) {
                    $tariff['services'] = SiteHelper::to_php_array($tariff['services']);
                    $tariff['conn_techs'] = SiteHelper::to_php_array($tariff['conn_techs']);

                    foreach ($tariff['services'] as $key => $service) {
                        if (!in_array($service, $address_services)) {
                            unset($auto_tariffs_list[$tariff['id']]);
                        }
                    }
                    foreach ($tariff['conn_techs'] as $key => $conn_tech) {
                        if (!in_array($conn_tech, $address_techs)) {
                            unset($auto_tariffs_list[$tariff['id']]);
                        }
                    }
                }
            }
        }

       $where_auto = '';
        if (!empty($auto_tariffs_list)) {
            $auto_tariffs_list = implode(', ', $auto_tariffs_list);
            $where_auto = ' OR t.id IN ('.$auto_tariffs_list.')';
        }

        $tariffs = array();
        $time = time();
        $tariffs = $connection
                        ->createCommand("
                                SELECT t.id
                                FROM tariffs t
                                LEFT JOIN zones__addresses_to_tariffs zatt ON t.id = zatt.tariff_id
                                WHERE (((zatt.address_id = '{$address_id}' AND zatt.abonent_type = '{$abonent_type}' AND zatt.publication_status = 1){$where_auto} ) AND (t.closed_at IS NULL or t.closed_at > {$time})) 
                                GROUP BY t.id
                                ORDER BY t.priority DESC, t.id ASC
                                ")
                        ->queryColumn();

        foreach ($tariffs as $key => $tariff) {
            $tariffs[$key] = Tariffs::find()->where(['id' => $tariff])->asArray()->one();
            $tariffs[$key]['services_techs_list'] = TariffsToServices::loadServicesListForTariffView($tariff);
        }

        
        return $tariffs;
    }

    public static function loadTariffsListForAddress($address_id, $conn_techs, $abonent_type){
        $tariffs = array();
        $tariffs['auto_tariffs'] = array();
        $tariffs['manual_tariffs'] = array();
        $tariffs['groups'] = array();
        foreach ($conn_techs as $key => $conn_tech) {
            $conn_tech_auto = false;
            $conn_tech_auto = ZCT::findOne([
                                                'address_id' => $address_id, 
                                                'connection_technology_id' => $conn_tech, 
                                                'abonent_type' => $abonent_type,
                                                'publication_status' => 1,
                                            ])->auto_tariffs;
            if ($conn_tech_auto) {
                $tariffs['auto_tariffs'][$conn_tech] = $conn_tech;
            } else {
                $tariffs_list = (new Query())
                                    ->select(['tariff_id'])
                                    ->from('zones__addresses_to_tariffs')
                                    ->where(['address_id' => $address_id, 'abonent_type' => $abonent_type, 'publication_status' => 1])
                                    ->all();

                $tariffs['manual_tariffs'] = ArrayHelper::map($tariffs_list, 'tariff_id', 'tariff_id');
            }
        }
        
        $tariffs['groups'] = (new Query())
                                    ->select(['tariffs_group_id'])
                                    ->from('zones__addresses_to_tariffs_groups')
                                    ->where(['address_id' => $address_id, 'abonent_type' => $abonent_type, 'publication_status' => 1])
                                    ->all();

        $tariffs['groups'] = ArrayHelper::index($tariffs['groups'], 'tariffs_group_id');
        $tariffs['groups'] = ArrayHelper::getColumn($tariffs['groups'], 'tariffs_group_id');

        return json_encode($tariffs, JSON_FORCE_OBJECT);
    }

    public static function loadManualTariffsForAddress($address_id, $abonent_type, $publication_status = false){
        $where = [
            'address_id' => $address_id,
            'abonent_type' => $abonent_type,
        ];
        if ($publication_status) {
            $where['publication_status'] = $publication_status;
        }
        $tariffs = (new Query())
            ->select(['tariff_id'])
            ->from('zones__addresses_to_tariffs')
            ->where($where)
            ->column();

        return $tariffs;
    }
}
