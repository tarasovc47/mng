<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
USE common\models\history\ZonesDistrictsAndAreasHistory;
USE common\models\UsersGroups;

class ZonesDistrictsAndAreas extends \yii\db\ActiveRecord
{
    public $types;
    public $updater;
    public $updated_at;

    function __construct(){
        parent::__construct();
        $this->types = [
                            1 => 'Округ',
                            2 => 'Район',
                        ];
    } 

    public static function tableName()
    {
        return 'zones__districts_and_areas';
    }

    public function rules()
    {
        return [
            [['name', 'type', 'created_at', 'cas_user_id', 'publication_status'], 'required'],
            [['type', 'parent_id', 'users_group_id', 'created_at', 'cas_user_id', 'publication_status', 'updater', 'updated_at'], 'integer'],
            [['name', 'comment'], 'string'],
            [['name', 'comment'], 'trim'],

            [['parent_id'], 'required', 'on' => 'type_is_area'],
            [['parent_id'], 'compare', 'compareValue' => -1, 'operator' => '!=', 'type' => 'number', 'on' => 'type_is_area', 'message' => 'Значение «Принадлежит к округу» должно быть выбрано.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'comment' => 'Примечание',
            'type' => 'Тип',
            'parent_id' => 'Принадлежит к округу',
            'users_group_id' => 'Группа службы эксплуатации',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ZonesDistrictsAndAreasQuery(get_called_class());
    }

    public function getUsersGroup()
    {
        return $this->hasOne(UsersGroups::className(), ['id' => 'users_group_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $history = new ZonesDistrictsAndAreasHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getDistrictList(){
        $first = [-1 => 'Не выбран'];
        $connection = Yii::$app->db;
        $districts = $connection
                            ->createCommand("SELECT id, name 
                                FROM zones__districts_and_areas
                                WHERE type = '1'")
                            ->queryAll();

        $districts = ArrayHelper::map($districts, 'id', 'name');
        return ArrayHelper::merge($first, $districts);
    }

    public static function getDistrict($id){
        $connection = Yii::$app->db;
        return $connection->createCommand("
                                SELECT id, name 
                                FROM zones__districts_and_areas
                                WHERE id = '".$id."'")
                            ->queryOne();
    }

    public static function getAreasList(){
        $connection = Yii::$app->db;
        $areas = $connection
                            ->createCommand("SELECT id, name 
                                FROM zones__districts_and_areas
                                WHERE type = '2'")
                            ->queryAll();

        return ArrayHelper::map($areas, 'id', 'name');
    }

    public static function getAreasListByDistrict($district_id){
        $connection = Yii::$app->db;
        $areas = $connection
                            ->createCommand("SELECT id, name 
                                FROM zones__districts_and_areas
                                WHERE type = '2' AND parent_id = {$district_id}")
                            ->queryAll();

        return ArrayHelper::map($areas, 'id', 'name');
    }
}
