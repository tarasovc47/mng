<?php

namespace frontend\modules\abonent\controllers;

use Yii;
use yii\helpers\Html;
use common\models\DocsArchive;
use common\models\DocsTypes;
use common\models\ConnectionTechnologies;
use common\models\search\DocsArchiveSearch;
use frontend\components\FrontendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\NotAcceptableHttpException;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class DocsArchiveController extends FrontendComponent
{
    private $abonentLeftMenu;

    public function beforeAction($action){
        if (Yii::$app->controller->action->id == 'index') {
            $currentUrl = '/'.Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/'.Yii::$app->controller->action->id.'?';

            if (isset(Yii::$app->request->get('DocsArchiveSearch')['abonent'])) {
                $abonent_id = Yii::$app->request->get('DocsArchiveSearch')['abonent'];
                $items = [
                        ['label' => 'Общая информация', 'url' => '/abonent/abonent/index?abonent='.$abonent_id],
                        ['label' => 'Документы', 'url' => '/abonent/docs-archive/index?DocsArchiveSearch[abonent]='.$abonent_id.'&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1'],
                ];
                $currentUrl .= 'DocsArchiveSearch[abonent]='.$abonent_id.'&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1';
            } elseif (isset(Yii::$app->request->get('DocsArchiveSearch')['client_id'])) {
                $client_id = Yii::$app->request->get('DocsArchiveSearch')['client_id'];
                $items = [
                        ['label' => 'Общая информация', 'url' => '/abonent/abonent/index?client_id='.$client_id],
                        ['label' => 'Документы', 'url' => '/abonent/docs-archive/index?DocsArchiveSearch[client_id]='.$client_id.'&DocsArchiveSearch[parent_id]=-1DocsArchiveSearch[publication_status]=1'],
                ];
                $currentUrl .= 'DocsArchiveSearch[client_id]='.$client_id.'&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1';
            }


            $this->abonentLeftMenu = $this->renderPartial('/_abonent-left-menu', ['items' => $items, 'currentUrl' => $currentUrl]);
        }
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $abonent = (isset($_GET["DocsArchiveSearch"]['abonent']) && !empty($_GET["DocsArchiveSearch"]['abonent'])) ? $_GET["DocsArchiveSearch"]['abonent'] : false;
        $client_id = (isset($_GET["DocsArchiveSearch"]['client_id']) && !empty($_GET["DocsArchiveSearch"]['client_id'])) ? $_GET["DocsArchiveSearch"]['client_id'] : false;

        if ($abonent || $client_id) {
            $searchModel = new DocsArchiveSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'abonent' => $abonent,
                'client_id' => $client_id,
                'abonentLeftMenu' => $this->abonentLeftMenu,
            ]);
        }

        throw new NotAcceptableHttpException('Не выбран лицевой счёт или абонент, воспользуйтесь поиском');
    }

    public function actionView($id)
    {
        $searchModel = new DocsArchiveSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $sub_doc = (Yii::$app->request->get('sub_doc')) ? 1 : 0;
        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sub_doc' => $sub_doc,
        ]);
    }

    public function actionCreate()
    {
        $sub_doc = (Yii::$app->request->get('sub_doc')) ? 1 : 0;
        $parent_file_info = array();
        if (!$sub_doc) {
            $abonent = Yii::$app->request->get('abonent');
            $client_id = Yii::$app->request->get('client_id');
        } else {
            $parent_id = Yii::$app->request->get('parent_id');
            $parent_file_info = DocsArchive::findOne($parent_id);
            $abonent = $parent_file_info['abonent'];
            $client_id = $parent_file_info['client_id'];
        }

        if ($abonent && $client_id && !$sub_doc) {
            return $this->redirect('/abonent/docs-archive/create?abonent='.$abonent);
        }

        if ($abonent || $client_id) {
            $model = new DocsArchive();
            $model->scenario = 'create';
            // все лицевые счета
            $client_ids = array();
            if (!$sub_doc) {
                if ($abonent) {
                    $client_ids = $model::getClientIDs($abonent);
                    foreach ($client_ids as $key => $client_id) {
                        $model->client_id = $client_id;
                        break;
                    }
                } elseif ($client_id) {
                    $client_ids[$client_id] = $client_id;
                    $model->client_id = $client_id;
                }
            } else {
                $model->client_id = $client_id;
                $client_ids = [$parent_file_info['client_id'] => $parent_file_info['client_id']];
            }
            

            if ($model->load(Yii::$app->request->post())) {
                $model->cas_user_id = $this->cas_user->id;
                $model->created_at = time();
                $model->opened_at = strtotime($model->opened_at);
                $model->abonent = $abonent;
                $model->publication_status = 1;
                if (!$sub_doc) {
                    $model->parent_id = -1;
                } else {
                    $model->parent_id = $parent_id;
                }                    

                $file = UploadedFile::getInstance($model, 'file');
                if ($file && $file->tempName) {
                    $model->file = $file;
                    $model->extension = $model->file->extension;
                    $model->name = "temp_name_for_validate";
                    
                }   
                if ($model->validate()) {
                    $model->uploadFile();
                    if ($model->save()) {
                        return $this->redirect(['view', 'id' => $model->id, 'DocsArchiveSearch[parent_id]' => $model->id]);
                    }
                }        
            } 

            $user_ids = DocsArchive::getLokiBasicServiceIDsList($model->client_id);
            $contracts = DocsArchive::getClientContractsList($model->client_id);


            return $this->render('create', [
                'model' => $model,
                'abonent' => $abonent,
                'client_id' => $client_id,
                'client_ids' => $client_ids,
                'user_ids' => $user_ids,
                'sub_doc' => $sub_doc,
                'contracts' => $contracts,
                'parent_file_info' => $parent_file_info,
            ]);
        }

        throw new NotAcceptableHttpException('Не выбран лицевой счёт или абонент, воспользуйтесь поиском');
    }

    protected function findModel($id)
    {
        if (($model = DocsArchive::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $sub_doc = (Yii::$app->request->get('sub_doc')) ? 1 : 0;

        $client_ids = array();
            if ($model->abonent) {
                $client_ids = $model::getClientIDs($model->abonent);
            } elseif ($model->client_id) {
                $client_ids[$model->client_id] = $model->client_id;
            }

        if ($model->load(Yii::$app->request->post())) {
            $model->opened_at = strtotime($model->opened_at);

            $file = UploadedFile::getInstance($model, 'file');
            if ($file && $file->tempName) {
                $model->file = $file;
                $model->extension = $model->file->extension;
                $model->name = "temp_name_for_validate";                   
            }

            if ($model->validate()) {
                if ($file && $file->tempName) {
                    $model->uploadFile();
                }
                $model->updated_at = time();
                $model->updater = $this->cas_user->id;
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id, 'DocsArchiveSearch[parent_id]' => $model->id, 'sub_doc' => $sub_doc]);
                }
            }  
        } 

        $user_ids = $model::getLokiBasicServiceIDsList($model->client_id);
        $contracts = DocsArchive::getClientContractsList($model->client_id);
        $model->getExtraData();

        return $this->render('update', [
            'model' => $model,
            'sub_doc' => $sub_doc,
            'client_ids' => $client_ids,
            'user_ids' => $user_ids,
            'contracts' => $contracts,
        ]);

        throw new NotAcceptableHttpException('Не выбран лицевой счёт или абонент, воспользуйтесь поиском');
    }

    public function actionRemove(){
        $id = Yii::$app->request->get('id');
        $status = (int)!Yii::$app->request->get('current_status');

        $model = $this->findModel($id);
        $model->getExtraData();
        $model->publication_status = $status;
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;
        if ($model->save()) {
            echo Json::encode('success');
        } else {
            echo Json::encode($model->getErrors());
        }

        die();
    }

    public function actionGetExtraDataForClient()
    {        
        $client_id = Yii::$app->request->get('client_id');
        $user_ids = DocsArchive::getLokiBasicServiceIDsList($client_id);
        $contracts = DocsArchive::getClientContractsList($client_id);
        $data['user_ids_html'] = Html::renderSelectOptions(-1, $user_ids);
        $options = ['prompt' => ''];
        $data['contracts_html'] = Html::renderSelectOptions(null, $contracts, $options);
        echo Json::encode($data);
        die();
    }

    public function actionGetOneContract(){
        $contract_id = Yii::$app->request->get('contract_id');
        $data = DocsArchive::getOneContract($contract_id);

        echo Json::encode($data);
        die();
    }

    public function actionGetConnTechs(){
        $service_types = Yii::$app->request->get('service_types');
        $conn_techs = Yii::$app->request->get('conn_techs');
        $data = ConnectionTechnologies::getTechnologiesList($service_types);
        $data = Html::renderSelectOptions($conn_techs, $data);

        echo Json::encode($data);
        die();
    }
}
