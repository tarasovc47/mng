<?php
	use yii\helpers\Html;
?>
<div class="a-dialog__header">
	<h4>Завершение заявки <?php echo $application->id; ?></h4>
</div>
<div class="a-dialog__content">
	<div class="form-group alert alert-danger errors hide"></div>
	<?php if(!empty($statuses)): ?>
		<div class="form-group choice-of-status">
			<label class="control-label" for="choice-of-status">Статус</label>
			<?php
				$options = [];

				if(isset($options[$application->application_status_id])){
					$options[$application->application_status_id] = [ "disabled" => true ];
				}

				echo Html::dropDownList("choice-of-status", false, $statuses, [ 
					'class' => 'form-control',
					'id' => 'choice-of-status',
					'prompt' => '&mdash; Выбрать &mdash;',
					'encode' => false,
					'options' => $options,
				]);
			?>
		</div>
	<?php endif ?>
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