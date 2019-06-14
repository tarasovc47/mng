<?php

namespace common\models\history;

use Yii;

class TariffsGroupsHistory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tariffs_groups_history';
    }

    public function rules()
    {
        return [
            [['origin_id', 'name', 'publication_status', 'created_at', 'cas_user_id', 'abonent_type'], 'required'],
            [['origin_id', 'publication_status', 'created_at', 'cas_user_id', 'abonent_type'], 'integer'],
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
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\TariffsGroupsHistoryQuery(get_called_class());
    }
}
