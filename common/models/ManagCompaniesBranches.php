<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\history\ManagCompaniesBranchesHistory;

class ManagCompaniesBranches extends \yii\db\ActiveRecord
{
    public $contacts;
    public $updated_at;
    public $updater;

    public static function tableName()
    {
        return 'manag_companies_branches';
    }

    public function rules()
    {
        return [
            [['company_id', 'created_at', 'name', 'cas_user_id', 'publication_status', 'actual_address_id'], 'required'],
            [['company_id', 'created_at', 'cas_user_id', 'publication_status', 'updated_at', 'updater'], 'integer'],
            [['name', 'actual_address_id', 'coordinates', 'comment'], 'string'],
            [['name', 'actual_address_id', 'coordinates', 'comment'], 'trim'],
            [['contacts'], 'each', 'rule' => ['string']],
            [['contacts'], 'each', 'rule' => ['trim']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Управляющая компания',
            'name' => 'Название',
            'actual_address_id' => 'Фактический адрес',
            'coordinates' => 'Координаты',
            'comment' => 'Примечание',
            'contacts' => 'Контактные лица',
            'created_at' => 'Дата создания',
            'cas_user_id' => 'Создал',
            'publication_status' => 'Статус публикации',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ManagCompaniesBranchesQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes){
        $history = new ManagCompaniesBranchesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getBranchesList($company_id){
        $connection = Yii::$app->db;
        $branches = $connection
                    ->createCommand("
                        SELECT id, name 
                        FROM manag_companies_branches 
                        WHERE company_id = '".$company_id."' AND publication_status = 1
                        ORDER BY id ASC
                    ")
                    ->queryAll();
        return ArrayHelper::map($branches, 'id', 'name');
    }
}
