<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 01.12.18
 * Time: 19:57
 */

namespace frontend\modules\yate\controllers;

use frontend\modules\yate\models\category_mn;
use frontend\components\FrontendComponent;

class WizardController extends FrontendComponent
{
    public function actionIndex(){
        $test = category_mn::find()->asArray()->orderBy('id')->all();

        return $this->render('index',['test'=>$test]);
    }
}