<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Настройки поиска';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile search-settings">
	<?php echo $userLeftMenu; ?>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>

    <div class="profile__search-settings">
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
		    				<?php echo Html::checkbox('disabled', true, ['disabled' => 'disabled']); ?>
		    			</td>
		    		</tr>
		    		<tr>
		    			<td>Лицевой счёт</td>
		    			<td class="search-settings__checkboxes">
		    				<?php echo Html::checkbox('disabled', true, ['disabled' => 'disabled']); ?>
		    			</td>
		    		</tr>

		    		<?php foreach ($fields as $key_field => $field): ?>
		    			<tr>
		    				<td class="search-settings__labels"><?= Html::label($field['label'], 'search-settings__checkbox__'.$field['id']) ?></td>
		    				<td class="search-settings__checkboxes">
		    					<?php 
		    						$checked = $field['display_default_setting'];
		    						if (isset($fields_values[$field['id']])) {
		    							$checked = $fields_values[$field['id']];
		    						}
		    						echo Html::checkbox(
		    												$field['id'], 
		    												$checked, 
		    												[
		    													'id' => 'search-settings__checkbox__'.$field['id'],
		    													'class' => 'search-settings__checkbox',
		    													'data' => [
		    														'field-id' => $field['id'],
		    													],
		    												]
		    											);
		    					?>		    					
		    				</td>
		    			</tr>
		    		<?php endforeach ?>
		    	</tbody>
		    </table>
	    </div>

	    <div class="form-group">
	        <?= Html::button('Сохранить', ['class' => 'btn btn-success', 'id' => 'search-settings__save']) ?>
	        <div class="alert alert-success" id="search-settings__success">Изменения сохранены <i class="fa fa-check-circle-o fa-lg" aria-hidden="true"></i></div>
            <div class="alert alert-danger" id="search-settings__danger">Что-то пошло не так, ничего не сохранилось <i class="fa fa-times-circle-o fa-lg" aria-hidden="true"></i></div>
	    </div>
	</div>
</div>