<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "manag_companies_branches_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property string $name
 * @property string $actual_address_id
 * @property string $coordinates
 * @property string $comment
 * @property integer $company_id
 * @property integer $publication_status
 * @property integer $created_at
 * @property integer $cas_user_id
 */
class ManagCompaniesBranchesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manag_companies_branches_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'name', 'actual_address_id', 'company_id', 'publication_status', 'created_at', 'cas_user_id'], 'required'],
            [['origin_id', 'company_id', 'publication_status', 'created_at', 'cas_user_id'], 'integer'],
            [['comment'], 'string'],
            [['name', 'actual_address_id', 'coordinates'], 'string', 'max' => 255],
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
            'actual_address_id' => 'Actual Address ID',
            'coordinates' => 'Coordinates',
            'comment' => 'Comment',
            'company_id' => 'Company ID',
            'publication_status' => 'Publication Status',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ManagCompaniesBranchesHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ManagCompaniesBranchesHistoryQuery(get_called_class());
    }
}
