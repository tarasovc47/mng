<?php
namespace common\widgets;

use Yii;
use common\assets\AddressSearchWidgetAsset;

class AddressSearch extends \yii\bootstrap\Widget
{
	public $attribute;
	public $place;
	public $template;
    public $model;

	public function init()
    {
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
        	$this->place = "d6d79307-3f3b-4ed3-93db-c07145a46938";
        }

        AddressSearchWidgetAsset::register($this->view);
    }

	public function run(){
		return $this->render("address-search/" . $this->template, [
            'model' => $this->model,
			'attribute' => $this->attribute,
			'place' => $this->place,
		]);
	}
}