<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

class ZonesAccessAgreements extends \yii\db\ActiveRecord
{
    public $file;

    public static function tableName()
    {
        return 'zones__access_agreements';
    }

    public function rules()
    {
        return [
            [['label', 'oper_id', 'manag_company_id', 'created_at', 'closed_at', 'rent_price', 'cas_user_id', 'price_is_ratio', 'opened_at', 'auto_prolongation'], 'required'],
            [['name', 'label', 'comment', 'extension'], 'string'],
            [['name', 'label', 'comment', 'extension'], 'trim'],
            [['oper_id', 'manag_company_id', 'created_at', 'closed_at', 'cas_user_id', 'price_is_ratio', 'opened_at', 'auto_prolongation'], 'integer'],
            [['rent_price'], 'double'],
            [['file'], 'file', 'extensions' => 'png, jpg, jpeg, xls, pdf, doc, docx, xslx', 'maxSize' => 20000000 ],
            ['closed_at', 'compare', 'compareAttribute' => 'opened_at', 'operator' => '>', 'on' => 'not_auto_prolongation'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя файла',
            'label' => 'Название документа',
            'oper_id' => 'Оператор',
            'manag_company_id' => 'Управляющая компания',
            'opened_at' => 'Дата заключения',
            'closed_at' => 'Действует до',
            'comment' => 'Примечание',
            'rent_price' => 'Стоимость аренды',
            'file' => 'Загрузить документ',
            'price_is_ratio' => 'Процентное соотношение от количества пользователей',
            'extension' => 'Расширение',
            'created_at' => 'Дата создания',
            'auto_prolongation' => 'Автоматическая пролонгация',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesAccessAgreementsQuery(get_called_class());
    }

    public static function getAccessAgreementsByCompany($company_id)
    {
        $time = time();
        $connection = Yii::$app->db;
        $agreements = $connection
                            ->createCommand("SELECT id, label FROM zones__access_agreements WHERE manag_company_id = {$company_id} AND (closed_at > {$time} OR auto_prolongation = 1)")
                            ->queryAll();
        return ArrayHelper::map($agreements, 'id', 'label');
    }

    public static function getAgreementsForCompany($company_id)
    {
        $connection = Yii::$app->db;
        $agreements = $connection
                            ->createCommand("SELECT id, label, opened_at, closed_at, auto_prolongation  FROM zones__access_agreements WHERE manag_company_id = {$company_id}")
                            ->queryAll();
        return $agreements;
    }
}
