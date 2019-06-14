<?php 
    use common\models\ZonesFlats;
    use common\models\ZonesFloors;

	$flats_item = ZonesFlats::getFlatsForPorch($porch_id, 1);
    $offices_item = ZonesFlats::getFlatsForPorch($porch_id, 2);
    $floors_item = ZonesFloors::getFloorsForPorches($porch_id);
    $floors_html = $this->render('___floors', [
                        'floors_item' => $floors_item,
                        'flats_item' => $flats_item,
                        'porch_id' => $porch_id,
                        'offices_item' => $offices_item,
                    ]);
?>



<div class="zones__address__porch-panel-heading panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<a class="collapse-toggle" href="#<?= $porch_id ?>" data-toggle="collapse" data-parent="#<?= $parent_div ?>">Подъезд <span data-porch-id="<?= $porch_id ?>" class="porch-name"><?= $porch_name ?></span>
			</a>
			<span class="zones__address__porch-control">
				<i class="fa fa-pencil" aria-hidden="true" data-porch-name="<?= $porch_name ?>" data-porch-id="<?= $porch_id ?>"></i>
				<i class="fa fa-trash" aria-hidden="true" data-porch-name="<?= $porch_name ?>" data-porch-id="<?= $porch_id ?>"></i>
			</span>
		</h4>
	</div>
	<div id="<?= $porch_id ?>" class="panel-collapse collapse" data-porch-id="<?= $porch_id ?>">
		<div class="panel-body">
			<button type="button" class="btn btn-success btn-xs zones__address__add-floor" data-porch-id="<?= $porch_id ?>"><i class="fa fa-plus" aria-hidden="true"></i> Добавить этаж</button>
			<button type="button" class="btn btn-success btn-xs zones__address__add-floors" data-porch-id="<?= $porch_id ?>"><i class="fa fa-plus" aria-hidden="true"></i> Добавить диапазон этажей</button>
			<?= $floors_html ?>
		</div>
	</div>
</div>


