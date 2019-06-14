<?php
namespace frontend\modules\ipmon\controllers;
use common\components\SiteHelper;
use Yii;
use frontend\components\FrontendComponent;
//use yii\bootstrap\ActiveField;
use yii\bootstrap\ActiveForm;

class AjaxController extends FrontendComponent
{
    private function NetsUnit($id){
        if(!isset($id)) $id=1;
        return json_decode(file_get_contents('http://1.1.70.53:8080/api_v0/nets_unit/' . SiteHelper::TagStripper($id)));
    }
    private function NetsUnitPort($id){
        return json_decode(file_get_contents('http://1.1.70.53:8080/api_v0/nets_unit_port/' . SiteHelper::TagStripper($id)));
    }
    private function NetsLine($id){
        return json_decode(file_get_contents('http://1.1.70.53:8080/api_v0/nets_line/' . SiteHelper::TagStripper($id)));
    }
    private function IPv4($id){
        return json_decode(file_get_contents('http://1.1.70.53:8080/api_v0/nets_ipv4/' . SiteHelper::TagStripper($id)));
    }
    private function Vlans($id){
        return json_decode(file_get_contents('http://1.1.70.53:8080/api_v0/nets_vlan/' . SiteHelper::TagStripper($id)));
    }
    private function Vendor_model($id){
        return json_decode(file_get_contents('http://1.1.70.53:8080/api_v0/vendor_model/' . SiteHelper::TagStripper($id)));
    }
    private function Vendor($id){
        return json_decode(file_get_contents('http://1.1.70.53:8080/api_v0/vendor/' . SiteHelper::TagStripper($id)));
    }
    private function AddressFias($id){
        return json_decode(file_get_contents('http://1.1.70.53:8080/api_v0/address_fias/' . SiteHelper::TagStripper($id)));
    }
    public function actionIndex($id=null){
        print_r($this->NetsUnit($id));
    }

    public function actionRoot($id=1){
//        $id = Yii::$app->request->get()['id'];
//        if(!isset($id)) $id = 1;
//        if (Yii::$app->request->isAjax) {
        if (1) {
            if(!is_numeric($id)) $id=1;
            $portListRoot = array();
            $root_unit = ($this->NetsUnit($id));
            for ($i = 0; $i < count($root_unit->nets_ports); $i++) {
                if ($root_unit->nets_ports[$i]->type_ports[0] != 5) {
                    $portListRoot[] = $root_unit->nets_ports[$i]->id;
                }
            }
            $tree[0]['count'] = count($portListRoot);
            unset ($portListRoot);

            if(isset($root_unit->vendor_model)){
                $root_model = ($this->Vendor_model($root_unit->vendor_model));
                $root_vendor = ($this->Vendor($root_model->vendor));
                $tree[0]['vendor_model'] = $root_model->name;
                $tree[0]['vendor'] = $root_vendor->name;
            }else{
                $tree[0]['vendor_model'] = null;
                $tree[0]['vendor'] = null;
            }

            //Заполняем инфу о корневом юните
            if(isset($root_unit->address)){
                $address_fias = ($this->AddressFias($root_unit->address));
                $tree[0]['fias_text'] = $address_fias->fias_text;
                $tree[0]['fias_uuid'] = $address_fias->fias_uuid;
                $tree[0]['longitude'] = $address_fias->lon;
                $tree[0]['latitude'] = $address_fias->lat;
            }else{
                $tree[0]['fias_text'] = null;
                $tree[0]['fias_uuid'] = null;
                $tree[0]['longitude'] = null;
                $tree[0]['latitude'] = null;
            }

            $tree[0]['key'] = $root_unit->id;
            $tree[0]['name'] = $root_unit->name;
            $tree[0]['comment'] = $root_unit->comment;
            $tree[0]['id'] = $root_unit->id;
            $tree[0]['version'] = $root_unit->version;
            $tree[0]['last_author'] = $root_unit->last_author;
            $tree[0]['modifyed'] = $root_unit->modifyed;
            $tree[0]['folder'] = true;
            $tree[0]['title'] = "<span class='badge' style='background: red'>".$tree[0]['count']."</span> ".$root_unit->name;
            //Информация для fancytree
            if (count($root_unit->nets_ports) > 1) {
                $tree[0]['lazy'] = true;  //Дерево,ветка
            }else{
                $tree[0]['leaf'] = true;   //Лист на ветке
            }

            for ($i = 0; $i < count($root_unit->nets_ports); $i++) {
//                SiteHelper::debug($root_unit->nets_ports);
                if ($root_unit->nets_ports[$i]->type_ports[0] == 5) {
                    $tmp_unit_port = $this->NetsUnitPort($root_unit->nets_ports[$i]->id);
                    if(isset($tmp_unit_port->nets_IPv4s[0])) {
                        $tree[0]['ip'] = $this->IPv4($tmp_unit_port->nets_IPv4s[0])->address; //IP адрес корневого юнита
                    }
//                $tree_data['text'] .='<b>'.$ip->address."</b> (".$unit_comment[0].")";
                }
            }

//            echo implode(",", $portListRoot);
            if (!Yii::$app->request->isAjax) {
                SiteHelper::debug($tree);
                SiteHelper::debug($root_unit);

            }else{
                echo json_encode($tree);
            }
        }
    }

    public function actionChild($key=1)
    {
        $key = Yii::$app->request->get()['key'];
        if (!is_numeric($key)) $key = 1;
        $RootUnit = $this->NetsUnit($key);

        $debug = 1;
        $tmp0 = array();
        $tmp1 = array();
        $childs = array();
        $jstree = array();
        //          ПОРТЫ
        for ($i = 0; $i < count($RootUnit->nets_ports); $i++) {
            if ($RootUnit->nets_ports[$i]->type_ports[0] != 5) {
                $childs[$RootUnit->nets_ports[$i]->id]['RootDownPort'] = $RootUnit->nets_ports[$i]->id;
                $childs[$RootUnit->nets_ports[$i]->id]['RootDownPortName'] = $RootUnit->nets_ports[$i]->name;
                $tmp0[] = $RootUnit->nets_ports[$i]->id;  //ID портов
                $tmp1[] = $RootUnit->nets_ports[$i]->nets_line; //ID линий с портов юнита
            }
        }
//        SiteHelper::debug($RootUnit);
        if(count($tmp0)>0){
            //        ЛИНИИ
            $RootLines = $this->NetsLine('?id__in=' . implode(",", $tmp1))->results;

            $tmp1 = array();
            for ($i = 0; $i < count($RootLines); $i++) {
                for ($y = 0; $y < count($RootLines[$i]->nets_ports); $y++) {
                    if ($RootLines[$i]->nets_ports[$y]->nets_unit != $key) {
                        $tmp1 = $RootLines[$i]->nets_ports[$y]->id;
                        $tmp0[] = $RootLines[$i]->nets_ports[$y]->id;
                    } else {
                        $tmp_id = $RootLines[$i]->nets_ports[$y]->id;
                    }
                }
                $childs[$tmp_id]['ChildUpPort'] = $tmp1;
                $childs[$tmp_id]['line'] = $RootLines[$i]->id;
//            $tmp_id = explode('-', trim($RootLines[$i]->name));
//            $childs[trim($tmp_id[0])]['port_b'] = trim($tmp_id[1]);
//            $tmp[] =  trim($tmp_id[1]);
            }
            SiteHelper::debug($RootLines);


            $ChildUpPorts = $this->NetsUnitPort('?id__in=' .  implode(",", $tmp0));

            $tmp0 = array();
            $tmp1 = array();
            $tmp2 = array();

            for ($i = 0; $i < $ChildUpPorts->count; $i++) {
                foreach ($childs as $key => $value) {
                    if ($ChildUpPorts->results[$i]->id == $childs[$key]['ChildUpPort']) {
                        $childs[$key]['ip_id'] = current($ChildUpPorts->results[$i]->nets_IPv4s);
                        $childs[$key]['vl_id'] = current($ChildUpPorts->results[$i]->nets_VLANs);
                        $childs[$key]['key'] = $ChildUpPorts->results[$i]->nets_unit;
                    }
                }

//                if (isset($ChildUpPorts->results[$i]->nets_IPv4s[0])) {
                    if(!in_array(current($ChildUpPorts->results[$i]->nets_IPv4s),$tmp1))
                    $tmp1[] = current($ChildUpPorts->results[$i]->nets_IPv4s); //IPшники
//                }
//                if (isset($ChildUpPorts->results[$i]->nets_VLANs[0])) {
                    if(!in_array(current($ChildUpPorts->results[$i]->nets_VLANs),$tmp2))
                    $tmp2[] = current($ChildUpPorts->results[$i]->nets_VLANs); //Vlan
//                }
                $tmp0[] = $ChildUpPorts->results[$i]->nets_unit;  //Юниты
            }

//            $ChildIdList = ;//список id-шников дочерних юнитов

            if ((count($tmp1)>0)&&(!in_array("",$tmp1))) {
                $ChildIPAddress = $this->IPv4('?id__in=' . implode(",", $tmp1));


                for ($i = 0; $i < $ChildIPAddress->count; $i++) {
                    foreach ($childs as $j => $value) {
                        if ($ChildIPAddress->results[$i]->id == $childs[$j]['ip_id']) {
                            $childs[$j]['ip'] = $ChildIPAddress->results[$i]->address;
                        }
                    }
                }
            }
            die();
//            SiteHelper::debug($tmp2);
            if (count($tmp2)>0) {
                $ChildVlan = $this->Vlans('?id__in=' . implode(",", $tmp2));
                for ($i = 0; $i < $ChildVlan->count; $i++) {
                    foreach ($childs as $j => $value) {
                        if ($ChildVlan->results[$i]->id == $childs[$j]['vl_id']) {
                            $childs[$j]['vlan'] = $ChildVlan->results[$i]->name;
                        }
                    }
                }
            }
            unset($tmp2);
            //Вытаскиваем ip адреса дочерних юнитов

/// Дочерние юниты

            $ChildUnits = ($this->NetsUnit('?id__in=' . implode(",", $tmp0)));
            $tmp0 = array();
            $tmp1 = array();

            for ($i = 0; $i < $ChildUnits->count; $i++) {
                $isroot = false;
                if (count($ChildUnits->results[$i]->nets_ports) > 1) {
                    $isroot = true;
                };

                if (isset($ChildUnits->results[$i]->vendor_model)) {
                    $tmp[] = $ChildUnits->results[$i]->vendor_model;
                }

                foreach ($childs as $key => $value) {
                    if ($ChildUnits->results[$i]->id == $childs[$key]['key']) {
                        $childs[$key]['comment'] = $ChildUnits->results[$i]->comment;
                        $childs[$key]['name'] = $ChildUnits->results[$i]->name;
                        $childs[$key]['count'] = '';
                        $childs[$key]['id'] = $ChildUnits->results[$i]->id;
                        $childs[$key]['version'] = $ChildUnits->results[$i]->version;
                        $childs[$key]['last_author'] = $ChildUnits->results[$i]->last_author;
                        $childs[$key]['modifyed'] = $ChildUnits->results[$i]->modifyed;
                        if(isset($ChildUnits->results[$i]->vendor_model)){
                            $childs[$key]['vendor_model_id'] = $ChildUnits->results[$i]->vendor_model;
                        }
                        $tmp0[] = $ChildUnits->results[$i]->vendor_model;

                        if (count($ChildUnits->results[$i]->nets_ports) > 1) {
                            $childs[$key]['count'] = count($ChildUnits->results[$i]->nets_ports) - 1;
                        }

                        if ($isroot) {
                            $childs[$key]['type'] = 'root';
//                        $childs[$key]['children'] = array();
                        }
                    }

                }
            }

//        echo $ChildIpIdList;
            $ChildVendorModel = $this->Vendor_model('?id__in=' . implode(",", $tmp0));
            $tmp0 = array();
            for ($i = 0; $i < $ChildVendorModel->count; $i++) {
                foreach ($childs as $j => $value) {
                    if ((isset($childs[$j]['vendor_model_id']))&&($ChildVendorModel->results[$i]->id == $childs[$j]['vendor_model_id'])) {
                        $childs[$j]['vendor'] = $ChildVendorModel->results[$i]->vendor;
                        $childs[$j]['vendor_model_name'] = $ChildVendorModel->results[$i]->name;
                        $tmp0[] = $ChildVendorModel->results[$i]->vendor;
                    }
                }
            }

            $ChildModel = $this->Vendor('?id__in=' . implode(",", $tmp0));
            $tmp0 = array();
            for ($i = 0; $i < $ChildModel->count; $i++) {
                foreach ($childs as $j => $value) {
                    if (isset($childs[$j]['vendor'])) {
                        if ($ChildModel->results[$i]->id == $childs[$j]['vendor']) {
                            $childs[$j]['vendor_name'] = $ChildModel->results[$i]->name;
                        }
                    }
                }
            }

            usort($childs, function ($a, $b) {
                return $a['RootDownPort'] <=> $b['RootDownPort'];
            });

            foreach ($childs as $i => $value) {
                $jstree[$i]['key'] = $childs[$i]['key'];
                if (isset($childs[$i]['ip'])) {
                    $jstree[$i]['ip'] = $childs[$i]['ip'];
                } else {
                    $jstree[$i]['ip'] = null;
                }
                $jstree[$i]['title'] = "<span class='badge' style='background: red'>" . $childs[$i]['count'] . "</span>" . " <font color='blue'>" . $jstree[$i]['ip'] . "</font>" . explode(" ", $childs[$i]['comment'])[2];
                if (isset($childs[$i]['type'])) {
                    $jstree[$i]['lazy'] = true;
                    $jstree[$i]['folder'] = true;

//                $jstree[$i]['icon'] = 'fa  fa-hdd-o';
                }
                $jstree[$i]['comment'] = $childs[$i]['comment'];
                $jstree[$i]['name'] = $childs[$i]['name'];
                $jstree[$i]['count'] = $childs[$i]['count'];
                $jstree[$i]['id'] = $childs[$i]['id'];
                $jstree[$i]['version'] = $childs[$i]['version'];
                $jstree[$i]['last_author'] = $childs[$i]['last_author'];
                $jstree[$i]['modifyed'] = $childs[$i]['modifyed'];
                if(isset($childs[$i]['vendor_model'])){
                    $jstree[$i]['vendor_model'] = $childs[$i]['vendor_model'];
                }

            }

            unset($RootUnit);
            unset($RootLines);
            unset($ChildUnits);
            unset($ChildModel);
            unset($ChildVendorModel);

        }
        $tmp0 = array();
        SiteHelper::debug($childs);
    }
    /*
    public function actionRoot($id=null){
//        $id = Yii::$app->request->get();

//           print_r($id);
           if (Yii::$app->request->isAjax) {
//        if (1) {
            if(!is_numeric($id)) $id=1;
            if(!isset($id)) $id=1;
            $root_unit = ($this->NetsUnit($id));
            $tmp = array();
            $RootPortIdList = '';  //список id-шников портов на корневом юните, по ним будем вытаскивать инфу по портам
            for ($i = 0; $i < count($root_unit->nets_ports); $i++) {
                if ($root_unit->nets_ports[$i]->type_ports[0] != 5) {
                    $tmp[] = $root_unit->nets_ports[$i]->id;
                }
            }

            if (count($tmp)>0){
                $RootPortIdList = implode(",",$tmp);
                $RootPorts = $this->NetsUnitPort('?id__in='.$RootPortIdList);
                $tree[0]['count'] = $RootPorts->count;
            }else{
                $tree[0]['count'] = '';
            }


//        error_log($root_unit_json);
            if(isset($root_unit->vendor_model)){
                $root_model = ($this->Vendor_model($root_unit->vendor_model));
                $root_vendor = ($this->Vendor($root_model->vendor));
                $tree[0]['vendor_model'] = $root_model->name;
                $tree[0]['vendor'] = $root_vendor->name;
            }else{
                $tree[0]['vendor_model'] = null;
                $tree[0]['vendor'] = null;
            }

            if(isset($root_unit->address)){
                $address_fias = ($this->AddressFias($root_unit->address));
                $tree[0]['fias_text'] = $address_fias->fias_text;
                $tree[0]['fias_uuid'] = $address_fias->fias_uuid;
                $tree[0]['lon'] = $address_fias->lon;
                $tree[0]['lat'] = $address_fias->lat;
            }else{
                $tree[0]['fias_text'] = null;
                $tree[0]['fias_uuid'] = null;
                $tree[0]['lon'] = null;
                $tree[0]['lat'] = null;
            }

            $tree[0]['key'] = $root_unit->id;
            $tree[0]['name'] = $root_unit->name;
            $tree[0]['comment'] = $root_unit->comment;
            $tree[0]['id'] = $root_unit->id;
            $tree[0]['version'] = $root_unit->version;
            $tree[0]['last_author'] = $root_unit->last_author;
            $tree[0]['modifyed'] = $root_unit->modifyed;
            $tree[0]['folder'] = true;



            if (count($root_unit->nets_ports) > 1) {
                $tree[0]['lazy'] = true;
            }else{
                $tree[0]['leaf'] = true;
            }
            for ($i = 0; $i < count($root_unit->nets_ports); $i++) {
                if ($root_unit->nets_ports[$i]->type_ports[0] == 5) {
                    $tmp_unit_port = $this->NetsUnitPort($root_unit->nets_ports[$i]->id);
                    if(isset($tmp_unit_port->nets_IPv4s[0])) {
                        $tree[0]['ip'] = $this->IPv4($tmp_unit_port->nets_IPv4s[0])->address; //IP адрес корневого юнита
                    }
//                $tree_data['text'] .='<b>'.$ip->address."</b> (".$unit_comment[0].")";
                }
            }
            $tree[0]['title'] = "<span class='badge' style='background: red'>".$tree[0]['count']."</span> ".$root_unit->name;


            if (!Yii::$app->request->isAjax) {
               SiteHelper::debug($tree);
                SiteHelper::debug($root_unit);

            }else{
                echo json_encode($tree);
            }
        }
    }
    */
    /*
    public function actionChild()
    {
//        if (Yii::$app->request->isAjax) {
        if (1) {

            $key = Yii::$app->request->get()['key'];
            if (!isset($key)) $key = 1;
//            if (!is_numeric($key)) $key = 1;
//        echo "the key is ".SiteHelper::TagStripper($key);
            $childs = array();
//
            $tmp = array();
            $root_unit = $this->NetsUnit($key);
            //          ПОРТЫ
            for ($i = 0; $i < count($root_unit->nets_ports); $i++) {
                if ($root_unit->nets_ports[$i]->type_ports[0] != 5) {
                    $childs[$root_unit->nets_ports[$i]->id]['name'] = $root_unit->nets_ports[$i]->name;
                    $childs[$root_unit->nets_ports[$i]->id]['port_id'] = $root_unit->nets_ports[$i]->id;
                    $tmp[] = $root_unit->nets_ports[$i]->id;
                }
            }


            $RootPortIdList = implode(",", $tmp);//список id-шников портов на корневом юните, по ним будем вытаскивать инфу по портам
            $tmp = array();

            $RootPorts = $this->NetsUnitPort('?id__in=' . $RootPortIdList);
            unset($RootPortIdList);
            unset($root_unit);
            for ($i = 0; $i < count($RootPorts->results); $i++) {
                $childs[$RootPorts->results[$i]->id]['port_a'] = $RootPorts->results[$i]->id;
                $childs[$RootPorts->results[$i]->id]['port_a_name'] = $RootPorts->results[$i]->name;
                if ($RootPorts->results[$i]->nets_line != null) {
                    $childs[$RootPorts->results[$i]->id]['line'] = $RootPorts->results[$i]->nets_line;

                    $tmp[] = $RootPorts->results[$i]->nets_line;
                }

            }
            $RootPortLineIdList = implode(",", $tmp); //список id-шников линий между портами корневого и дочернего юнита
            $tmp = array();
            //        ЛИНИИ
            $RootLines = $this->NetsLine('?id__in=' . $RootPortLineIdList)->results;
            unset($RootPortLineIdList);
            unset($RootPorts);

            for ($i = 0; $i < count($RootLines); $i++) {
                for ($y = 0; $y < count($RootLines[$i]->nets_ports); $y++) {
                    if ($RootLines[$i]->nets_ports[$y]->nets_unit != $key) {
                        $tmp[] = $RootLines[$i]->nets_ports[$y]->id;
                        $tmp1 = $RootLines[$i]->nets_ports[$y]->id;
                    } else {
                        $tmp_id = $RootLines[$i]->nets_ports[$y]->id;
                    }
                }
                $childs[$tmp_id]['port_b'] = $tmp1;
//            $tmp_id = explode('-', trim($RootLines[$i]->name));
//            $childs[trim($tmp_id[0])]['port_b'] = trim($tmp_id[1]);
//            $tmp[] =  trim($tmp_id[1]);
            }

//        SiteHelper::debug($tmp);
            $ChildPortIdList = implode(",", $tmp);
//        echo $ChildPortIdList;
//        SiteHelper::debug($RootLines);

            $tmp = array();
//        echo $ChildPortIdList;
            $ChildUpPorts = $this->NetsUnitPort('?id__in=' . $ChildPortIdList);
            unset($ChildPortIdList);
//        usort($childs, function ($a, $b) {
//            return $a['port_a_name'] <=> $b['port_a_name'];
//        });

//        SiteHelper::debug($ChildUpPorts);
            $tmp1 = array();
//       SiteHelper::debug($childs);
            for ($i = 0; $i < $ChildUpPorts->count; $i++) {
                foreach ($childs as $key => $value) {
                    if ($ChildUpPorts->results[$i]->id == $childs[$key]['port_b']) {
                        $childs[$key]['port_b_id'] = $ChildUpPorts->results[$i]->id;
                        $childs[$key]['ip_id'] = current($ChildUpPorts->results[$i]->nets_IPv4s);
                        $childs[$key]['key'] = $ChildUpPorts->results[$i]->nets_unit;
                    }
                }
                if (isset($ChildUpPorts->results[$i]->nets_IPv4s[0])) {
                    $tmp1[] = current($ChildUpPorts->results[$i]->nets_IPv4s);
                }
                $tmp[] = $ChildUpPorts->results[$i]->nets_unit;
            }

//        SiteHelper::debug($ChildUpPorts);

            $ChildIpIdList = implode(",", $tmp1);//список id-шников ip адресов дочерних юнитов
            $ChildIdList = implode(",", $tmp);//список id-шников дочерних юнитов

            $tmp = array();
            $tmp1 = array();


/// Дочерние юниты

            $ChildUnits = ($this->NetsUnit('?id__in=' . $ChildIdList));
            for ($i = 0; $i < $ChildUnits->count; $i++) {
                $isroot = false;
                if (count($ChildUnits->results[$i]->nets_ports) > 1) {
                    $isroot = true;
                };

                if (isset($ChildUnits->results[$i]->vendor_model)) {
                    $tmp[] = $ChildUnits->results[$i]->vendor_model;
                }

                foreach ($childs as $key => $value) {
                    if ($ChildUnits->results[$i]->id == $childs[$key]['key']) {
                        $childs[$key]['comment'] = $ChildUnits->results[$i]->comment;
                        $childs[$key]['name'] = $ChildUnits->results[$i]->name;
                        $childs[$key]['count'] = '';
                        $childs[$key]['id'] = $ChildUnits->results[$i]->id;
                        $childs[$key]['version'] = $ChildUnits->results[$i]->version;
                        $childs[$key]['last_author'] = $ChildUnits->results[$i]->last_author;
                        $childs[$key]['modifyed'] = $ChildUnits->results[$i]->modifyed;
                        $childs[$key]['vendor_model'] = $ChildUnits->results[$i]->vendor_model;
//                    $tree[0]['vendor'] = $root_vendor->name;
//                    $tree[0]['fias_text'] = $address_fias->fias_text;
//                    $tree[0]['fias_uuid'] = $address_fias->fias_uuid;
//                    $tree[0]['lon'] = $address_fias->lon;
//                    $tree[0]['lat'] = $address_fias->lat;
//                    $tree[0]['count'] = $RootPorts->count;
                        if (count($ChildUnits->results[$i]->nets_ports) > 1) {
                            for ($k = 0; $k < count($ChildUnits->results[$i]->nets_ports); $k++) {
                                if ($ChildUnits->results[$i]->nets_ports[$k]->type_ports[0] == 6) $childs[$key]['count'] = $childs[$key]['count'] + 1;
                            }
                        } else {
//                        $childs[$key]['count']=1;
                        }
                        if ($isroot) {
                            $childs[$key]['type'] = 'root';
//                        $childs[$key]['children'] = array();
                        }
                    }

                }
            }

//        echo $ChildIpIdList;
            if ($ChildIpIdList != "") {
                $ChildIPAddresses = $this->IPv4('?id__in=' . $ChildIpIdList);
                for ($i = 0; $i < $ChildIPAddresses->count; $i++) {
                    foreach ($childs as $j => $value) {
                        if ($ChildIPAddresses->results[$i]->id == $childs[$j]['ip_id']) {
                            $childs[$j]['ip'] = $ChildIPAddresses->results[$i]->address;
                        }
                    }
                }
            }
            //Вытаскиваем ip адреса дочерних юнитов


            $ChildVendorModelIdList = implode(",", $tmp);
            $tmp = array();

            $ChildVendorModel = $this->Vendor_model('?id__in=' . $ChildVendorModelIdList);
            for ($i = 0; $i < $ChildVendorModel->count; $i++) {
                foreach ($childs as $j => $value) {
                    if ($ChildVendorModel->results[$i]->id == $childs[$j]['vendor_model']) {
                        $childs[$j]['vendor'] = $ChildVendorModel->results[$i]->vendor;
                        $childs[$j]['vendor_model_name'] = $ChildVendorModel->results[$i]->name;
                        $tmp[] = $ChildVendorModel->results[$i]->vendor;
                    }
                }
            }
            $ChildVendorIdList = implode(",", $tmp);
            $tmp = array();

            $ChildModel = $this->Vendor('?id__in=' . $ChildVendorIdList);
            for ($i = 0; $i < $ChildModel->count; $i++) {
                foreach ($childs as $j => $value) {
                    if (isset($childs[$j]['vendor'])) {
                        if ($ChildModel->results[$i]->id == $childs[$j]['vendor']) {
                            $childs[$j]['vendor_name'] = $ChildModel->results[$i]->name;
                        }
                    }
                }
            }


            usort($childs, function ($a, $b) {
                return $a['port_a_name'] <=> $b['port_a_name'];
            });

            foreach ($childs as $i => $value) {
                $jstree[$i]['key'] = $childs[$i]['key'];
                if (isset($childs[$i]['ip'])) {
                    $jstree[$i]['ip'] = $childs[$i]['ip'];
                } else {
                    $jstree[$i]['ip'] = null;
                }
                $jstree[$i]['title'] = "<span class='badge' style='background: red'>" . $childs[$i]['count'] . "</span>" . " <font color='blue'>" . $jstree[$i]['ip'] . "</font>" . explode(" ", $childs[$i]['comment'])[2];
                if (isset($childs[$i]['type'])) {
                    $jstree[$i]['lazy'] = true;
                    $jstree[$i]['folder'] = true;

//                $jstree[$i]['icon'] = 'fa  fa-hdd-o';
                }
                $jstree[$i]['comment'] = $childs[$i]['comment'];
                $jstree[$i]['name'] = $childs[$i]['name'];
                $jstree[$i]['count'] = $childs[$i]['count'];
                $jstree[$i]['id'] = $childs[$i]['id'];
                $jstree[$i]['version'] = $childs[$i]['version'];
                $jstree[$i]['last_author'] = $childs[$i]['last_author'];
                $jstree[$i]['modifyed'] = $childs[$i]['modifyed'];
                $jstree[$i]['vendor_model'] = $childs[$i]['vendor_model'];


            }


//        echo $ChildVendorModelIdList;
//        SiteHelper::debug($tmpVendorModel)

            echo json_encode($jstree);
//        SiteHelper::debug($jstree);

        }else{
            die("403 Forbidden");
        }
    }*/

}