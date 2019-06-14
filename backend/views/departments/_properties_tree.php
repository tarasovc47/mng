<?php
	use yii\helpers\Html;
?>
<ul class='<?= $classes ?>'>
	<? foreach($properties as $key => $prop): ?>
		<li class='properties-tree__property' data-id='<?= $prop['id']; ?>' data-sort='<?= $prop['sort']; ?>'>
			<div class='property-name'>
				<?= $prop["name"]; ?>
				<div class='property-actions'>
					<?php 
						/*echo Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
							[ '/properties/view', 'id' => $prop['id'] ], 
							[ 'title' => 'Подробнее' ]
						);*/

						if($editAccess){
							echo Html::a('<span class="glyphicon glyphicon-pencil"></span>', 
								[ '/properties/update', 'id' => $prop['id'] ], 
								[ 'title' => 'Редактировать' ]
							);
						}
					?>
				</div>
			</div>
			<? if($prop['children']): ?>
				<?= $this->render('_properties_tree', [
						'properties' => $prop['children'],
						'editAccess' => $editAccess,
						'classes' => 'properties-tree__list',
					]);
				?>
			<? endif ?>
		</li>
	<? endforeach ?>
</ul>