<?php

namespace common\models;

use Yii;
use common\models\history\ZonesAddressesToConnectionTechnologiesHistory;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use common\models\ConnectionTechnologies;
use yii\db\Query;

class ZonesAddressesToConnectionTechnologies extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;
    
    public static function tableName()
    {
        return 'zones__addresses_to_connection_technologies';
    }

    public function rules()
    {
        return [
            [['address_id', 'connection_technology_id', 'abonent_type', 'auto_tariffs', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['address_id', 'connection_technology_id', 'abonent_type', 'auto_tariffs', 'publication_status', 'created_at', 'cas_user_id', 'updated_at', 'updater'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_id' => 'Address ID',
            'connection_technology_id' => 'Connection Technology ID',
            'abonent_type' => 'Abonent Type',
            'auto_tariffs' => 'Auto Tariffs',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesAddressesToConnectionTechnologiesQuery(get_called_class());
    }

    public function getConnTechs()
    {
        return $this->hasOne(ConnectionTechnologies::className(), ['id' => 'connection_technology_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $history = new ZonesAddressesToConnectionTechnologiesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();

        parent::afterSave($insert, $changedAttributes);
    }

    public static function getAutoTariffsTechs($address_id, $abonent_type){
        $connection = Yii::$app->db;
        $auto_tariffs_techs = $connection
                        ->createCommand("
                                SELECT connection_technology_id
                                FROM zones__addresses_to_connection_technologies
                                WHERE address_id = '{$address_id}' AND abonent_type = '{$abonent_type}' AND auto_tariffs = 1 AND publication_status = 1
                                ")
                        ->queryAll();
        return ArrayHelper::getColumn($auto_tariffs_techs, 'connection_technology_id');
    }

    public static function loadTechsListByService($address_id, $service_id, $abonent_type){
        $connection = Yii::$app->db;
        $technologies = $connection
                            ->createCommand("
                                SELECT ct.name
                                FROM zones__addresses_to_connection_technologies zatct
                                LEFT JOIN connection_technologies ct ON zatct.connection_technology_id = ct.id
                                LEFT JOIN services s ON ct.service_id = s.id
                                WHERE 
                                    zatct.address_id = '{$address_id}' 
                                    AND zatct.abonent_type = {$abonent_type} 
                                    AND s.id = {$service_id} 
                                    AND zatct.publication_status = 1
                            ")
                            ->queryColumn();

        return $technologies;
    }



    public static function loadTechsForAddress($address_id, $abonent_type, $publication_status = false){
        $where = [
            'address_id' => $address_id,
            'abonent_type' => $abonent_type,
        ];
        if ($publication_status) {
            $where['publication_status'] = $publication_status;
        }
        $technologies = (new Query())
            ->select(['connection_technology_id'])
            ->from('zones__addresses_to_connection_technologies')
            ->where($where)
            ->column();

        return $technologies;
    }
}
