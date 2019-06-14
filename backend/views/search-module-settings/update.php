<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SearchModuleSettings */

$this->title = 'Настройки поиска для модуля: ' . $model->descr;
$this->params['breadcrumbs'][] = ['label' => 'Настройки поиска для модулей', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="search-module-settings-update">

    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>

    <div class="search-module-settings-form">

	    <?php $form = ActiveForm::begin(); ?>

		<h3>Отображение столбцов в результатах поиска</h3>
	    <div class="table-responsive">
		    <table class="table table-striped">
		    	<thead>
		    		<tr>
		    			<td>Название поля</td>
		    			<td class="search-settings__show-checkboxes">Отображать</td>
		    		</tr>
		    	</thead>
		    	<tbody>
		    		<tr>
		    			<td>Номер абонента</td>
		    			<td class="search-settings__show-checkboxes">
		    				<?php echo Html::checkbox('disabled', true, ['disabled' => 'disabled']); ?>
		    			</td>
		    		</tr>
		    		<tr>
		    			<td>Лицевой счёт</td>
		    			<td class="search-settings__show-checkboxes">
		    				<?php echo Html::checkbox('disabled', true, ['disabled' => 'disabled']); ?>
		    			</td>
		    		</tr>

		    		<?php foreach ($fields as $key_field => $field): ?>
		    			<tr>
		    				<td class="search-settings__labels" data-field-id="<?php echo $field['id'] ?>"><?php echo Html::label($field['descr'], 'search-settings__checkbox__'.$field['id']) ?></td>
		    				<td class="search-settings__show-checkboxes">
		    					<?php 
		    						foreach ($module_fields_values as $key_value => $value) {
		    							if ($value['field_id'] == $field['id']){
		    								switch ($value['value']) {
		    									case '0':
		    										echo Html::checkbox($field['id'], false, ['id' => 'search-settings__checkbox__'.$field['id']]);
		    										break;

		    									case '1':
		    										echo Html::checkbox($field['id'], true, ['id' => 'search-settings__checkbox__'.$field['id']]);
		    										break;

		    									default:
		    										break;
		    								}
		    							}
		    						}
		    					?>		    					
		    				</td>
		    			</tr>
		    		<?php endforeach ?>
		    	</tbody>
		    </table>
	    </div>

	    <div class="form-group">
	        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
	    </div>

	    <?php ActiveForm::end(); ?>
	</div>

</div>
