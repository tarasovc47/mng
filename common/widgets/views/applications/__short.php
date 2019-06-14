<?php
	use yii\helpers\Html;
	use common\components\SiteHelper;

	$client = false;
	if(isset($clients[$application->loki_basic_service_id])){
		$client = $clients[$application->loki_basic_service_id];
	}

	$responsible = false;
	if(isset($application->responsibleUser) && !empty($application->responsibleUser)){
		$responsible = $application->responsibleUser->last_name;
		$responsible .= " ";
		$responsible .= $application->responsibleUser->first_name;
	}

	$taken = $application->getFirstTakenEvent();
	$set = $application->getFirstSetResponsibleEvent();
?>
<?php echo Html::checkbox("application", false, [ 
	"value" => $application->id,
	"class" => "application-short_checkbox",
]); ?>
<div class="application-short__content">
	<div class="application-short__last-attributes">
		<?php echo $this->render('__short_attributes', [
		    	'attributes' => $application->applicationsEvents[0]->applicationAttributes->attributes,
		    	'attributes_repository' => $attributes_repository,
		    	'event' => $application->applicationsEvents[0],
		    	'id' => 0,
		   		'level' => 1,
		    ]);
		?>
	</div>
	<div class="application-short__client-address_post"><?php echo $client["address_post"]; ?></div>
	<div class="application-short__dates">
		<div class="application-short__created-at">
			<time title="Дата создания" class="data">
				<?php echo SiteHelper::dateAgo($application->applicationsEvents[0]->created_at); ?>
			</time>
		</div>
		<div class="application-short__taken">
			<time title="Принято в работу" class="data">
				<?php if($taken): ?>
					<?php echo SiteHelper::dateAgo($taken->created_at); ?>
				<?php endif ?>
			</time>
		</div>
		<div class="application-short__set">
			<time title="Назначен ответственный" class="data">
				<?php if($set): ?>
					<?php echo SiteHelper::dateAgo($set->created_at); ?>
				<?php endif ?>
			</time>
		</div>
		<div class="application-short__responsible">
			Ответственный<br>
			<span class="data">
				<?php if($responsible): ?>
					<?php echo $responsible; ?>
				<?php else: ?>
					Не назначен
				<?php endif ?>
			</span>
		</div>
	</div>
</div>