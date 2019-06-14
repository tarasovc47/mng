<?php
	use yii\helpers\Html;
	use common\components\SiteHelper;
	use common\widgets\ApplicationsEvents;
	use common\models\Access;

	$class = "application";
	if($application->applicationsStatus->id == 1){
		// Открыта
		$class .= " new";
	}
	if($application->applicationsStatus->id == 6){
		// Переназначена в другой отдел
		$class .= " new";
	}

	$date = $application->applicationsEvents[0]->created_at;
	$date = SiteHelper::russianDate($date, true) . " " . date("H:i:s", $date);

	$author = $application->applicationsEvents[0]->casUser->last_name . " " . $application->applicationsEvents[0]->casUser->first_name;

	if($application->responsible == 0){
		$responsible = "Не назначен";
	}
	else{
		$responsible = $application->responsibleUser->last_name;
		$responsible .= " ";
		$responsible .= $application->responsibleUser->first_name;
	}

	$client = false;
	if(isset($clients[$application->loki_basic_service_id])){
		$client = $clients[$application->loki_basic_service_id];
	}
?>
<div class="<?php echo $class; ?>" data-id="<?php echo $application->id; ?>">
	<div class="application-id"><?php echo $application->id; ?></div>
	<div class="application-info">
		<div class="application-info__app">
			<div class="item application-created">
				<div class="caption">Дата создания</div>
				<div class="data"><?php echo $date; ?></div>
			</div>
			<div class="item application-author">
				<div class="caption">Автор</div>
				<div class="data"><?php echo $author; ?></div>
			</div>
			<div class="item application-status">
				<div class="caption">Текущий статус</div>
				<div class="data"><?php echo $application->applicationsStatus->name; ?></div>
			</div>
			<div class="item application-type">
				<div class="caption">Тип</div>
				<div class="data"><?php echo $application->applicationsType->name; ?></div>
			</div>
			<div class="item application-responsible">
				<div class="caption">Ответственный</div>
				<div class="data"><?php echo $responsible; ?></div>
			</div>
			<div class="item application-department">
				<div class="caption">Отдел</div>
				<div class="data"><?php echo $application->department->name; ?></div>
			</div>
			<div class="item application-connection_technology">
				<div class="caption">Технология подключения</div>
				<div class="data"><?php echo $application->connectionTechnology->name; ?></div>
			</div>
		</div>
		<?php if($client): ?>
			<div class="application-info__client">
				<div class="item client-client_id">
					<div class="caption">Лицевой счёт</div>
					<div class="data"><?php echo $client["client_id"]; ?></div>
				</div>
				<div class="item client-name">
					<div class="caption">Имя/название</div>
					<div class="data"><?php echo $client["name"]; ?></div>
				</div>
				<div class="item client-client_type">
					<div class="caption">Тип клиента</div>
					<div class="data"><?php echo $client["client_type_descr"]; ?></div>
				</div>
				<div class="item client-balance">
					<div class="caption">Баланс</div>
					<div class="data"><?php echo $client["balance"]; ?></div>
				</div>
				<div class="item client-contact_phone">
					<div class="caption">Контактный телефон</div>
					<div class="data"><?php echo $client["contact_phone"]; ?></div>
				</div>
				<div class="item client-provider">
					<div class="caption">Провайдер</div>
					<div class="data"><?php echo $client["provider"]; ?></div>
				</div>
				<div class="item client-subprovider">
					<div class="caption">Субпровайдер</div>
					<div class="data"><?php echo $client["subprovider"]; ?></div>
				</div>
				<div class="item client-address_jur">
					<div class="caption">Юридический адрес</div>
					<div class="data"><?php echo $client["address_jur"]; ?></div>
				</div>
				<div class="item client-address_post">
					<div class="caption">Фактический адрес</div>
					<div class="data"><?php echo $client["address_post"]; ?></div>
				</div>
				<div class="item client-inn">
					<div class="caption">ИНН</div>
					<div class="data"><?php echo $client["inn"]; ?></div>
				</div>
			</div>
		<?php endif ?>
	</div>
	<div class="application-attributes">
		<?php foreach($application->applicationsEvents as $event): ?>
			<?php if($event->applicationAttributes): ?>
				<div class="application-attributes__line">
					<?php echo $this->render('__application_attributes', [
							'model' => $event->applicationAttributes,
					    	'attributes' => [],
					    	'attributes_repository' => $attributes_repository,
					    	'id' => 0,
					   		'level' => 1,
					    ]);
					?>
				</div>
			<?php endif ?>
			<?php if($event->applicationProperties): ?>
				<div class="application-attributes__line">
					<?php echo $this->render('__application_properties', [
							'model' => $event->applicationProperties,
					    	'properties' => [],
					    	'properties_repository' => $properties_repository,
					    	'id' => 0,
					   		'level' => 1,
					    ]);
					?>
				</div>
			<?php endif ?>
		<?php endforeach ?>
	</div>
	<div class="application-actions">
		<?php
			$buttons = [
				'set_department' => false,
				'set_responsible' => false,
				'refuse' => false,
				'complete' => false,
				'handle' => false,
			];

			// Инженеры сетевых технологий
			if($template == "engineer"){
				$access_set_department = Access::hasAccess($user->id, $user->roles, 13);
				$access_set_responsible = Access::hasAccess($user->id, $user->roles, 12);

				// Принято в работу, нет ответственного или ответственный сам текущий пользователь
				if(($application->applicationsStatus->id == 2) && (($application->responsible == 0) || ($application->responsible == $user->id))){
					$buttons["set_department"] = $access_set_department;
					$buttons["set_responsible"] = $access_set_responsible;
				}
			}

			// Бригадир службы эксплуатации
			if($template == "brigadier"){
				$access_set_department = Access::hasAccess($user->id, $user->roles, 16);
				$access_set_responsible = Access::hasAccess($user->id, $user->roles, 18);

				// Возможно здесь нужны проверки на статус заявки или еще какие-нибудь условия
				$buttons["set_department"] = $access_set_department;
				$buttons["set_responsible"] = $access_set_responsible;
			}

			// Инженер службы эксплуатации
			if($template == "nod"){
				$access_refuse = Access::hasAccess($user->id, $user->roles, 21);

				$buttons["refuse"] = $access_refuse;
				$buttons["complete"] = true;
			}

			// Техническая поддержка
			if($template == "support"){
				$buttons["handle"] = true;
			}

			if($buttons["set_responsible"]){
				echo Html::button('<i class="fa fa-user"></i>&nbsp;&nbsp;Назначить ответственного', [ 'class' => 'btn btn-primary responsible' ]);
			}

			if($buttons["set_department"]){
				echo Html::button('<i class="fa fa-building-o"></i>&nbsp;&nbsp;Передать в другой отдел', [ 'class' => 'btn btn-info department' ]);
			}

			if($buttons["complete"]){
				echo Html::button('<i class="fa fa-wrench"></i>&nbsp;&nbsp;Завершить', [ 'class' => 'btn btn-success complete' ]);
			}

			if($buttons["refuse"]){
				echo Html::button('<i class="fa fa-ban"></i>&nbsp;&nbsp;Отказаться', [ 'class' => 'btn btn-danger refuse' ]);
			}

			if($buttons["handle"]){
				echo Html::button('<i class="fa fa-check"></i>&nbsp;&nbsp;Обработать заявку', [ 'class' => 'btn btn-success handle' ]);
			}
		?>
	</div>
	<div class="application-history">
		<div class="application-history__title">
			<strong class="caption is-history">История</strong>
			<i class="switch fa fa-toggle-off"></i>
			<strong class="caption is-comment pale">Комментарии</strong>
		</div>
		<?php foreach($application->applicationsEvents as $event): ?>
			<?php echo ApplicationsEvents::widget([ "event" => $event ]);	?>
			<?php if(!empty($event->applicationComment)): ?>
				<div class="application-history__element is-comment hide">
					<span class="application-history__element-date">
						<i><?php echo SiteHelper::russianDate($event->created_at, true); ?></i>
						<i><?php echo date("H:i:s", $event->created_at); ?></i>
					</span>
					<span class="application-history__element-body">
						<i><?php echo $event->casUser->last_name . " " . $event->casUser->first_name; ?></i>:<br>
						<?php echo nl2br($event->applicationComment->comment); ?>
					</span>
				</div>
			<?php endif ?>
		<?php endforeach ?>
	</div>
</div>