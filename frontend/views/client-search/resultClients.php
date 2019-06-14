<div class="table-responsive">	
	<table class="table">
		<thead>
			<tr>
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

			<?php foreach ($response['clients'] as $client): ?>
				<tr>
					<td>
						<?php if (isset($client['client_id']) && !empty($client['client_id'])): ?>
							<a href="/abonent/abonent/index?client_id=<?php echo $client['client_id'] ?>"><?php echo $client['client_id'] ?></a>
						<?php endif ?>	
					</td>

					<?php if ($fields_values['4'] == 1): ?>
						<td>
							<?php if (isset($client['name']) && !empty($client['name'])): ?>
								<?php echo $client['name'] ?>
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
							<?php if (isset($client['contact_phone']) && !empty($client['contact_phone'])): ?>
								<?php echo $client['contact_phone'] ?>
							<?php endif ?>
						</td>
					<?php endif ?>

					<?php if ($fields_values['8'] == 1 || $fields_values['9'] == 1): ?>
						<td>
							<?php if ($fields_values['8'] == 1 && isset($client['address_jur']) && !empty($client['address_jur'])): ?>
								<p><strong>Юр.:</strong> <?php echo $client['address_jur'] ?></p>
							<?php endif ?>	
							<?php if ($fields_values['9'] == 1 && isset($client['address_post']) && !empty($client['address_post'])): ?>
								<p><strong>Почт.:</strong> <?php echo $client['address_post'] ?></p>
							<?php endif ?>					
						</td>
					<?php endif ?>

					<?php if ($fields_values['10'] == 1): ?>
						<td>
							<?php if (isset($client['passport']) && !empty($client['passport'])): ?>
								<?php echo $client['passport'] ?>
							<?php endif ?>
						</td>
					<?php endif ?>

					<?php if ($fields_values['11'] == 1): ?>
						<td>
							<?php if (isset($client['inn']) && !empty($client['inn'])): ?>
								<?php echo $client['inn'] ?>
							<?php endif ?>
						</td>
					<?php endif ?>

					<?php if ($fields_values['12'] == 1): ?>
						<td>
							<?php if ($client['person_use_srv_as_org'] == 1): ?>
								<p><strong>Физическое лицо в коммерческих целях</strong></p>
							<?php endif ?>
							<?php if (isset($client['client_type_descr']) && !empty($client['client_type_descr'])): ?>
								<?php echo $client['client_type_descr'] ?>
							<?php endif ?>
						</td>
					<?php endif ?>

					<?php if ($fields_values['13'] == 1): ?>
						<td class="search__results__services">
							<?php if (isset($client['logins']) && !empty($client['logins'])): ?>
								<?php foreach ($client['logins'] as $key_service => $service): ?>

									<p class="search__results__services-list"><strong><?php echo $service['service_descr'] ?>: </strong>

									<?php 
										$enabled = ''; $disabled = ''; $no_money = ''; $agreement_closed = '';
									
										foreach ($service['logins'] as $key_login => $login){
											if ($login['f3'] == 0){ 
												$disabled .= "<span class='search__results__services-login text-muted'>".$login['f2']."&nbsp;&nbsp;</span>";

											} elseif ($login['disable_reason'] == 'no money'){
												$no_money .= "<span class='search__results__services-login text-danger'>".$login['f2']."&nbsp;&nbsp;</span>";

											} elseif ($login['disable_reason'] == 'agreement closed'){
												$agreement_closed .= "<span class='search__results__services-login text-muted'>".$login['f2']."&nbsp;&nbsp;</span>";

											} else {
												$enabled .= "<span class='search__results__services-login'>".$login['f2']."&nbsp;&nbsp;</span>";
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
							<?php if (isset($client['balance']) && !empty($client['balance'])): ?>
								<?php echo $client['balance'] ?>
							<?php endif ?>
						</td>
					<?php endif ?>

					<td>
						<div class="form-group">
							<a href="/abonent/docs-archive/index?DocsArchiveSearch[client_id]=<?php echo $client['client_id'] ?>&&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1" target="_blank" class="btn btn-info btn-sm">Документы</a>
						</div>
						<div class="form-group">
							<a href="/techsup/applications/create?client=<?php echo $client['client_id'] ?>" target="_blank" class="btn btn-success btn-sm">Тех. поддержка</a>
						</div>
					</td>

				</tr>
			<?php endforeach ?>
		    </div>
		</tbody>
	</table>
</div>	

<?php if (!($count_clients <= 50)): ?>
	<ul class="pagination">
		<?php if ($page == 1): ?>
			<li class="disabled"><a href="#">&laquo;</a></li>
		<?php else: ?>
			<li><a href="#" data-dest-page="<?php echo $page-1 ?>">&laquo;</i></a></li>
		<?php endif; ?>

		<?php for ($i=1; $i <= $count_clients/50+1; $i++): ?>
			<?php if ($page == $i): ?>
				<li class="active"><span><?php echo $i ?></span></li>
			<?php else: ?>
				<li><a href="#" data-dest-page="<?php echo $i ?>"><?php echo $i ?></a></li>
			<?php endif; ?>
		<?php endfor; ?>

		<?php if ($page >= ($count_clients/50)): ?>
			<li class="disabled"><a href="#">&raquo;</a></li>
		<?php else: ?>
			<li><a href="#" data-dest-page="<?php echo $page+1 ?>">&raquo;</a></li>
		<?php endif ?>
	</ul>
<?php endif ?>				
