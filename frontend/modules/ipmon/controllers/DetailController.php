<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 13.01.17
 * Time: 15:39
 */

namespace frontend\modules\ipmon\controllers;
use frontend\components\FrontendComponent;
use frontend\modules\ipmon\models\DetailModel;
use Yii;

class DetailController extends FrontendComponent
{
    public function actionIndex(){
        $model = new DetailModel();
        $post =  Yii::$app->request->get();
        return $this->renderPartial('index',compact('model','post'));
    }
}