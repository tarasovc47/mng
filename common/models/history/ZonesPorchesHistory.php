<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "zones__porches_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property string $porch_name
 * @property integer $address_id
 * @property integer $created_at
 * @property integer $cas_user_id
 * @property integer $publication_status
 */
class ZonesPorchesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zones__porches_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'porch_name', 'address_id', 'created_at', 'cas_user_id', 'publication_status'], 'required'],
            [['origin_id', 'address_id', 'created_at', 'cas_user_id', 'publication_status'], 'integer'],
            [['porch_name'], 'string', 'max' => 255],
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
            'porch_name' => 'Porch Name',
            'address_id' => 'Address ID',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
            'publication_status' => 'Publication Status',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ZonesPorchesHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ZonesPorchesHistoryQuery(get_called_class());
    }
}
