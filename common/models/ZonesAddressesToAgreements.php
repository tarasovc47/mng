<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\history\ZonesAddressesToAgreementsHistory;

class ZonesAddressesToAgreements extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;

    public static function tableName()
    {
        return 'zones__addresses_to_agreements';
    }

    public function rules()
    {
        return [
            [['address_id', 'agreement_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['address_id', 'agreement_id', 'publication_status', 'created_at', 'cas_user_id', 'updated_at', 'updater'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_id' => 'Address ID',
            'agreement_id' => 'Agreement ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesAddressesToAgreementsQuery(get_called_class());
    }

    public function getAgreement()
    {
        return $this->hasOne(ZonesAccessAgreements::className(), ['id' => 'agreement_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $history = new ZonesAddressesToAgreementsHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();

        parent::afterSave($insert, $changedAttributes);
    }

    public static function loadAgreementsListForAddress($address_id, $publication_status = false){
        $where_status = '';
        if ($publication_status) {
            $where_status = " AND publication_status = '{$publication_status}'";
        }
        $connection = Yii::$app->db;
        $agreements = $connection
                            ->createCommand("
                                SELECT agreement_id 
                                FROM zones__addresses_to_agreements 
                                WHERE address_id = '{$address_id}'".$where_status)
                            ->queryAll();

        return ArrayHelper::getColumn($agreements, 'agreement_id');
    }
}
