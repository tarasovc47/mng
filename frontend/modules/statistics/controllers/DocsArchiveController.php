<?php

namespace frontend\modules\statistics\controllers;

use Yii;
use yii\helpers\Html;
use frontend\components\FrontendComponent;
use common\models\DocsArchive;

class DocsArchiveController extends FrontendComponent
{
    public function actionIndex()
    {
    	$statistics = DocsArchive::getStatistics();
        return $this->render('index', [
        	'statistics' => $statistics
        ]);
    }

}
