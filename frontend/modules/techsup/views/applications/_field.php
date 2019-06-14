<?php 
	$field['data'] = unserialize($field['data']); 
	$what_a_field = $field['data']['type'];
	$what_a_field .= isset($field['data']['view']) ? "_" . $field['data']['view'] : "";

	echo $this->render("__" . $what_a_field, [ 
		"field" => $field, 
		"what_a_field" => $what_a_field,
		"loki_basic_service_id" => $loki_basic_service_id,
	]);
?>