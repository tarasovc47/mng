<?php
	use yii\helpers\Html;
?>
<ul class='<?= $classes ?>'>
	<? foreach($attributes as $key => $attribute): ?>
		<li class='attributes-tree__attribute' data-id='<?= $attribute['id']; ?>' data-sort='<?= $attribute['sort']; ?>'>
			<div class='attribute-name'>
				<?= $attribute["name"]; ?>
				<div class='attribute-actions'>
					<?php 
						echo Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
							[ '/attributes/view', 'id' => $attribute['id'] ], 
							[ 'title' => 'Подробнее' ]
						);

						if($editAccess){
							echo Html::a('<span class="glyphicon glyphicon-pencil"></span>', 
								[ '/attributes/update', 'id' => $attribute['id'] ], 
								[ 'title' => 'Редактировать' ]
							);
						}
					?>
				</div>
			</div>
			<? if($attribute['children']): ?>
				<?= $this->render('_attributes_tree', [
						'attributes' => $attribute['children'],
						'editAccess' => $editAccess,
						'classes' => 'attributes-tree__list',
					]);
				?>
			<? endif ?>
		</li>
	<? endforeach ?>
</ul>