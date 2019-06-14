<div class="zones__address__flat">
	<?= Yii::$app->params['zones__room_types'][$room_type] ?> <?= $flat ?> 
	<span class="zones__address__flat-control">
		<i class="fa fa-pencil" aria-hidden="true" data-room-type="<?=$room_type ?>" data-flat-name="<?= $flat ?>" data-flat-id="<?= $flat_id ?>"></i>
		<i class="fa fa-trash" aria-hidden="true" data-room-type="<?=$room_type ?>" data-flat-name="<?= $flat ?>" data-flat-id="<?= $flat_id ?>"></i>
	</span>
</div>