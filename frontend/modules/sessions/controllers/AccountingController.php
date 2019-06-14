<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 03.04.18
 * Time: 11:05
 */

namespace frontend\modules\sessions\controllers;

use frontend\components\FrontendComponent;
use common\models\radius\RadiusMain;
use common\models\Access;
use yii\web\ForbiddenHttpException;
use Yii;

class AccountingController extends FrontendComponent
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

        if(!$this->permissions['access']){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $this->view->title = "Мониторинг сессий";
        return true;
    }



    public function actionIndex($login=null,$macaddr=null,$ipv4=null,$ipv6=null)
    {
        $accountingCard = '<div class="well" id="ArchiveCard">Задайте параметры поиска</div>';
        $post = Yii::$app->request->post('RadiusMain');
        if(isset($post['login'])){
            $query = new RadiusMain;
            $accountingCard = $this->renderPartial('accounting',[
                    'accounting'=>$query->Accounting($post['login'],$post['macaddr'],$post['ipv4'],$post['ipv6']),
                    'service_code' => $this->service_auth_code,
                    'post'=>$post,
                    'permissions'=>$this->permissions
                ]);
        }
        return $this->render('index',
            [
                'post'=>$post,
                'accountingCard'=>$accountingCard,
                'model'=> new RadiusMain(),
                'user'=>$this->cas_user,
                'nas'=>RadiusMain::loadNAS(),
                'permissions'=>$this->permissions
            ]
        );
    }
}