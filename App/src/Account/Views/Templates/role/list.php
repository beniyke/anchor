<?php
$this->setSection('title', $role_list_view_model->getPageTitle());
$this->setSection('heading', $role_list_view_model->getHeading());
?>

<?php if ($role_list_view_model->hasRoles() && $this->canAccessAction('create')): ?>
	<?php $this->setSection('action', component('link')->content('<span class="fa fa-plus"></span> Create Role')->data(['href' => $role_list_view_model->getCreateActionUrl()])->attributes(['class' => 'btn btn-primary btn-lg'])->render()) ?>
<?php endif ?>

<?php $this->startSection('content') ?>
<div class="card">
	<?php if (! $role_list_view_model->hasRoles()): ?>
		<?= component('no-result')->data($role_list_view_model->getNoResultComponentData())->render() ?>
	<?php else: ?>
		<div class="table-responsive">
			<table class="table table-striped table-hover text-nowrap">
				<thead>
					<th>#</th>
					<th>Role</th>
					<th>&nbsp;</th>
					<th class="text-end">Date</th>
				</thead>
				<tbody>
					<?php $sn = 0; ?>
					<?php foreach ($role_list_view_model->getRolesItems() as $role): ?>
						<tr>
							<td style="width: 20px;"><?= ($sn += 1) ?>.</td>
							<td>
								<div class="fw-bold"><?= $role->getName() ?></div>
								<div class="text-muted text-sm"><?=$role->getDescription()?></div>
							</td>
							<td class="table-action">
								<?php if ($this->canAccessAction('edit|delete')):?>
								<div class="dropdown position-relative">
									<button type="button" class="btn btn-outline-primary btn-lg dropdown-toggle fw-bold" data-bs-toggle="dropdown" aria-expanded="false">
										Action
									</button>
									<ul class="dropdown-menu">
										<?php if ($this->canAccessAction('edit')):?>
										<li>
											<?= component('link')->content('Edit')->data(['href' => $role_list_view_model->getEditActionUrl($role->getSlug())])->attributes(['class' => 'dropdown-item text-primary'])->render() ?>
										</li>
										<?php endif?>

										<?php if ($role->canBeDelete() && $this->canAccessAction('delete')):?>
										<li>
											<?= component('delete')->content('Delete')->data(['url' => $role_list_view_model->getDeleteActionUrl($role->getSlug()), 'important-fields' => $this->importantFormFields('delete')])->attributes(['class' => 'dropdown-item text-danger'])->render() ?>
										</li>
										<?php endif?>
									</ul>
								</div>
								<?php else:?>
									<button class="btn btn-outline-secondary disabled btn-lg"><span class="fa fa-lock"></span> Locked</button>
								<?php endif?>
							</td>
							<td class="text-end text-muted">
								<?= $role->getFormattedUpdatedAt() ?>
								<div class="small fw-medium">Last updated</div>
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</div>
		<div class="card-footer">
			<?= component('pagination')->data(['paginator' => $role_list_view_model->getRoles()])->render() ?>
		</div>
	<?php endif ?>
</div>
<?php if ($this->canAccessAction('delete')):?>
<?= $this->layout()->modal('confirm'); ?>
<?php endif?>
<?php $this->endSection() ?>

<?php if ($this->canAccessAction('delete')):?>
<?php $this->startSection('script') ?>
	<script src="<?= assets('js/confirm.js') ?>"></script>
<?php $this->endSection() ?>
<?php endif?>

<?= $this->extend('user-template'); ?>