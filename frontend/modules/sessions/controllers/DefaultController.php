<?php

namespace frontend\modules\sessions\controllers;

use common\components\SiteHelper;
use frontend\components\FrontendComponent;
use common\models\radius\RadiusMain;
use yii\web\ForbiddenHttpException;
use common\models\Access;
use Yii;
use yii\widgets\ActiveForm;
use yii\web\Response;
/**
 * Default controller for the `telephony` module
 */
class DefaultController extends FrontendComponent
{
    /**
     * Renders the index view for the module
     * @return string
     */

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

        if(!$this->permissions['access']){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $this->view->title = "Мониторинг сессий";
        return true;
    }

//    public $headers = array('Content-Type: application/json');

//    public function RestApiRequest(){
//        return json_decode(file_get_contents('http://1.1.70.53:8080/api_v0/nets_unit/'));
//    }

    public function actionValidate()
    {
        $model = new RadiusMain();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionDisconnect($login=null,$session_name=null){
        if(Yii::$app->request->isAjax) {
            $data = [];
            if ($this->permissions['kill_sess'] > 1) {
//            print_r($login);
//            print_r($session_name);
                $diss = new RadiusMain();
                $data = $diss->Disconnect($login,$session_name);
//                SiteHelper::debug($data);

            }
            $json = [];

            foreach ($data as $serv_ip => $task_data) {
                if ($task_data['new_task']) {
                    $json['notifyText'] = "Задача на завершение сессии поставлена";
                    $json['notifyTS'] = $task_data['task']['ts'];
                } else {
                    $json['notifyText'] = "Задача на завершение уже установлена";
                    $json['notifyTS'] = $task_data['task']['ts'];
                }
            }

            return json_encode($json);
        }
    }

    public function actionAccounting(){
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $accounting_html = '';
            $accounting = [];
            if(isset($post)){
                $query = new RadiusMain;
                $accounting = $query->Accounting($post['login'],$post['macaddr'],$post['ipv4'],$post['ipv6']);
            }
            return $this->renderPartial('accounting',[
                'accounting'=>$accounting,
                'service_code' => $this->service_auth_code,
                'post'=>$post,
                'permissions'=>$this->permissions
            ]);
        }else{
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
    }



    public function actionIndex($id=null)
    {
        return $this->render('index',
            [
                'model'=> new RadiusMain(),
                'user'=>$this->cas_user,
                'nas'=>RadiusMain::loadNAS(),
                'permissions'=>$this->permissions
            ]
        );
    }
}
