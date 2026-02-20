<?php $this->setSection('title', $edit_role_view_model->getPageTitle()) ?>
<?php $this->setSection('heading', $edit_role_view_model->getHeading()) ?>

<?php $this->setSection('back', $edit_role_view_model->getBackUrl()) ?>

<?php $this->startSection('content') ?>
<form method="POST" action="<?= $edit_role_view_model->getFormActionUrl() ?>">
	<?= $this->importantFormFields('patch') ?>

	<div class="card">
		<div class="card-header border-bottom">
			<div class="fw-bold">Edit Role Details</div>
		</div>
		<div class="card-body">
			<div class="mb-3">
				<?= component('label')->content('Name')->attributes(['class' => 'form-label fw-bold'])->render() ?>
				<?= component('input')->content($edit_role_view_model->getRoleName())->attributes(['class' => 'form-control form-control-lg', 'type' => 'text', 'name' => 'name', 'required' => true, 'placeholder' => 'Enter role name'])->render() ?>
			</div>

			<div class="mb-3">
				<?= component('label')->content('Description')->attributes(['class' => 'form-label fw-bold'])->render() ?>
				<?= component('textarea')->content($edit_role_view_model->getRoleDescription())->attributes(['class' => 'form-control form-control-lg', 'name' => 'description', 'required' => true, 'placeholder' => 'Enter role description'])->render() ?>
			</div>
		</div>

		<div class="card-header border-top">
			<div class="fw-bold">Permission</div>
			<div class="small text-muted">Toggle capabilities for this role. Changes take effect on next login.</div>
		</div>

		<div class="table-responsive">
			<table class="table table-bordered table-striped mb-0">
				<tbody>
					<?php foreach ($edit_role_view_model->getPermissionRegistry() as $category => $permissions): ?>
						<tr>
							<td>
								<div class="row">
									<div class="col">
										<h5 class="mb-2 fw-bold"><?= $category ?></h5>
									</div>
								</div>
								<div class="row">
									<?php foreach ($permissions as $slug => $label): ?>
										<?php
                                        $is_section = str($slug)->contains('.section')->get();
									    $is_manage = str($slug)->contains('.manage')->get();
									    $class = 'form-check-input p-2 me-2 pointer permission-checkbox';
									    if ($is_section) {
									        $class .= ' permission-section';
									    } elseif ($is_manage) {
									        $class .= ' permission-manage';
									    } else {
									        $class .= ' permission-action';
									    }
									    ?>
										<div class="<?= $is_section ? 'col-12' : 'col-lg-3 col-6' ?> <?= $is_section ? 'mb-2' : '' ?>">
											<div class="form-check m-1">
												<?= component('checkbox')->attributes([
									                'class' => $class,
									                'name' => 'permission[]',
									                'value' => $slug,
									                'id' => 'perm-' . str_replace('.', '-', $slug),
									                'checked' => $edit_role_view_model->isPermissionChecked($slug)
									            ])->render() ?>
												<?= component('label')->content($label)->attributes([
									                'class' => 'form-check-label pointer',
									                'for' => 'perm-' . str_replace('.', '-', $slug)
									            ])->render() ?>
											</div>
										</div>
									<?php endforeach ?>
								</div>
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</div>
		<div class="card-footer bg-light">
			<?= component('submit')->content('Update Role')->attributes(['class' => 'btn btn-lg btn-primary'])->render() ?>
		</div>
	</div>
</form>

<?php $this->endSection() ?>

<?php $this->startSection('script') ?>
	<?= $this->include('role.inc.permission-checkbox')?>
<?php $this->endSection() ?>

<?= $this->extend('user-template'); ?>