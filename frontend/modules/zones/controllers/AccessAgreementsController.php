<?php

namespace frontend\modules\zones\controllers;

use Yii;
use common\models\ZonesAccessAgreements;
use common\models\search\ZonesAccessAgreementsSearch;
use frontend\components\FrontendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\models\UploadForm;
use common\components\SiteHelper;
use common\models\Operators;
use common\models\ManagCompanies;
use common\models\history\ZonesAccessAgreementsHistory;
use common\models\Access;
use yii\web\ForbiddenHttpException;

class AccessAgreementsController extends FrontendComponent
{
	public $permission;
    
    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }

        $this->permission = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 25); // 25 - id доступа к договорам доступа

        if(!$this->permission){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        return true;
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new ZonesAccessAgreementsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $opers = Operators::loadList();
        $companies = ManagCompanies::getCompaniesList();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'opers' => $opers,
            'companies' => $companies,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
    	if($this->permission != 2){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        $model = new ZonesAccessAgreements();
        $model->auto_prolongation = 1;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->auto_prolongation) {
                $model->closed_at = 0;
            } else {
                $model->closed_at = strtotime($model->closed_at);
                $model->scenario = 'not_auto_prolongation';
            }
            $file = UploadedFile::getInstance($model, 'file');
            if ($file && $file->tempName) {
                $model->file = $file;
                if ($model->validate(['file'])) {
                    $dir = Yii::getAlias('@frontend/web/media/archive/access_agreements');
                    $fileName = SiteHelper::genUniqueKey(30). '.' . $model->file->extension;
                    while (file_exists($dir."/".$fileName)) {
                        $fileName = SiteHelper::genUniqueKey(30). '.' . $model->file->extension;
                    }
                    $model->extension = $model->file->extension;
                    $model->file->saveAs($dir."/".$fileName);
                    $model->file = $fileName; // без этого ошибка
                    $model->name = "/media/archive/access_agreements/".$fileName;
                }
            }
            $model->created_at = time();
            $model->opened_at = strtotime($model->opened_at);
            $model->cas_user_id = $this->cas_user->id;
            $model->rent_price = SiteHelper::tofloat($model->rent_price);

            if ($model->save()) {
                $history = new ZonesAccessAgreementsHistory();
                $history->setAttributes($model->getAttributes());
                $history->origin_id = $model->id;
                $history->save();

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionUpdate($id)
    {
    	if($this->permission != 2){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->auto_prolongation) {
                $model->closed_at = 0;
            } else {
                $model->closed_at = strtotime($model->closed_at);
                $model->scenario = 'not_auto_prolongation';
            }

/*            $file = UploadedFile::getInstance($model, 'file');
            if ($file && $file->tempName) {
                $model->file = $file;
                if ($model->validate(['file'])) {
                    $dir = Yii::getAlias('@frontend/web/media/archive/access_agreements');
                    $fileName = SiteHelper::genUniqueKey(30). '.' . $model->file->extension;
                    while (file_exists($dir."/".$fileName)) {
                        $fileName = SiteHelper::genUniqueKey(30). '.' . $model->file->extension;
                    }
                    $model->extension = $model->file->extension;
                    $model->file->saveAs($dir."/".$fileName);
                    $model->file = $fileName; // без этого ошибка
                    $model->name = "/media/archive/access_agreements/".$fileName;
                }
            }*/
            
            $model->opened_at = strtotime($model->opened_at);
            $model->rent_price = SiteHelper::tofloat($model->rent_price);
            if ($model->save()) {
                $history = new ZonesAccessAgreementsHistory();
                $history->setAttributes($model->getAttributes());
                $history->origin_id = $model->id;
                $history->created_at = time();
                $history->cas_user_id = $this->cas_user->id;
                $history->save();

                return $this->redirect(['view', 'id' => $model->id]);
            }
        } 

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = ZonesAccessAgreements::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
