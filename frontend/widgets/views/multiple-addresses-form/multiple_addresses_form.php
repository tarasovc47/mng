<?php
    use common\widgets\AddressSearch;
?>


<div id="multiple-addresses-form-widget">
	<h4>Поиск адресов</h4>
	<?= AddressSearch::widget([
	    'model' => $model,
	    'attribute' => '',
	    'template' => 'place-find-editable',
	]); ?>

	<div id="multiple-addresses-form-table"></div>
</div>
