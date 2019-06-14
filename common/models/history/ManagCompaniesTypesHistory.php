<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "manag_companies_types_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property string $name
 * @property string $short_name
 * @property string $comment
 * @property integer $publication_status
 * @property integer $created_at
 * @property integer $cas_user_id
 */
class ManagCompaniesTypesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manag_companies_types_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'name', 'short_name', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['origin_id', 'publication_status', 'created_at', 'cas_user_id'], 'integer'],
            [['comment'], 'string'],
            [['name', 'short_name'], 'string', 'max' => 255],
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
            'short_name' => 'Short Name',
            'comment' => 'Comment',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ManagCompaniesTypesHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ManagCompaniesTypesHistoryQuery(get_called_class());
    }
}
