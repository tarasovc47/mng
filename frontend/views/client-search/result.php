<?php if ($error): ?>
	<div class = 'alert alert-danger'><?php echo $error ?></div>
<?php endif ?>

<?php if ($response && (!(empty($response['clients'])) || !(empty($response['abonents'])))): ?>
	<!-- Nav tabs -->
	<div class="search__results-panel">
		<ul class="nav nav-tabs">
			<?php if (isset($response['abonents']) && !empty($response['abonents'])): ?>
				<li class="search__tab active"><a href="#abonents" data-toggle="tab">Абоненты</a></li>
			<?php endif; ?>

			<?php if (isset($response['clients']) && !empty($response['clients'])): ?>
				<li class="search__tab 
					<?php if (empty($response['abonents'])): ?>
						active
					<?php endif ?>
				"><a href="#clients" data-toggle="tab">Лицевые счета без привязки к абоненту</a></li>
			<?php endif; ?>
		</ul>
	<!-- Tab panes -->
		<div id="search__results-panel" class="tab-content">
			<?php if (isset($response['abonents']) && !empty($response['abonents'])): ?>
		   		<div class='tab-pane active' data-tab='abonents' id="abonents">

					<?php echo $abonents ?>

				</div>
			<?php endif ?>
			
			<?php if (isset($response['clients']) && !empty($response['clients'])): ?>
		   		<div class="tab-pane 
		   			<?php if (empty($response['abonents'])): ?>
						active
					<?php endif ?>
		   		" data-tab='clients' id="clients">

		   			<?php echo $clients ?>
								
	   			</div>
	    	<?php endif ?>
	    </div>
	</div>
	
<?php endif ?>