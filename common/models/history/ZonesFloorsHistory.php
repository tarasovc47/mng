<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "zones__floors_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property string $porch_id
 * @property integer $floor_name
 * @property integer $created_at
 * @property integer $cas_user_id
 * @property integer $publication_status
 */
class ZonesFloorsHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zones__floors_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'porch_id', 'floor_name', 'created_at', 'cas_user_id', 'publication_status'], 'required'],
            [['origin_id', 'floor_name', 'created_at', 'cas_user_id', 'publication_status'], 'integer'],
            [['porch_id'], 'string', 'max' => 255],
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
            'porch_id' => 'Porch ID',
            'floor_name' => 'Floor Name',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
            'publication_status' => 'Publication Status',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ZonesFloorsHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ZonesFloorsHistoryQuery(get_called_class());
    }
}
