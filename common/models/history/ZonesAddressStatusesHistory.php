<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "zones__address_statuses_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property string $name
 * @property string $comment
 * @property integer $tariffs_required
 * @property integer $cas_user_id
 * @property integer $created_at
 * @property integer $publication_status
 */
class ZonesAddressStatusesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zones__address_statuses_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'name', 'tariffs_required', 'cas_user_id', 'created_at', 'publication_status'], 'required'],
            [['origin_id', 'tariffs_required', 'cas_user_id', 'created_at', 'publication_status'], 'integer'],
            [['comment'], 'string'],
            [['name'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'comment' => 'Comment',
            'tariffs_required' => 'Tariffs Required',
            'cas_user_id' => 'Cas User ID',
            'created_at' => 'Created At',
            'publication_status' => 'Publication Status',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ZonesAddressStatusesHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ZonesAddressStatusesHistoryQuery(get_called_class());
    }
}
