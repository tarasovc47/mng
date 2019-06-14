<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 07.03.18
 * Time: 10:57
 */

namespace frontend\modules\sessions\controllers;


use frontend\components\FrontendComponent;
use Yii;

class AuthorizationController extends FrontendComponent
{
    public function actionLog(){
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            print_r($post);
        }
    }
}