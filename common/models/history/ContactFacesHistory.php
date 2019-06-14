<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "contact_faces_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property string $name
 * @property string $comment
 * @property integer $publication_status
 * @property integer $created_at
 * @property integer $cas_user_id
 */
class ContactFacesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contact_faces_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'name', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['origin_id', 'publication_status', 'created_at', 'cas_user_id'], 'integer'],
            [['comment'], 'string'],
            [['name'], 'string', 'max' => 255],
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
            'comment' => 'Comment',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ContactFacesHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ContactFacesHistoryQuery(get_called_class());
    }
}
