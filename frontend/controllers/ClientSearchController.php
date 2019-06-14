<?php

namespace frontend\controllers;

use Yii;
use common\models\ClientSearch;
use common\models\SearchFields;
use common\models\SearchFieldsSettings;
use yii\helpers\ArrayHelper;
use frontend\components\FrontendComponent;

class ClientSearchController extends FrontendComponent
{
    public function actionIndex()
    {
        $this->view->title .= "Поиск абонентов";

        $model = new ClientSearch;

		$tariffPlans = $model->getTariffPlans();
		$providers = $model->getProviders();
		$subproviders = $model->getSubproviders();
		$clientTypes = $model->getClientTypes();
		$services = $model->servicesForView;
		$criterions = $model->criterions;

        return $this->render('index',
        					[
								'tariffPlans' => $tariffPlans,
								'providers' => $providers,
								'subproviders' => $subproviders,
								'clientTypes' => $clientTypes,
								'services' => $services,
        						'criterions' => $criterions
        					]);
    }

    public function actionClientSearch()
	{
		$model = new ClientSearch;

		if (isset($_POST['dataForSearch'])) {
            $dataForSearch = $_POST['dataForSearch'];
        }

        if (isset($_POST['page'])) {
            $page = $_POST['page'];
        } else{
            $page = 1;
        }

        if (isset($_POST['tab'])) {
            $tab = $_POST['tab'];
        }

        if (isset($_POST['onlyActive'])) {
            $onlyActive = $_POST['onlyActive'];
        }

		$error = '';

		$response = $model->clientSearch($dataForSearch, $page, $tab, $onlyActive);

		if (!($response) || (empty($response['clients'])) && (empty($response['abonents']))) {
			$error = "Введён неверный либо нерезультативный поисковый запрос";
		}

		$fields = SearchFields::getFieldsList();
		$fields_values = SearchFieldsSettings::getValuesList(0, $this->cas_user->id);

        if (!isset($fields_values) || empty($fields_values)) {
            $fields_values = SearchFieldsSettings::getValuesList($this->cas_user->department_id, 0);
        }

        if (!isset($fields_values) || empty($fields_values)) {
            $fields_values = ArrayHelper::map($fields, 'id', 'display_default_setting');
        }

        foreach ($fields as $key => $field) {
        	if (!array_key_exists($key, $fields_values)) {
        		$fields_values[$key] = $field['display_default_setting'];
        	}
        }

		$abonents='';
		$clients='';

		$dataForSearch = json_encode($dataForSearch);

		if (isset($response['abonents'])) {
			$abonents = Yii::$app->controller->renderPartial('@frontend/views/client-search/resultAbonents', [
				'response' => $response, 
				'page' => $page, 
				'count_abonents' => $response['count_abonents'][0]['count'],
	        	'cas_user' => $this->cas_user,
	        	'fields' => $fields,
	        	'fields_values' => $fields_values,
			]);
		}

		if (isset($response['clients'])) {
			$clients = Yii::$app->controller->renderPartial('@frontend/views/client-search/resultClients', [
				'response' => $response, 
				'page' => $page, 
				'count_clients' => $response['count_clients'][0]['count'],
	        	'cas_user' => $this->cas_user,
	        	'fields' => $fields,
	        	'fields_values' => $fields_values,
			]);
		}

		switch ($tab) {
			case 'both':
				$searchResult = Yii::$app->controller->renderPartial('@frontend/views/client-search/result', 
									[
										'response' => $response, 
										'error' => $error,
										'abonents' => $abonents,
										'clients' => $clients
									]);
				break;

			case 'abonents':
				$searchResult = $abonents;
				break;

			case 'clients':
				$searchResult = $clients;
				break;
			
			default:
				break;
		}

		echo json_encode($searchResult);
        die();
	}
}
