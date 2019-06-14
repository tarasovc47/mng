<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\history\ZonesAddressStatusesHistory;

class ZonesAddressStatuses extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;
    public $tariffsRequiredStatuses = [
        'Нет',
        'Да',
    ];

    public static function tableName()
    {
        return 'zones__address_statuses';
    }

    public function rules()
    {
        return [
            [['name', 'tariffs_required'], 'required'],
            [['name', 'comment'], 'string'],
            [['name', 'comment'], 'trim'],
            [['tariffs_required', 'publication_status', 'created_at', 'cas_user_id', 'updater', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'comment' => 'Примечание',
            'tariffs_required' => 'Необходима привязка тарифов в зонах присутствия'
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesAddressStatusesQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        $history = new ZonesAddressStatusesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();

        parent::afterSave($insert, $changedAttributes);
    }

    public static function getStatusesList(){
        $connection = Yii::$app->db;
        $opers = $connection
                            ->createCommand("SELECT id, name FROM zones__address_statuses")
                            ->queryAll();

        return ArrayHelper::map($opers, 'id', 'name');
    }
}
