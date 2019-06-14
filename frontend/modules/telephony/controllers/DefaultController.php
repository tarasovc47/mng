<?php

namespace frontend\modules\telephony\controllers;

use frontend\components\FrontendComponent;

/**
 * Default controller for the `telephony` module
 */
class DefaultController extends FrontendComponent
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function beforeAction($action)
    {
        $this->view->title = "Телефония";
        return parent::beforeAction($action);
    }

//    public $headers = array('Content-Type: application/json');

//    public function RestApiRequest(){
//        return json_decode(file_get_contents('http://1.1.70.53:8080/api_v0/nets_unit/'));
//    }

    public function actionIndex($id=null)
    {
        return $this->render('index');
//        $request = $this->RestApiRequest();
    }
}
