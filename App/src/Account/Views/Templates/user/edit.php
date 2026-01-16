<?php $this->setSection('title', $edit_user_view_model->getPageTitle())?>
<?php $this->setSection('heading', $edit_user_view_model->getHeading())?>

<?php $this->setSection('back', $edit_user_view_model->getBackUrl())?>

<?php $this->startSection('content')?>
<form action="<?= $edit_user_view_model->getFormActionUrl()?>" method="POST">
	<?= $this->importantFormFields('patch')?>
	<div class="card">
		<div class="card-body">
			<div class="mb-3">
				<?= component('label')->content('Name')->attributes(['class' => 'form-label fw-bold'])->render()?>
				<?= component('input')->content($edit_user_view_model->getName())->attributes(['class' => 'form-control form-control-lg', 'type' => 'text', 'name' => 'name', 'required' => true, 'placeholder' => 'Enter a name'])->render()?>
                <?= component('error')->attributes(['name' => 'name'])->render()?>
			</div>
			<div class="mb-3">
				<?= component('label')->content('Email')->attributes(['class' => 'form-label fw-bold'])->render()?>
				<?= component('input')->content($edit_user_view_model->getEmail())->attributes(['class' => 'form-control form-control-lg', 'type' => 'email', 'name' => 'email', 'required' => true, 'placeholder' => 'Enter an email'])->render()?>
				<?= component('error')->attributes(['name' => 'email'])->render()?>
			</div>
			<div class="row">
				<div class="col-md-4 mb-3">
					<?= component('label')->content('Role')->attributes(['class' => 'form-label fw-bold'])->render()?>
					<?= component('select')->attributes(['class' => 'form-select form-control-lg', 'name' => 'role', 'required' => true])->options($edit_user_view_model->getRolesForDropdown())->selected($edit_user_view_model->getRoleId())->render()?>
					<?= component('error')->attributes(['name' => 'role'])->render()?>
				</div>
				<div class="col-md-4 mb-3">
					<?= component('label')->content('Gender')->attributes(['class' => 'form-label fw-bold'])->render()?>
					<?= component('select')->attributes(['class' => 'form-select form-control-lg', 'name' => 'gender', 'required' => true])->options($edit_user_view_model->getGendersForDropdown())->selected($edit_user_view_model->getGender())->render()?>
					<?= component('error')->attributes(['name' => 'gender'])->render()?>
				</div>
				<div class="col-md-4 mb-3">
					<?= component('label')->content('Status')->attributes(['class' => 'form-label fw-bold'])->render()?>
					<?= component('select')->attributes(['class' => 'form-select form-control-lg', 'name' => 'status', 'required' => true])->options($edit_user_view_model->getStatusForDropdown())->selected($edit_user_view_model->getStatus())->render()?>
					<?= component('error')->attributes(['name' => 'status'])->render()?>
				</div>
			</div>
			<div class="mb-3">
				<?= component('submit')->content('Update')->attributes(['class' => 'btn btn-primary btn-lg'])->render()?>
			</div>
		</div>
	</div>
</form>
<?php $this->endSection()?>

<?= $this->extend('user-template'); ?>