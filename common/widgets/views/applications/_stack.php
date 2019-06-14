<?php
	use common\widgets\Applications;

	$id = str_replace("/", "", $stack_id);
?>
<div class="panel panel-default">
	<div class="panel-heading application-stack-id">
		<a data-toggle="collapse" href="#<?php echo $id; ?>"><?php echo $stack_id; ?></a>
		<?php foreach($applications as $application): ?>
            <?php if($application->applicationsStack->id == $stack_id): ?>
            	<div class="application-short" data-id="<?= $application->id; ?>">
					<?php echo Applications::short($application, $template, $clients, $attributes_repository); ?>
            	</div>
            <?php endif ?>
        <?php endforeach; ?>
	</div>
	<div id="<?php echo $id; ?>" class="panel-collapse collapse">
		<div class="panel-body">
			<?php foreach($applications as $application): ?>
				<?php if($application->applicationsStack->id == $stack_id): ?>
	            	<div class="application-container" data-id="<?php echo $application->id; ?>">
						<?php echo Applications::application($application, $template, $user, $clients, $attributes_repository, $properties_repository); ?>
	            	</div>
	            <?php endif ?>
	        <?php endforeach ?>
		</div>
	</div>
</div>