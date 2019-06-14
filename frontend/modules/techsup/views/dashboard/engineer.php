<?php
	use yii\bootstrap\Tabs;
	use common\widgets\Applications;

	use yii\helpers\Html;
?>
<h1>Рабочий стол</h1>
<div class="dashboard-engineer">
	<?php
		$applications = Applications::widget([ 
			"applications" => $applications,
			"template" => "engineer",
			"user" => $user,
		]);

		echo Tabs::widget([
		    'items' => [
		        [
		            'label' => 'Заявки',
		            'content' => $applications,
		            'active' => true,
		            'options' => [
		            	'class' => 'engineer-applications'
		            ],
		        ],
		    ],
		]);
	?>
</div>