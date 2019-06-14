<?php

namespace common\models;

use Yii;
use common\models\TariffsGroups;

class ZonesAddressesToTariffsGroups extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;
    public static function tableName()
    {
        return 'zones__addresses_to_tariffs_groups';
    }

    public function rules()
    {
        return [
            [['address_id', 'tariffs_group_id', 'abonent_type', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['address_id', 'tariffs_group_id', 'abonent_type', 'publication_status', 'created_at', 'cas_user_id', 'updated_at', 'updater'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_id' => 'Address ID',
            'tariffs_group_id' => 'Tariff ID',
            'abonent_type' => 'Abonent Type',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesAddressesToTariffsGroupsQuery(get_called_class());
    }

    public function getTariffsGroup()
    {
        return $this->hasOne(TariffsGroups::className(), ['id' => 'tariffs_group_id']);
    }

    public static function loadGroupsListForAddress($address_id, $abonent_type, $publication_status = false){
        $where_status = '';
        if ($publication_status) {
            $where_status = " AND publication_status = '{$publication_status}'";
        }
        $connection = Yii::$app->db;
        $groups = $connection
                        ->createCommand("
                                SELECT tariffs_group_id
                                FROM zones__addresses_to_tariffs_groups
                                WHERE address_id = {$address_id} AND abonent_type = {$abonent_type}
                                ".$where_status)
                        ->queryColumn();

        return $groups;
    }

    public static function loadGroupsListForAddressView($address_id, $abonent_type){
        $tariffs = [];
        $groups = self::find()->where(['address_id' => $address_id, 'abonent_type' => $abonent_type, 'publication_status' => 1])->all();

        foreach ($groups as $key => $group) {
            foreach ($group->tariffsGroup->tariffsToGroups as $key => $tariff) {
                $tariffs[$tariff->tariff->id] = Tariffs::find()->where(['id' => $tariff->tariff->id])->asArray()->one();
                $tariffs[$tariff->tariff->id]['services_techs_list'] = TariffsToServices::loadServicesListForTariffView($tariff->tariff->id);
            }
        }
        
        return $tariffs;
    }
}
