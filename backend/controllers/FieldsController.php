<?php

namespace backend\controllers;

use Yii;
use common\models\Fields;
use common\models\Attributes;
use common\models\ApplicationsStatuses;
use common\models\search\FieldsSearch;
use backend\components\BackendComponent;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\ForbiddenHttpException;

class FieldsController extends BackendComponent
{
    public $class;
    public $url;
    public $breadcrumb;

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
                default:
            }
        }

        if($noAccess){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        return true;
    }

    public function actionView($id){
        $model = $this->findModel($id);
        $this->prepareSettings($model->target_table, $model->target_id);

        if(!$this->class){
            throw new NotAcceptableHttpException('Невозможно выполнить запрос.');
        }

        $target = $this->class::findOne($model->target_id);

        if(!$target){
            throw new NotAcceptableHttpException('Не удалось определить к чему относится поле.');
        }

        return $this->render('view', [
            'model' => $model,
            'target' => $target,
            'url' => $this->url,
            'breadcrumbs' => $this->breadcrumbs,
        ]);
    }

    public function actionCreate(){
        $target_id = Yii::$app->request->get("id");
        $target_table = Yii::$app->request->get("target");
        $this->prepareSettings($target_table, $target_id);

        if(!$target_id || !$this->class){
            throw new NotAcceptableHttpException('Невозможно выполнить запрос.');
        }

        if($target_id && (($target = $this->class::findOne($target_id)) !== null)){
            $model = new Fields();
            $model->target_id = $target_id;
            $model->target_table = $this->class::tableName();

            if($model->load(Yii::$app->request->post())){

                if($model->type && ($model->type == "list")){
                    $model->scenario = 'list';
                }

                if($model->validate()){
                    $model->prepareDataAttribute();

                    if($model->save()){
                        return $this->redirect([$this->url . '/view', 'id' => $target_id]);
                    }
                }
            }

            return $this->render('create', [
                'model' => $model,
                'target' => $target,
                'url' => $this->url,
                'breadcrumbs' => $this->breadcrumbs,
            ]);
        }
        else{
            throw new NotAcceptableHttpException('Не удалось определить к чему создается поле.');
        }          
    }

    public function actionUpdate($id){
        $model = $this->findModel($id);
        $model->prepareAttributesFromData();
        $this->prepareSettings($model->target_table, $model->target_id);

        if(!$this->class){
            throw new NotAcceptableHttpException('Невозможно выполнить запрос.');
        }

        $target = $this->class::findOne($model->target_id);

        if(!$target){
            throw new NotAcceptableHttpException('Не удалось определить к чему относится поле.');
        }

        if($model->load(Yii::$app->request->post())){

            if($model->type && ($model->type == "list")){
                $model->scenario = 'list';
            }

            if($model->validate()){
                $model->prepareDataAttribute();

                if($model->save()){
                    return $this->redirect(['view', 'id' => $model->id]);
                }

            }
        }

        return $this->render('update', [
            'model' => $model,
            'target' => $target,
            'url' => $this->url,
            'breadcrumbs' => $this->breadcrumbs,
        ]);
    }

    protected function findModel($id){
        if (($model = Fields::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function prepareSettings($target_table, $target_id){
        switch($target_table){
            case "attributes":
                $this->class = new Attributes();
                $this->url = "attributes";

                $attribute = Attributes::findOne($target_id);
                $this->breadcrumbs[] = [ 'label' => 'Отделы компании', 'url' => '/departments/index' ];
                $this->breadcrumbs[] = [ 'label' => $attribute->department->name, 'url' => [ '/departments/view', 'id' => $attribute->department->id ]];
                break;
            case "applications_statuses":
                $this->class = new ApplicationsStatuses();
                $this->url = "applications-statuses";
                $this->breadcrumb = ['label' => "Модуль «Техподдержка» :: Статусы заявок", 'url' => ['/techsup/applications-statuses/index']];
                break;
            default:
                $this->class = false;
        }
    }
}
