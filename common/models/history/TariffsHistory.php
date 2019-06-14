<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "tariffs_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property string $name
 * @property string $comment
 * @property integer $for_abonent_type
 * @property integer $created_at
 * @property integer $closed_at
 * @property integer $package
 * @property integer $opened_at
 * @property integer $cas_user_id
 * @property integer $priority
 * @property integer $public
 * @property integer $price
 */
class TariffsHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariffs_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'name', 'for_abonent_type', 'created_at', 'package', 'opened_at', 'cas_user_id', 'priority', 'public', 'price'], 'required'],
            [['origin_id', 'for_abonent_type', 'created_at', 'closed_at', 'package', 'opened_at', 'cas_user_id', 'priority', 'public', 'price'], 'integer'],
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
            'for_abonent_type' => 'For Abonent Type',
            'created_at' => 'Created At',
            'closed_at' => 'Closed At',
            'package' => 'Package',
            'opened_at' => 'Opened At',
            'cas_user_id' => 'Cas User ID',
            'priority' => 'Priority',
            'public' => 'Public',
            'price' => 'Price',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\TariffsHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\TariffsHistoryQuery(get_called_class());
    }
}
