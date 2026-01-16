<?php $this->setSection('title', $login_view_model->page_title)?>
<?php $this->setSection('heading', $login_view_model->heading)?>
<?php $this->setSection('subheading', $login_view_model->subheading)?>

<?php $this->startSection('content')?>
<form action="<?= $login_view_model->getFormActionUrl()?>" method="POST">

    <div class="mb-3">
        <?= component('label')->content('Email Address')->attributes(['class' => 'form-label fw-bold'])->render()?>
        <?= component('input')->attributes(['type' => 'email', 'class' => 'form-control form-control-lg', 'autocomplete' => 'off', 'required' => true, 'name' => 'email', 'placeholder' => 'Enter your email address'])->render()?>
    </div>

    <div class="mb-3">
        <?= component('label')->content('Password')->attributes(['class' => 'form-label fw-bold'])->render()?>

        <div class="input-group input-group-flat <?= $login_view_model->getErrorClass('password')?>">
            <?= component('input')->attributes(['type' => 'password', 'class' => 'form-control form-control-lg', 'autocomplete' => 'off', 'required' => true, 'placeholder' => 'Enter your password', 'id' => 'password', 'name' => 'password'])->render()?>

            <span class="input-group-text <?= $login_view_model->hasError('password') ? 'border-danger' : ''?>">
                <?= component('link')->content('<i class="align-middle text-muted" data-feather="eye"></i>')->attributes(['class' => 'link-secondary show-p', 'data-field' => '#password', 'data-bs-toggle' => 'tooltip', 'aria-label' => 'Show password', 'data-bs-original-title' => 'Show password'])->render()?>
            </span>
        </div>

        <div class="my-1">
            <?= component('link')->content('Forgot Password?')->data(['href' => $login_view_model->getForgotPasswordUrl()])->attributes(['class' => 'fw-bold'])->render()?>
        </div>
    </div>

    <div class="form-footer">
        <?= $this->csrf()?>
        <?= $this->referer()?>
        <?= component('submit')->content('Sign In')->attributes(['class' => 'btn btn-primary btn-lg w-100'])->render()?>
    </div>
</form>
<?php if (! $login_view_model->hasSetup()) { ?>
<div class="text-center text-secondary mt-3">
    Don't have an account? <?= component('link')->content('Sign Up')->data(['href' => $login_view_model->getSignupUrl()])->attributes(['class' => 'fw-bold'])->render()?>
</div>
<?php }?>
<?php $this->endSection()?>

<?= $this->extend('auth-template')?>