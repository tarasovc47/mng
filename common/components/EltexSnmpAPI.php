<?php
namespace common\components;
use Codeception\PHPUnit\Constraint\Page;
use Yii;

class EltexSnmpAPI
{
    private $community = "sysadmin";  //snmp community

    protected function ipNetToMediaIfIndex($ip){  //ipNetToMediaIfIndex   1.3.6.1.2.1.4.22.1.1
        //echo "snmpwalk -v2c -c ".$this->community." ".$ip." 1.3.6.1.2.1.4.22.1.1";
        $a = snmpwalk($ip, "$this->community", "1.3.6.1.2.1.4.22.1.1");
//        $b=[];
//        for ($i=0; $i < count($a); $i++) {
//            $b[]= explode(": ",$a[$i])[1]-99999;
//        }
        return $a;
    }

    protected function ipNetToMediaPhysAddress($ip,$IfIndex){  //ipNetToMediaPhysAddress  1.3.6.1.2.1.4.22.1.2
        try{
            $a = snmpwalk($ip, "$this->community", "1.3.6.1.2.1.4.22.1.2.".$IfIndex);
        }catch  ( yii\base\ErrorException $e ){
//            $a = $e->getMessage();
            $a = 0;
        }

        return $a;
//        echo "snmpwalk -v2c -c ".$this->community." 10.60.0.171 1.3.6.1.2.1.4.22.1.2";
    }
    protected function ipNetToMediaNetAddress($ip,$IfIndex){  //ipNetToMediaNetAddress  1.3.6.1.2.1.4.22.1.3
        try{
            $a = snmpwalk($ip, "$this->community", "1.3.6.1.2.1.4.22.1.3.".$IfIndex);
        }catch  ( yii\base\ErrorException $e ){
//            $a = $e->getMessage();
            $a = 0;
        }

        return $a;
    }

    protected function ipNetToMediaType($ip,$IfIndex){  //ipNetToMediaType  1.3.6.1.2.1.4.22.1.4
        /*
            other (1)
            invalid (2)
            dynamic (3)
            static (4)
         */

        try{
            $a = snmpwalk($ip, "$this->community", "1.3.6.1.2.1.4.22.1.4.".$IfIndex);
        }catch  ( yii\base\ErrorException $e ){
//            $a = $e->getMessage();
            $a = 0;
        }


        return $a;

    }

    public static function SwModel($ip,$community){
        try{
            $tmp = snmpwalk($ip, "$community", "1.3.6.1.2.1.1.2.0");
            $a = str_replace('iso','1',explode(":",$tmp[0])[1]);

        }catch  ( yii\base\ErrorException $e ){
//            $a = $e->getMessage();
            $a = 0;
        }
        return $a;
    }

//    public function ipNetToMediaEntry(){
        /*
         * root@black:~# snmpwalk -v2c -c sysadmin 10.60.0.171 1.3.6.1.2.1.4.22.1.1
           iso.3.6.1.2.1.4.22.1.1.102220.10.60.1.1 = INTEGER: 102220
           iso.3.6.1.2.1.4.22.1.1.102999.10.70.1.2 = INTEGER: 102999
           root@black:~# snmpwalk -v2c -c sysadmin 10.60.0.171 1.3.6.1.2.1.4.22.1.2
           iso.3.6.1.2.1.4.22.1.2.102220.10.60.1.1 = Hex-STRING: E4 8D 8C 0B 40 FC
           iso.3.6.1.2.1.4.22.1.2.102999.10.70.1.2 = Hex-STRING: 10 1F 74 E7 50 74
           root@black:~# snmpwalk -v2c -c sysadmin 10.60.0.171 1.3.6.1.2.1.4.22.1.3
           iso.3.6.1.2.1.4.22.1.3.102220.10.60.1.1 = IpAddress: 10.60.1.1
           iso.3.6.1.2.1.4.22.1.3.102999.10.70.1.2 = IpAddress: 10.70.1.2
           root@black:~# snmpwalk -v2c -c sysadmin 10.60.0.171 1.3.6.1.2.1.4.22.1.4
           iso.3.6.1.2.1.4.22.1.4.102220.10.60.1.1 = INTEGER: 3
           iso.3.6.1.2.1.4.22.1.4.102999.10.70.1.2 = INTEGER: 3

         */
//    }

    public function MacByIP($ip,$vlan,$hostIP){
        $index  = $vlan + 99999;
        $a = $this->ipNetToMediaPhysAddress($ip,$index);
        $b = $this->ipNetToMediaNetAddress($ip,$index);
        $c = $this->ipNetToMediaType($ip,$index);
        $tbl = [];
        if($a!=0){
            for($i=0;$i<count($a);$i++){
//            $aa = explode(": ",$a[$i])[1];
                $tmp = explode(": ",$b[$i])[1];
                if($tmp==long2ip($hostIP)) {
                    $tbl[] = [
                        "mac" => implode(":", explode(" ", trim(explode(": ", $a[$i])[1]))),
                        "ip" => $tmp,
                        "type" => explode(": ", $c[$i])[1],
                    ];
                }
            }
        }
        return $tbl;
    }

    protected function ipNetToMediaEntry($ip,$vlan){
        $index  = $vlan + 99999;
        $a = $this->ipNetToMediaPhysAddress($ip,$index);
        $b = $this->ipNetToMediaNetAddress($ip,$index);
        $c = $this->ipNetToMediaType($ip,$index);
        if($a==0){
            $tbl = [];
        }else{
            for($i=0;$i<count($a);$i++){
//            $aa = explode(": ",$a[$i])[1];
                $tbl[] = [
                    "mac" => implode(":",explode(" ",trim(explode(": ",$a[$i])[1]))),
                    "ip" => explode(": ",$b[$i])[1],
                    "type" => explode(": ",$c[$i])[1],
                ];
            }
        }
        return $tbl;
    }

    public function ArpTable($ip,$vlan,$exc_vlan){
        $indexes = [];   // index=>vlan
        $a = $this->ipNetToMediaIfIndex($ip);
        $b = [];
       if($vlan == null){
            for ($i=0; $i < count($a); $i++) {
                $b[explode(": ",$a[$i])[1]-99999] = explode(": ",$a[$i])[1]-99999;
//            $indexes[$b-99999]=$b;
            }
        }else{
            $b[$vlan]=$vlan;
        }

        $arp = [];
        foreach ($b as $vid) {
            if($vid!=$exc_vlan) {
                $arp[$vid] = $this->ipNetToMediaEntry($ip, $vid);
            }
       }
       return $arp;
    }



    public function MakeStatic($switch,$ip,$mac,$vlan){
        $mac = str_replace([".",":"],"",trim($mac));
        return snmpset($switch,$this->community,"1.3.6.1.2.1.4.22.1.2.10".($vlan-1).".".$ip,'x',$mac).
        snmpset($switch,$this->community,"1.3.6.1.2.1.4.22.1.3.10".($vlan-1).".".$ip,'a',$ip).
        snmpset($switch,$this->community,"1.3.6.1.2.1.4.22.1.4.10".($vlan-1).".".$ip,'i',4);
//        echo("snmpset -v2c -c ".$this->community." ".$switch." 1.3.6.1.2.1.4.22.1.2.10".($vlan-1).".".$ip." x '".$mac."' 1.3.6.1.2.1.4.22.1.3.10".($vlan-1).".".$ip." a ".$ip." 1.3.6.1.2.1.4.22.1.4.10".($vlan-1).".".$ip." i 4");
    }

    public function RemoveStatic($switch,$ip,$mac,$vlan){
        $mac = str_replace([".",":"],"",trim($mac));
        return snmpset($switch,$this->community,"1.3.6.1.2.1.4.22.1.2.10".($vlan-1).".".$ip,'x',$mac).
        snmpset($switch,$this->community,"1.3.6.1.2.1.4.22.1.3.10".($vlan-1).".".$ip,'a',$ip).
        snmpset($switch,$this->community,"1.3.6.1.2.1.4.22.1.4.10".($vlan-1).".".$ip,'i',2);
//        return "snmpset -v2c -c ".$this->community." ".$switch." 1.3.6.1.2.1.4.22.1.2.10".($vlan-1).".".$ip." x '".$mac."' 1.3.6.1.2.1.4.22.1.3.10".($vlan-1).".".$ip." a ".$ip." 1.3.6.1.2.1.4.22.1.4.10".($vlan-1).".".$ip." i 2";
    }
}