<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\history\ZonesAddressTypesHistory;

class ZonesAddressTypes extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;

    public static function tableName()
    {
        return 'zones__address_types';
    }

    public function rules()
    {
        return [
            [['name', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['name', 'comment'], 'string'],
            [['name', 'comment'], 'trim'],
            [['updater', 'updated_at', 'publication_status', 'created_at', 'cas_user_id'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'comment' => 'Примечание',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesAddressTypesQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        $history = new ZonesAddressTypesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();

        parent::afterSave($insert, $changedAttributes);
    }

    public static function getAddressTypesList(){
        $first = [-1 => '—'];
        $connection = Yii::$app->db;
        $types = $connection
                            ->createCommand("SELECT id, name 
                                FROM zones__address_types")
                            ->queryAll();

        $types = ArrayHelper::map($types, 'id', 'name');
        return ArrayHelper::merge($first, $types);
    }
}
