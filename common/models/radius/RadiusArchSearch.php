<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 05.04.18
 * Time: 11:34
 */

namespace common\models\radius;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class RadiusArchSearch extends RadiusArch
{
    public function rules()
    {

        return [

            [['login','macaddr','ipv4','ipv6'],'trim'],
            ['ipv4','ip','ipv4'=>true,'subnet'=> null, 'expandIPv6'=> false,"message"=>"Неверный IP адрес!"],
            ['macaddr','validateMacAddr','skipOnEmpty'=> true,'skipOnError'=>true],
            [['begin','end'],'required'],
            [['login','macaddr','ipv4','ipv6'],function($attribute, $params, $validator){
                if(empty($this->login) && empty($this->macaddr) && empty($this->ipv4) && empty($this->ipv6)) {
                    $this->addError($attribute,"Заполните хотя бы одно из полей");
                }
            },'skipOnEmpty'=> false,'skipOnError'=>false],
        ];
    }

    public function validateMacAddr($attribute, $params){
        if(!filter_var($this->$attribute,FILTER_VALIDATE_MAC)){
            $this->addError($attribute, "Неверный MAC адрес!");
        }
    }

    public function search($params){
       $query = RadiusArch::find()
            ->asArray();
//                ->where(['login'=>$post['RadiusArch']['login']])
//                ->orWhere(['mac_addr'=>$post['RadiusArch']['macaddr']])
//                ->orWhere(['ipv4_addr'=>$post['RadiusArch']['ipv4']])
//            ->where(['between','started_at',$begin,$end]);
//                ->all();


//        if($login!=''){
//            $query->andWhere(['login'=>$login]);
//        }
//        if($macaddr!=''){
//            $query->andWhere(['mac_addr'=>$macaddr]);
//        }
//        if($ipv4!=''){
//            $query->andWhere(['ipv4_addr'=>$ipv4]);
//        }
//        if($ipv6!=''){
//            $query->andWhere(['ipv6_prefix'=>$ipv6]);
//        }


        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'pagination'=>[
                'pageSize' => 10,
                'pageSizeParam'=>false,
                'forcePageParam' => false
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['login' => $this->login]);
        $query->andFilterWhere(['mac_addr' => $this->macaddr]);
        $query->andFilterWhere(['ipv4_addr' => $this->ipv4]);
        $query->andFilterWhere(['ipv6_prefix' => $this->ipv6]);
        $query->andFilterWhere(['>=', 'stopped_at', $this->begin]);//->orFilterWhere(['>=', 'stopped_at', $this->begin]);
        $query->andFilterWhere(['<=', 'started_at', $this->end]);//->orFilterWhere(['<=', 'stopped_at', $this->end]);
        return $dataProvider;
    }
}