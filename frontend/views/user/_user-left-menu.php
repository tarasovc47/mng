<div class="list-group profile__left-menu">
	<?php foreach ($items as $key_item => $item): ?>
		<a href="<?php echo $item['url'] ?>" class="list-group-item <?php if ($item['url'] == $currentUrl): ?> active <?php endif ?>"><?php echo $item['label'] ?></a>
	<?php endforeach ?>
</div>