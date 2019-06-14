<div class="field-label"><?php echo $field->label; ?></div>
<div class="field-results">
	<?php foreach($field->data["allowedValues"] as $value => $name): ?>
		<?php foreach($field->results as $result): ?>
			<?php if($result["value"] == $value): ?>
				<div class="field-result"><?php echo $name; ?></div>
			<?php endif ?>
		<?php endforeach ?>
	<?php endforeach ?>
</div>