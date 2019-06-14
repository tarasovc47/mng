<?php
	use yii\helpers\Html;
?>
<div class='create-section panel panel-default' data-loki-basic-service-id='<?= $loki_basic_service_id; ?>' data-connection-technology-id="<?= $connection_technology_id; ?>">
	<div class='create-section__login panel-heading'><h4><?= $login; ?></h4></div>
	<div class='panel-body'>
		<div class="create-section__error"></div>
		<?= $html; ?>
		<div class='departments'>
			<div class='departments-title'>В какой отдел направится заявка</div>
			<? foreach($departments as $id => $department): ?>
				<div class='department' data-id='<?= $id; ?>' data-scenario="0">
					<?= $department; ?> <i class='fa fa-check-circle-o'></i>
					<div class="department-hint"></div>
				</div>
			<? endforeach ?>
		</div>
		<a href='#' class='departments-show-all'>Показать все</a>
		<div class="brigades-nod hide">
			<?= Html::dropDownList("brigades-nod", 
				$default_brigade, 
				$brigades, 
				[ 
					'class' => 'form-control', 
					'prompt' => '&mdash; Выберите бригаду &mdash;', 
					'encode' => false 
				]);
			?>
		</div>
	</div>
</div>