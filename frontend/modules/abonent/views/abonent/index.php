<?php
	
	use common\models\ClientSearch;

	if (isset($abonentData) && !empty($abonentData)) {
		$h1 = "Карточка абонента ".$abonentData['abonent']['id'];
	} elseif (isset($clientData) && !empty($clientData)) {
		$h1 = "Карточка лицевого счёта ".$clientData['client_id'];
	}
	
	use yii\helpers\Html;
	$this->params['breadcrumbs'][] = $h1;
?>
<h1 class="page-header"><?=$h1?></h1>

<?php echo $abonentLeftMenu; ?>

<?php if (isset($abonentData) && !empty($abonentData)): ?>
	<div class="abonent__abonent">
		<p class="h4" data-abonent="<?php echo $abonentData['abonent']['id'] ?>">Абонент <?php echo $abonentData['abonent']['id'] ?>, <?php echo $abonentData['abonent']['name'] ?></p>
		<ul class="nav nav-tabs">

		<?php $active = true; ?>
		<?php foreach ($abonentData['base_clients'] as $key_client_ids => $client_ids): ?>
			<li class="abonent__tab-client-id <?php if ($active): ?> active <?php endif; $active = false; ?>">
				<a href="#" data-client-id="<?php echo $client_ids['client_id'] ?>"><?php echo $client_ids['client_id'] ?></a>
			</li>
		<?php endforeach ?>

		</ul>

		<?php $active = true; ?>
		<div class="abonent__tab-content-client-id tab-content">
			<?php foreach ($abonentData['base_clients'] as $key_clients_ids => $clients_id): ?>
				<div class="tab-pane client-id-tab <?php if ($active): ?> active <?php endif; $active = false; ?>" data-client-id="<?php echo $clients_id['client_id'] ?>">

					<table class="table table-condensed">
						<thead>
							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><strong>Лицевой счёт</strong></td>
								<td data-client-id="<?php echo $clients_id['client_id'] ?>"><?php echo $clients_id['client_id'] ?></td>
							</tr>
							<tr>
								<td><strong>Имя/название</strong></td>
								<td><?php echo $clients_id['name'] ?></td>
							</tr>
							<tr>
								<td><strong>Тип клиента</strong></td>
								<td><?php echo $clients_id['client_type_descr'] ?></td>
							</tr>
							<tr>
								<td><strong>Баланс</strong></td>
								<td><?php echo $clients_id['balance'] ?></td>
							</tr>
							<tr>
								<td><strong>Контактный телефон</strong></td>
								<td><?php echo $clients_id['contact_phone'] ?></td>
							</tr>
							<tr>
								<td><strong>Провайдер</strong></td>
								<td><?php echo $clients_id['provider'] ?></td>
							</tr>
							<tr>
								<td><strong>Субпровайдер</strong></td>
								<td><?php echo $clients_id['subprovider'] ?></td>
							</tr>
							<tr>
								<td><strong>Юридический адрес</strong></td>
								<td><?php echo $clients_id['address_jur'] ?></td>
							</tr>
							<tr>
								<td><strong>Фактический адрес</strong></td>
								<td><?php echo $clients_id['address_post'] ?></td>
							</tr>
							<tr>
								<td><strong>ИНН</strong></td>
								<td><?php echo $clients_id['inn'] ?></td>
							</tr>
							
							
						</tbody>
					</table>
				</div>
			<?php endforeach ?>
		</div>

		<?php if (isset($clients_id['services']) && !empty($clients_id['services'])): ?>
			<?php $i = 0; // счетчик коллапсов ?>
			<?php foreach ($abonentData['base_clients'] as $key_clients_ids => $clients_id): ?>
				<div class="panel-group" id="abonent__services-accordion" role="tablist" aria-multiselectable="true">
					<?php foreach ($clients_id['services'] as $key_service => $service): ?>
					
						<div class="panel panel-info">
							<div class="panel-heading" role="tab" id="heading__<?php echo $i.'-'.$clients_id['client_id']; ?>">
						      <h4 class="panel-title">
						        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse__<?php echo $i.'-service'; ?>" aria-expanded="true" aria-controls="collapse__<?php echo $i.'-service'; ?>">
						          <?php echo $clients_id['client_id']." &ndash; ".$key_service."" ?>
						        </a>
						      </h4>
						    </div>

						    <div id="collapse__<?php echo $i.'-service'; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading__<?php echo $i.'-service'; ?>">
							    <div class="panel-body">
							        <table class="table">
										<thead>
											<tr>
												<td>Логин</td>
												<td>Тарифный план</td>
												<td>Порог отключения</td>
												<td>Дата начала оказания услуги</td>
												<td>Дата завершения оказания услуги</td>
												<td>Адрес оказания услуги</td>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($service as $key_user_id => $user_id): ?>
												<tr class=" 
													<?php 
														if (($user_id['enabled'] == 0) || ((strtotime($user_id['date_expire']) != '' ) && strtotime($user_id['date_expire']) <= time() )) {
															echo 'active';
														} elseif ($clients_id['balance'] <= $user_id['water_mark']) {
															echo 'danger';
														} else {
															echo 'success';
														}
													?>
												">
													<td data-user-id="<?php echo $user_id['user_id']; ?>" data-enabled="<?php echo $user_id['enabled'] ?>"><?php echo $user_id['user_id']; ?></td>
													<td><?php echo $user_id['tariff_plan']; ?></td>
													<td><?php echo $user_id['water_mark']; ?></td>
													<td><?php echo $user_id['date_create']; ?></td>
													<td><?php echo $user_id['date_expire'];?></td>
													<td><?php echo $user_id['address_kladr'];?></td>
												</tr>
											<?php endforeach ?>
										</tbody>
									</table>
							    </div>
							</div>
						</div>
						<?php $i++; ?>
					<?php endforeach ?>
				</div>
				
			<?php endforeach ?>
		<?php endif ?>
	</div>
<?php endif ?>

<?php if (isset($clientData) && !empty($clientData)): ?>
	<div class="abonent__client">
		<p class="h4" data-client-id="<?php echo $clientData['client_id'] ?>">Лицевой счет <?php echo $clientData['client_id']?>, <?php echo $clientData['name'] ?></p>
		<div class="client-id-tab">
			<table class="table table-condensed">
				<thead>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><strong>Тип клиента</strong></td>
						<td><?php echo $clientData['client_type_descr'] ?></td>
					</tr>
					<tr>
						<td><strong>Баланс</strong></td>
						<td><?php echo $clientData['balance'] ?></td>
					</tr>
					<tr>
						<td><strong>Контактный телефон</strong></td>
						<td><?php echo $clientData['contact_phone'] ?></td>
					</tr>
					<tr>
						<td><strong>Провайдер</strong></td>
						<td><?php echo $clientData['provider'] ?></td>
					</tr>
					<tr>
						<td><strong>Субпровайдер</strong></td>
						<td><?php echo $clientData['subprovider'] ?></td>
					</tr>
					<tr>
						<td><strong>Юридический адрес</strong></td>
						<td><?php echo $clientData['address_jur'] ?></td>
					</tr>
					<tr>
						<td><strong>Фактический адрес</strong></td>
						<td><?php echo $clientData['address_post'] ?></td>
					</tr>
					<tr>
						<td><strong>ИНН</strong></td>
						<td><?php echo $clientData['inn'] ?></td>
					</tr>
					
					
				</tbody>
			</table>
		</div>	

		<?php if (isset($clientData['services']) && !empty($clientData['services'])): ?>
			<?php $i = 0; // счетчик коллапсов ?>
			<div class="panel-group" id="abonent__services-accordion" role="tablist" aria-multiselectable="true">
				<?php foreach ($clientData['services'] as $key_service => $service): ?>
				
					<div class="panel panel-info">
						<div class="panel-heading" role="tab" id="heading__<?php echo $i.'-service'; ?>">
					      <h4 class="panel-title">
					        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse__<?php echo $i.'-service'; ?>" aria-expanded="true" aria-controls="collapse__<?php echo $i.'-service'; ?>">
					          <?php echo $key_service ?>
					        </a>
					      </h4>
					    </div>

					    <div id="collapse__<?php echo $i.'-service'; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading__<?php echo $i.'-service'; ?>">
						    <div class="panel-body">
						        <table class="table">
									<thead>
										<tr>
											<td>В заявку</td>
											<td>Логин</td>
											<td>Тарифный план</td>
											<td>Порог отключения</td>
											<td>Дата начала оказания услуги</td>
											<td>Дата завершения оказания услуги</td>
											<td>Адрес оказания услуги</td>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($service as $key_user_id => $user_id): ?>
											<tr class=" 
												<?php 
													if (($user_id['enabled'] == 0) || ((strtotime($user_id['date_expire']) != '' ) && $user_id['date_expire'] <= time() )) {
														echo 'active';
													} elseif ($clientData['balance'] <= $user_id['water_mark']) {
														echo 'danger';
													} else {
														echo 'success';
													}
												?>
											">
												<td><?= Html::checkbox('create_application', false); ?></td>
												<td data-user-id="<?php echo $user_id['user_id']; ?>" data-enabled="<?php echo $user_id['enabled'] ?>"><?php echo $user_id['user_id']; ?></td>
												<td><?php echo $user_id['tariff_plan']; ?></td>
												<td><?php echo $user_id['water_mark']; ?></td>
												<td><?php echo $user_id['date_create']; ?></td>
												<td><?php echo $user_id['date_expire'];?></td>
												<td><?php echo $user_id['address_kladr'];?></td>
											</tr>
										<?php endforeach ?>
									</tbody>
								</table>
						    </div>
						</div>
					</div>
					<?php $i++; ?>
				<?php endforeach ?>
			</div>	
		<?php endif ?>	
	</div>
<?php endif ?>