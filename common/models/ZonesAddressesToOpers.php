<?php

namespace common\models;

use Yii;
use common\models\history\ZonesAddressesToOpersHistory;
use common\models\Operators;
use yii\helpers\ArrayHelper;

class ZonesAddressesToOpers extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;

    public static function tableName()
    {
        return 'zones__addresses_to_opers';
    }

    public function rules()
    {
        return [
            [['address_id', 'oper_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['address_id', 'oper_id', 'publication_status', 'created_at', 'cas_user_id', 'updated_at', 'updater'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_id' => 'Address ID',
            'oper_id' => 'Oper ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesAddressesToOpersQuery(get_called_class());
    }

    public function getOperator()
    {
        return $this->hasOne(Operators::className(), ['id' => 'oper_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $history = new ZonesAddressesToOpersHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();

        parent::afterSave($insert, $changedAttributes);
    }


    public static function loadOpersListForAddressView($address_id){
        $connection = Yii::$app->db;
        $opers = $connection
                            ->createCommand("
                                SELECT o.name
                                FROM zones__addresses_to_opers zato
                                LEFT JOIN operators o ON zato.oper_id = o.id
                                WHERE zato.address_id = '{$address_id}' and zato.publication_status = 1
                            ")
                            ->queryAll();
        return $opers;
    }

    
    public static function loadOpersForAddress($address_id, $publication_status = false){
        $where_status = '';
        if ($publication_status) {
            $where_status = " AND publication_status = '{$publication_status}'";
        }
        $connection = Yii::$app->db;
        $opers = $connection
                            ->createCommand("
                                SELECT oper_id 
                                FROM zones__addresses_to_opers 
                                WHERE address_id = '{$address_id}'".$where_status)
                            ->queryAll();

        return ArrayHelper::getColumn($opers, 'oper_id');
    }
}
