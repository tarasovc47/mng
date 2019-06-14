<?php
	 /** Список чекбосками/радиокнопками */ 
	use yii\helpers\Html;

	$cardinality = $field['data']['cardinality'];
	$type = ($cardinality == 1) ? "radio" : "checkbox";
	$classes = "attribute-field " . $what_a_field;
	$classes .= $field['data']['required'] ? " required" : "";
?>
<div class="<?= $classes; ?>" data-cardinality="<?= $cardinality; ?>" data-field="<?= $what_a_field; ?>" data-id="<?= $field['id']; ?>">
	<div class="attribute-field__label"><?= $field['label']; ?><? if($field['data']['required']): ?> <span class="req">*</span><? endif ?></div>
	<div class="attribute-field__error"></div>
	<? $first = true; ?>
	<? foreach($field['data']['allowedValues'] as $value => $name): ?>
		<div class="<? echo $type; if($first){ echo " first"; $first = false; } ?>">
			<label for="af_<?= $loki_basic_service_id . $field['id'] . $value; ?>">
				<?php
					$checked = ($field['data']['default_value'] == $value);

					echo Html::$type($field['name'] . "_" . $loki_basic_service_id, 
						$checked, 
						[ 
							"value" => $value,
							"id" => "af_" . $loki_basic_service_id . $field['id'] . $value,
							"class" => "control",
						]
					);
				?>
				<span class="attribute-field__value-name"><?= $name; ?></span>
			</label>
		</div>
	<? endforeach ?>
</div>