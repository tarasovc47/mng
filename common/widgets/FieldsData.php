<?php
namespace common\widgets;

use Yii;

class FieldsData extends \yii\bootstrap\Widget
{
	public $field;

	public function init()
    {
        if($this->field === null){
            throw new InvalidConfigException('Атрибут "field" обязательно должен быть указан.');
        }
    }

	public function run(){
		return $this->render("fields-data/field", [
            'field' => $this->field,
		]);
	}
}