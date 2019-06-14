<?php

namespace common\models\history;

use Yii;

class ContactsOfficesHistory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'contacts_offices_history';
    }

    public function rules()
    {
        return [
            [['origin_id', 'name', 'publication_status', 'cas_user_id', 'created_at'], 'required'],
            [['origin_id', 'publication_status', 'cas_user_id', 'created_at'], 'integer'],
            [['comment'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origin_id' => 'Origin ID',
            'name' => 'Name',
            'comment' => 'Comment',
            'publication_status' => 'Publication Status',
            'cas_user_id' => 'Cas User ID',
            'created_at' => 'Created At',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ContactsOfficesHistoryQuery(get_called_class());
    }
}
