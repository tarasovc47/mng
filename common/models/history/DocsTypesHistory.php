<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "docs_types_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property string $name
 * @property integer $available_for
 * @property string $folder
 * @property integer $sub_document
 * @property integer $created_at
 * @property integer $cas_user_id
 */
class DocsTypesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'docs_types_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'name', 'available_for', 'folder', 'sub_document', 'created_at', 'cas_user_id'], 'required'],
            [['origin_id', 'available_for', 'sub_document', 'created_at', 'cas_user_id'], 'integer'],
            [['name', 'folder'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'available_for' => 'Available For',
            'folder' => 'Folder',
            'sub_document' => 'Sub Document',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return DocsTypesHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\DocsTypesHistoryQuery(get_called_class());
    }
}
