<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\history\ContactFacesPhonesHistory;

class ContactFacesPhones extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;
    
    public static function tableName()
    {
        return 'contact_faces_phones';
    }
    public function rules()
    {
        return [
            [['contact_face_id', 'phone', 'publication_status', 'cas_user_id', 'created_at'], 'required'],
            [['contact_face_id', 'publication_status', 'cas_user_id', 'created_at', 'updated_at', 'updater'], 'integer'],
            [['phone'], 'string', 'length' => 10],
            [['comment'], 'string'],
            [['comment', 'phone'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contact_face_id' => 'Contact Face ID',
            'phone' => 'Номер телефона',
            'comment' => 'Примечание',
            'publication_status' => 'Publication Status',
            'cas_user_id' => 'Cas User ID',
            'created_at' => 'Created At',
        ];
    }

    public function afterSave($insert, $changedAttributes){
        $history = new ContactFacesPhonesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function find()
    {
        return new \common\models\query\ContactFacesPhonesQuery(get_called_class());
    }

    public static function getContactPhones($contact_id){
        $connection = Yii::$app->db;
        $phones = $connection->createCommand("SELECT id, phone FROM contact_faces_phones WHERE contact_face_id = '{$contact_id}' AND publication_status = 1")->queryAll();
        return ArrayHelper::map($phones, 'id', 'phone');
    }

    public static function getContactPhonesComments($contact_id){
        $connection = Yii::$app->db;
        $phones = $connection->createCommand("SELECT id, comment FROM contact_faces_phones WHERE contact_face_id = '{$contact_id}' AND publication_status = 1")->queryAll();
        return ArrayHelper::map($phones, 'id', 'comment');
    }

    public static function issetPhone($phone, $contact_id = false){
        $where_contact = '';
        if ($contact_id) {
            $where_contact = " AND contact_face_id != '".$contact_id."'";
        }
        $connection = Yii::$app->db;
        $phones = $connection->createCommand("SELECT contact_face_id, phone FROM contact_faces_phones WHERE phone = '{$phone}' AND publication_status = 1".$where_contact)->queryAll();
        return $phones;
    }
}
