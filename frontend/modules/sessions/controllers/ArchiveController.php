<?php
namespace frontend\modules\sessions\controllers;
use common\models\radius\RadiusArch;
use common\models\radius\RadiusArchSearch;
use Yii;
use frontend\components\FrontendComponent;
use common\models\Access;
use yii\data\Pagination;
use yii\web\ForbiddenHttpException;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\data\ActiveDataProvider;

class ArchiveController extends FrontendComponent
{
    protected $permissions;

    protected $service_auth_code = [
        "A" => "Активация",
        "D" => "Деактивация сервиса",
        "T" => "NAS запросил активацию",
        "F" => "NAS сообщил об ошибке",
        "SF" => "Действие принято, но транзакция не выполнена",
        "S" => "NAS ответил об успешной активации"
    ];
    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }
        $this->permissions['access'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 14); //14 - id доступа к SessMon
        $this->permissions['history'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 15); //14 - id доступа к SessMon
        $this->permissions['kill_sess'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 17); //14 - id доступа к SessMon
        $this->permissions['blacklist'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 31); //14 - id доступа к SessMon

        if(!$this->permissions['access']){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $this->view->title = "Мониторинг сессий";
        return true;
    }

    public function actionValidate()
    {
        $model = new RadiusArch();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionIndex($id=null)
    {
        $AccountingLog = [];
        $archiveData = '';
        $pages = [];

        $post = Yii::$app->request->post();
        /*if(isset($post['RadiusArch'])){
            $query = RadiusArch::find()
                ->select('*')
                ->asArray()
//                ->where(['login'=>$post['RadiusArch']['login']])
//                ->orWhere(['mac_addr'=>$post['RadiusArch']['macaddr']])
//                ->orWhere(['ipv4_addr'=>$post['RadiusArch']['ipv4']])
                ->where(['between','started_at',$post['RadiusArch']['begin'],$post['RadiusArch']['end']]);
//                ->all();

            if($post['RadiusArch']['login']!=''){
                $query->andWhere(['login'=>$post['RadiusArch']['login']]);
            }
            if($post['RadiusArch']['macaddr']!=''){
                $query->andWhere(['mac_addr'=>$post['RadiusArch']['macaddr']]);
            }
            if($post['RadiusArch']['ipv4']!=''){
                $query->andWhere(['ipv4_addr'=>$post['RadiusArch']['ipv4']]);
            }
            if($post['RadiusArch']['ipv6']!=''){
                $query->andWhere(['ipv6_prefix'=>$post['RadiusArch']['ipv6']]);
            }

            $countQuery = clone $query;
            $pages = new Pagination(['totalCount'=>$countQuery->count(),'pageSize'=>10 ,'pageSizeParam'=>false, 'forcePageParam' => false]);
            $dataProvider = new ActiveDataProvider([
                'query'=>$query,
                'pagination'=>[
                    'pageSize' => 10,
                    'pageSizeParam'=>false,
                    'forcePageParam' => false
                    ]
                ]);
//            $AccountingLog = $query->offset($pages->offset)->limit($pages->limit)->all();
            $archiveData = $this->renderPartial('archive',[
                'dataProvider'=>$dataProvider,
                'pages'=>$pages
            ]);
        }*/
//        if(isset($post['RadiusArch'])) {
//
//        }


        $searchModel = new RadiusArchSearch();
        $dataProvider = $searchModel->search($post);
        if(!isset($post['RadiusArchSearch']['begin'])){
            $dataProvider->query->andFilterWhere(['login'=>"empty"]); //Из - за этого может не работать пагинация, это фильтр по умолчанию
        }
//        $archiveData = $this->renderPartial('archive',[
//            'dataProvider'=>$dataProvider,
//            'searchModel'=>$searchModel,
//            'pages'=>$pages
//        ]);
        return $this->render('index',
            [
            'permissions'=>$this->permissions,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            ]
            );
//        return $this->render('index',
//            [
//                'AccountingLog'=> $archiveData ,
//                'post'=>$post,
//                'pages'=>$dataProvider,
//                'model'=> new RadiusArch(),
//                'user'=>$this->cas_user,
//                'permissions'=>$this->permissions
//            ]
//         );
    }

}