<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 02.08.18
 * Time: 10:19
 */

namespace frontend\modules\sessions\controllers;

use frontend\components\FrontendComponent;
use common\models\radius\RadiusMain;
use common\models\Access;
use yii\web\ForbiddenHttpException;
use Yii;
use common\models\radius\AccountingBlacklist;
use yii\widgets\ActiveForm;
use yii\web\Response;


class BlacklistController  extends FrontendComponent
{
    protected $permissions;

    protected $service_auth_code = [
        "A" => "Активация",
        "D" => "Деактивация сервиса",
        "T" => "NAS запросил активацию",
        "F" => "NAS сообщил об ошибке",
        "SF" => "Действие принято, но транзакция не выполнена",
        "S" => "NAS ответил об успешной активации"
    ];
    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }
        $this->permissions['access'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 14); //14 - id доступа к SessMon
        $this->permissions['history'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 15); //14 - id доступа к SessMon
        $this->permissions['kill_sess'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 17); //14 - id доступа к SessMon
        $this->permissions['blacklist'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 31); //14 - id доступа к SessMon

        if($this->permissions['blacklist']<1){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $this->view->title = "Мониторинг сессий";
        return true;
    }

    public function actionIndex(){
            $searchModel = new AccountingBlacklist();
            $post = Yii::$app->request->post();
            if(isset($post['AccountingBlacklist']['save'])){
                $searchModel->saveItem($post['AccountingBlacklist']);
            }


            $dataProvider = $searchModel->search($post);

            return $this->render('index',[
                'permissions'=>$this->permissions,
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
    }

    public function actionDelete($id=null){
        $searchModel = new AccountingBlacklist();
        $searchModel->deleteItem($id);
        return $this->redirect('/sessions/blacklist');
    }

    public function actionValidate()
    {
        $model = new AccountingBlacklist();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }


}