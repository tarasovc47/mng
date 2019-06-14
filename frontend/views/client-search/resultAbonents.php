<div class="table-responsive">	
	<table class="table">
		<thead>
			<tr>
				<td>Номер абонента</td>

				<?php if ($fields_values['3'] == 1): ?>
					<td><?php echo $fields['3']['label'] ?></td>
				<?php endif ?>
				
				<td>Лицевой счёт</td>

				<?php if ($fields_values['4'] == 1): ?>
					<td><?php echo $fields['4']['label'] ?></td>
				<?php endif ?>

				<?php if ($fields_values['5'] == 1 || $fields_values['6'] == 1): ?>
					<td><?php echo $fields['5']['label'] ?></td>
				<?php endif ?>

				<?php if ($fields_values['7'] == 1): ?>
					<td><?php echo $fields['7']['label'] ?></td>
				<?php endif ?>

				<?php if ($fields_values['8'] == 1 || $fields_values['9'] = 1): ?>
					<td>Адрес</td>
				<?php endif ?>

				<?php if ($fields_values['10'] == 1): ?>
					<td><?php echo $fields['10']['label'] ?></td>
				<?php endif ?>

				<?php if ($fields_values['11'] == 1): ?>
					<td class="search__results__inn"><?php echo $fields['11']['label'] ?></td>
				<?php endif ?>

				<?php if ($fields_values['12'] == 1): ?>
					<td><?php echo $fields['12']['label'] ?></td>
				<?php endif ?>

				<?php if ($fields_values['13'] == 1): ?>
					<td class="search__results__services"><?php echo $fields['13']['label'] ?></td>
				<?php endif ?>
				
				<?php if ($fields_values['14'] == 1): ?>
					<td><?php echo $fields['14']['label'] ?></td>
				<?php endif ?>

				<td></td>
			</tr>
		</thead>
		<tbody>
			
			<?php foreach ($response['abonents'] as $key_abonent => $abonent): ?>
				<?php $i=1; ?>
				<?php foreach ($abonent['clients'] as $key_client => $client): ?>
					<tr class="search__results__abonent-info">
						<?php if ($i == 1): ?>
							<td rowspan="<?php echo count($abonent['clients']) ?>">
								<a href="/abonent/abonent/index?abonent=<?php echo $abonent['abonent'] ?>"><?php echo $abonent['abonent'] ?></a>
							</td>

							<?php if ($fields_values['3'] == 1): ?>
								<td rowspan="<?php echo count($abonent['clients']) ?>">
									<?php echo $abonent['abonent_name'] ?>
								</td>
							<?php endif ?>
						<?php endif ?>

						<td>
							<?php if (isset($client['f2']) && !empty($client['f2'])): ?>
								<?php echo $client['f2'] ?>
							<?php endif ?>
						</td>


						<?php if ($fields_values['4'] == 1): ?>
							<td>
								<?php if (isset($client['f6']) && !empty($client['f6'])): ?>
									<?php echo $client['f6'] ?>
								<?php endif ?>
							</td>
						<?php endif ?>

						<?php if ($fields_values['5'] == 1 || $fields_values['6'] == 1): ?>
							<td>
								<?php if ($fields_values['5'] == 1 && isset($client['oper_descr']) && !empty($client['oper_descr'])): ?>
									<p><?php echo $client['oper_descr'] ?></p>
									<?php if ($fields_values['6'] == 1 && isset($client['suboper_descr']) && !empty($client['suboper_descr'])): ?>
										<p><strong>Субпровайдер:</strong> <?php echo $client['suboper_descr'] ?></p>
									<?php endif ?> 
								<?php endif ?>
							</td>
						<?php endif ?>

						<?php if ($fields_values['7'] == 1): ?>
							<td>
								<?php if (isset($client['f3']) && !empty($client['f3'])): ?>
									<?php echo $client['f3'] ?>
								<?php endif ?>
							</td>
						<?php endif ?>

						<?php if ($fields_values['8'] == 1 || $fields_values['9'] == 1): ?>
							<td>
								<?php if ($fields_values['8'] == 1 && isset($client['f7']) && !empty($client['f7'])): ?>
									<p><strong>Юр.:</strong> <?php echo $client['f7'] ?></p>
								<?php endif ?>	
								<?php if ($fields_values['9'] == 1 && isset($client['f8']) && !empty($client['f8'])): ?>
									<p><strong>Почт.:</strong> <?php echo $client['f8'] ?></p>
								<?php endif ?>					
							</td>
						<?php endif ?>

						<?php if ($fields_values['10'] == 1): ?>
							<td>
								<?php if (isset($client['f9']) && !empty($client['f9'])): ?>
									<?php echo $client['f9'] ?>
								<?php endif ?>
							</td>
						<?php endif ?>

						<?php if ($fields_values['11'] == 1): ?>
							<td class="search__results__inn">
								<?php if (isset($client['f10']) && !empty($client['f10'])): ?>
									<?php echo $client['f10'] ?>
								<?php endif ?>	
							</td>
						<?php endif ?>

						<?php if ($fields_values['12'] == 1): ?>
							<td>
								<?php if ($client['f4'] == 1): ?>
									<p><strong>Физическое лицо в коммерческих целях</strong></p>
								<?php endif ?>
								<?php if (isset($client['client_type_descr']) && !empty($client['client_type_descr'])): ?>
									<?php echo $client['client_type_descr'] ?>
								<?php endif ?>
							</td>
						<?php endif ?>

						<?php if ($fields_values['13'] == 1): ?>
							<td class="search__results__services">
								<?php if (isset($client['f13']) && !empty($client['f13'])): ?>
									<?php foreach ($client['f13'] as $key_service => $service): ?>

										<p class="search__results__services-list"><strong><?php echo $service['service_descr'] ?>: </strong>

											<?php 
												$enabled = ''; $disabled = ''; $no_money = ''; $agreement_closed = '';
											
												foreach ($service['logins'] as $key_login => $login){
													if ($login['f3'] == 0){ 
														$disabled .= "<span class='search__results__services-login text-muted'>".$login['f2']."</span>";

													} elseif ($login['disable_reason'] == 'no money'){
														$no_money .= "<span class='search__results__services-login text-danger'>".$login['f2']."</span>";

													} elseif ($login['disable_reason'] == 'agreement closed'){
														$agreement_closed .= "<span class='search__results__services-login text-muted'>".$login['f2']."</span>";

													} else {
														$enabled .= "<span class='search__results__services-login'>".$login['f2']."</span>";
													}
												}
											 
												echo $enabled; 
												echo $no_money; 
												echo $agreement_closed;
												echo $disabled; 
											?>
											</p>
									<?php endforeach; ?>
								<?php endif ?>
							</td>
						<?php endif ?>

						<?php if ($fields_values['14'] == 1): ?>
							<td>
								<?php if (isset($client['f12']) && !empty($client['f12'])): ?>
									<?php echo $client['f12'] ?>
								<?php endif ?>
							</td>
						<?php endif ?>

						<?php if ($i == 1): ?>
							<td rowspan="<?php echo count($abonent['clients']) ?>">
								<div class="form-group">
									<a href="/abonent/docs-archive/index?DocsArchiveSearch[abonent]=<?php echo $abonent['abonent'] ?>&&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1" target="_blank" class="btn btn-info btn-sm">Документы</a>
								</div>
								<div class="form-group">
									<a href="/techsup/applications/create?abonent=<?php echo $abonent['abonent'] ?>" target="_blank" class="btn btn-success btn-sm">Тех. поддержка</a>
								</div>
							</td>
						<?php endif ?>
						

					</tr>

					<?php $i++; ?>
				<?php endforeach ?>
			<?php endforeach ?>
		</tbody>
	</table>
</div>

<?php if (!($count_abonents <= 50)): ?>
	<ul class="pagination">
		<?php if ($page == 1): ?>
			<li class="disabled"><a href="#">&laquo;</a></li>
		<?php else: ?>
			<li><a href="#" data-dest-page="<?php echo $page-1 ?>">&laquo;</i></a></li>
		<?php endif; ?>

		<?php for ($i=1; $i <= $count_abonents/50+1; $i++): ?>
			<?php if ($page == $i): ?>
				<li class="active"><span><?php echo $i ?></span></li>
			<?php else: ?>
				<li><a href="#" data-dest-page="<?php echo $i ?>"><?php echo $i ?></a></li>
			<?php endif; ?>
		<?php endfor; ?>

		<?php if ($page >= ($count_abonents/50)): ?>
			<li class="disabled"><a href="#">&raquo;</a></li>
		<?php else: ?>
			<li><a href="#" data-dest-page="<?php echo $page+1 ?>">&raquo;</a></li>
		<?php endif ?>



	</ul>
<?php endif ?>

				
				