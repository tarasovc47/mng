<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "zones__addresses_to_connection_technologies_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property integer $address_id
 * @property integer $connection_technology_id
 * @property integer $abonent_type
 * @property integer $auto_tariffs
 * @property integer $cas_user_id
 * @property integer $created_at
 * @property integer $publication_status
 */
class ZonesAddressesToConnectionTechnologiesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zones__addresses_to_connection_technologies_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'address_id', 'connection_technology_id', 'abonent_type', 'auto_tariffs', 'cas_user_id', 'created_at', 'publication_status'], 'required'],
            [['origin_id', 'address_id', 'connection_technology_id', 'abonent_type', 'auto_tariffs', 'cas_user_id', 'created_at', 'publication_status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origin_id' => 'Origin ID',
            'address_id' => 'Address ID',
            'connection_technology_id' => 'Connection Technology ID',
            'abonent_type' => 'Abonent Type',
            'auto_tariffs' => 'Auto Tariffs',
            'cas_user_id' => 'Cas User ID',
            'created_at' => 'Created At',
            'publication_status' => 'Publication Status',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ZonesAddressesToConnectionTechnologiesHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ZonesAddressesToConnectionTechnologiesHistoryQuery(get_called_class());
    }
}
