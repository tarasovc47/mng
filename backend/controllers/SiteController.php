<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\filters\AccessControl;
use common\models\LoginForm;
use backend\components\BackendComponent;
use common\models\Login;

class SiteController extends BackendComponent
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionNoAccess(){
        $this->layout = "no-access";
        return $this->render('no-access');
    }

    public function actionLogout(){
        $domain = 'http://'.Yii::$app->params['domain'];
        $model = new Login();
        $model->Logout();
        return $this->redirect("https://cas.t72.ru/user/cas/logout?service=".$domain)->send();
    }
}
