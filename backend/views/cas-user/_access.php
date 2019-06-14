<?php

use yii\helpers\Html;
use common\models\Access;
?>
<div class="cas-user-access">
    <? foreach($accessSettings['modules'] as $module_id => $module): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= $module['name'] ?></h3>
            </div>
            <div class="panel-body">
                <? foreach($module['settings'] as $module_setting_id => $module_setting): ?>
                    <div class="form-group">
                        <?= Html::label($module_setting['name'], 'module_setting_' . $module_setting_id, [ 'class' => 'control-label' ]) ?>
                        <?= Html::dropDownList(
                            'module_setting_' . $module_setting_id, 
                            $module_setting['access_value'], 
                            Access::getValues(), 
                            [
                                'class' => 'form-control', 
                                'id' => 'module_setting_' . $module_setting_id, 
                                'data-key' => $module_setting["key"], 
                                'data-access' => $module_setting['access_id'],
                            ]) 
                        ?>
                        <div class="hint-block">
                            <div class="changes-saved">Изменения сохранены</div>
                            <?= $module_setting['descr'] ?>
                            <?php
                                $html = "<div class='about-setting'>";
                                switch($module_setting['type']){
                                    case 0:
                                        $html .= "Настройка не пересекается с пользователем или его отделами.";
                                        break;
                                    case 1:
                                        $html .= "Настройка наследуется от отдела ";
                                        $html .= Html::a($accessSettings['departments'][$module_setting['department']]['name'], ['/departments/view', 'id' => $module_setting['department']]);
                                        break;
                                    case 2:
                                        $html .= "Индивидуальная настройка пользователя";
                                        break;
                                    default:
                                }
                                echo $html . "</div>";
                            ?>
                        </div>
                    </div>
                <? endforeach ?>
            </div>
        </div>
    <? endforeach ?>
</div>