<?php

namespace common\models\history;

use Yii;

class DocsArchiveToConnectionTechnologiesHistory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'docs_archive_to_connection_technologies_history';
    }

    public function rules()
    {
        return [
            [['origin_id', 'doc_id', 'conn_tech_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['origin_id', 'doc_id', 'conn_tech_id', 'publication_status', 'created_at', 'cas_user_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origin_id' => 'Origin ID',
            'doc_id' => 'Doc ID',
            'conn_tech_id' => 'Conn Tech ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\DocsArchiveToConnectionTechnologiesHistoryQuery(get_called_class());
    }
}
