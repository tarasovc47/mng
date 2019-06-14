<?php
	if(!is_array($field->data)){
		$field->data = unserialize($field->data);
	}

	$what_a_field = $field['data']['type'];
	$what_a_field .= isset($field['data']['view']) ? "_" . $field['data']['view'] : "";
?>
<div class="field-wrap">
	<div class="field <?= $what_a_field; ?>">
		<?php echo $this->render("_" . $what_a_field, [ 
			"field" => $field,
		]);?>
	</div>
</div>