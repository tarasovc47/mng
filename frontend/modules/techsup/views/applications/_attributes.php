<?php
	use yii\helpers\Html;
?>
<? if(!$children): ?>
	<div class="attributes">
<? endif ?>
	<? foreach($attrs as $key => $attr): ?>
		<?php
			$classes = "checkbox attribute";

			$classes .= $children ? " child" : " start";

			if(isset($fields[$attr['id']])){
				$classes .= " has_fields";
			}
		?>
		<div style="padding-left: <?= $padding; ?>px;" class="<?= $classes; ?>" data-attr="<?= $attr['id']; ?>">
			<label>
				<?= Html::checkbox('', false,[ "data-id" => $attr['id'], "data-level" => $level, 'class' => 'attribute_chx' ]) . $attr['name']; ?>
			</label>
			<? if($attr['comment']): ?>
				<div class="attribute-desc"><?= nl2br($attr['comment']); ?></div>
			<? endif ?>

			<? if(isset($fields[$attr['id']])): ?>
				<? foreach($fields[$attr['id']] as $field): ?>
					<?= $this->render("_field", [
			            'field' => $field,
		            	'loki_basic_service_id' => $loki_basic_service_id,
			        ]); ?>
				<? endforeach ?>
			<? endif ?>

			<? if($attr['children']): ?>
				<?= $this->render("_attributes", [
		            'attrs' => $attr['children'], 
		            'fields' => $fields,
		            'children' => true, 
		            'padding' => $padding + 7,
		            'loki_basic_service_id' => $loki_basic_service_id,
		            'level' => $level + 1,
		        ]); ?>
			<? endif ?>
		</div>
	<? endforeach ?>
<? if(!$children): ?>
	</div>
<? endif ?>