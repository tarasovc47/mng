<?php
	use yii\helpers\Html;
?>
<div class="applications-filters">
	<div class="applications-filters__content">
		<div class="filter__private-applications checkbox">
			<label for="private-applications">
				<?= Html::checkbox("private-applications", false, [ 
					"id" => "private-applications",
					"data-user-id" => $user->id,
				]); ?>
				Только собственные
			</label>
		</div>
	</div>
</div>