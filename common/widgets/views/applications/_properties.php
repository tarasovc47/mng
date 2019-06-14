<?php
	use yii\helpers\Html;
?>
<?php if(!$children): ?>
	<div class="properties">
<?php endif ?>
	<?php foreach($properties as $key => $property): ?>
		<?php
			$classes = "checkbox property";

			$classes .= $children ? " child" : " start";

			/*
			if(isset($fields[$attribute['id']])){
				$classes .= " has_fields";
			}

			if(isset($fields[$attribute['id']])):
				foreach($fields[$attribute['id']] as $field):
					echo $this->render("_field", [
			            'field' => $field,
		            	'loki_basic_service_id' => $loki_basic_service_id,
			        ]); 
				 endforeach
			endif
			*/
		?>
		<div class="<?php echo $classes; ?>" data-prop="<?php echo $property['id']; ?>">
			<label>
				<?php echo Html::checkbox('', false,[ 
						"data-id" => $property['id'],
						'class' => 'property_chx',
					]) 
					. $property['name']; 
				?>
			</label>
			<?php if($property['comment']): ?>
				<div class="property-desc"><?php echo nl2br($property['comment']); ?></div>
			<?php endif ?>

			<?php if($property['children']): ?>
				<?php echo $this->render("_properties", [
		            'properties' => $property['children'], 
		            'children' => true, 
		            // 'fields' => $fields,
		            // 'loki_basic_service_id' => $loki_basic_service_id,
		        ]); ?>
			<?php endif ?>
		</div>
	<?php endforeach ?>
<?php if(!$children): ?>
	</div>
<?php endif ?>