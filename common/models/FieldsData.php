<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\components\SiteHelper;

class FieldsData extends \yii\db\ActiveRecord
{
    public static function tableDataName($name)
    {
        return 'fields_data_' . $name;
    }

    public static function validateFields($attributes_ids, $data){
        $status = true;
        $required_fields = [];

        $attributes_ids = SiteHelper::to_php_array($attributes_ids);

        $fields = (new Query())
            ->select(['*'])
            ->from(Fields::tableName())
            ->where([ "target_id" => $attributes_ids, "target_table" => "attributes" ])
            ->all();
        $fields = ArrayHelper::index($fields, 'id');

        foreach($fields as $id => $field){
            $field_data = unserialize($field['data']);
            $fields[$id]['data'] = $field_data;

            if($field_data['required']){
                $required_fields[$id] = $id;
            }
        }

        $status = self::areAllRequiredFieldsExist($required_fields, $data);

        if($status){
            $fields_ids = [];
            foreach($data as $field_id => $field_data){
                if(!in_array($field_id, $fields_ids) && !isset($fields[$field_id])){
                    $fields_ids[] = $field_id;
                }
            }

            if(!empty($fields_ids)){
                $add_fields = (new Query())
                    ->select(['*'])
                    ->from(Fields::tableName())
                    ->where(["id" => $fields_ids])
                    ->all();
                $add_fields = ArrayHelper::index($add_fields, 'id');

                foreach($add_fields as $id => $field){
                    $add_fields[$id]['data'] = unserialize($field['data']);
                }

                $fields = ArrayHelper::merge($fields, $add_fields);
            }

            foreach($data as $field_id => $field_data){
                $field = $fields[$field_id];
                $what_a_field = $field['data']['type'];
                $what_a_field .= isset($field['data']['view']) ? "_" . $field['data']['view'] : "";

                if($what_a_field == "list_checkbox"){
                    if($field['data']['required'] && (!isset($field_data['value']) || empty($field_data['value']))){
                        $data[$field_id]['error'] = "Поле обязательно для заполнения";
                        $status = false;
                        continue;
                    }

                    if(($field['data']['cardinality'] > 0) && (count($field_data['value']) > $field['data']['cardinality'])){
                        $data[$field_id]['error'] = "Количество отмеченных значений не должно превышать " . $field['data']['cardinality'];
                        $status = false;
                        continue;
                    }
                }
            }

            $data["status"] = $status;
        }
        else{
            $data["status"] = "too_little_fields";
            $data["error"] = "Произошла ошибка. Перезагрузите страницу и попробуйте снова";
        }

        return $data;
    }

    private static function areAllRequiredFieldsExist($required_fields, $fields){
        foreach($fields as $field_id => $field_data){
            if(in_array($field_id, $required_fields)){
                unset($required_fields[$field_id]);
            }
        }

        return empty($required_fields);
    }

    public static function createData($data, $application_event, $created_at, $cas_user_id){
        $fields = array_keys($data);
        $fields = (new Query())
            ->select(['id', 'name'])
            ->from(Fields::tableName())
            ->where(["id" => $fields])
            ->all();
        $fields = ArrayHelper::map($fields, 'id', 'name');

        foreach($data as $field_id => $field_data){
            if(is_array($field_data['value'])){
                $values = [];
                foreach($field_data['value'] as $value){
                    $values[] = [ $application_event, $value, $created_at, $cas_user_id ];
                }
                
                Yii::$app->db->createCommand()
                        ->batchInsert(self::tableDataName($fields[$field_id]), [
                            'application_event_id', 
                            'value',
                            'created_at',
                            'cas_user_id'
                        ], $values)
                        ->execute();
            }
            else{
                Yii::$app->db->createCommand()
                    ->insert(self::tableDataName($fields[$field_id]), [
                        'application_event_id' => $application_event, 
                        'value' => $field_data['value'],
                        'created_at' => $created_at,
                        'cas_user_id' => $cas_user_id
                    ])
                    ->execute();
            }
        }

        return true;
    }
}
