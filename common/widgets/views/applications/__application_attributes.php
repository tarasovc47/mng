<?php
	use common\widgets\FieldsData;
	use common\components\SiteHelper;

	if(empty($attributes) && ($level == 1)){
		$attributes = SiteHelper::to_php_array($model->attributes);
	}
?>
<?php if(!empty($attributes)): ?>

	<?php if($level == 1): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<a data-toggle="collapse" href="#attributes_event_<?= $model->applicationEvent->id; ?>"><?php echo $attributes_repository[$attributes[0]]->department->name; ?></a>
			</div>
			<div id="attributes_event_<?= $model->applicationEvent->id; ?>" class="panel-collapse collapse">
				<div class="panel-body">
	<?php endif ?>

	<?php foreach($attributes as $key => $attribute_id): ?>
		<?php unset($attributes[$key]); ?>
		<?php if(($attributes_repository[$attribute_id]->parent_id == $id)): ?>
			<?php
				$attribute = $attributes_repository[$attribute_id];
				$attribute->loadFieldsDataByEvent($model->applicationEvent->id);
			?>
			<div class="attribute"><?php echo $attribute->name; ?>
				<?php
					if(!empty($attribute->fields)){
						foreach($attribute->fields as $field){
							echo FieldsData::widget([
								'field' => $field,
							]);
						}
					}
				?>
				<?php echo $this->render("__application_attributes", [
						'model' => $model,
				    	'attributes' => $attributes,
				    	'attributes_repository' => $attributes_repository,
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