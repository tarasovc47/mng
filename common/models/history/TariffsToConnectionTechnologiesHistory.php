<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "tariffs_to_connection_technologies_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property integer $tariff_id
 * @property integer $connection_technology_id
 * @property integer $cas_user_id
 * @property integer $created_at
 * @property integer $publication_status
 */
class TariffsToConnectionTechnologiesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariffs_to_connection_technologies_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'tariff_id', 'connection_technology_id', 'cas_user_id', 'created_at', 'publication_status'], 'required'],
            [['origin_id', 'tariff_id', 'connection_technology_id', 'cas_user_id', 'created_at', 'publication_status'], 'integer'],
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
            'tariff_id' => 'Tariff ID',
            'connection_technology_id' => 'Connection Technology ID',
            'cas_user_id' => 'Cas User ID',
            'created_at' => 'Created At',
            'publication_status' => 'Publication Status',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\TariffsToConnectionTechnologiesHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\TariffsToConnectionTechnologiesHistoryQuery(get_called_class());
    }
}
