<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 04.02.19
 * Time: 21:46
 */

namespace frontend\modules\tools\controllers;
use common\components\SiteHelper;
use frontend\components\FrontendComponent;
use Yii;
use common\models\tools\Rpis;
class RpiController extends FrontendComponent
{
    public function actionIndex(){
        $searchModel = new Rpis();
//            $dataProvider = $searchModel->search(Yii::$app->request->get());
        $dataProvider = $searchModel->search(Yii::$app->request->post());
        return $this->render('index',
            [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
    }

    public function actionUpdate($id){
        $post = Yii::$app->request->post();
        if(isset($post['Rpis'])){
            $rpi = Rpis::find()->where(['id'=>$id])->one();
            $rpi->config = $post['Rpis']['config'];
            $rpi->save();
            return $this->redirect("/tools/rpi/update?id=".$id);
        }
        return $this->render('update',
            [
                'rpi' => Rpis::find()->where(['id'=>$id])->asArray()->one(),
                'model' => Rpis::findOne($id)
            ]);
    }
}