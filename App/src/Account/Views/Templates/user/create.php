<?php $this->setSection('title', $create_user_view_model->getPageTitle())?>
<?php $this->setSection('heading', $create_user_view_model->getHeading())?>

<?php $this->setSection('back', $create_user_view_model->getBackUrl())?>

<?php $this->startSection('content')?>
<form action="<?= $create_user_view_model->getFormActionUrl()?>" method="POST">
    <?= $this->importantFormFields('put')?>
	<div class="card">
		<div class="card-body">
			<div class="mb-3">
				<?= component('label')->content('Name')->attributes(['class' => 'form-label fw-bold'])->render()?>
				<?= component('input')->attributes(['class' => 'form-control form-control-lg', 'type' => 'text', 'name' => 'name', 'required' => true, 'placeholder' => 'Enter a name'])->render()?>
                <?= component('error')->attributes(['name' => 'name'])->render()?>
			</div>
			<div class="mb-3">
				<?= component('label')->content('Email')->attributes(['class' => 'form-label fw-bold'])->render()?>
				<?= component('input')->attributes(['class' => 'form-control form-control-lg', 'type' => 'email', 'name' => 'email', 'required' => true, 'placeholder' => 'Enter an email'])->render()?>
                <?= component('error')->attributes(['name' => 'email'])->render()?>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<?= component('label')->content('Role')->attributes(['class' => 'form-label fw-bold'])->render()?>
					<?= component('select')->attributes(['class' => 'form-select form-control-lg', 'name' => 'role', 'required' => true])->options($create_user_view_model->getRolesForDropdown())->render()?>
					<?= component('error')->attributes(['name' => 'role'])->render()?>
				</div>
				<div class="col-md-6 mb-3">
					<?= component('label')->content('Gender')->attributes(['class' => 'form-label fw-bold'])->render()?>
					<?= component('select')->attributes(['class' => 'form-select form-control-lg', 'name' => 'gender', 'required' => true])->options($create_user_view_model->getGendersForDropdown())->render()?>
					<?= component('error')->attributes(['name' => 'gender'])->render()?>
				</div>
			</div>
			<div class="mb-3">
				<?= component('submit')->content('Save')->attributes(['class' => 'btn btn-primary btn-lg'])->render()?>
			</div>
		</div>
	</div>
</form>
<?php $this->endSection()?>

<?= $this->extend('user-template'); ?>