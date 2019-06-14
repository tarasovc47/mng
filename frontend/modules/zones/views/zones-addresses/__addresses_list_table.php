<?php 
	use common\components\SiteHelper;
	use yii\helpers\Html;
?>
<div id="addresses-list__table">
	<table class="table">
		<thead>
			<tr>
				<th><div id="addresses-list__table__check-all"><i class="fa fa-check-square-o" aria-hidden="true"></i> Выделить всё</div></th>
				<th>Адрес</th>
			</tr>
		</thead>
		<tbody>
			<?php if (isset($uuid_list) && !empty($uuid_list)): ?>
				<?php foreach ($uuid_list as $key => $uuid): ?>
					<tr>
						<td><?= Html::checkbox(
												null, 
												in_array($uuid, $checked_list), 
												[
													'data' => [
														'uuid' => $uuid, 
														'on' => ' ', 
														'off' => ' ', 
														'size' => 'mini', 
														'onstyle' => 'warning'
													], 
													'class' => 'zones__addresses__address-list-checkbox'
												]) ?>
						</td>
						<td><?= SiteHelper::getAddressNameByUuid($uuid) ?></td>
					</tr>
				<?php endforeach ?>
			<?php endif ?>
		</tbody>
	</table>

	<?php if (isset($uuid_list) && !empty($uuid_list)): ?>
		<?php if (!($count_uuid <= 30)): ?>
			<ul class="pagination" id="zones__addresses__address-list-pagination">
				<?php if ($page == 1): ?>
					<li class="disabled"><a href="#">&laquo;</a></li>
				<?php else: ?>
					<li><a class="zones__addresses__page-link" href="#" data-dest-page="<?php echo $page-1 ?>">&laquo;</i></a></li>
				<?php endif; ?>

				<?php for ($i=1; $i <= $count_uuid/30+1; $i++): ?>
					<?php if ($page == $i): ?>
						<li class="active"><span><?php echo $i ?></span></li>
					<?php else: ?>
						<li><a class="zones__addresses__page-link" href="#" data-dest-page="<?php echo $i ?>"><?php echo $i ?></a></li>
					<?php endif; ?>
				<?php endfor; ?>

				<?php if ($page >= ($count_uuid/30)): ?>
					<li class="disabled"><a href="#">&raquo;</a></li>
				<?php else: ?>
					<li><a class="zones__addresses__page-link" href="#" data-dest-page="<?php echo $page+1 ?>">&raquo;</a></li>
				<?php endif ?>
			</ul>
		<?php endif ?>
	<?php endif ?>
</div>

<div id="addresses-list__chosen">
    <table class="table">
        <thead>
            <tr>
                <th><div id="addresses-list__table__uncheck-all"><i class="fa fa-times" aria-hidden="true"></i> Удалить всё</div></th>
                <th>Выбранные адреса</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            	if (isset($checked_list) && !empty($checked_list)) {
                	foreach ($checked_list as $key => $uuid) {
                		echo $this->render('___addresses_list_chosen_row', [
	                		'uuid' => $uuid,
	                    ]);
                	}
            	} 
            ?>
        </tbody>
    </table>
</div>
