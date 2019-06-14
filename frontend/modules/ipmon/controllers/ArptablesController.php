<?php

namespace frontend\modules\ipmon\controllers;
use common\components\SiteHelper;
use frontend\components\FrontendComponent;
use common\models\arps\ArpTables;
use Yii;
use common\models\Access;
use yii\web\ForbiddenHttpException;
use yii\widgets\ActiveForm;
use yii\web\Response;

class ArptablesController extends FrontendComponent
{

    protected $permission;

    public function beforeAction($action){
        if(!parent::beforeAction($action)){
            return false;
        }
        $this->permission['makestatic'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 8);
        $this->permission['addrouter'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 5);
        $this->permission['commenting'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 6);
        $this->permission['read'] = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 9); //9 - id доступа к ARP
        if(!$this->permission['read']){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }
        $this->view->title = "IpMon | ARP Таблицы";
        return true;
    }

    public function actionStatus(){
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
//            print_r($post);
//            die();
            $ip = $post['ip'];
            $credentials = ArpTables::find()->where(['ip'=>long2ip($post['router_ip'])])->one();
            $arp_record= ArpTables::IpArpPrintIP($credentials,$ip);
            if(empty($arp_record)){
                $interface = $post['iface'];
                $ip  = $post['ip'];
                $mac  = '00:00:00:00:00:00';
            }else{
                $interface = $arp_record[0]['interface'];
                $ip  = $arp_record[0]['address'];
                $mac  = $arp_record[0]['mac-address'];
            }
            switch ($post['status']){
                case "true":
                    echo "Making static MAC ".$mac." for IP ".$ip." on interface ".$interface;
                    $ARRAY_wr = ArpTables::IpArpStaticWrite($credentials,$interface,$ip,$mac);
                    print_r($ARRAY_wr);
                    break;

                case "false":
                    echo "Making dynamic ".$ip;
                    $current_ip = ArpTables::IpArpRemoveOne($credentials,$ip);
                    print_r($current_ip);
                    break;
            }
        }
    }

    public function actionComment(){
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();

            switch($post['request']){
                case 0:
                    $id = $post['router_id'];
                    $addr = $post['ip'];
                    $current_comment = [];
                    $current_comment['comment'] = "";
                    $credentials = ArpTables::findOne($id);
                    $tmp = ArpTables::IpArpFindOne($credentials,$addr);
                    if(isset($tmp[0]['comment'])){
                        $current_comment['comment'] = iconv('cp1251', 'utf-8', $tmp[0]['comment']);
                    }
                    $current_comment['rid'] = $id;
                    return json_encode($current_comment);
                    break;
                case 1:
                    if(isset($post['comment'])){
                        $credentials = ArpTables::findOne($post['comment']['router_id']);
//                        print_r($post);
//                        die();
                        $addresses = ArpTables::IPAddressesPrint($credentials);
                        $ip_range = array();
                        $json = array();
                        $json ['router_id'] = ip2long($credentials->ip);
                        $json ['iface'] = $post['comment']['iface'];
//                    $post['this'] = $this->IpArpAddComment($post['rid'],$post['ip'], $post['comment']);
//                        for ($i = 0; $i < count($addresses); $i++) {
//                            $ip = explode('/', $addresses[$i]['address'])[0];
//                            $mask = explode('/', $addresses[$i]['address'])[1];
//                            $network = SiteHelper::cidr2network($ip, $mask);
//                            $interf = $addresses[$i]['interface'];
//                            $ip_range[$interf] = $this->cidr2range($network . "/" . $mask);
//                        }


//                        $longip = ip2long($post['ip']);
//                        foreach($ip_range as $net => $range){
//                            if(($longip<ip2long($ip_range[$net][1]))&&($longip>ip2long($ip_range[$net][0]))){
//                                $json['net'] = $net;
//                            }
//                        }
                        $json['message'] = ArpTables::IpArpAddComment($credentials,$post['comment']['ip'],SiteHelper::TagStripper($post['comment']['comment']));
                        return json_encode($json);
                    }
                    break;
            }
        }
    }

    public function actionArps(){
        if (Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $networks = new ArpTables();
//            print_r($post);
//            die();
            $credentials = $networks->find()->where(['ip'=>long2ip($post['router'])])->one();
            $CurrentNetworkArp = $networks->ARP($post['net'],$credentials);
            $result['html']= $this->renderPartial('arptable',[
                'arp'=>$CurrentNetworkArp,
                'net'=>$post['net'],
                'emptyMAC'=>$networks->EmptyMAC,
                'permissions'=>$this->permission,
                'router'=>$credentials->id
            ]);
            $tmp_free = current($CurrentNetworkArp)['freeCount']-1;
            $tmp_static = current($CurrentNetworkArp)['staticCount'];
            $tmp_dyn = current($CurrentNetworkArp)['totalCount']-$tmp_free-$tmp_static;

            $result['counts']['free'] = $tmp_free;
            $result['counts']['static'] = $tmp_static;
            $result['counts']['dynamic'] = $tmp_dyn;

            return json_encode($result);
        }
    }

    public function actionIndex($id = null){
        $send['permissions'] = $this->permission;
        switch($id){
            case null:
                $post = Yii::$app->request->post();
                if (Yii::$app->request->isAjax) {
                    if (isset($post['ArpTables'])) {
                        if ($this->permission['addrouter'] > 1) {
//                            print_r($post['ArpTables']);
                            $send['post'] = ArpTables::Saverouter($post['ArpTables']);
//                            $send['post'] = $this->actionSaverouter($post['ArpTables']);
                        }
                    }
                }
                $template = 'index';
                $send['routers']=ArpTables::find()->where(['visible'=>true])->orderBy('id')->asArray()->all();

            break;

            case 0:
                if($this->permission['addrouter']>1){
                    $send['model'] = new ArpTables();
                    $send['router'] = [
                        'ip'=>'',
                        'description'=>'',
                        'user'=>'',
                        'apipass'=>'',
                        'rwuser'=>'',
                        'apiwrpass'=>'',
                        'text'=>'',
                        'visible'=>1,
                    ];
                    $template = 'edit';
                }else{
                    $template = 'noaccess';
                }
                break;

            default:
                $networks = new ArpTables();
                $template = 'list';
                $send['id'] = $id;
                $credentials = $networks->find()->where(['ip'=>long2ip($id)])->one();
                $tmp = $networks->Networks($credentials);
                $vtemp = [];
                $atemp = [];
                foreach ($tmp['vlans'] as $vlan=>$vdata) {
                    $vtemp[$vdata['name']]['comment'] = "";
                    $vtemp[$vdata['name']]['vlan-id'] = $vdata['vlan-id'];
                    if(isset($vdata['comment'])){
                        $vtemp[$vdata['name']]['comment'] = mb_convert_encoding($vdata['comment'], 'utf-8', 'cp1251');
                    }
                }

                $send['timeout'] = $tmp['addrs']['timeout'];
                unset($tmp['addrs']['timeout']);
                foreach ($tmp['addrs'] as $iface=>$idata) {
                    $range = SiteHelper::cidr2rangeInLong($idata['address']);
                    if($range[0]!=$range[1]) {
                        $atemp[ip2long($idata['network'])]=[
                            'subnet'=>$idata['network'],
                            'range'=>$range,
                            'mask'=>explode("/",$idata['address'])[1],
                            'iface'=>$idata['interface'],
                            'vlan'=> isset($vtemp[$idata['interface']]) ? $vtemp[$idata['interface']]['vlan-id'] : "",
                            'comment'=> isset($vtemp[$idata['interface']]) ? $vtemp[$idata['interface']]['comment'] : (isset($idata['comment']) ? $idata['comment'] : "")
                        ];
                    }


//                    $atemp[$idata['name']]['comment'] = "";
//                    $atemp[$idata['name']]['vlan-id'] = $idata['vlan-id'];
//                    if(isset($idata['comment'])){
//                        $atemp[$vdata['name']]['comment'] = mb_convert_encoding($vdata['comment'], 'utf-8', 'cp1251');
//                    }
                }


//                $atemp = $tmp['addrs'];
                $send['subnetslist'] = $atemp;
                $send['tmp'] = $tmp;
                $send['router'] = $credentials;
        }

        return $this->render($template,$send);
    }

    public function actionValidate()
    {
        $model = new ArpTables();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionEditrouter($id = null){
        $send['permissions'] = $this->permission;
        if($this->permission['addrouter']>1) {
            $send['cas'] = $this->cas_user;
            $model = new ArpTables();
            $send['id'] = $id;
            $send['model'] = $model;
            $send['router'] = ArpTables::find()->where(['ip' => long2ip($id)])->one();
            $send['access'] = ArpTables::LoadAccesses($send['cas']['cas_id'],$send['cas']['department_id'],$send['router']['id']);
            $send['data'] = ArpTables::IPAddressesPrint($send['router']);
            $template = 'edit';
        }else{
            $template = 'noaccess';
        }
        return $this->render($template,$send);
    }
}