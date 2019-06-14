<?php
	use yii\bootstrap\Tabs;
	use common\widgets\Applications;
?>
<h1>Рабочий стол</h1>
<div class="dashboard-brigadier">
	<?php
		$applications = Applications::widget([ 
			"applications" => $applications,
			"template" => "brigadier",
			"user" => $user,
		]);

		echo Tabs::widget([
		    'items' => [
		        [
		            'label' => 'Заявки',
		            'content' => $applications,
		            'active' => true,
		            'options' => [
		            	'class' => 'brigadier-applications'
		            ],
		        ],
		    ],
		]);
	?>
</div>