<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use common\models\Departments;
use common\components\SiteHelper;

class Access extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'access';
    }

    public function rules()
    {
        return [
            [['module_setting_key', 'value'], 'required'],
            [['cas_user_id', 'department_id', 'module_setting_key', 'value'], 'integer'],
            [['descr'], 'string'],
            [['descr'], 'trim'],
        ];
    }

    public function attributeLabels(){
        return [
            'id' => 'ID',
            'cas_user_id' => 'Cas User ID',
            'department_id' => 'Department ID',
            'module_setting_key' => 'Module Setting Key',
            'value' => 'Value',
            'descr' => 'Descr',
        ];
    }

    public static function getValues($value = -1){
        $values = array(
            0 => "Нет доступа",
            1 => "Просмотр",
            2 => "Просмотр и редактирование",
        );

        if($value > -1)
            return $values[$value];

        return $values;
    }

    # Получает все настройки доступа для отдела компании
    public static function getAllModulesSettingsForDepartment($department_id){
        $data = [];

        $department_accesses = (new Query())
            ->from(self::tableName())
            ->where([ 'cas_user_id' => 0, 'department_id' => $department_id ])
            ->all();

        $settings = (new Query())
            ->select([
                'm.descr as module_name',
                'm.id as module_id',
                'ms.name as module_setting_name',
                'ms.id as module_setting_id',
                'ms.descr as module_setting_descr',
                'ms.uniq_key as module_setting_key',
                'a.id as access_id',
                'a.descr as access_descr',
                'a.value as access_value',
            ])
            ->from("modules as m")
            ->leftJoin('modules_settings as ms', 'm.id = ms.module_id')
            ->leftJoin('access as a', 'ms.uniq_key = a.module_setting_key')
            ->where([ 'a.cas_user_id' => 0, 'a.department_id' => $department_id ])
            ->all();

        foreach($settings as $setting){
            $module_id = $setting["module_id"];
            $module_setting_id = $setting["module_setting_id"];

            if(!isset($data['modules'][$module_id])){
                $data['modules'][$module_id]["name"] = $setting["module_name"];
                $data['modules'][$module_id]["settings"] = [];
            }

            if(!empty($module_setting_id)){
                $data['modules'][$module_id]["settings"][$module_setting_id] = [
                    "name" => $setting["module_setting_name"],
                    "descr" => $setting["module_setting_descr"],
                    "access_value" => $setting["access_value"],
                    "access_id" => $setting["access_id"],
                ];
            }
        }
        return $data;
    }

    # Получает все настройки доступов которые существуют в системе
    # + уточняет (перезаписывает) их индивидуальными настройками пользователя
    public static function getAllModulesSettingsForCasUser($cas_user){
        $data = [];
//        SiteHelper::debug($cas_user->roles);
//        die();
//        $cas_names = SiteHelper::to_php_array($cas_user->roles);
        $cas_names = $cas_user->roles;

        $departments = Departments::getDepartmentsByCasNames($cas_names);


        $user_accesses = (new Query())
            ->from(self::tableName())
            ->where([ 'cas_user_id' => $cas_user->id ])
            ->all();

        $departments_accesses = (new Query())
            ->from(self::tableName())
            ->where([ 'cas_user_id' => 0 ])
            ->all();

        $settings = (new Query())
            ->select([
                'm.descr as module_name',
                'm.id as module_id',
                'ms.name as module_setting_name',
                'ms.id as module_setting_id',
                'ms.descr as module_setting_descr',
                'ms.uniq_key as module_setting_key',
            ])
            ->from("modules as m")
            ->leftJoin('modules_settings as ms', 'm.id = ms.module_id')
            ->all();

        foreach($settings as $setting){
            $module_id = $setting["module_id"];
            $module_setting_id = $setting["module_setting_id"];
            $module_setting_key = $setting["module_setting_key"];

            if(!isset($data['modules'][$module_id])){
                $data['modules'][$module_id]["name"] = $setting["module_name"];
                $data['modules'][$module_id]["settings"] = [];
            }

            if(!empty($module_setting_id)){
                $access_value = "";
                $access_id = "";
                $type = 0;
                $department = 0;

                foreach($departments_accesses as $access){
                    if(isset($departments[$access["department_id"]])){
                        if($access["module_setting_key"] == $module_setting_key){
                            if( ($type == 0) || (($type == 1) && ($access["value"] > $access_value)) ){
                                $access_value = $access["value"];
                                $access_id = $access["id"];
                                $type = 1;
                                $department = $access["department_id"];
                            }
                        }
                    }
                }

                foreach($user_accesses as $access){
                    if($access["module_setting_key"] == $module_setting_key){
                        $access_value = $access["value"];
                        $access_id = $access["id"];
                        $type = 2;
                        $department = 0;
                        break;
                    }
                }

                $data['modules'][$module_id]["settings"][$module_setting_id] = [
                    "name" => $setting["module_setting_name"],
                    "descr" => $setting["module_setting_descr"],
                    "key" => $module_setting_key,
                    "access_value" => $access_value,
                    "access_id" => $access_id,
                    "type" => $type,
                    "department" => $department,
                ];
            }
        }

        $data['departments'] = $departments;

        return $data;
    }

    # Проверка доступа для конкретного пользователя к определенной настройке как по его cas_user_id, так и по его принадлежности к отделу
    public static function hasAccess($cas_user_id, $roles, $module_setting_key){
        $departments = Departments::getDepartmentsByCasNames($roles);
        $departments_ids = '';

        foreach($departments as $id => $department){
            $departments_ids .= ($departments_ids == '') ? $id : "," . $id;
        }

        $access = (new Query())
            ->select(['value'])
            ->from('access');

        if(!empty($departments_ids)){
            $access->where([
                'or', 
                ['and', 'cas_user_id = :cas_user_id', 'module_setting_key = :module_setting_key'],
                ['and', 'cas_user_id = 0', 'department_id IN (' . $departments_ids . ') ', 'module_setting_key = :module_setting_key']
            ]);
        }
        else{
            $access->where(['and', 'cas_user_id = :cas_user_id', 'module_setting_key = :module_setting_key']);
        }

        $access = $access->addParams([':cas_user_id' => $cas_user_id])
            ->addParams([':module_setting_key' => $module_setting_key])
            ->orderBy([ "cas_user_id" => SORT_DESC, "value" => SORT_DESC ])
            ->one();

        return $access['value'];
    }

    # Удаление индивидуального доступа
    public static function removeAccess($cas_user_id, $module_setting_key){
        Yii::$app->db->createCommand()
            ->delete(self::tableName(), [ 
                'cas_user_id' => $cas_user_id,
                'module_setting_key' => $module_setting_key,
            ])
            ->execute();
    }

    # Установка индивидуального доступа
    public static function insertAccess($cas_user_id, $module_setting_key, $value, $descr = ""){
        Yii::$app->db->createCommand()
            ->insert(self::tableName(), [
                'cas_user_id' => $cas_user_id,
                'department_id' => 0, 
                'module_setting_key' => $module_setting_key,
                'value' => $value,
                'descr' => $descr,
            ])
            ->execute();
    }
}
