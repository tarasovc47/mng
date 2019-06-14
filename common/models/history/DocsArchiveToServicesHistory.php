<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "docs_archive_to_services_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property integer $doc_id
 * @property integer $service_id
 * @property integer $publication_status
 * @property integer $created_at
 * @property integer $cas_user_id
 */
class DocsArchiveToServicesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'docs_archive_to_services_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'doc_id', 'service_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['origin_id', 'doc_id', 'service_id', 'publication_status', 'created_at', 'cas_user_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origin_id' => 'Origin ID',
            'doc_id' => 'Doc ID',
            'service_id' => 'Service ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\DocsArchiveToServicesHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\DocsArchiveToServicesHistoryQuery(get_called_class());
    }
}
