<?php
	use common\components\SiteHelper;

	// Передача данных из PHP в JS
	echo SiteHelper::dataFromPHPtoJS('tariffPlans', $tariffPlans);
	echo SiteHelper::dataFromPHPtoJS('providers', $providers);
	echo SiteHelper::dataFromPHPtoJS('subproviders', $subproviders);
	echo SiteHelper::dataFromPHPtoJS('clientTypes', $clientTypes);
	echo SiteHelper::dataFromPHPtoJS('services', $services);
	echo SiteHelper::dataFromPHPtoJS('criterions', $criterions);

	$h1 = "Поиск абонентов";

	$this->params['breadcrumbs'][] = $h1;
?>
<h1 class="page-header"><?php echo $h1 ?></h1>
<div class="search__area">
	<div class="search__levels">
		<div class="form-inline">
			<div class="checkbox search__only-active-logins hidden">
			    <label>
			          <input type="checkbox"> Искать только в активных сервисах
			        </label>
			  </div>

			<div class="search__level search__search-level-first">
				<div class="form-group">
					<select name="search__criterion" class="form-control search__criterion">
						<?php foreach ($criterions as $criterion): ?>
							<option value="<?php echo $criterion['source'].'.'.$criterion['criterion'] ?>" data-criterion = "<?php echo $criterion['criterion'] ?>" data-condition="<?php echo $criterion['condition'] ?>" data-source="<?php echo $criterion['source'] ?>"><?php echo $criterion['descr'] ?></option>
						<?php endforeach ?>
					</select>
				</div>

				<div class="search__query-zone">
					<div class="form-group">
						<input type="text" name="request" class="form-control search__default" placeholder="Введите запрос...">
					</div>
					<button class="btn btn-default hidden search__delete-level-button" type="button"><i class="fa fa-close"></i></button>
					<button type="button" class="btn btn-default search__add-level-button"><i class="fa fa-plus"></i></button>
				</div>
 
				<div class="search__hidden-helper-fields">
					<input type="text" name="hidden-helper" class="search__default-helper hidden">

					<div class="search__address-helper">
						<input type="text" data-address="city" class="hidden" value="Тюмень">
						<input type="text" data-address="avenue" class="hidden">
						<input type="text" data-address="building" class="hidden">
						<input type="text" data-address="housing" class="hidden">
						<input type="text" data-address="apartment" class="hidden">
					</div>
				</div>
			</div> 

			<button type="button" class="btn btn-primary search__search-button" >Искать</button>
			<button type="button" class="btn btn-link search__clear-button" >Очистить форму</button>
		</div>
	</div>
	
	<div class="search__result"></div>
</div>
	
