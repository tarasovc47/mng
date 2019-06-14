<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "manag_companies_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property string $name
 * @property string $jur_address_id
 * @property string $actual_address_id
 * @property string $coordinates
 * @property string $comment
 * @property integer $publication_status
 * @property integer $created_at
 * @property integer $cas_user_id
 * @property integer $parent_id
 * @property integer $company_type
 * @property integer $abonent
 */
class ManagCompaniesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manag_companies_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'name', 'jur_address_id', 'actual_address_id', 'publication_status', 'created_at', 'cas_user_id', 'parent_id', 'company_type'], 'required'],
            [['origin_id', 'publication_status', 'created_at', 'cas_user_id', 'parent_id', 'company_type', 'abonent'], 'integer'],
            [['comment'], 'string'],
            [['name', 'jur_address_id', 'actual_address_id', 'coordinates'], 'string', 'max' => 255],
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
            'jur_address_id' => 'Jur Address ID',
            'actual_address_id' => 'Actual Address ID',
            'coordinates' => 'Coordinates',
            'comment' => 'Comment',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
            'parent_id' => 'Parent ID',
            'company_type' => 'Company Type',
            'abonent' => 'Abonent',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ManagCompaniesHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ManagCompaniesHistoryQuery(get_called_class());
    }
}
