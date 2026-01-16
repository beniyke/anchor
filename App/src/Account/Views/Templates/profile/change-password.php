<?php $this->setSection('title', $change_password_view_model->getPageTitle())?>
<?php $this->setSection('heading', $change_password_view_model->getHeading())?>

<?php $this->startSection('content')?>

<?php if ($change_password_view_model->shouldShowPasswordUpdateWarning()) { ?>
<div class="card alert-warning">
    <div class="card-body">
         <h4 class="fw-bold text-dark">Time to Update Your Password!</h4>
        <p class="text-dark">Hey there! Just a friendly heads-up that it's been over 30 days since your last password update. For your security, our policy **requires** that you update it regularly.</p>
        <p class="text-dark">Please take a moment to set a new password below.</p>
    </div>
</div>
<?php }?>

<div class="row">
    <div class="col">
        <div class="tab">
            <?= $this->inc('account-menu')?>
            <div class="tab-content">
                <div class="tab-pane active show" role="tabpanel">
                    <form method="POST" action="<?= $change_password_view_model->getFormActionUrl()?>">

                        <div class="mb-3">
                            <?= component('label')->content('Old Password')->attributes(['class' => 'form-label fw-bold'])->render()?>
                            <div class="input-group input-group-flat <?= $change_password_view_model->getErrorClass('old_password')?>">
                                <?= component('input')->attributes(['type' => 'password', 'class' => 'form-control form-control-lg', 'placeholder' => 'Enter Old password', 'autocomplete' => 'off', 'id' => 'old-password', 'name' => 'old_password', 'required' => true])->render()?>
                                <span class="input-group-text <?= $change_password_view_model->hasError('old_password') ? 'border-danger' : ''?>">
                                    <?= component('link')->content('<i class="align-middle text-muted" data-feather="eye"></i>')->attributes(['href' => '#', 'class' => 'link-secondary show-p', 'data-field' => '#old-password', 'data-bs-toggle' => 'tooltip', 'aria-label' => 'Show password', 'data-bs-original-title' => 'Show password'])->render()?>
                                </span>
                            </div>
                            <?= component('error')->attributes(['name' => 'old_password'])->render()?>
                        </div>

                        <div class="mb-3">
                             <?= component('label')->content('New Password')->attributes(['class' => 'form-label fw-bold'])->render()?>
                             <div class="input-group input-group-flat <?= $change_password_view_model->getErrorClass('new_password')?>">
                                <?= component('input')->attributes(['type' => 'password', 'class' => 'form-control form-control-lg', 'placeholder' => 'Enter New password', 'autocomplete' => 'off', 'id' => 'new-password', 'name' => 'new_password', 'required' => true])->render()?>
                                <span class="input-group-text <?= $change_password_view_model->hasError('new_password') ? 'border-danger' : ''?>">
                                    <?= component('link')->content('<i class="align-middle text-muted" data-feather="eye"></i>')->attributes(['href' => '#', 'class' => 'link-secondary show-p', 'data-field' => '#new-password', 'data-bs-toggle' => 'tooltip', 'aria-label' => 'Show password', 'data-bs-original-title' => 'Show password'])->render()?>
                                </span>
                            </div>
                            <?= component('error')->attributes(['name' => 'new_password'])->render()?>
                        </div>

                        <div class="mb-3">
                            <?= component('label')->content('Confirm New Password')->attributes(['class' => 'form-label fw-bold'])->render()?>
                             <div class="input-group input-group-flat <?= $change_password_view_model->getErrorClass('confirm_password')?>">
                                <?= component('input')->attributes(['type' => 'password', 'class' => 'form-control form-control-lg', 'placeholder' => 'Confirm New password', 'autocomplete' => 'off', 'id' => 'confirm-password', 'name' => 'confirm_password', 'required' => true])->render()?>
                                <span class="input-group-text <?= $change_password_view_model->hasError('confirm_password') ? 'border-danger' : ''?>">
                                    <?= component('link')->content('<i class="align-middle text-muted" data-feather="eye"></i>')->attributes(['href' => '#', 'class' => 'link-secondary show-p', 'data-field' => '#confirm-password', 'data-bs-toggle' => 'tooltip', 'aria-label' => 'Show password', 'data-bs-original-title' => 'Show password'])->render()?>
                                </span>
                            </div>
                            <?= component('error')->attributes(['name' => 'confirm_password'])->render()?>
                        </div>
                        <div class="mb-3">
                            <?= $this->importantFormFields('patch')?>
                            <button type="submit" class="btn btn-primary btn-lg">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection()?>

<?= $this->extend('user-template'); ?>