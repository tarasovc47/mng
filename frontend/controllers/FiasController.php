<?php
namespace frontend\controllers;

use Yii;
use common\components\SiteHelper;
use frontend\components\FrontendComponent;
use yii\helpers\Json;

class FiasController extends FrontendComponent
{
	public $query;

	public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }

        $this->query = http_build_query(Yii::$app->request->get());

        return true;
    }

    public function actionPlaceFind(){
    	$path = "/fias/place/find.json";
    	return $this->FiasApiHandler($path);
    }

    public function actionPlaceInsert(){
    	$path = "/fias/place/insert.json";
        $user = '&user=' . $this->cas_user->login;
        return $this->FiasApiHandler($path, $user);
    }

    public function actionPlaceSelect(){
    	$path = "/fias/place/select.json";
        return $this->FiasApiHandler($path);
    }

    public function actionFiasSelect(){
        $path = "/fias/fias/select.json";
        return $this->FiasApiHandler($path);
    }

    public function actionFiasWidget(){
    	$path = "/fias/fias/widget.json";
        return $this->FiasApiHandler($path);
    }

    public function actionFiasText(){
        $path = "/fias/fias/text.json";
        return $this->FiasApiHandler($path);
    }

    private function FiasApiHandler($path, $user = ''){
    	$url = "https://api.t72.ru" . $path . "?" . $this->query . $user;

    	$data = SiteHelper::getSslPage($url);
        return $data;
    }
}