<?php
	use yii\helpers\Html;
?>
<div class="a-dialog__header">
	<h4>Передача заявки <?php echo $application->id; ?> в другой отдел</h4>
</div>
<div class="a-dialog__content">
	<div class="form-group alert alert-danger errors hide"></div>
	<div class="form-group choice-of-department">
		<label class="control-label" for="choice-of-department">Выбрать отдел</label>
		<?php
			$options = [];

			if($application->department_id){
				$options[$application->department_id] = [ "disabled" => true ];
			}

			echo Html::dropDownList("choice-of-department", false, $departments, [ 
				'class' => 'form-control',
				'id' => 'choice-of-department',
				'prompt' => '&mdash; Выбрать &mdash;',
				'encode' => false,
				'options' => $options
			]);
		?>
	</div>
	<div class="form-group brigades-nod hide">
		<label class="control-label" for="choice-of-brigade">Выбрать бригаду</label>
		<?php
			$options = [];

			if($application->group_id){
				$brigades[$application->group_id] = [ "disabled" => true ];
			}

			echo Html::dropDownList("choice-of-brigade", $default_brigade, $brigades, [ 
				'class' => 'form-control',
				'id' => 'choice-of-brigade',
				'prompt' => '&mdash; Выбрать &mdash;',
				'encode' => false,
				'options' => $options
			]);
		?>
	</div>
	<div class="form-group">
		<?php
			echo $this->render("_attributes", [
				"attributes" => $attributes["attrs"],
	            'children' => false, 
			]);
		?>
	</div>
	<div class="form-group comment">
		<?php echo Html::button("Комментарий", [ 'class' => 'btn show-comment' ]); ?>
		<div class="comment-form hide">
			<label class="control-label" for="comment">Комментарий</label>
			<?php
				echo Html::textarea('comment', '', [
					'class' => 'form-control',
					'id' => 'comment',
				]);
			?>
		</div>
	</div>
</div>
<div class="a-dialog__footer">
	<?php echo Html::button("Отправить", [ 'class' => 'btn btn-success ok' ]); ?>
	<?php echo Html::button("Отмена", [ 'class' => 'btn btn-default cancel' ]); ?>
</div>