<?php
namespace common\widgets;

use Yii;

class ApplicationsEvents extends \yii\bootstrap\Widget
{
	public $event;

	public function init()
    {
        if($this->event === null){
            throw new InvalidConfigException('Атрибут "event" обязательно должен быть указан.');
        }
    }

	public function run(){
		return $this->render("applications-events/event", [
            'event' => $this->event,
		]);
	}
}