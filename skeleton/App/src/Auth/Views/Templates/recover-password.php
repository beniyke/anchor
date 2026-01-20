<?php $this->setSection('title', $forgotpassword_view_model->page_title)?>
<?php $this->setSection('heading', $forgotpassword_view_model->heading)?>
<?php $this->setSection('subheading', $forgotpassword_view_model->subheading)?>

<?php $this->startSection('content')?>
<form action="<?= $forgotpassword_view_model->getFormActionUrl()?>" method="POST">
     <?= $this->csrf()?>
    <div class="mb-3">
        <?= component('label')->content('Email Address')->attributes(['class' => 'form-label fw-medium'])->render()?>
        <?= component('input')->attributes(['type' => 'email', 'name' => 'email', 'required' => true, 'placeholder' => 'Enter your email address', 'class' => 'form-control form-control-lg', 'autocomplete' => 'off'])->render()?>
    </div>
    <div class="mb-3">
        <?= component('submit')->content('Reset Password')->attributes(['class' => 'btn btn-primary btn-lg w-100'])->render()?>
    </div>
    <div class="text-center text-muted my-4">
        <?= component('link')->content('Send me back')->data(['href' => $forgotpassword_view_model->getLoginUrl()])->attributes(['class' => 'fw-bold'])->render()?> to the Login page.
    </div>
</form>
<?php $this->endSection()?>

<?= $this->extend('auth-template')?>