<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 12.04.18
 * Time: 16:30
 */

namespace frontend\modules\tools\controllers;
//Контроллер времменный

use frontend\components\FrontendComponent;

class UrlsController extends FrontendComponent
{
    public function actionIndex(){
        return $this->render('index');
    }
}