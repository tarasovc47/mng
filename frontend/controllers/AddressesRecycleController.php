<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Json;
use frontend\components\FrontendComponent;
use common\models\AddressesRecycle;
use common\components\Dadata;
use yii\data\ActiveDataProvider;

class AddressesRecycleController extends FrontendComponent
{
    public function actionIndex()
    {
		$dataProvider = new ActiveDataProvider([
		    'query' => AddressesRecycle::find()->where("postcode = '-1'")->limit(30),
		    'pagination' => [
		        'pageSize' => 30,
		    ],
		    'sort' => [
		        'defaultOrder' => [
		            'id' => SORT_ASC,
		        ]
		    ],
		]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAllCompanies()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => AddressesRecycle::find(),
            'pagination' => [
                'pageSize' => 30,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ]
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIdentifyActual(){
        $companies = AddressesRecycle::find()->where(['actual_company' => null])->limit(100)->all();
        foreach ($companies as $company) {
            $company->isCompanyActual();
            if (!$company->save()) {
                print_r($company->getErrors());
                die();
            }
        }
        die('done');
    }

    public function actionLoadPostcode(){
        $companies = AddressesRecycle::find()->where("postcode IS NULL AND conclusion_address != 7 AND dadata_suggest_address != ''")->limit(500)->all();
        foreach ($companies as $company) {
            $company->loadPostcode();
            if (!$company->save()) {
                print_r($company->getErrors());
                die();
            }
        }
        die('done');
    }


    /*public function actionRewrite(){
        $companies = Yii::$app->request->get('companies');
        foreach ($companies as $company => $status) {
            $model = AddressesRecycle::findOne($company);
            if ($model) {
                $model->conclusion_address = (int)$status;
                $model->save();                
            }
        }
        echo Json::encode('success');
        die();
    }*/

    /*public function actionRecycle()
    {
        $companies = AddressesRecycle::find()->where(['conclusion_address' => null])->limit(100)->all();
    	if ($companies) {
    		foreach ($companies as $key => $company) {
    			$company->autoRecycleCompany();

    			if (!$company->save()) {
    				print_r($company->getErrors());
    				die();
    			}
    		}
    	}
        return $this->redirect('index');
    }

    */
}
