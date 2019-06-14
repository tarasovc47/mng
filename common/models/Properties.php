<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use common\components\SiteHelper;

class Properties extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'properties';
    }

    public function getChildren(){
        return $this->hasMany(self::className(), ['parent_id' => 'id']);
    }

    public function getParent(){
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    public function rules()
    {
        return [
            [['comment'], 'string'],
            [['parent_id', 'sort', 'application_type_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['application_type_id'], 'required', 'on' => 'main_attribute'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'comment' => 'Комментарий',
            'parent_id' => 'Родительский атрибут',
            'sort' => 'Сортировка',
            'application_type_id' => 'Тип заявки',
        ];
    }

    public static function find()
    {
        return new \common\models\query\PropertiesQuery(get_called_class());
    }

    public static function loadList($map = [], $parent_id = 0, $offset = 0, $properties = [], $where = []){
        if(empty($map)){
            $map = [ 0 => '&mdash; Корень &mdash;' ];
            $properties = (new Query())
                ->select(['*'])
                ->from(self::tableName())
                ->orderBy(['id' => SORT_ASC]);

            if(!empty($where)){
                $properties = $properties->where($where);
            }

            $properties = $properties->all();

            $controller = Yii::$app->controller->id;
            $action = Yii::$app->controller->action->id;

            if(($controller == "properties") && ($action == "update")){
                $ids = [ 0 => Yii::$app->request->get('id') ];
                $properties = self::removeProperties($properties, $ids);
            }
        }

        if(!empty($properties)){ 
            foreach($properties as $key => $attr){
                if($parent_id == $attr['parent_id']){
                    unset($properties[$key]);
                    $map[$attr['id']] = '';

                    for($i = 0; $i < $offset; $i++) $map[$attr['id']] .= '&nbsp;';

                    $map[$attr['id']] .= $attr['name'];
                    $map = self::loadList($map, $attr['id'], $offset + 5, $properties);
                }
            }
        }

        return $map;
    }

    public static function removeProperties($properties, $ids){
        foreach($properties as $key => $prop){
            if(in_array($prop['id'], $ids)){
                unset($properties[$key]);
            }
            else{
                if(in_array($prop['parent_id'], $ids)){
                    $ids[] = $prop['id'];
                    self::removeProperties($properties, $ids);
                }
            }                
        }

        return $properties;
    }

    public static function loadProperties($application_type_id = false){
        $properties = (new Query())
            ->select(['*'])
            ->from(self::tableName())
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        $properties = self::loadTree($properties);

        if(!$application_type_id){
            return $properties;
        }

        $types = [];
        if(!empty($properties)){
            foreach($properties as $prop){
                if(($prop["application_type_id"] != 0) && (!in_array($prop["application_type_id"], $types))){
                    $types[] = $prop["application_type_id"];
                }
            }
        }

        $types = (new Query())
            ->select(['*'])
            ->from(ApplicationsTypes::tableName())
            ->where(["id" => $types])
            ->all();

        foreach($types as $key => $type){
            $types[$key]["properties"] = [];

            foreach($properties as $prop){
                if($prop["application_type_id"] == $type["id"]){
                    $types[$key]["properties"][] = $prop;
                }
            }
        }

        return $types;
    }

    public static function loadTree($properties, $parent_id = 0){
        $data = [];

        if(!empty($properties)){
            foreach($properties as $key => $prop){
                if($parent_id == $prop['parent_id']){
                    unset($properties[$key]);
                    unset($prop['parent_id']);

                    $data[$prop['id']] = $prop;
                    $data[$prop['id']]['children'] = self::loadTree($properties, $prop['id']);
                }
            }
        }

        return $data;
    }

    public static function loadTreeByType($application_type_id){
        $props = (new Query())
            ->select(['*'])
            ->from(self::tableName())
            ->where([ "application_type_id" => $application_type_id ])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        $ids = [];
        foreach($props as $prop){
            $ids[] = $prop['id'];
        }

        $props = self::loadChildren($ids, $props);

        $ids = ArrayHelper::getColumn($props, 'id');
        /*$fields = (new Query())
            ->select(['*'])
            ->from('fields')
            ->where(["target_id" => $ids, "target_table" => "attributes", "status" => 1])
            ->all();
        $fields = ArrayHelper::index($fields, null, 'target_id');

        $scenarios = (new Query())
            ->select(['ts.id as scenario_id', 'ts.techsup_attribute_id', 'd.id as department_id'])
            ->from('techsup__scenarios as ts')
            ->leftJoin('departments as d', 'ts.department_id = d.id')
            ->where(["ts.techsup_attribute_id" => $ids, "ts.status" => 1])
            ->all();
        $scenarios = ArrayHelper::index($scenarios, null, 'techsup_attribute_id');

        
        $attributes['fields'] = $fields;
        $attributes['scenarios'] = $scenarios;*/

        $properties['properties'] = self::loadTree($props);

        return $properties;
    }

    private static function loadChildren($ids, $properties){
        $more = (new Query())
            ->select(['*'])
            ->from(self::tableName())
            ->where([ "parent_id" => $ids ])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        if(!empty($more)){
            $ids = [];
            foreach($more as $one){
                $ids[] = $one['id'];
            }

            $properties = ArrayHelper::merge($properties, $more);
            $properties = self::loadChildren($ids, $properties);
        }

        return $properties;
    }
}
