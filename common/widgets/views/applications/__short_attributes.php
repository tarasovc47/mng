<?php
	use common\widgets\FieldsData;
	use common\components\SiteHelper;
?>
<?php if(!empty($attributes)): ?>
	<?php 
		if(!is_array($attributes)){
			$attributes = SiteHelper::to_php_array($attributes);
		}
	?>
	<?php foreach($attributes as $key => $attribute_id): ?>
		<?php unset($attributes[$key]); ?>
		<?php if(($attributes_repository[$attribute_id]->parent_id == $id)): ?>
			<?php
				$attribute = $attributes_repository[$attribute_id];
				$attribute->loadFieldsDataByEvent($event->id); 
			?>
			<div class="attribute hide" data-level="<?php echo $level; ?>">
				<?php echo ($level > 1) ? "<span class='arrow'>&nbsp;<i class='fa fa-long-arrow-right'></i>&nbsp;</span>" . $attribute->name : $attribute->name ; ?>
				<?php
					if(!empty($attribute->fields)){
						foreach($attribute->fields as $field){
							echo FieldsData::widget([
								'field' => $field,
							]);
						}
					}
				?>
			</div>
			<?php echo $this->render("__short_attributes", [ 
					"attributes" => $attributes,
					"attributes_repository" => $attributes_repository,
					"event" => $event,
					"id" => $attribute->id,
					"level" => $level + 1,
				]); 
			?>
		<?php endif ?>
	<?php endforeach ?>
<?php endif ?>