<?php

namespace frontend\modules\techsup\controllers;

use Yii;
use frontend\components\FrontendComponent;
use yii\web\ForbiddenHttpException;
use common\models\Applications;

class DashboardController extends FrontendComponent
{
    public $default_sort = [
        "applications" => [ 
            "created_at" => SORT_DESC, 
            "applications.id_spec" => SORT_ASC 
        ],
    ];
    public $general_where = [ '!=', 'application_status_id', 8 ];

    public function beforeAction($action){
        $this->view->title = "Рабочий стол | Техподдержка";
        return parent::beforeAction($action);
    }

    public function actionIndex(){
        if($this->cas_user->department_id == 3){
            return $this->redirect("/techsup/dashboard/engineer");
        }

        if($this->cas_user->department_id == 2){
            if(!empty($this->cas_user->usersGroup)){
                if($this->cas_user->usersGroup->head_id == $this->cas_user->id){
                    return $this->redirect("/techsup/dashboard/brigadier");
                }
                else{
                    return $this->redirect("/techsup/dashboard/nod");
                }
            }
            else{
                // Теоретически такого быть не может
                throw new ForbiddenHttpException('Критическая ошибка, обратитесь в техническую поддержку.');
            }
        }

        if($this->cas_user->department_id == 1){
            return $this->redirect("/techsup/dashboard/support");
        }

        die("You are not prepared!");
    }

    // Рабочий стол инженера сетевых технологий
    public function actionEngineer(){
        if($this->cas_user->department_id != 3){
            throw new ForbiddenHttpException('Нет доступа');
        }

        $applications = Applications::find()
            ->joinWith('applicationsEvents')
            ->where([ "department_id" => 3 ])
            ->andWhere($this->general_where)
            ->orderBy($this->default_sort["applications"])
            ->all();

        return $this->render('engineer', [
            'applications' => $applications,
            'user' => $this->cas_user,
        ]);
    }

    // Рабочий стол бригидара nod
    public function actionBrigadier(){
        if(($this->cas_user->department_id != 2) || empty($this->cas_user->usersGroup) || ($this->cas_user->usersGroup->head_id != $this->cas_user->id)){
            throw new ForbiddenHttpException('Нет доступа');
        }

        $applications = Applications::find()
            ->joinWith([
                'applicationsEvents', 
                'applicationsEvents.applicationComment',
                'applicationsEvents.casUser',
                'applicationsEvents.applicationAttributes',
            ])
            ->where([ "applications.department_id" => 2, "applications.group_id" => $this->cas_user->usersGroup->id ])
            ->andWhere($this->general_where)
            ->orderBy($this->default_sort["applications"])
            ->all();

        return $this->render('brigadier', [
            'applications' => $applications,
            'user' => $this->cas_user,
        ]);
    }

    public function actionNod(){
        if(($this->cas_user->department_id != 2) || empty($this->cas_user->usersGroup)){
            throw new ForbiddenHttpException('Нет доступа');
        }

        $applications = Applications::find()
            ->joinWith([
                'applicationsEvents', 
                'applicationsEvents.applicationComment',
                'applicationsEvents.casUser',
                'applicationsEvents.applicationAttributes',
            ])
            ->where([ 
                "applications.department_id" => 2, 
                "applications.group_id" => $this->cas_user->usersGroup->id,
                "applications.responsible" => $this->cas_user->id 
            ])
            ->andWhere($this->general_where)
            ->orderBy($this->default_sort["applications"])
            ->all();

        return $this->render('nod', [
            'applications' => $applications,
            'user' => $this->cas_user,
        ]);
    }

    public function actionSupport(){
        if($this->cas_user->department_id != 1){
            throw new ForbiddenHttpException('Нет доступа');
        }

        $applications = Applications::find()
            ->joinWith([
                'applicationsEvents', 
                'applicationsEvents.applicationComment',
                'applicationsEvents.casUser',
                'applicationsEvents.applicationAttributes',
            ])
            ->where([ 
                "applications.department_id" => 1,
            ])
            ->andWhere($this->general_where)
            ->orderBy($this->default_sort["applications"])
            ->all();

        return $this->render('support', [
            'applications' => $applications,
            'user' => $this->cas_user,
        ]);
    }
}
