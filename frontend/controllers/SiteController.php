<?php
namespace frontend\controllers;

use Yii;
use frontend\components\FrontendComponent;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
//use frontend\models\PasswordResetRequestForm;
//use frontend\models\ResetPasswordForm;
//use frontend\models\SignupForm;
//use frontend\models\ContactForm;
use common\models\CasUser;
use common\models\Login;
use common\models\LoginForm;

class SiteController extends FrontendComponent
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
        $this->getView()->title = "Центр управления полетами";
        return $this->render('index');
    }

    public function actionLogin()
    {
        Yii::$app->layout='main-login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $return_url = empty(Yii::$app->request->get('return'))? \yii\helpers\Url::to(['/']) :Yii::$app->request->get('return');
            return $this->redirect($return_url);
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout(){
        $domain = 'http://'.Yii::$app->params['domain'];
        $model = new Login();
        $model->Logout();
        return $this->redirect("https://cas.t72.ru/user/cas/logout?service=".$domain)->send();
    }

    public function actionTest(){
        return $this->render("test");
    }
}
