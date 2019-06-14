<?php

namespace frontend\modules\ipmon\controllers;

use frontend\components\FrontendComponent;
use common\models\Access;

class DefaultController extends FrontendComponent
{
    public $headers = array('Content-Type: application/json');
    private $permisson;

    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }

        $this->permisson = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 8);

        if(!$this->permisson){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $this->view->title = "IpMon | ARP Таблица";
        
        return true;
    }

    /*public function beforeAction($action)
    {
        $this->view->title = "IpMon | Сеть";
        return parent::beforeAction($action);
    }*/

    public function RestApiRequest(){
        return json_decode(file_get_contents('http://1.1.70.53:8080/api_v0/nets_unit/'));
    }

    public function actionIndex($id=null)
    {
        // $request = $this->RestApiRequest();
        return  $this->render('index',compact('id'));
    }
}
