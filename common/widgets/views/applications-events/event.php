<?php
	use common\components\SiteHelper;
	use common\models\CasUser;
	use common\models\Departments;
	use common\models\ApplicationsStatuses;
	
	$user_init = $event->casUser->last_name . " " . $event->casUser->first_name;

	if(!empty($event->vars)){
		$event->vars = unserialize($event->vars);
	}

	switch($event["type"]){
		case 1:
			$message = "Заявка создана.";
			break;
		case 2:
			$message = "Заявка принята в работу.";
			break;
		case 3:
			$responsible = CasUser::findOne($event->vars["responsible"]);
			$message = "Ответственным за заявку назначен " . $responsible->last_name . " " . $responsible->first_name . ".";
			break;
		case 4:
			$department = Departments::findOne($event->vars["department_id"]);
			$message = "Заявка передана в отдел " . $department->name . ".";
			break;
		case 5:
			$message = "Отказ от заявки.";
			break;
		case 6:
			$status = ApplicationsStatuses::findOne($event->vars["status_id"]);
			$message = "Установлен статус: " . $status->name . ".";
			break;
		case 7:
			$message = "Заявка исполнена.";
			break;
		case 8:
			$department = Departments::findOne($event->vars["department_id"]);
			$message = "Заявка отправлена на доработку в отдел " . $department->name . ".";
			break;
		case 9:
			$message = "Заявка закрыта.";
			break;
		default:
			$message = '';
	}
?>
<div class="application-history__element is-history">
	<span class="application-history__element-date">
		<i><?php echo SiteHelper::russianDate($event->created_at, true); ?></i>
		<i><?php echo date("H:i:s", $event->created_at); ?></i>
	</span>
	<span class="application-history__element-body">
		<i><?php echo $user_init; ?></i>: 
		<?php echo $message ?>
	</span>
</div>