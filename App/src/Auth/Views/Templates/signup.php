<?php $this->setSection('title', $signup_view_model->page_title)?>
<?php $this->setSection('heading', $signup_view_model->heading)?>
<?php $this->setSection('subheading', $signup_view_model->subheading)?>

<?php $this->startSection('content')?>
<form action="<?= $signup_view_model->getFormActionUrl()?>" method="POST">

    <div class="mb-3">
        <?= component('label')->content('Name')->attributes(['class' => 'form-label fw-bold'])->render()?>
        <?= component('input')->attributes(['type' => 'text', 'name' => 'name', 'required' => true, 'placeholder' => 'Enter your Name', 'autocomplete' => 'off', 'class' => 'form-control form-control-lg'])->render()?>
        <?= component('error')->attributes(['name' => 'name'])->render()?>
    </div>

    <div class="mb-3">
        <?= component('label')->content('Gender')->attributes(['class' => 'form-label fw-bold'])->render()?>
        <?= component('select')->attributes(['class' => 'form-select form-control-lg', 'name' => 'gender', 'required' => true])->options(['male' => 'Male', 'female' => 'Female'])->render()?>
        <?= component('error')->attributes(['name' => 'gender'])->render()?>
    </div>

    <div class="mb-3">
        <?= component('label')->content('Email Address')->attributes(['class' => 'form-label fw-bold'])->render()?>
        <?= component('input')->attributes(['class' => 'form-control form-control-lg', 'type' => 'email', 'name' => 'email', 'required' => true, 'placeholder' => 'Enter your email address', 'autocomplete' => 'off'])->render()?>
       <?= component('error')->attributes(['name' => 'email'])->render()?>
    </div>

    <div class="mb-3">
        <?= component('label')->content('Password')->attributes(['class' => 'form-label fw-bold'])->render()?>
        <div class="input-group input-group-flat <?= $signup_view_model->getErrorClass('password')?>">
            <?= component('input')->attributes(['class' => 'form-control form-control-lg', 'type' => 'password', 'placeholder' => 'Enter your password', 'autocomplete' => 'off', 'id' => 'password', 'name' => 'password', 'required' => true])->render()?>

            <span class="input-group-text <?= $signup_view_model->hasError('password') ? 'border-danger' : ''?>">
                <?= component('link')->content('<span data-feather="eye"></span>')->attributes(['class' => 'link-secondary show-p', 'data-field' => '#password', 'data-bs-toggle' => 'tooltip', 'aria-label' => 'Show password', 'data-bs-original-title' => 'Show password'])->render()?>
            </span>
        </div>
       <?= component('error')->attributes(['name' => 'password'])->render()?>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Confirm Password</label>
        <div class="input-group input-group-flat <?= $signup_view_model->getErrorClass('confirm_password')?>">
            <?= component('input')->attributes(['class' => 'form-control form-control-lg', 'type' => 'password', 'placeholder' => 'Confirm your password', 'autocomplete' => 'off', 'id' => 'confirm-password', 'name' => 'confirm_password', 'required' => true])->render()?>

            <span class="input-group-text <?= $signup_view_model->hasError('confirm_password') ? 'border-danger' : ''?>">
                <?= component('link')->content('<span data-feather="eye"></span>')->attributes(['class' => 'link-secondary show-p', 'data-field' => '#confirm-password', 'data-bs-toggle' => 'tooltip', 'aria-label' => 'Show password', 'data-bs-original-title' => 'Show password'])->render()?>
            </span>
        </div>
        <?= component('error')->attributes(['name' => 'confirm_password'])->render()?>
    </div>

    <div class="mb-3">
        <?= $this->csrf()?>
        <?= component('submit')->content('Sign Up')->attributes(['class' => 'btn btn-primary btn-lg w-100'])->render()?>
    </div>
</form>
<div class="text-center text-muted my-3">
    Already have account? <?= component('link')->content('Log In')->data(['href' => $signup_view_model->getLoginUrl()])->attributes(['class' => 'fw-bold'])->render()?>
</div>
<?php $this->endSection()?>

<?= $this->extend('auth-template')?>