<?php
namespace frontend\widgets;

use frontend\assets\MulpipleAddressesFormWidgetAsset;

class MultipleAddressesForm extends \yii\bootstrap\Widget
{   
    public $attribute;
    public $place;
    public $template;
    public $model;

    public function init(){
        parent::init();

        if($this->model === null){
            throw new InvalidConfigException('Атрибут "model" обязательно должен быть указан.');
        }

        if($this->attribute === null){
            throw new InvalidConfigException('Атрибут "attribute" обязательно должен быть указан.');
        }

        if($this->template === null){
            throw new InvalidConfigException('Атрибут "template" обязательно должен быть указан.');
        }

        if($this->place === null){
            $this->place = "";
        }

        MulpipleAddressesFormWidgetAsset::register($this->view);
    }

    public function run(){
        return $this->render("multiple-addresses-form/multiple_addresses_form", [
            'model' => $this->model,
            'attribute' => $this->attribute,
            'place' => $this->place,
            'template' => $this->template,
        ]);
    }
}