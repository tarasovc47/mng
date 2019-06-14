<?php

namespace common\models;

use Yii;
use yii\db\Query;
use common\components\SiteHelper;

class ApplicationsAttributes extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'applications_attributes';
    }

    public function getApplicationEvent()
    {
        return $this->hasOne(ApplicationsEvents::className(), ["id" => "application_event_id"]);
    }

    public function rules()
    {
        return [
            [['application_event_id'], 'required'],
            [['application_event_id'], 'integer'],
            [['attributes'], 'validateAttributes'],
            [['attributes'], 'required', 'message' => 'Обязательно нужно выбрать атрибуты.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_event_id' => 'Application Event ID',
            'attributes' => 'Attributes',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ApplicationsAttributesQuery(get_called_class());
    }

    public function validateAttributes($attribute, $params)
    {
        $ids = SiteHelper::to_php_array($this->$attribute);

        $attributes = (new Query())
            ->select(['*'])
            ->from(Attributes::tableName())
            ->where(["id" => $ids])
            ->all();

        $first = 0;
        // Проверяется не были ли присланы какие-то "лишние" атрибуты
        // Проверяется что атрибут первого уровня только один
        foreach($attributes as $attr){
            if($attr["parent_id"] === 0){
                $first++;
            }

            if(in_array($attr["id"], $ids)){
                $ids = array_flip($ids);
                unset($ids[$attr["id"]]);
                $ids = array_flip($ids);
            }
        }

        // Проверяется возможность построения нормального дерева, не присутствуют ли лишние атрибуты
        $attributes = $this->canBuildTree($attributes);

        if(!empty($ids) || !empty($attributes) || ($first != 1)){
            $this->addError($attribute, 'Произошла ошибка. Перезагрузите страницу и попробуйте снова.');
        }
    }

    private function canBuildTree($attributes, $parent_id = 0){
        if(!empty($attributes)){
            foreach($attributes as $key => $attr){
                if($parent_id == $attr['parent_id']){
                    unset($attributes[$key]);

                    $attributes = $this->canBuildTree($attributes, $attr['id']);
                }
            }
        }

        return $attributes;
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
