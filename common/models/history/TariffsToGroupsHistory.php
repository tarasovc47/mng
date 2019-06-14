<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "tariffs_to_groups_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property integer $tariffs_group_id
 * @property integer $tariff_id
 * @property integer $publication_status
 * @property integer $created_at
 * @property integer $cas_user_id
 */
class TariffsToGroupsHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariffs_to_groups_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'tariffs_group_id', 'tariff_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['origin_id', 'tariffs_group_id', 'tariff_id', 'publication_status', 'created_at', 'cas_user_id'], 'integer'],
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
            'tariffs_group_id' => 'Tariffs Group ID',
            'tariff_id' => 'Tariff ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\TariffsToGroupsHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\TariffsToGroupsHistoryQuery(get_called_class());
    }
}
