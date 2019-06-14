<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 09.11.18
 * Time: 23:49
 */

namespace backend\controllers;


use Yii;
use common\models\LoginForm;
use yii\web\Controller;

class LoginController extends Controller
{
    public function actionIndex(){
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

            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }

}