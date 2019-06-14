<?php
	use yii\helpers\Html;
?>
<?php if(!$children): ?>
	<div class="attributes">
<?php endif ?>
	<?php foreach($attributes as $key => $attribute): ?>
		<?php
			$classes = "checkbox attribute";

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
		<div class="<?php echo $classes; ?>" data-attr="<?php echo $attribute['id']; ?>">
			<label>
				<?php echo Html::checkbox('', false,[ 
						"data-id" => $attribute['id'],
						'class' => 'attribute_chx',
					]) 
					. $attribute['name']; 
				?>
			</label>
			<?php if($attribute['comment']): ?>
				<div class="attribute-desc"><?php echo nl2br($attribute['comment']); ?></div>
			<?php endif ?>

			<?php if($attribute['children']): ?>
				<?php echo $this->render("_attributes", [
		            'attributes' => $attribute['children'], 
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