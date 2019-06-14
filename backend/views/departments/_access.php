<?php

use yii\helpers\Html;
use common\models\Access;
?>
<div class="departments-access">
	<? if(!empty($accessSettings["modules"])): ?>
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
	                                'data-setting' => $module_setting_id, 
	                                'data-access' => $module_setting['access_id'] 
	                            ]) 
	                        ?>
	                        <div class="hint-block">
	                            <div class="changes-saved">Изменения сохранены</div>
	                            <?= $module_setting['descr'] ?>
	                        </div>
	                    </div>
	                <? endforeach ?>
	            </div>
	        </div>
	    <? endforeach ?>
	<? endif ?>
</div>