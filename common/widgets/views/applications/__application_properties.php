<?php
	use common\widgets\FieldsData;
	use common\components\SiteHelper;

	if(empty($properties) && ($level == 1)){
		$properties = SiteHelper::to_php_array($model->properties);
	}
?>
<?php if(!empty($properties)): ?>

	<?php if($level == 1): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<a data-toggle="collapse" href="#attributes_event_<?= $model->applicationEvent->id; ?>">Техническая поддержка<?php //echo $properties_repository[$properties[0]]->department->name; ?></a>
			</div>
			<div id="attributes_event_<?= $model->applicationEvent->id; ?>" class="panel-collapse collapse">
				<div class="panel-body">
	<?php endif ?>

	<?php foreach($properties as $key => $property_id): ?>
		<?php unset($properties[$key]); ?>
		<?php if(($properties_repository[$property_id]->parent_id == $id)): ?>
			<?php
				$attribute = $properties_repository[$property_id];
			?>
			<div class="property"><?php echo $attribute->name; ?>
				<?php echo $this->render("__application_properties", [
						'model' => $model,
				    	'properties' => $properties,
				    	'properties_repository' => $properties_repository,
				    	'id' => $attribute->id,
						"level" => $level + 1,
					]); 
				?>
			</div>
		<?php endif ?>
	<?php endforeach ?>

	<?php if($level == 1): ?>
				</div>
			</div>
		</div>
	<?php endif ?>

<?php endif ?>