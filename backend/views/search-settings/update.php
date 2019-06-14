<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Departments;

/* @var $this yii\web\View */
/* @var $model common\models\Departments */

$this->title = 'Настройка поиска для отдела: ' . Departments::findOne($department)['name'];
$this->params['breadcrumbs'][] = ['label' => 'Настройка поиска для отделов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="search-settings-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="search-settings-form">
	    <?php $form = ActiveForm::begin(); ?>

			<h3>Отображение столбцов в результатах поиска</h3>
		    <div class="table-responsive">
			    <table class="table table-striped">
			    	<thead>
			    		<tr>
			    			<td>Название поля</td>
			    			<td class="search-settings__checkboxes">Отображать</td>
			    		</tr>
			    	</thead>
			    	<tbody>
			    		<tr>
			    			<td>Номер абонента</td>
			    			<td class="search-settings__checkboxes">
			    				<?= Html::checkbox('disabled', true, ['disabled' => 'disabled']); ?>
			    			</td>
			    		</tr>
			    		<tr>
			    			<td>Лицевой счёт</td>
			    			<td class="search-settings__checkboxes">
			    				<?= Html::checkbox('disabled', true, ['disabled' => 'disabled']); ?>
			    			</td>
			    		</tr>

			    		<?php foreach ($fields as $key_field => $field): ?>
			    			<tr>
			    				<td class="search-settings__labels" data-field-id="<?= $field['id'] ?>"><?=Html::label($field['label'], 'search-settings__checkbox__'.$field['id']) ?></td>
			    				<td class="search-settings__checkboxes">
			    					<?php
			    					$checked = $field['display_default_setting'];
			    					if (isset($department_fields_values[$field['id']])) {
			    						$checked = $department_fields_values[$field['id']];
			    					}
			    					echo Html::checkbox(
				    						$field['id'], 
				    						$checked, 
				    						['id' => 'search-settings__checkbox__'.$field['id']]
			    						) 
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
<pre><?php print_r($fields) ?></pre>