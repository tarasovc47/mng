<?php
	use yii\helpers\StringHelper;

	$model_name = StringHelper::basename(get_class($model));
	$field_model_name = "field-" . mb_strtolower($model_name) . "-" . mb_strtolower($attribute);
?>
<div class="form-group address-search <?= $field_model_name; ?>">
	<label class="control-label"><?= $model->getAttributeLabel($attribute); ?></label>
	<div>
		<a href="#" data-name="<?= $model_name . "[" . $attribute . "]"; ?>" class="place-find-editable editable editable-click editable-empty" data-type="PlaceFindEditable" data-value=""></a>
	</div>
	<div class="help-block"></div>
</div>