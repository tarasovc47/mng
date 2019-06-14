<?php

namespace common\models;

use Yii;
use common\models\history\TariffsToConnectionTechnologiesHistory;
use yii\helpers\ArrayHelper;

class TariffsToConnectionTechnologies extends \yii\db\ActiveRecord
{
    public $updater;
    public $updated_at;
    
    public static function tableName()
    {
        return 'tariffs_to_connection_technologies';
    }

    public function rules()
    {
        return [
            [['tariff_id', 'connection_technology_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['tariff_id', 'connection_technology_id', 'publication_status', 'created_at', 'cas_user_id', 'updater', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tariff_id' => 'Tariff ID',
            'connection_technology_id' => 'Connection Technology ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\TariffsToConnectionTechnologiesQuery(get_called_class());
    }

    // Настройка связей с другими таблицами
    public function getConnTech()
    {
        return $this->hasOne(ConnectionTechnologies::className(), ['id' => 'connection_technology_id']);
    }

    public function afterSave($insert, $changedAttributes){
        $history = new TariffsToConnectionTechnologiesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getConnTechsForTariff($tariff_id, $publication_status = false)
    {
        $connection = Yii::$app->db;
        $where_status = '';
        if ($publication_status) {
            $where_status = ' AND publication_status = '.$publication_status;
        }
        $services = $connection
                            ->createCommand("SELECT id, connection_technology_id FROM ".self::tableName()." WHERE tariff_id = '".$tariff_id."'".$where_status)
                            ->queryAll();

        return ArrayHelper::map($services, 'id', 'connection_technology_id');
    }

    public static function loadTechsListByService($tariff_id, $service_id){
        $connection = Yii::$app->db;
        $technologies = $connection
                            ->createCommand("
                                SELECT ct.name
                                FROM tariffs_to_connection_technologies tct
                                LEFT JOIN connection_technologies ct ON tct.connection_technology_id = ct.id
                                LEFT JOIN services s ON ct.service_id = s.id
                                WHERE 
                                    tct.tariff_id = '{$tariff_id}' 
                                    AND s.id = {$service_id} 
                                    AND tct.publication_status = 1
                            ")
                            ->queryColumn();

        return $technologies;
    }
}
