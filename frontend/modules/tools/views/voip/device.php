<?
use common\components\SiteHelper;
use yii\widgets\ActiveForm;

//SiteHelper::debug($data);
//SiteHelper::debug($sip_accounts);
//die();

$contexts = [
   'mobileMG'=>'mobileMG',
   'allout'=>'allout',
   'onlycity'=>'onlycity',
   'emergency'=>'emergency'
];


$form = ActiveForm::begin([
    'id' => 'device_form',
    'method' => 'post',
//    'enableClientValidation' => false,
//    'enableAjaxValidation' => true,
//    'validationUrl' => '/sessions/validate',
//    'options' => ['data-pjax' => true]
]);?>
    <input hidden name="GateData[mac]" value="<?=$mac?>">
    <input hidden name="GateData[ip]" value="<?=$ip?>">
    <table class="table table-condensed table-hover">
        <tr>
            <th style="width: 30px;">Порт</th>
            <th style="width: 30px;"><i class="fa fa-fw fa-power-off"></i></th>
            <th>Номер</th>
            <th style="width: 120px!important;">Ограничения</th>
            <? if($permissions['edit']>1){
                ?><th style="width: 30px!important;"><?
                if(!empty ($data)){
                    ?><i class="fa fa-fw fa-trash"></i><?
                }?></th><?
            }?>
            <?if($permissions['passwords']>0){?><th>Пароль</th><?}?>
        </tr>
            <?php
            if (empty ($data))
            {
                for ($i = 1; $i <= $n; $i++)
                {
                    ?><tr class="active">
                    <td><?=$i?></td>
                    <td><? if($permissions['edit']>1){
                        ?><input hidden name="GateData[<?=$i?>][changed]" id="changed<?=$i?>" type="" value="0">
                          <input hidden name="GateData[<?=$i?>][device_id]" id="dev_id<?=$i?>" type="" value="<?=$device_id?>">
                          <input hidden name="GateData[<?=$i?>][last_state]" id="last_state<?=$i?>"  value="0">
                          <input data-port-id="<?=$i?>" class="" id="state<?=$i?>" onchange="Changed(<?=$i?>);TrigContext(<?=$i?>);" type="checkbox" name="GateData[<?=$i?>][state]" value="<?=1?>"><? }
                    ?></td>
                    <td><? if($permissions['edit']>1){
                        ?><input onchange="Changed(<?=$i?>)" data-port-id="<?=$i?>" class='form-control ' type="text" name="GateData[<?=$i?>][ntel]" value="00<?=$i?>"><? }
                    ?></td>
                    <td><? if($permissions['edit']>1){ ?>
                        <select disabled onchange="Changed(<?=$i?>)" data-port-id="<?=$i?>" class="form-control " id="context<?=$i?>" name="GateData[<?=$i?>][context]"><?
                            foreach ($contexts as $context){
                                ?><option class="<?=$context?>"><?=$context?></option><?
                            }
                            ?>
                        </select><? } ?>
                    </td>
                    <?
                    if($permissions['edit']>1) {
                        switch ($permissions['passwords']) {
                            case 0:
                            case 1:
                                ?><td><input name="GateData[<?= $i ?>][pass]" id="pass" hidden="hidden" value=""></td><?
                                break;
                            case 2:
                                ?>
                                <td>
                                <div class="input-group">
                                    <input onchange="Changed(<?=$i?>)" data-port-id="<?=$i?>" name="GateData[<?= $i ?>][pass]" class="form-control" id="pass<?= $i ?>" value="">
                                    <span class="input-group-btn">
                                            <a onclick="setPasswd('pass')" class="btn btn-info">gen</a>
                                        </span>
                                </div>
                                </td>
                                <?
                                break;
                        }
                    }
                    ?>
                    </tr><?
                }
            }
            else
            {

                for ($i = 1; $i <= count($data); $i++)
                {
                    $style = "success";
                    if ($data[$i]['state']!=1){
                        $style = "active";
                    }
                    ?><tr class='<?=$style?>' id="tr<?=$i?>"><?
                    $r=$i-1;

                    if (count($data)>2)
                    {
                        echo "<td class=".$style.">".$r."</td>";
                    }
                    else
                    {
                        echo "<td class=".$style.">".$i."</td>";
                    }
                    ?>
                    <td class=<?=$style?>>
                        <input hidden name="GateData[<?=$i?>][changed]" id="changed<?=$i?>" value="0">
                        <input hidden name="GateData[<?=$i?>][device_id]" id="dev_id<?=$i?>" type="" value="<?=$device_id?>">
                        <input hidden name="GateData[<?=$i?>][last_state]" id="last_state<?=$i?>"  value="<?php if($data[$i]['state']==1){ echo "1"; }else{ echo '0';}?>">
                    <? if($permissions['edit']>1){
                        ?><input onchange="Changed(<?=$i?>); TrigContext(<?=$i?>)" id="state<?=$i?>" data-port-id="<?=$i?>" type="checkbox" name="GateData[<?=$i?>][state]" value="1" <?php if($data[$i]['state']==1){ echo "checked"; }?> >
                    <? }else{
                        if($data[$i]['state']==1){ echo '<i class="fa fa-check"></i>'; }
                    }?>
                    </td>
                    <td class="<?=$style?>"><? if($permissions['edit']>1){
                        ?><input onchange="Changed(<?=$i?>)" data-port-id="<?=$i?>" class='form-control ' type='text' name='GateData[<?=$i?>][ntel]' value='<?=$data[$i]['atel']?>'><? }else{
                            echo $data[$i]['atel'];
                        }?>
                    </td>
                    <td>
                    <? if($permissions['edit']>1){

                        ?><select id="context<?=$i?>" <?if(!$data[$i]['state']){ echo 'disabled'; }?> name="GateData[<?=$i?>][context]" onchange="Changed(<?=$i?>)" data-port-id="<?=$i?>" class="form-control "><?
                            foreach ($contexts as $context){
                                $selected = '';
                                if(isset($sip_accounts[$data[$i]['atel']]['context'])){
                                    if($sip_accounts[$data[$i]['atel']]['context']==$context)
                                        $selected = 'selected';
                                }
                                ?><option class="<?=$context?>" <?=$selected?>><?=$context?></option><?
                            }
                            ?>
                        </select><?
                    }?>
                    </td>

                    <? if($permissions['edit']>1){
                        ?><td>
                            <input type="checkbox" value="1" id="del<?=$i?>" onchange="Changed(<?=$i?>);Deleting(<?=$i?>)" name="GateData[<?=$i?>][delete]">
                        </td><?
                    }?>

                    <?
                    if($permissions['edit']>1) {
                        switch ($permissions['passwords']) {
                            case 0:
                            case 1:
                                ?><td class="hidden"><input name="GateData[<?= $i ?>][pass]" id="pass" readonly hidden value="<?= $data[$i]['apass'] ?>"></td><?
                                break;
                            case 2:
                                ?>
                                <td>
                                <div class="input-group">
                                    <input onchange="Changed(<?=$i?>)" data-port-id="<?=$i?>" name="GateData[<?= $i ?>][pass]" class="form-control" id="pass<?= $i ?>" value="<?= $data[$i]['apass'] ?>">
                                    <span class="input-group-btn">
                                <a onclick="setPasswd('pass<?= $i ?>')" class="btn btn-info">gen</a>
                            </span>
                                </div>
                                </td>
                                <?
                                break;
                        }
                    }
                   /* echo "<td class=".$style."><input class='form-control' type=\"text\" name=\"pass".$i."\" value=\"".."\"></td>";*/
                    echo "</tr>";
                }
            }

            ?>
    </table>
    <button class="btn btn-warning" type="submit">Сохранить</button>

<?
ActiveForm::end();



//SiteHelper::debug($data);
//SiteHelper::debug($mac);
/*function Avail($domain,$timeout){
    $curlInit = curl_init($domain);
    curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,$timeout);
    curl_setopt($curlInit,CURLOPT_HEADER,true);
    curl_setopt($curlInit,CURLOPT_NOBODY,true);
    curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
//получение ответа
    $response = curl_exec($curlInit);
    curl_close($curlInit);
    $response = $response ? $response = true : $response = false;
    return $response;
}
$dbconn = pg_connect("host=localhost dbname=domains user=postgres password=password") or die('Could not connect: '.pg_last_error());
$q = pg_query("SELECT * FROM domains_list WHERE active = true");
while($data = pg_fetch_assoc($query)){
    pg_update($dbconn,'domains',['availability'=>Avail($data['domain_name'],1)],['domain_name'=>$data['domain_name']]);
}
exit();*/