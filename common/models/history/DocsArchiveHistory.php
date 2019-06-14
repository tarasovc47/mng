<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "docs_archive_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property integer $parent_id
 * @property string $name
 * @property integer $doc_type_id
 * @property string $label
 * @property string $descr
 * @property integer $abonent
 * @property string $client_id
 * @property integer $loki_basic_service_id
 * @property integer $opened_at
 * @property string $extension
 * @property integer $created_at
 * @property integer $cas_user_id
 */
class DocsArchiveHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'docs_archive_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'parent_id', 'name', 'doc_type_id', 'label', 'client_id', 'opened_at', 'created_at', 'cas_user_id'], 'required'],
            [['origin_id', 'parent_id', 'doc_type_id', 'abonent', 'opened_at', 'created_at', 'cas_user_id', 'billing_contract_date'], 'integer'],
            [['name', 'label', 'descr', 'client_id', 'extension', 'billing_contract_id', 'billing_contract_name', 'billing_contract_type'], 'string', 'max' => 255],
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
            'parent_id' => 'Parent ID',
            'name' => 'Name',
            'doc_type_id' => 'Doc Type ID',
            'label' => 'Label',
            'descr' => 'Descr',
            'abonent' => 'Abonent',
            'client_id' => 'Client ID',
            'opened_at' => 'Opened At',
            'extension' => 'Extension',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return DocsArchiveHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\DocsArchiveHistoryQuery(get_called_class());
    }
}
