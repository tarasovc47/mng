<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use common\components\SiteHelper;

class Attributes extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'attributes';
    }

    public function getFields(){
        return $this->hasMany(Fields::className(), ['target_id' => 'id'])->where([ "target_table" => self::tableName() ]);
    }

    public function getDepartment(){
        return $this->hasOne(Departments::className(), ['id' => 'department_id']);
    }

    public function getChildren(){
        return $this->hasMany(Attributes::className(), ['parent_id' => 'id']);
    }

    public function getParent(){
        return $this->hasOne(Attributes::className(), ['id' => 'parent_id']);
    }

    public function rules()
    {
        return [
            [['name', 'connection_technology_id'], 'required'],
            [['comment'], 'string'],
            [['comment', 'name'], 'trim'],
            [['parent_id', 'connection_technology_id', 'sort', 'department_id', 'application_type_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['connection_technology_id'], 'default', 'value' => 0],

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
            'connection_technology_id' => 'Технология подключения',
            'sort' => 'Сортировка',
            'department_id' => 'Отдел компании',
            'application_type_id' => 'Тип заявки',
        ];
    }

    public static function find()
    {
        return new \common\models\query\AttributesQuery(get_called_class());
    }

    public static function loadList($map = [], $parent_id = 0, $offset = 0, $attrs = [], $where = []){
        if(empty($map)){
            $map = [ 0 => '&mdash; Корень &mdash;' ];
            $attrs = (new Query())
                ->select(['*'])
                ->from(self::tableName())
                ->orderBy(['id' => SORT_ASC]);

            if(!empty($where)){
                $attrs = $attrs->where($where);
            }

            $attrs = $attrs->all();

            $controller = Yii::$app->controller->id;
            $action = Yii::$app->controller->action->id;

            if(($controller == "attributes") && ($action == "update")){
                $ids = [ 0 => Yii::$app->request->get('id') ];
                $attrs = self::removeAttrs($attrs, $ids);
            }
        }

        if(!empty($attrs)){ 
            foreach($attrs as $key => $attr){
                if($parent_id == $attr['parent_id']){
                    unset($attrs[$key]);
                    $map[$attr['id']] = '';

                    for($i = 0; $i < $offset; $i++) $map[$attr['id']] .= '&nbsp;';

                    $map[$attr['id']] .= $attr['name'];
                    $map = self::loadList($map, $attr['id'], $offset + 5, $attrs);
                }
            }
        }

        return $map;
    }

    public static function loadAttributes($department_id, $connection_technologies = false, $services = false){
        $attributes = (new Query())
            ->select(['*'])
            ->from(self::tableName())
            ->where(["department_id" => $department_id])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        $attributes = self::loadTree($attributes);

        if(!$connection_technologies){
            return $attributes;
        }

        $connection_technologies = [];
        if(!empty($attributes)){
            foreach($attributes as $attribute){
                if(($attribute["connection_technology_id"] != 0) && (!in_array($attribute["connection_technology_id"], $connection_technologies))){
                    $connection_technologies[] = $attribute["connection_technology_id"];
                }
            }
        }

        $connection_technologies = (new Query())
            ->select(['*'])
            ->from(ConnectionTechnologies::tableName())
            ->where(["id" => $connection_technologies])
            ->all();

        $services_id = [];

        foreach($connection_technologies as $key => $technology){
            $connection_technologies[$key]["attributes"] = [];

            foreach($attributes as $attribute){
                if($attribute["connection_technology_id"] == $technology["id"]){
                    $connection_technologies[$key]["attributes"][] = $attribute;
                }
            }

            if($services && !in_array($technology['service_id'], $services_id)){
                $services_id[] = $technology['service_id'];
            }
        }

        if(!$services){
            return $connection_technologies;
        }

        $services = (new Query())
            ->select(['*'])
            ->from(Services::tableName())
            ->where(["id" => $services_id])
            ->all();

        foreach($services as $key => $service){
            $services[$key]["connection_technologies"] = [];

            foreach($connection_technologies as $technology){
                if($technology["service_id"] == $service["id"]){
                    $services[$key]["connection_technologies"][] = $technology;
                }
            }
        }

        return $services;
    }


    public static function removeAttrs($attrs, $ids){
        foreach($attrs as $key => $attr){
            if(in_array($attr['id'], $ids)){
                unset($attrs[$key]);
            }
            else{
                if(in_array($attr['parent_id'], $ids)){
                    $ids[] = $attr['id'];
                    self::removeAttrs($attrs, $ids);
                }
            }                
        }

        return $attrs;
    }

    public static function loadTree($attrs, $parent_id = 0){
        $data = [];

        if(!empty($attrs)){
            foreach($attrs as $key => $attr){
                if($parent_id == $attr['parent_id']){
                    unset($attrs[$key]);
                    unset($attr['parent_id']);

                    $data[$attr['id']] = $attr;
                    $data[$attr['id']]['children'] = self::loadTree($attrs, $attr['id']);
                }
            }
        }

        return $data;
    }

    public static function getConnTechs(){
        $connTechs = (new Query())
            ->select(['ct.name as conn_name', 'ct.id', 's.name'])
            ->from('connection_technologies as ct')
            ->leftJoin('services as s', 'ct.service_id = s.id')
            ->all();

        return ArrayHelper::map($connTechs, 'id', 'conn_name', 'name');
    }

    public static function getAllAttributesWithServicesAndConnTechs($department_id = 0){
        $tree = [];
        $attrs = (new Query())
            ->select(['attrs.id', 
                    'attrs.name', 
                    'attrs.parent_id', 
                    'attrs.connection_technology_id', 
                    'attrs.sort',
                    'ct.name as techname',
                    'ct.id as techid',
                    'ct.service_id as techserviceid',
                    's.name as servicename',
                    's.id as serviceid'])
            ->from(self::tableName() . ' as attrs')
            ->leftJoin('connection_technologies as ct', 'attrs.connection_technology_id = ct.id')
            ->leftJoin('services as s', 'ct.service_id = s.id')
            ->orderBy([ "sort" => SORT_ASC ]);

        if($department_id != 0){
            $attrs = $attrs->where(["attrs.department_id" => $department_id]);
        }

        $attrs = $attrs->all();

        if(!empty($attrs)){ 
            foreach($attrs as $key => $attr){
                if($attr['parent_id'] == 0){
                    $serviceId = $attr['serviceid'];
                    $techId = $attr['techserviceid'];

                    if(!isset($tree[$serviceId])){
                        $tree[$serviceId]['name'] = $attr['servicename'];
                        $tree[$serviceId]['techs'] = [];
                    }

                    if(!isset($tree[$serviceId]['techs'][$techId])){
                       $tree[$serviceId]['techs'][$techId]['name'] = $attr['techname']; 
                       $tree[$serviceId]['techs'][$techId]['attrs'] = [];
                    }
                }
            }

            $attrs = self::loadTree($attrs);

            foreach($attrs as $attr){
                $serviceId = $attr['serviceid'];
                $techId = $attr['techserviceid'];

                $tree[$serviceId]['techs'][$techId]['attrs'][] = $attr; 
            }
        }
        
        return $tree;
    }

    public static function saveSort($arraySort){
        if(is_array($arraySort) && !empty($arraySort)){
            foreach($arraySort as $branch){
                $data = self::saveSortHandler($branch);
            }

            $query = "UPDATE " . self::tableName() ." SET sort = CASE id" . $data["when"] . " END WHERE id IN(" . $data["ids"] . ")";
            $command = Yii::$app->db->createCommand($query)->execute();
        }

        return true;
    }

    public static function saveSortHandler($arraySort, $data = [ "when" => "", "ids" => "" ]){
        if(is_array($arraySort) && !empty($arraySort)){
            foreach($arraySort as $id => $item){
                $id = (int)$id;
                $sort = (int)$item['sort'];

                $data["when"] .= " WHEN " . $id . " THEN " . $sort;
                $data["ids"] .= empty($data["ids"]) ? $id : "," . $id;

                if(isset($item['children']) && !empty($item['children'])){
                    $data = self::saveSortHandler($item['children'], $data);
                }
            }
        }

        return $data;
    }

    public static function loadTreeByTechnologyId($ct_id, $department_id){
        $attrs = (new Query())
            ->select(['*'])
            ->from(self::tableName())
            ->where(["connection_technology_id" => $ct_id, "department_id" => $department_id])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        $ids = [];
        foreach($attrs as $attr){
            $ids[] = $attr['id'];
        }

        $attrs = self::loadChildren($ids, $attrs);

        $ids = ArrayHelper::getColumn($attrs, 'id');
        $fields = (new Query())
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

        $attributes['attrs'] = self::loadTree($attrs);
        $attributes['fields'] = $fields;
        $attributes['scenarios'] = $scenarios;

        return $attributes;
    }

    private static function loadChildren($ids, $attrs){
        $more = (new Query())
            ->select(['*'])
            ->from(self::tableName())
            ->where(["parent_id" => $ids])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        if(!empty($more)){
            $ids = [];
            foreach($more as $one){
                $ids[] = $one['id'];
            }

            $attrs = ArrayHelper::merge($attrs, $more);
            $attrs = self::loadChildren($ids, $attrs);
        }

        return $attrs;
    }

    public function loadFieldsDataByEvent($application_event_id){
        if(!empty($this->fields)){
            foreach($this->fields as $key => $field){
                $data = (new Query())
                    ->select(['*'])
                    ->from(FieldsData::tableDataName($field->name))
                    ->where(["application_event_id" => $application_event_id])
                    ->all();

                $field->results = $data;
            }
        }

        return true;
    }

    public static function returnType($ids){
        if(!is_array($ids)){
            $ids = SiteHelper::to_php_array($ids);
        }

        $attributes = (new Query())
            ->select(['*'])
            ->from("attributes")
            ->where(["id" => $ids])
            ->all();

        foreach($attributes as $attr){
            if($attr['application_type_id'] != 0){
                return $attr['application_type_id'];
            }
        }

        return 0;
    }
}
