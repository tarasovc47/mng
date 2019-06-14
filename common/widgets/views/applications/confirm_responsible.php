<?php
	use yii\helpers\Html;
?>
<div class="a-dialog__header">
	<h4>Назначение ответственного за заявку <?php echo $application->id; ?></h4>
</div>
<div class="a-dialog__content">
	<div class="form-group alert alert-danger errors hide"></div>
	<div class="form-group choice-of-responsible">
		<label class="control-label" for="choice-of-responsible">Назначить ответственного</label>
		<?php
			$options = [];

			if($application->responsible){
				$options[$application->responsible] = [ "disabled" => true ];
			}

			echo Html::dropDownList("choice-of-responsible", false, $users, [ 
				'class' => 'form-control',
				'id' => 'choice-of-responsible',
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
	<?php echo Html::button("Назначить", [ 'class' => 'btn btn-success ok' ]); ?>
	<?php echo Html::button("Отмена", [ 'class' => 'btn btn-default cancel' ]); ?>
</div>