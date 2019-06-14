<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\ContactFaces;
use common\models\history\ContactFacesEmailsHistory;

class ContactFacesEmails extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;

    public static function tableName()
    {
        return 'contact_faces_emails';
    }

    public function getContactFaces()
    {
        return $this->hasOne(ContactFaces::className(), ['id' => 'contact_face_id']);
    }

    public function rules()
    {
        return [
            [['contact_face_id', 'email', 'publication_status', 'cas_user_id', 'created_at'], 'required'],
            [['contact_face_id', 'publication_status', 'cas_user_id', 'created_at', 'updated_at', 'updater'], 'integer'],
            [['email', 'comment'], 'string'],
            [['email', 'comment'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contact_face_id' => 'Contact Face ID',
            'email' => 'Email',
            'comment' => 'Примечание',
            'publication_status' => 'Publication Status',
            'cas_user_id' => 'Cas User ID',
            'created_at' => 'Created At',
        ];
    }

    public function afterSave($insert, $changedAttributes){
        $history = new ContactFacesEmailsHistory();
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
        return new \common\models\query\ContactFacesEmailsQuery(get_called_class());
    }

    public static function getContactEmails($contact_id){
        $connection = Yii::$app->db;
        $emails = $connection->createCommand("SELECT id, email FROM contact_faces_emails WHERE contact_face_id = '{$contact_id}' AND publication_status = 1")->queryAll();
        return ArrayHelper::map($emails, 'id', 'email');
    }

    public static function getContactEmailsComments($contact_id){
        $connection = Yii::$app->db;
        $emails = $connection->createCommand("SELECT id, comment FROM contact_faces_emails WHERE contact_face_id = '{$contact_id}' AND publication_status = 1")->queryAll();
        return ArrayHelper::map($emails, 'id', 'comment');
    }
}
