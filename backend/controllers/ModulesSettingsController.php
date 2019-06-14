<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 20.11.18
 * Time: 21:42
 */

namespace backend\controllers;

use common\components\SiteHelper;
use yii\web\ForbiddenHttpException;
use backend\components\BackendComponent;
use Yii;
use common\models\modules_settings\MngModules;
use yii\web\NotFoundHttpException;
use common\models\Access;
use yii\helpers\Json;
use common\models\SearchFields;
use common\models\SearchFieldsSettings;
use common\models\Properties;
use common\models\Services;
use common\models\Attributes;

class ModulesSettingsController extends BackendComponent
{
    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }

        $noAccess = false;
        if($this->permission == 1){
            switch(Yii::$app->controller->action->id){
                case 'create':
                    $noAccess = true;
                    break;
                case 'update':
                    $noAccess = true;
                    break;
                case 'access-update':
                    $noAccess = true;
                    break;
                default:
            }
        }

        if($noAccess){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        return true;
    }

    public function actionIndex(){
        $searchModel = new MngModules();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionAccessUpdate()
    {
        if(!Yii::$app->request->isAjax){
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model = Access::findOne(Yii::$app->request->post("id"));
        if($model !== null){
            $model->value = Yii::$app->request->post("value");
            if($model->save()){
                return Json::encode(['status' => 'success']);
            }
        }
        return Json::encode(['status' => 'error']);
    }



    protected function findModel($id)
    {
        if (($model = MngModules::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCreate()
    {
        $model = new MngModules();
        if($model->load(Yii::$app->request->post())){
            $post = Yii::$app->request->post()['MngModules'];
            $model->name = $post['name'];
            $model->descr = $post['descr'];
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        };

        /*
        SiteHelper::debug(Yii::$app->request->post());
        die();
        if(){
//            $model->descr = mb_strtolower($model->cas_name);
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
*/
        return $this->render('create', [
            'model' => $model,
        ]);
    }
}