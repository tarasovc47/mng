<?php
	use yii\web\Session;
	use common\widgets\Applications;
	use yii\bootstrap\Modal;

	$session = Yii::$app->session;
?>
<div id="Applications" data-sid="<?= $session['sid']; ?>">
	<div id="socket-unavaliable" class="hide alert alert-danger">Невозможно подключиться к рабочему столу, попробуйте перезагрузить страницу.</div>
	<div id="applications-messages"></div>
	<div class="applications-stacks">
		<? foreach($applications_stacks as $stack_id): ?>
			<div class="application-stack" data-id="<?= $stack_id; ?>">
				<?= Applications::stack($stack_id, $applications, $attributes_repository, $properties_repository, $clients, $template, $user); ?>
			</div>
		<? endforeach ?>
	</div>
	<?php
		Modal::begin([
		    'header' => false,
		    'footer' => false,
		    'id' => 'A-dialog',
		    'options' => [
		    	'data-backdrop' => 'static',
		    	'data-keyboard' => 'false',
		    ],
		    'closeButton' => false,
		]);
	?>
		<div class="a-dialog__wrap"></div>
	<?php Modal::end(); ?>
</div>
	<?php
		/*use common\models\Applications as Apps;

		$array = [
			['Z/2017/000000008', '1'],
		];

		$test = Apps::findAll(['Z/2017/000000008', '1', 'Z/2017/000000007', '1']);
		print_r($test);*/
		/*use common\widgets\ApplicationsFilters;

		echo ApplicationsFilters::widget([
			'user' => $user,
		]);*/
	?>