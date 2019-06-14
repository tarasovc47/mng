<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\history\ContactsOfficesHistory;

class ContactsOffices extends \yii\db\ActiveRecord
{
    public $updater;
    public $updated_at;

    public static function tableName()
    {
        return 'contacts_offices';
    }

    public function rules()
    {
        return [
            [['name', 'publication_status', 'cas_user_id', 'created_at'], 'required'],
            [['name', 'comment'], 'string'],
            [['name', 'comment'], 'trim'],
            [['publication_status', 'cas_user_id', 'created_at', 'updater', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Должность',
            'comment' => 'Примечание',
            'publication_status' => 'Статус публикации',
            'cas_user_id' => 'Cas User ID',
            'created_at' => 'Created At',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ContactsOfficesQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes){
        $history = new ContactsOfficesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getOfficesList(){
        $connection = Yii::$app->db;
        $offices = $connection
                            ->createCommand("SELECT id, name FROM contacts_offices WHERE publication_status = 1")
                            ->queryAll();
        return ArrayHelper::map($offices, 'id', 'name');
    }
}
