<?php
	use yii\helpers\Html;
?>
<div class="a-dialog__header">
	<h4>Отказ от заявки <?php echo $application->id; ?></h4>
</div>
<div class="a-dialog__content">
	<div class="form-group alert alert-danger errors hide"></div>
	<div class="form-group comment">
		<div class="comment-form">
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