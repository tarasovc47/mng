<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\components\SiteHelper;
class Departments extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'departments';
    }

    public function getUsersGroups(){
        return $this->hasMany(UsersGroups::className(), ['department_id' => 'id']);
    }

    public function getCasUsers(){
        return $this->hasMany(CasUser::className(), ['department_id' => 'id']);
    }

    public function rules()
    {
        return [
            [['name', 'cas_name'], 'required'],
            [['name', 'cas_name'], 'string', 'max' => 255],
            [['name', 'cas_name'], 'trim'],
            [['cas_name'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'cas_name' => 'Cas название',
        ];
    }

    public static function find()
    {
        return new \common\models\query\DepartmentsQuery(get_called_class());
    }

    public static function findByCasname($cas_name)
    {
        return self::findOne(['cas_name' => $cas_name]);
    }

    public static function loadList(){
        $departments = (new Query())
            ->select(['id', 'name'])
            ->from('departments')
            ->orderBy(["id" => SORT_ASC])
            ->all();

        return ArrayHelper::map($departments, 'id', 'name');
    }

    public static function getDepartmentsByCasNames($cas_names){
        $departments = [];
        if(!is_array($cas_names)){
//            $cas_names = SiteHelper::to_php_array($cas_names);

        }

        foreach($cas_names as $key => $cas_name){
            $cas_name = mb_strtolower($cas_name);
            $cas_names[$key] = trim($cas_name, '"');
        }

        $records = (new Query())
            ->select(['*'])
            ->from('departments')
            ->where([ 'cas_name' => $cas_names ])
            ->all();



        foreach($records as $record){
            $departments[$record['id']]['name'] = $record['name'];
            $departments[$record['id']]['cas_name'] = $record['cas_name'];
        }

        return $departments;
    }
}
