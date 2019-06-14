<?php

namespace common\models;

use Yii;
use yii\db\Query;
use common\components\SiteHelper;

class ApplicationsProperties extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'applications_properties';
    }

    public function getApplicationEvent()
    {
        return $this->hasOne(ApplicationsEvents::className(), ["id" => "application_event_id"]);
    }

    public function rules()
    {
        return [
            [['application_event_id', 'properties'], 'required'],
            [['application_event_id'], 'integer'],
            [['properties'], 'validateProperties'],
            [['properties'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_event_id' => 'Application Event ID',
            'properties' => 'Properties',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ApplicationsPropertiesQuery(get_called_class());
    }

    public function validateProperties($attribute, $params)
    {
        $ids = SiteHelper::to_php_array($this->$attribute);

        $properties = (new Query())
            ->select(['*'])
            ->from(Properties::tableName())
            ->where(["id" => $ids])
            ->all();

        // Проверяется не были ли присланы какие-то "лишние" атрибуты
        foreach($properties as $prop){
            if(in_array($prop["id"], $ids)){
                $ids = array_flip($ids);
                unset($ids[$prop["id"]]);
                $ids = array_flip($ids);
            }
        }

        // Проверяется возможность построения нормального дерева, не присутствуют ли лишние атрибуты
        $properties = $this->canBuildTree($properties);

        if(!empty($ids) || !empty($properties)){
            $this->addError($attribute, 'Произошла ошибка. Перезагрузите страницу и попробуйте снова.');
        }
    }

    private function canBuildTree($properties, $parent_id = 0){
        if(!empty($properties)){
            foreach($properties as $key => $prop){
                if($parent_id == $prop['parent_id']){
                    unset($properties[$key]);

                    $properties = $this->canBuildTree($properties, $prop['id']);
                }
            }
        }

        return $properties;
    }

    public function prepareErrors(){
        $response = [];
        $errors = $this->getErrors();

        foreach($errors as $attribute => $messages){
            foreach($messages as $message){
                $response[] = $message;
            }
        }

        return $response;
    }
}
