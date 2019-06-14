<?php

namespace common\components;

use Yii;
use common\models\Login;
use common\models\CasUser;
use common\models\Departments;
use common\components\SiteHelper;
use frontend\assets\AppAsset;

class Base extends \yii\web\Controller
{
	protected $cas_user;
	public $menuTopItems;
	public $menuLeftItems;
	public $breadcrumbs;
	public $htmlClasses;

    public function beforeAction($action){

        if(!parent::beforeAction($action)){
            return false;
        }
//

        Yii::$app->session->open();
        $domain = 'http://' . Yii::$app->params['domain'];

            if (Yii::$app->user->isGuest) {
                $requestUrl = Yii::$app->request->url;
                if($requestUrl=='/'){
                    return $this->redirect('/login')->send();
                }
                Yii::$app->user->loginUrl = ['/login', 'return' => $requestUrl];
                return $this->redirect(Yii::$app->user->loginUrl)->send();
//
            }
            $model = new Login();
            $this->cas_user = $model->Get();
            if (!$this->cas_user) {
                $this->cas_user = Yii::$app->user->identity;
                if ($this->cas_user = $model->Login($this->cas_user)) {
                    return $this->redirect('/')->send();
                }
                return false;
            }
//
//            if(!is_array($this->cas_user->roles)){
//                $this->cas_user->roles = SiteHelper::to_php_array($this->cas_user->roles);
//                die();
//            }

            $this->htmlClasses = Yii::$app->controller->module->id;
            $this->htmlClasses .= " " . Yii::$app->controller->id;
            $this->htmlClasses .= " " . Yii::$app->controller->action->id;

//        }

        return true;
    }
//    Old procedure
	/*public function beforeAction($action){
		if(!parent::beforeAction($action)){
			return false;
		}

		Yii::$app->session->open();

		$domain = 'http://' . Yii::$app->params['domain'];
		$model = new Login();
		$this->cas_user = $model->Get();

		if(!$this->cas_user){
			if(isset($_GET['ticket'])){
				$this->cas_user = SiteHelper::getSslPage("https://cas.t72.ru/user/cas/serviceValidate?ticket={$_GET['ticket']}&format=json&group=true");
				$this->cas_user = json_decode($this->cas_user, true);
				
				if(isset($this->cas_user['cas:serviceresponse']['cas:authenticationsuccess'])){				 	
				 	if($this->cas_user = $model->Login($this->cas_user)){
						$this->redirect('/')->send();
				 	}
				 	else{
				 		$this->redirect("https://cas.t72.ru/user/cas/logout?service=".$domain)->send();
				 	}			
				}
				else{
				 	$this->redirect("https://cas.t72.ru/user/cas/logout?service=".$domain)->send();
				}
			}
			else{
				$this->redirect("https://cas.t72.ru/user/cas/login?service=".$domain)->send();
			}

			return false;
		}
		
		if(!is_array($this->cas_user->roles)){
			$this->cas_user->roles = SiteHelper::to_php_array($this->cas_user->roles);
		}

        $this->htmlClasses = Yii::$app->controller->module->id;
        $this->htmlClasses .= " " . Yii::$app->controller->id;
        $this->htmlClasses .= " " . Yii::$app->controller->action->id;

	    return true;
	}*/
}
