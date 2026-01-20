<?php if ($layout->getUser()?->hasMissingProfileFields()): ?>
	<div class="alert alert-warning alert-dismissible" role="alert">
		<div class="alert-icon">
			<i class="align-middle me-1" data-feather="alert-triangle"></i>
		</div>
		<div class="alert-message">
			Incomplete profile: Please update your
			<?= component('link')->content(str($layout->getUser()->getMissingProfileFields())->prettyImplode()->get())->data(route() != route_name('profile') ? ['href' => url(route_name('profile'))] : [])->attributes(['class' => 'text-dark fw-bold'])->render() ?>
		</div>
	</div>
<?php endif; ?>