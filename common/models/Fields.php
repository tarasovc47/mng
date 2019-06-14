<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\components\SiteHelper;
use yii\web\BadRequestHttpException;

class Fields extends \yii\db\ActiveRecord
{
    public $type;
    public $typeList = [
        "list" => "Список",
        "text" => "Текстовое поле",
        "number" => "Поле для числа",
        "textarea" => "Текстовая область",
        "bool" => "Логическое",
        "datetime" => "Дата и время",
    ];
    public $default_value;
    public $required;
    public $cardinality;
    public $cardinalityList = [
        1 => "1",
        2 => "2",
        3 => "3",
        4 => "4",
        5 => "5",
        0 => "Не ограничено",
    ];
    public $min;
    public $max;
    public $allowedValues;
    public $view;
    public $viewList = [
        "select" => "Выпадающий список",
        "checkbox" => "Чекбоксы/Радиокнопки",
    ];
    public $format;
    public $formatList = [];

    private $results;

    public function setResults($results)
    {
        $this->results = $results;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function init()
    {
        parent::init();
        $this->trigger(self::EVENT_INIT);

        $this->formatList = [
            "D-MM-YYYY" => date("d-m-Y"),
            "D-MM-YYYY H:m" => date("d-m-Y H:i"),
        ];
    }

    public static function tableName()
    {
        return 'fields';
    }

    // getAttributes зарезервированная Yii функция
    public function getAttr()
    {
        return $this->hasOne(Attributes::className(), ['id' => 'target_id'])->where([ "target_table" => Attributes::tableName() ]);
    }

    public function getApplicationsStatuses()
    {
        return $this->hasOne(getApplicationsStatuses::className(), ['id' => 'target_id'])->where([ "target_table" => ApplicationsStatuses::tableName() ]);
    }

    public function rules()
    {
        return [
            [['label', 'name', 'target_id', 'status', 'type', 'target_table'], 'required'],
            [['descr', 'data', 'default_value', 'type', "view", 'allowedValues', 'target_table', 'format'], 'string'],
            [['descr', 'data', 'default_value', 'type', "view", 'allowedValues', 'target_table', 'format', 'label', 'name'], 'trim'],
            [['label', 'name'], 'string', 'max' => 255],
            [['target_id', 'status', 'required', 'cardinality', 'min', 'max'], 'integer'],
            [['name'], 'unique'],
            [['name'], 'match', 'pattern' => '/^[a-zA-Z\_]+$/i', 'message' => 'Только латинские буквы и нижнее подчёркивание'],
            
            [['allowedValues'], 'validateAllowedValues', 'on' => 'list'],
            [['allowedValues'], 'required', 'on' => 'list'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label' => 'Название',
            'name' => 'Машинное имя',
            'descr' => 'Описание',
            'data' => 'Информация о поле',
            'target_id' => 'Относится к',
            'status' => 'Статус',
            "type" => "Тип поля",
            "default_value" => "Значение по умолчанию",
            "required" => "Обязательное поле",
            "cardinality" => "Количество значений",
            "min" => "Миним.",
            "max" => "Макс.",
            "allowedValues" => "Список допустимых значений",
            "view" => "Внешний вид",
            "format" => "Формат",
        ];
    }

    public static function find()
    {
        return new \common\models\query\FieldsQuery(get_called_class());
    }

    public function validateAllowedValues($attribute, $params){
        $allowedValues = explode("\n", $this->$attribute);
        foreach($allowedValues as $pair){
            $pair = explode("|", $pair);

            if(!is_numeric($pair[0])){
                $this->addError($attribute, 'Ключ должен быть числом.');
                break;
            }
        }
    }

    public function afterSave($insert, $changedAttributes){
        if($insert){
            $table = "fields_data_" . $this->name;
            switch($this->type){
                case "list":
                    $value_type = 'integer';
                    break;
                case "text":
                    $value_type = 'text';
                    break;
                case "number":
                    $value_type = 'integer';
                    break;
                case "textarea":
                    $value_type = 'text';
                    break;
                case "bool":
                    $value_type = 'integer';
                    break;
                case "datetime":
                    $value_type = 'integer';
                    break;
            }

            if(!isset($value_type)){
                throw new BadRequestHttpException('Возникла непредвиденная ошибка');
            }

            Yii::$app->db
                ->createCommand('CREATE TABLE "'. $table .'" (
                    "id" serial NOT NULL,
                    "field_id" integer NOT NULL,
                    "application_event_id" integer NOT NULL,
                    "value" ' . $value_type . ' NOT NULL,
                    "created_at" integer NOT NULL,
                    "cas_user_id" integer NOT NULL
                );')
                ->execute();

            Yii::$app->db
                ->createCommand('ALTER TABLE "'. $table .'" ADD CONSTRAINT "'. $table .'_id" PRIMARY KEY ("id");')
                ->execute();
        }
    }

    public function prepareDataAttribute(){
        $data['required'] = (isset($this->required) && !empty($this->required));
        $data['type'] = $this->type;
        $data['cardinality'] = (int)$this->cardinality;

        switch($this->type){
            case "list":
                $data['default_value'] = ($this->default_value == "") ? "" : (int)$this->default_value;
                $data['view'] = $this->view;

                $allowedValues = explode("\n", $this->allowedValues);
                foreach($allowedValues as $pair){
                    $pair = explode("|", $pair);
                    $data["allowedValues"][$pair[0]] = $pair[1];
                }
                break;
            case "number":
                $data['default_value'] = (int)$this->default_value;
                $data['min'] = (int)$this->min;
                $data['max'] = (int)$this->max;
                break;
            case "bool":
                $data['default_value'] = !( ($this->default_value == "0") || empty($this->default_value) );
                break;
            case "datetime":
                $data['default_value'] = $this->default_value;
                $data['format'] = $this->format;
                break;
            default:
                $data['default_value'] = $this->default_value;
        }

        $data = serialize($data);
        $this->data = $data;
        return true;
    }

    public function prepareAttributesFromData(){
        $data = unserialize($this->data);

        $this->type = $data['type'];
        $this->required = $data['required'];
        $this->default_value = $data['default_value'];
        $this->cardinality = $data['cardinality'];
        $this->min = isset($data['min']) ? $data['min'] : false;
        $this->max = isset($data['max']) ? $data['max'] : false;

        if(isset($data['allowedValues'])){
            $allowedValues = [];
            foreach($data['allowedValues'] as $value => $name){
                $allowedValues[] = $value . "|" . $name;
            }

            $this->allowedValues = implode("\n", $allowedValues);
        }

        $this->view = isset($data['view']) ? $data['view'] : false;
        return true;
    }

    public static function getStatuses($value = -1){
        $statuses = array(
            1 => "Активно",
            0 => "Не активно",
        );

        if($value > -1)
            return $statuses[$value];

        return $statuses;
    }
}
