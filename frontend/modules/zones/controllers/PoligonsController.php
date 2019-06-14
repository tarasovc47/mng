<?php

namespace frontend\modules\zones\controllers;

use Yii;
use yii\helpers\Json;

class PoligonsController extends \frontend\components\FrontendComponent
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionExport()
    {
        $data = Yii::$app->request->post('json');
        $data = Json::encode($data);
        $fp = fopen('results.json', 'w');
		fwrite($fp, json_encode($data));
		fclose($fp);
    }

}
