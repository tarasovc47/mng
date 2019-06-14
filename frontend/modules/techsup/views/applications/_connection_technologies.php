<?php
	use yii\helpers\Html;
?>
<div class="connection_technologies">
	<? foreach($conn_techs as $key => $conn_tech): ?>
		<div class="checkbox connection_technology">
			<label>
				<?= Html::checkbox('', false,[ 
					'class' => "connection_technology_chx" ,
					"data-id" => $conn_tech['id'],
					"data-billing-id" => $conn_tech['billing_id'],
				])
				.
				$conn_tech['name']; ?>
			</label>
		</div>
	<? endforeach ?>
</div>