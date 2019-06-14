<?php
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 04.09.18
 * Time: 15:20
 */

namespace common\models;


use yii\db\ActiveRecord;
use Yii;
use common\components\SiteHelper;
use yii\db\Query;

class Asterisk extends ActiveRecord
{
    public $ip;
    public function attributeLabels()
    {
        return [
            'ip' => 'IP адрес',
        ];
    }

    public function rules()
    {
       return [
           ['ip','trim'],
           ['ip','required'],
           ['ip','ip','ipv4'=>true,'subnet'=> null, 'expandIPv6'=> false,"message"=>"Неверный IP адрес!"],
       ];
    }

    public static function getDb() {
        return Yii::$app->get('db_sip');
    }

    public static function tableName()
    {
//        return 'sip_buddies_test';  //Тестовая
        return 'sip_buddies';           //Боевая
    }

    public static function VoipInsert($number,$secret,$context,$action,$gate_id,$user_id){
        (new Query())
            ->createCommand()
            ->insert('voip',[
                'number'=>$number,
                'password'=>$secret,
                'user_id'=>$user_id,
                'date'=>time(),
                'action'=>$action,
                'gate_id'=>$gate_id,
                'context'=>$context,
            ])->execute();
        return 0;
    }

    public static function UpdateData($data,$user_id){
        foreach ($data as $sip){
//            SiteHelper::debug($sip);
            if(Asterisk::find()->where(['name'=>$sip['ntel']])->exists()){   //Проверяем существует ли запись, если да - то update ее, иначе insert
                $sip_account = Asterisk::find()->where(['name'=>$sip['ntel']])->one();
                $account = (new Query())->from('voip')->where(['number'=>$sip['ntel']])->orderBy(['date'=>SORT_DESC]);

//                SiteHelper::debug($account->one());
//                die();
                if((!$account->exists()||($account->select('action')->scalar()==2))){
//                    $voip_id = $account->scalar();
                       Asterisk::VoipInsert($sip_account->name,$sip_account->secret,$sip_account->context,1,$sip['device_id'],$user_id);
                }

//                $voip_id = $account->scalar();
                if(isset($sip['delete'])){

                    Asterisk::VoipInsert($sip['ntel'],$sip['pass'],$sip_account->context,2,$sip['device_id'],$user_id);
                    $sip_account->delete();
                    return 0;
                }

                if(($sip_account->secret!=$sip['pass'])||($sip_account->context!=$sip['context'])){
                    Asterisk::VoipInsert($sip['ntel'],$sip['pass'],$sip['context'],4,$sip['device_id'],$user_id);
                }

                if($sip['last_state']==0){
                    Asterisk::VoipInsert($sip['ntel'],$sip['pass'],$sip['context'],5,$sip['device_id'],$user_id);
                }
//                die();
//                if(($sip_account->secret==$sip['pass'])&&($sip_account->context==$sip['context'])){
//                    Asterisk::VoipInsert($sip['ntel'],$sip['pass'],$sip['context'],4,1,$user_id);
//                }
//                SiteHelper::debug($sip_account->context);
//                SiteHelper::debug($sip);
//                die();
//                SiteHelper::debug($sip_account);
//
//                die();

            }else{
                if(!isset($sip['delete'])) {
//                    if (!$sip['delete']) {
                        $sip_account = new Asterisk();
//                        SiteHelper::debug($sip);
//                        die();
                        Asterisk::VoipInsert($sip['ntel'], $sip['pass'], $sip['context'], 1, $sip['device_id'], $user_id);
//                    }
                }
            };
            if(!isset($sip['delete'])) {
                $sip_account->context = $sip['context'];
                $sip_account->name = $sip['ntel'];
                $sip_account->host = "dynamic";
                $sip_account->dtmfmode = "auto";
                $sip_account->language = "ru";
                $sip_account->disallow = "all";
                $sip_account->allow = "ulaw";
                $sip_account->regseconds = 0;
                $sip_account->lastms = 20;
                $sip_account->transport = "udp";
                $sip_account->type = "friend";
                $sip_account->{'call-limit'} = "2";
                $sip_account->nat = "force_rport,comedia";
                $sip_account->secret = $sip['pass'];
                $sip_account->callerid = $sip['ntel']." <".$sip['ntel'].">";
                $sip_account->save(false);
            }
        }
        return 0;
    }
}