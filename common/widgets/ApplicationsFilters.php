<?php
namespace common\widgets;

use Yii;

class ApplicationsFilters extends \yii\bootstrap\Widget
{
    public $user;

	public function init(){
        if($this->user === null){
            throw new InvalidConfigException('Атрибут "applications" обязательно должен быть указан.');
        }
        // ApplicationsAssets::register($this->view);
    }

	public function run(){
		return $this->render("applications-filters/view", [
            'user' => $this->user,
        ]);
	}
}