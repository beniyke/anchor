<?php $this->setSection('title', $resetpassword_view_model->page_title)?>
<?php $this->setSection('heading', $resetpassword_view_model->heading)?>
<?php $this->setSection('subheading', $resetpassword_view_model->subheading)?>

<?php $this->startSection('content')?>
<form action="<?= $resetpassword_view_model->getFormActionUrl()?>" method="POST">
    <?= $this->csrf()?>
    <?= $this->method('put')?>
    <?= $this->callback()?>
    <div class="mb-3">
        <?= component('label')->content('New Password')->attributes(['class' => 'form-label fw-bold'])->render()?>
        <div class="input-group input-group-flat <?= $resetpassword_view_model->getErrorClass('new_password')?>">
            <?= component('input')->attributes(['class' => 'form-control form-control-lg', 'type' => 'password', 'placeholder' => 'Enter new password', 'autocomplete' => 'off', 'id' => 'password', 'name' => 'new_password', 'required' => true])->flagIf($resetpassword_view_model->hasError('new_password'))->render()?>
            <span class="input-group-text <?= $resetpassword_view_model->hasError('new_password') ? 'border-danger' : ''?>">
                <?= component('link')->content('<span data-feather="eye"></span>')->attributes(['class' => 'link-secondary show-p', 'data-field' => '#password', 'data-bs-toggle' => 'tooltip', 'aria-label' => 'Show password', 'data-bs-original-title' => 'Show password'])->render()?>
            </span>
        </div>
       <?= component('error')->attributes(['name' => 'new_password'])->render()?>
    </div>
    <div class="mb-3">
         <?= component('label')->content('Confirm Password')->attributes(['class' => 'form-label fw-bold'])->render()?>
        <div class="input-group input-group-flat <?= $resetpassword_view_model->getErrorClass('confirm_password')?>">
            <?= component('input')->attributes(['class' => 'form-control form-control-lg', 'type' => 'password', 'placeholder' => 'Confirm new password', 'autocomplete' => 'off', 'id' => 'confirm-password', 'name' => 'confirm_password', 'required' => true])->flagIf($resetpassword_view_model->hasError('confirm_password'))->render()?>
            <span class="input-group-text <?= $resetpassword_view_model->hasError('confirm_password') ? 'border-danger' : ''?>">
                <?= component('link')->content('<span data-feather="eye"></span>')->attributes(['class' => 'link-secondary show-p', 'data-field' => '#confirm-password', 'data-bs-toggle' => 'tooltip', 'aria-label' => 'Show password', 'data-bs-original-title' => 'Show password'])->render()?>
            </span>
        </div>
        <?= component('error')->attributes(['name' => 'confirm_password'])->render()?>
    </div>
    <div class="mb-3">
        <?= component('submit')->content('Save Password')->attributes(['class' => 'btn btn-primary btn-lg w-100'])->render()?>
    </div>
</form>
<?php $this->endSection()?>

<?= $this->extend('auth-template')?>