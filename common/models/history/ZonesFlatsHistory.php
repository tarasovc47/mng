<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "zones__flats_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property string $flat_name
 * @property integer $floor_id
 * @property integer $room_type
 * @property integer $created_at
 * @property integer $cas_user_id
 * @property integer $publication_status
 */
class ZonesFlatsHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zones__flats_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'flat_name', 'floor_id', 'room_type', 'created_at', 'cas_user_id', 'publication_status'], 'required'],
            [['origin_id', 'floor_id', 'room_type', 'created_at', 'cas_user_id', 'publication_status'], 'integer'],
            [['flat_name'], 'string', 'max' => 255],
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
            'flat_name' => 'Flat Name',
            'floor_id' => 'Floor ID',
            'room_type' => 'Room Type',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
            'publication_status' => 'Publication Status',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ZonesFlatsHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ZonesFlatsHistoryQuery(get_called_class());
    }
}
