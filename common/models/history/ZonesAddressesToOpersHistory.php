<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "zones__addresses_to_opers_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property integer $address_id
 * @property integer $oper_id
 * @property integer $cas_user_id
 * @property integer $created_at
 * @property integer $publication_status
 */
class ZonesAddressesToOpersHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zones__addresses_to_opers_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'address_id', 'oper_id', 'cas_user_id', 'created_at', 'publication_status'], 'required'],
            [['origin_id', 'address_id', 'oper_id', 'cas_user_id', 'created_at', 'publication_status'], 'integer'],
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
            'oper_id' => 'Oper ID',
            'cas_user_id' => 'Cas User ID',
            'created_at' => 'Created At',
            'publication_status' => 'Publication Status',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ZonesAddressesToOpersHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ZonesAddressesToOpersHistoryQuery(get_called_class());
    }
}
