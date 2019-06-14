<?php

namespace frontend\controllers;

use frontend\components\FrontendComponent;

class AuthController extends FrontendComponent
{
	public function actionTest()
	{
		return $this->render('test');
	}

	public function actionLogout()
	{
		
	}
	
}
