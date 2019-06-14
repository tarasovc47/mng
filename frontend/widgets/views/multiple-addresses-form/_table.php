<?php 
	use yii\helpers\Html;
	use common\components\SiteHelper;
?>

<?= Html::label('Выберите адреса:') ?>

<div class="checkbox" id="all_checked">
	<?= Html::checkbox('all_checked', false, ['label' => 'Выбрать все']) ?>
</div>

<?php foreach ($addresses as $key => $address): ?>
	<div class="checkbox address-checkbox">
		<?= Html::checkbox(
			'DynamicModel[addresses]['.$key.']', 
			false, 
			[
				'value' => $key,
				'label' => SiteHelper::getAddressNameByUuid($address),
			]
		) ?>
	</div>
<?php endforeach ?>
