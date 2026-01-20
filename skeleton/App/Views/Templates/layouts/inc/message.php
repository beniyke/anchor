<?php if (flash()->hasSuccess()): ?>
	<div class="alert alert-success alert-dismissible flash-message" role="alert">
		<div class="alert-icon">
			<i class="align-middle me-1" data-feather="bell"></i>
		</div>
		<div class="alert-message">
			<?= flash()->getSuccess() ?>
		</div>
		<div class="px-3">
			<a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
		</div>
	</div>
<?php endif; ?>

<?php if (flash()->hasError()): ?>
	<div class="alert alert-danger alert-dismissible flash-message" role="alert">
		<div class="alert-icon">
			<i class="align-middle me-1" data-feather="bell"></i>
		</div>
		<div class="alert-message">
			<?php $errors = flash()->getError(); ?>
			<?php if (is_array($errors)): ?>
				<ul class="mb-0">
					<?php foreach ($errors as $error): ?>
						<?php foreach ($error as $message): ?>
							<li><?= $message ?></li>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<?= $errors ?>
			<?php endif; ?>
		</div>
		<div class="px-3">
			<a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
		</div>
	</div>
<?php endif; ?>