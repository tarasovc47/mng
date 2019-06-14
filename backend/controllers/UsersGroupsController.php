<?php

namespace backend\controllers;

use Yii;
use backend\components\BackendComponent;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\models\UsersGroups;
use common\models\Departments;
use common\models\CasUser;
use common\models\Access;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class UsersGroupsController extends BackendComponent
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
                default:
            }
        }

        if($noAccess){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

        return true;
    }

    public function actionCreate()
    {
        $model = new UsersGroups();

        $department = false;
        if(Yii::$app->request->get("department")){
            $department = Departments::findOne(Yii::$app->request->get("department"));
        }

        if(!$department){
            throw new NotAcceptableHttpException('Не удалось определить отдел.');
        }

        $model->department_id = $department->id;

        if($model->load(Yii::$app->request->post())){
            if($model->save()){                
                return $this->redirect(['/departments/update', 'id' => $model->department_id, 'tab' => 'groups']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $head_id = $model->head_id;

        if($model->load(Yii::$app->request->post())){
            if($model->save()){
                // Для службы эксплуатации
                if($model->department_id == 2){
                    $this->updateNodBrigadier($model, $head_id);
                }

                return $this->redirect(['/departments/update', 'id' => $model->department_id, 'tab' => 'groups']);
            }
        }

        $list = [
            0 => "&mdash; Не отмечен &mdash;"
        ];

        $users = CasUser::loadList([ "group_id" => $model->id ]);
        $list = ArrayHelper::merge($list, $users);

        return $this->render('update', [
            'model' => $model,
            'users' => $list,
        ]);
    }

    public function actionSave(){
        if(!Yii::$app->request->isAjax){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $data = Yii::$app->request->post("data");
        CasUser::saveGroups($data);
        return Json::encode(['status' => 'success']);
    }

    protected function findModel($id){
        if (($model = UsersGroups::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function updateNodBrigadier($model, $head_id){
        if($model->head_id != $head_id){
            if($head_id){
                Access::removeAccess($head_id, 11); // Принятно в работу
                Access::removeAccess($head_id, 16); // Назначить ответственного (расширенная)
                Access::removeAccess($head_id, 18); // Передать в другой отдел (расширенная)
            }

            if($model->head_id){
                $descr = "Автоматическая установка доступа для бригадира.\nbackend\controllers\UsersGroupsController -> updateNodBrigadier\nДата установки: " . date("d.m.Y H:i:s");

                Access::insertAccess($model->head_id, 11, 2, $descr); // Принятно в работу
                Access::insertAccess($model->head_id, 16, 2, $descr); // Назначить ответственного (расширенная)
                Access::insertAccess($model->head_id, 18, 2, $descr); // Передать в другой отдел (расширенная)
            }
        }
    }
}
