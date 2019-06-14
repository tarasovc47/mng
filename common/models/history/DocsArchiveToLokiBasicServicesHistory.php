<?php

namespace common\models\history;

use Yii;

class DocsArchiveToLokiBasicServicesHistory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'docs_archive_to_loki_basic_services_history';
    }

    public function rules()
    {
        return [
            [['origin_id', 'doc_id', 'loki_basic_service_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['origin_id', 'doc_id', 'loki_basic_service_id', 'publication_status', 'created_at', 'cas_user_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origin_id' => 'Origin ID',
            'doc_id' => 'Doc ID',
            'loki_basic_service_id' => 'Loki Basic Service ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\DocsArchiveToLokiBasicServicesHistoryQuery(get_called_class());
    }
}
