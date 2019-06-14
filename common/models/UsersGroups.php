<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class UsersGroups extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'users_groups';
    }

    public function getDepartment()
    {
        return $this->hasOne(Departments::className(), ['id' => 'department_id']);
    }

    public function rules()
    {
        return [
            [['department_id', 'head_id'], 'integer'],
            [['department_id', 'name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'trim'],
        ];
    }

    public function getCasUsers()
    {
        return $this->hasMany(CasUser::className(), [ 'group_id' => 'id' ]);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'head_id' => 'Руководитель',
            'department_id' => 'Department ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\UsersGroupsQuery(get_called_class());
    }

    public static function loadList($condition = []){
        $groups = (new Query())
            ->select(['id', 'name'])
            ->from(self::tableName());

        if(!empty($condition)){
            $groups = $groups->where($condition);
        }
            
        $groups = $groups->all();

        return ArrayHelper::map($groups, 'id', 'name');
    }
}
