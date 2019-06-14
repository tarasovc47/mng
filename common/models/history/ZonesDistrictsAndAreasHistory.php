<?php

namespace common\models\history;

use Yii;

class ZonesDistrictsAndAreasHistory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'zones__districts_and_areas_history';
    }

    public function rules()
    {
        return [
            [['origin_id', 'name', 'type', 'parent_id', 'cas_user_id', 'created_at', 'publication_status'], 'required'],
            [['origin_id', 'type', 'parent_id', 'users_group_id', 'cas_user_id', 'created_at', 'publication_status'], 'integer'],
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
            'type' => 'Type',
            'parent_id' => 'Parent ID',
            'users_group_id' => 'Brigade',
            'cas_user_id' => 'Cas User ID',
            'created_at' => 'Created At',
            'publication_status' => 'Publication Status',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesDistrictsAndAreasHistoryQuery(get_called_class());
    }
}
