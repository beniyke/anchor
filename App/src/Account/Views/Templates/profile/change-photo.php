<?php $this->setSection('title', $change_photo_view_model->getPageTitle())?>
<?php $this->setSection('heading', $change_photo_view_model->getHeading())?>

<?php $this->startSection('content')?>
<div class="row">
    <div class="col">
        <div class="tab">
            <?= $this->inc('account-menu')?>
            <div class="tab-content">
                <div class="tab-pane active show" role="tabpanel">
                    <div class="row align-items-center">
                        <div class="col-md-auto mb-3 mb-md-0 text-center">
                            <?php if ($change_photo_view_model->hasPhoto()) { ?>
                                <img src="<?= $change_photo_view_model->getAvatarUrl()?>" alt="<?= $change_photo_view_model->getUserName()?>" class="img-fluid rounded-circle mb-2" width="90" height="90">
                            <?php } else { ?>
                                <i class="align-middle text-muted fas fa-fw fa-user-circle fa-5x"></i>
                            <?php } ?>
                        </div>
                        <div class="col-auto">
                            <form action="<?= $change_photo_view_model->getFormActionUrl()?>" method="POST" enctype="multipart/form-data">

                                <div class="mb-3">
                                    <?= component('label')->content('Profile Photo')->attributes(['class' => 'form-label fw-bold'])->render()?>
                                    <?= component('input')->attributes(['type' => 'file', 'name' => 'photo', 'required' => true, 'placeholder' => 'Choose Picture', 'autocomplete' => 'off', 'accept' => 'image/*', 'class' => 'form-control form-control-lg file '.($change_photo_view_model->hasError('photo') ? 'is-invalid' : ''), 'data-formats' => 'png, jpeg, jpg, JPEG', 'data-maxsize' => '2mb', 'data-info' => '.picture-upload-box'])->render()?>
                                    <div class="picture-upload-box my-2"></div>
                                    <small class="form-hint">Only JPG, PNG and JPEG formats are accepted. The maximum upload size is 2MB.</small>
                                    <?= component('error')->attributes(['name' => 'photo'])->render()?>
                                </div>

                                <div class="mb-3">
                                    <?= $this->importantFormFields('patch')?>
                                    <?= component('submit')->content('Upload')->attributes(['class' => 'btn btn-primary btn-lg'])->render()?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection()?>

<?php $this->startSection('script')?>
<script src="<?= assets('js/file-client-validator.js')?>"></script>
<?php $this->endSection()?>

<?= $this->extend('user-template'); ?>