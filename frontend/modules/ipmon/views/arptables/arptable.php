<?php
//\common\components\SiteHelper::debug($arp);
//die();
$ArpData = current($arp);
$gate = $ArpData['gateway'];
$inc = 0;
$eth = $ArpData['interface'];
?><table class="table  table-hover table-condensed table-responsive"><?
foreach($ArpData['hosts'] as $key => $Data) {
    $inc++;
    $unreachable = false;
    if((explode(".",$key)[3]==0)||(explode(".",$key)[3]==1)||(explode(".",$key)[3]==255)||($key == $gate)){
        $unreachable = true;
    }
    $dynamic = true;
    if($Data['dynamic'] == 'true'){
        $dynamic = false;
    }
    if($unreachable){
        $style = 'active';
    }else {
        if ($dynamic) {
            $static = 'checked';
            $style = 'danger';
            $lock= 'fa-lock';
            $lock_color = '713691';
        } else {
            $lock= 'fa-unlock';
            $lock_color = '0aa684';
            if ($Data['MAC'] != $emptyMAC) {
                $style = 'warning';
            } else {
                $style = 'success';
            }
            $static = '';
        }
    }
    ?><tr class="<?=$style?> ">
    <td style="width: 40px!important;" class="hidden-xs">
        <span class="" ><?=$inc?></span>
    </td>
    <td style="width: 40px!important;"><?
        if(1) {
            if ($unreachable) {
                ?><span class="badge"><i class="fa fa-lock fa-fw"></i></span><?
            } else {
                if (0) {
                    ?><div class="material-switch ">
                    <input <?=$static?> id="<?=$key?>" onchange='ArpStatus($(this).prop("checked"),"<?=$key?>","<?=$ArpData['interface']?>","<?=$inc?>")' type="checkbox"/>
                    <label for="<?=$key?>" class="label-info"></label>
                    </div><?
                } else {
                    ?><input
                    hidden
                    type="checkbox" <?=$static?>
                    id="<?=$ArpData['interface'].$inc?>"
                    onclick='ArpStatus($(this).prop("checked"),"<?=$key?>","<?=$ArpData['interface']?>","<?=$inc?>")'/>
                        <a class="badge" style="background-color: #<?=$lock_color?>" onclick="$('#<?=$ArpData['interface'].$inc?>').click()" >
                    <i id="icon_<?=$inc?>" class="fa <?=$lock?> fa-fw"></i></a><?
                }
            }
        }else{
            if($dynamic) {
                ?><span class=""><i class="fa fa-lock fa-fw"></i></span><?
            }else{
                ?><span class=""><i class="fa fa-unlock fa-fw"></i></span><?
            }
        }
        ?></td>
    <td style="width: 85px!important;"><?=$key?></td>
    <td class="" style="width: 110px!important;"><?=$Data['MAC']?></td><?
    if(1) {
//        if($this->AllowEditComment>0) {
        ?><td><?
        if (($dynamic) && (!$unreachable)) {
            if ($permissions['commenting'] > 1) {
                ?><a href="#" onclick="LoadCurrentComment('<?=$router?>','<?=$key?>','<?=$ArpData['interface']?>')"><i class="fa fa-edit fa-fw"></i></a>
                <?//                                $htmlData['tabsData'] .= '<div ><input class="form-control" value="';
//                                $htmlData['tabsData'] .= '<span class="input-group-btn">';
//                                $htmlData['tabsData'] .= '<button class="btn btn-default" type="button" onclick="ChangeComment(\''.$inc.'\',\''.$Data['MAC'].'\')"><i id="Ic'.$inc.'" class="fa fa-edit fa-fw"></i></button>';
//                                $htmlData['tabsData'] .= '</span>';
            }
            ?><span class='hidden-xs'><?=$Data['comment']?></span><?
//                                $htmlData['tabsData'] .= " <span class='hidden-xs'>" . json_encode($Data) . "</span>";
        }
//                                $htmlData['tabsData'] .= '"></div>';
        ?></td><?
    }
//                        $htmlData['tabsData'] .= '</div>';
    ?></tr><?
}
?></table><?
