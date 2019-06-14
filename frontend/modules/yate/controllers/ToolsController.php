<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 01.12.18
 * Time: 20:04
 */

namespace frontend\modules\yate\controllers;


use frontend\components\FrontendComponent;

class ToolsController extends FrontendComponent
{
    public function actionReports(){
        return $this->render('reports');
    }
    public function actionCheckRoutes(){
        return $this->render('check-routes');
    }
    public function actionAccounts(){
        return $this->render('accounts');
    }
    public function actionMonitoring(){
        return $this->render('monitoring');
    }
}