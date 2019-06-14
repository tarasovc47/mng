<?php

namespace common\models;

use Yii;

class ApplicationsComments extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'applications_comments';
    }

    public function rules()
    {
        return [
            [['application_event_id', 'comment'], 'required'],
            [['application_event_id'], 'integer'],
            [['comment'], 'string'],
            [['comment'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_event_id' => 'Application Event ID',
            'comment' => 'Comment',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ApplicationsCommentsQuery(get_called_class());
    }
}
