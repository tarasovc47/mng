<?php
namespace frontend\modules\tools\controllers;
use Yii;
use frontend\components\FrontendComponent;
//use frontend\modules\tools\models\ConverterForm;

class ConverterController extends FrontendComponent
{
    public function beforeAction($action){
        $this->view->title = "Инструменты | Конвертер конфигурации";
        return parent::beforeAction($action);
    }

    public function actionAjax(){
        if( Yii::$app->request->isAjax ){
            print_r($this->Convert($_POST));
        }else{
            $this->redirect('index');
        }
    }

    private function RangeFromExplode($arr){
        $a = $arr[0];
        $b = $arr[1];
        $res = array();
        for($i=$a;$i<=$b;$i++){
            $res[] = $i;
        }
        return $res;
    }

    private function Convert($input_data){

        $interface_type = [
            "FastEthernet",
            "GigabitEthernet"
        ];
        $list = explode("\n", $input_data['ZyxelConfigText']);

//        print_r($list);
        $vlans = array();
        $zyxterface = array();
        $vlansText = array();
        $f = array(
            'vlan' => 0,
            'interface' => 0,
            'vid' => -1,
            'eth' => -1
        );
        $tmp = 0;
        $lastport = 0;
        foreach($list as $tmp){
            $item = trim($tmp);
            if((strpos($item,"vlan ")!==false)&&(strpos($item,"policy ")===false)){
                $f['vlan']=1;
                $f['vid'] = explode(" ",$item)[1];
                if(($f['vid']==1)||(!is_numeric($f['vid']))){ //проверка на 1 vlan и на число
                    $f['vlan']=0;
                }else{
                    $vlans[$f['vid']] = array();
                    $vlansText[] = $f['vid'];
                } //Исключаем 1ый vlan
            }

            if(strpos($item,"interface port-channel")!==false){
                $f['interface']=1;
                $f['eth'] = explode("interface port-channel ",$item)[1];
                $zyxterface[$f['eth']]=array();
//                $lastport=$f['eth'];
            }
            $ItemWordsCount = count(explode(" ",$item));

            if($f['interface']){
                if(strpos($item,"exit")!==false){
                    $f['interface']=0;
                }else{
//                    $zyxterface[$f['eth']][] = $item;

                    if(strpos($item,"pvid")!==false) {
                        $zyxterface[$f['eth']]['vid'] = explode(" ", $item)[1];
                    }
                    if(strpos($item,"bandwidth-limit ingress ")!==false) {
                        $zyxterface[$f['eth']]['limit'] = explode(" ", $item)[2];
                    }

                    if($item=="inactive") {
                        $zyxterface[$f['eth']][$item] = true;
                    }
                }
            }

            if($f['vlan']){
                if(strpos($item,"exit")!==false){
                    $f['vlan']=0;
                }else{
                    if(($ItemWordsCount>1)){
                        $Param = explode(" ",$item)[0];
                        $ParamValue = explode(" ",$item)[1];
                        switch ($Param){
                            case 'fixed':
                            case 'untagged':
                                $tmp = explode(",",$ParamValue);
//                                $vlans[$f['vid']][] = $tmp;
                                foreach ($tmp as $tmp_item){
//                                    $vlans[$f['vid']][$Param][] = strpos($tmp_item,"-");
                                    if(strpos($tmp_item,"-")>0){  ///Здесь косяк
                                        $tmprange = $this->RangeFromExplode(explode("-",$tmp_item));
                                        for($i=0;$i<count($tmprange);$i++){
                                            $vlans[$f['vid']][$Param][] = $tmprange[$i];
                                            if($tmprange[$i]>$lastport){
                                                $lastport=$tmprange[$i];
                                            }
                                        }//
                                    }else{
                                        $vlans[$f['vid']][$Param][] = $tmp_item;
                                        if($tmp_item>$lastport){
                                            $lastport=$tmp_item;
                                            $vlans[$f['vid']]['test'][] = $tmp_item;
                                        }
                                    }
                                }
                                break;

                            default:
                                $vlans[$f['vid']][$Param] = $ParamValue;
                                break;
                        }
                    }
                    else{
                        if($ItemWordsCount<3) $vlans[$f['vid']][$item] = '';
                    }
                }
            }
        }

//        print_r($vlans);

        $interfaces = array();


        for($p=1;$p<=28;$p++){
            $interfaces[$p] = array();
            foreach ($vlans as $vid=>$vlan_data){
                //switchport mode
                if(isset($vlans[$vid]['fixed'])) {
                    if (in_array($p, $vlans[$vid]['fixed'])) {
                        if (in_array($p, $vlans[$vid]['untagged'])) {
                            if (isset($zyxterface[$p]['vid'])) {
                                if ($zyxterface[$p]['vid'] == $vid) {
                                    $interfaces[$p]['vlan_untagged'][] = $vid;
                                }
                            }
                        } else {
                            $interfaces[$p]['vlan_tagged'][] = $vid;
                        }
                    }
                }

                if(isset($zyxterface[$p]['limit'])){
                    $interfaces[$p]['limit'] = $zyxterface[$p]['limit'];
                }

                //Shutdown / no shutdown
                if((isset($zyxterface[$p]['inactive']))||($p>$lastport)){
                    $interfaces[$p]['status']="shutdown";
                }else{
                    $interfaces[$p]['status']="no shutdown";
                }
            }
        }

        if ($lastport<25){
            $interfaces[25]=$interfaces[$lastport];
//            unset($interfaces[$lastport]);
            $interfaces[$lastport]['status'] = 'shutdown';
            unset($interfaces[$lastport]['vlan_tagged']);
            unset($interfaces[$lastport]['vlan_untagged']);
            unset($interfaces[$lastport]['vid']);
        }

        $EltexConfText = "vlan database \n";
        $EltexConfText .= "vlan ".implode(",",$vlansText)."\n";
        $EltexConfText .= "!\n";
        foreach ($interfaces as $port=>$portData){
            $EltexConfText .="interface ";

            if($port>=25){
                if(!$_POST['type']){
                    $EltexConfText .= $interface_type[1]."1/0/".($port-24)."\n";
                }else{
                    $EltexConfText .= $interface_type[1]."1/0/".($port)."\n";
                }
            }else{
                $EltexConfText .= $interface_type[$_POST['type']]."1/0/".$port."\n";
            }

            if(isset($interfaces[$port]['vlan_tagged'])){
                $EltexConfText .= " switchport mode trunk\n";
                $EltexConfText .= " no service-acl input\n";
                $EltexConfText .= " switchport trunk allowed vlan add ".implode(",",$interfaces[$port]['vlan_tagged'])."\n";
                if(isset($interfaces[$port]['vlan_untagged'])){
                    $EltexConfText .= " switchport trunk native vlan ".$interfaces[$port]['vlan_untagged'][0]."\n";
                }
            }else{
                if(isset($interfaces[$port]['vlan_untagged'])){
                    $EltexConfText .= " switchport mode access\n";
                    $EltexConfText .= " switchport access vlan ".$interfaces[$port]['vlan_untagged'][0]."\n";
                }
            }
            if(isset($interfaces[$port]['limit'])){
                $EltexConfText .= " traffic-shape ".$interfaces[$port]['limit']."\n";
                $EltexConfText .= " rate-limit ".$interfaces[$port]['limit']."\n";
            }
            $EltexConfText .= " ".$interfaces[$port]['status']."\n";
            $EltexConfText .= "exit\n";
            $EltexConfText .= "!\n";
        }

//        print_r($zyxterface);
        print_r($EltexConfText);
//        print_r($interfaces);
//        echo $lastport;
    }

    public function actionIndex(){

//        print_r(Yii::$app->request->post);
//        $model = new ConverterForm();
        return $this->render('index');


    }
}