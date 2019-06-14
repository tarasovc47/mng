<?php

namespace common\models\history;

use Yii;

class ZonesAccessAgreementsHistory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'zones__access_agreements_history';
    }

    public function rules()
    {
        return [
            [['origin_id', 'label', 'oper_id', 'manag_company_id', 'opened_at', 'closed_at', 'auto_prolongation', 'rent_price', 'price_is_ratio', 'cas_user_id', 'created_at'], 'required'],
            [['origin_id', 'oper_id', 'manag_company_id', 'opened_at', 'closed_at', 'auto_prolongation', 'price_is_ratio', 'cas_user_id', 'created_at'], 'integer'],
            [['comment'], 'string'],
            [['rent_price'], 'number'],
            [['name', 'label', 'extension'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origin_id' => 'Origin ID',
            'name' => 'Name',
            'label' => 'Label',
            'extension' => 'Extension',
            'oper_id' => 'Oper ID',
            'manag_company_id' => 'Manag Company ID',
            'opened_at' => 'Opened At',
            'closed_at' => 'Closed At',
            'auto_prolongation' => 'Auto Prolongation',
            'comment' => 'Comment',
            'rent_price' => 'Rent Price',
            'price_is_ratio' => 'Price Is Ratio',
            'cas_user_id' => 'Cas User ID',
            'created_at' => 'Created At',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesAccessAgreementsHistoryQuery(get_called_class());
    }
}
