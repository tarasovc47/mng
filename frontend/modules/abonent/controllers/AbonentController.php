<?php

namespace frontend\modules\abonent\controllers;

use Yii;
use common\models\ClientSearch;
use frontend\components\FrontendComponent;

class AbonentController extends FrontendComponent
{
	protected $abonentData;
	protected $clientData;
	private $abonentLeftMenu;

	public function beforeAction($action){
		$modelClientSearch = new ClientSearch;
		$currentUrl = '/'.\Yii::$app->controller->module->id.'/'.\Yii::$app->controller->id.'/'.\Yii::$app->controller->action->id.'?';

		if (Yii::$app->request->get('abonent')) {
            $abonent_id = Yii::$app->request->get('abonent');
            $this->abonentData = $modelClientSearch->searchOneAbonent($abonent_id);
            $items = [
					['label' => 'Общая информация', 'url' => '/abonent/abonent/index?abonent='.$this->abonentData['abonent']['id']],
					['label' => 'Документы', 'url' => '/abonent/docs-archive/index?DocsArchiveSearch[abonent]='.$this->abonentData['abonent']['id'].'&&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1'],
			];
			$currentUrl .= 'abonent='.$this->abonentData['abonent']['id'];
        } elseif (Yii::$app->request->get('client_id')) {
            $client_id = Yii::$app->request->get('client_id');
            $this->clientData = $modelClientSearch->searchOneClient($client_id);
            $items = [
					['label' => 'Общая информация', 'url' => '/abonent/abonent/index?client_id='.$this->clientData['client_id']],
					['label' => 'Документы', 'url' => '/abonent/docs-archive/index?DocsArchiveSearch[client_id]='.$this->clientData['client_id'].'&&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1'],
			];
			$currentUrl .= 'client_id='.$this->clientData['client_id'];
        }


		$this->abonentLeftMenu = $this->renderPartial('/_abonent-left-menu', ['items' => $items, 'currentUrl' => $currentUrl]);
		return parent::beforeAction($action);
	}

    public function actionIndex()
    {
        $modelClientSearch = new ClientSearch;

        if (Yii::$app->request->get('abonent')) {
            $this->view->title = "Карточка абонента";
        }
        if (Yii::$app->request->get('client')) {
            $this->view->title = "Карточка лицевого счёта";
        }
        
        return $this->render("index", ["abonentLeftMenu" => $this->abonentLeftMenu, "abonentData" => $this->abonentData, "clientData" => $this->clientData]);
    }

}
