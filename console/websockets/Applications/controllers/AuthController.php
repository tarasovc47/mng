<?php

namespace console\websockets\Applications\controllers;

use yii\db\Query;
use common\models\CasUser;
use common\components\SiteHelper;

class AuthController
{
	private static $instance;

	public static function Instance(){
		if(self::$instance == null)
			self::$instance = new self();
			
		return self::$instance;
	}

	public function Login($data){
		$user = false;

		if(isset($data["sid"]) && !empty($data["sid"])){
			$session = (new Query())
			    ->select(['id_user'])
			    ->from('sessions')
			    ->where(['sid' => $data["sid"]])
			    ->one();

			if(!empty($session)){
				$user = CasUser::findOne($session["id_user"]);
				$user->roles = SiteHelper::to_php_array($user->roles);
			}			
		}

		return $user;
	}
}