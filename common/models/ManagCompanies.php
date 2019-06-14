<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\ManagCompaniesTypes;
use common\models\history\ManagCompaniesHistory;

class ManagCompanies extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;

    public static function tableName()
    {
        return 'manag_companies';
    }

    public function rules()
    {
        return [
            [['name', 'jur_address_id', 'actual_address_id', 'publication_status', 'created_at', 'cas_user_id', 'company_type'], 'required'],
            [['name', 'jur_address_id', 'actual_address_id', 'coordinates', 'comment'], 'string'],
            [['name', 'jur_address_id', 'actual_address_id', 'coordinates', 'comment'], 'trim'],
            [['publication_status', 'parent_id', 'created_at', 'cas_user_id', 'company_type', 'abonent', 'updated_at', 'updater'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'jur_address_id' => 'Юридический адрес',
            'actual_address_id' => 'Фактический адрес',
            'coordinates' => 'Координаты фактического адреса',
            'comment' => 'Примечание',
            'publication_status' => 'Статус компании',
            'parent_id' => 'Родительская компания',
            'created_at' => 'Дата создания',
            'cas_user_id' => 'Создал',
            'company_type' => 'Тип компании',
            'abonent' => 'Номер абонента',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ManagCompaniesQuery(get_called_class());
    }

    public function getManagCompaniesTypes()
    {
        return $this->hasOne(ManagCompaniesTypes::className(), ['id' => 'company_type']);
    }

    public function afterSave($insert, $changedAttributes){
        $history = new ManagCompaniesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getCompaniesList(){
        $connection = Yii::$app->db;
        $companies = $connection
                            ->createCommand("
                                SELECT mc.id, mc.name, mct.short_name
                                FROM manag_companies mc
                                LEFT JOIN manag_companies_types mct ON mc.company_type = mct.id
                            ")
                            ->queryAll();
        foreach ($companies as $key => $company) {
            $companies[$key]['name'] = $company['short_name'].' '.$company['name'];
        }
        return ArrayHelper::map($companies, 'id', 'name');
    }

    public static function getActiveCompaniesList(){
        $connection = Yii::$app->db;
        $companies = $connection
                            ->createCommand("
                                SELECT mc.id, concat_ws(' ', mct.short_name, mc.name) as name
                                FROM manag_companies mc
                                LEFT JOIN manag_companies_types mct ON mc.company_type = mct.id
                                WHERE mc.publication_status = 1")
                            ->queryAll();

        return ArrayHelper::map($companies, 'id', 'name');
    }
}
