<?php
	use yii\bootstrap\Tabs;
	use common\widgets\Applications;
	use common\components\SiteHelper;
?>
<h1>Рабочий стол</h1>
<div class="dashboard-support">
	<?php
		$applications = Applications::widget([ 
			"applications" => $applications,
			"template" => "support",
			"user" => $user,
		]);

		echo Tabs::widget([
		    'items' => [
		        [
		            'label' => 'Заявки',
		            'content' => $applications,
		            'active' => true,
		            'options' => [
		            	'class' => 'support-applications'
		            ],
		        ],
		    ],
		]);
	?>
</div>