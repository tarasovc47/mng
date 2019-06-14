<?php
	use yii\helpers\Html;
?>
<div class="a-dialog__header">
	<h4>Обработка заявки <?php echo $application->id; ?></h4>
</div>
<div class="a-dialog__content">
	<div class="form-group alert alert-danger errors hide"></div>
	<div class="form-group">
		<?php
			echo $this->render("_properties", [
				"properties" => $properties["properties"],
	            'children' => false, 
			]);
		?>
	</div>
	<div class="form-group choice-of-action">
		<label class="control-label" for="choice-of-action">Действие</label>
			<?php
				$list = [
					"close" => "Закрыть",
					"revision" => "Отправить на доработку",
				];

				echo Html::dropDownList("choice-of-action", false, $list, [ 
					'class' => 'form-control',
					'id' => 'choice-of-action',
					'prompt' => '&mdash; Выбрать &mdash;',
					'encode' => false,
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