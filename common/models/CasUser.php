<?php

namespace common\models;

use Yii;
use yii\web\Cookie;
use yii\db\Query;
use common\components\SiteHelper;

class CasUser extends \yii\db\ActiveRecord
{
    public static function tableName(){
        return 'cas_user';
    }

    public function getApplicationsEvents(){
        return $this->hasMany(ApplicationsEvents::className(), ['cas_user_id' => 'id']);
    }

    public function getDepartment(){
        return $this->hasOne(Departments::className(), ['id' => 'department_id']);
    }

    public function getUsersGroup(){
        return $this->hasOne(UsersGroups::className(), ['id' => 'group_id']);
    }

    public function rules(){
        return [
            [['login', 'cas_id', 'group_id'], 'required'],
            [['login', 'roles', 'first_name', 'last_name', 'middle_name'], 'string'],
            [['login', 'roles', 'first_name', 'last_name', 'middle_name'], 'trim'],
            [['cas_id', 'department_id', 'group_id'], 'integer'],
            [['cas_id'], 'unique'],
        ];
    }

    public function attributeLabels(){
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'cas_id' => 'Cas ID',
            'roles' => 'Права',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'department_id' => 'Отдел',
            'group_id' => 'Группа отдела',
        ];
    }

    public static function find(){
        return new \common\models\query\CasUserQuery(get_called_class());
    }

    public static function loadByDepartment($department_id){
        $users = self::find()->where([ "department_id" => $department_id ])->all();

        $list = [];
        foreach($users as $user){
            $list[$user->id] = $user->last_name;
            $list[$user->id] .= " ";
            $list[$user->id] .= $user->first_name;
        }

        return $list;
    }

    public static function loadList($condition){
        $users = self::find()->where($condition)->all();

        $list = [];
        foreach($users as $user){
            $list[$user->id] = $user->last_name;
            $list[$user->id] .= " ";
            $list[$user->id] .= $user->first_name;
        }

        return $list;
    }

    public static function setDefaultGroup($id, $group_id){
        Yii::$app->db->createCommand()
            ->update(self::tableName(), 
                [ 'group_id' => $group_id ],
                [ 'id' => $id ]
            )->execute();

        return true;
    }

    public static function saveGroups($data){
        foreach($data as $group_id => $users_id){
            Yii::$app->db->createCommand()
                ->update(self::tableName(), 
                    [ 'group_id' => $group_id ],
                    [ 'id' => $users_id ]
                )->execute();
        }

        return true;
    }
}
