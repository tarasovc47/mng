<?php

namespace common\models;

use Yii;
use common\models\Tariffs;
use common\models\history\TariffsToGroupsHistory;
use yii\helpers\ArrayHelper;

class TariffsToGroups extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;

    public static function tableName()
    {
        return 'tariffs_to_groups';
    }

    public function rules()
    {
        return [
            [['tariffs_group_id', 'tariff_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['tariffs_group_id', 'tariff_id', 'publication_status', 'created_at', 'cas_user_id', 'updater', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tariffs_group_id' => 'Tariffs Group ID',
            'tariff_id' => 'Tariff ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\TariffsToGroupsQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        $history = new TariffsToGroupsHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();

        parent::afterSave($insert, $changedAttributes);
    }

    public function getTariff()
    {
        return $this->hasOne(Tariffs::className(), ['id' => 'tariff_id']);
    }

    public static function loadTariffsListForGroup($group_id, $publication_status = false){
        $where_status = '';
        if ($publication_status) {
            $where_status = " AND publication_status = '{$publication_status}'";
        }
        $connection = Yii::$app->db;
        $tariffs = $connection
                            ->createCommand("
                                SELECT tariff_id 
                                FROM tariffs_to_groups 
                                WHERE tariffs_group_id = '{$group_id}'".$where_status)
                            ->queryAll();

        return ArrayHelper::getColumn($tariffs, 'tariff_id');
    }
}
