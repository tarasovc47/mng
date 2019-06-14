<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "manag_companies_to_contacts_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property integer $company_id
 * @property integer $branch_id
 * @property integer $created_at
 * @property integer $cas_user_id
 * @property integer $publication_status
 * @property integer $contact_face_id
 * @property integer $contact_office_id
 * @property string $comment
 */
class ManagCompaniesToContactsHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manag_companies_to_contacts_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'company_id', 'branch_id', 'created_at', 'cas_user_id', 'publication_status', 'contact_face_id', 'contact_office_id'], 'required'],
            [['origin_id', 'company_id', 'branch_id', 'created_at', 'cas_user_id', 'publication_status', 'contact_face_id', 'contact_office_id'], 'integer'],
            [['comment'], 'string'],
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
            'company_id' => 'Company ID',
            'branch_id' => 'Branch ID',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
            'publication_status' => 'Publication Status',
            'contact_face_id' => 'Contact Face ID',
            'contact_office_id' => 'Contact Office ID',
            'comment' => 'Comment',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ManagCompaniesToContactsHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ManagCompaniesToContactsHistoryQuery(get_called_class());
    }
}
