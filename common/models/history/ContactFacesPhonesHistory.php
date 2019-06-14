<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "contact_faces_phones_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property integer $contact_face_id
 * @property string $phone
 * @property string $comment
 * @property integer $publication_status
 * @property integer $created_at
 * @property integer $cas_user_id
 */
class ContactFacesPhonesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contact_faces_phones_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'contact_face_id', 'phone', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['origin_id', 'contact_face_id', 'publication_status', 'created_at', 'cas_user_id'], 'integer'],
            [['phone', 'comment'], 'string', 'max' => 255],
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
            'contact_face_id' => 'Contact Face ID',
            'phone' => 'Phone',
            'comment' => 'Comment',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ContactFacesPhonesHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ContactFacesPhonesHistoryQuery(get_called_class());
    }
}
