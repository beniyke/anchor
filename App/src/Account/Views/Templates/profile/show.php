<?php $this->setSection('title', $profile_view_model->getPageTitle())?>
<?php $this->setSection('heading', $profile_view_model->getHeading())?>

<?php $this->startSection('content')?>
<div class="row">
    <div class="col">
        <div class="tab">
            <?= $this->inc('account-menu')?>
            <div class="tab-content">
                <div class="tab-pane active show" role="tabpanel">
                    <form action="<?= $profile_view_model->getFormActionUrl()?>" method="POST">
                        <div class="mb-3">
                            <?= component('label')->content('Name')->attributes(['class' => 'form-label fw-bold'])->render()?>
                            <?= component('input')->content($profile_view_model->getFieldValue('name'))->attributes(['type' => 'text', 'name' => 'name', 'required' => true, 'placeholder' => 'Enter your Name', 'autocomplete' => 'off', 'class' => 'form-control form-control-lg pwdf'])->render()?>
                            <?= component('error')->attributes(['name' => 'name'])->render()?>
                        </div>

                        <div class="mb-3">
                             <?= component('label')->content('Email Address')->attributes(['class' => 'form-label fw-bold'])->render()?>
                            <?= component('input')->content($profile_view_model->getFieldValue('email'))->attributes(['type' => 'email', 'name' => 'email', 'required' => true, 'placeholder' => 'Enter your Email Address', 'autocomplete' => 'off', 'class' => 'form-control form-control-lg pwdf'])->render()?>
                            <?= component('error')->attributes(['name' => 'email'])->render()?>
                        </div>

                        <div class="mb-3">
                             <?= component('label')->content('Gender')->attributes(['class' => 'form-label fw-bold'])->render()?>
                            <?= component('select')->attributes(['class' => 'form-select form-control-lg pdwf', 'name' => 'gender', 'required' => true])->options($profile_view_model->getGenders())->selected($profile_view_model->getFieldValue('gender'))->render()?>
                            <?= component('error')->attributes(['name' => 'gender'])->render()?>
                        </div>

                        <div class="mb-3">
                             <?= component('label')->content('Phone Number')->attributes(['class' => 'form-label fw-bold'])->render()?>
                            <?= component('input')->content($profile_view_model->getFieldValue('phone'))->attributes(['type' => 'tel', 'name' => 'phone', 'required' => true, 'placeholder' => 'Enter your Phone Number', 'autocomplete' => 'off', 'class' => 'form-control form-control-lg pwdf'])->render()?>
                            <?= component('error')->attributes(['name' => 'phone'])->render()?>
                        </div>

                        <div class="mb-3 d-none password-box">
                             <?= component('label')->content('Confirm Password')->attributes(['class' => 'form-label fw-bold'])->render()?>
                            <div class="input-group input-group-flat <?= $profile_view_model->getErrorClass('password')?>">
                                <?= component('input')->attributes(['type' => 'password', 'class' => 'form-control form-control-lg', 'placeholder' => 'Enter Your password', 'autocomplete' => 'off', 'id' => 'password', 'name' => 'password', 'required' => true])->render()?>
                                <span class="input-group-text <?= $profile_view_model->hasError('password') ? 'border-danger' : ''?>">
                                    <?= component('link')->content('<i class="align-middle text-muted" data-feather="eye"></i>')->attributes(['href' => '#', 'class' => 'link-secondary show-p', 'data-field' => '#password', 'data-bs-toggle' => 'tooltip', 'aria-label' => 'Show password', 'data-bs-original-title' => 'Show password'])->render()?>
                                </span>
                            </div>
                            <?= component('error')->attributes(['name' => 'password'])->render()?>
                        </div>

                        <div class="mb-3">
                            <?= $this->importantFormFields('patch')?>
                            <button type="submit" class="btn btn-primary btn-lg save-button">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection()?>

<?php $this->startSection('script')?>
<script type="text/javascript">
	$('.save-button').on('click', function(){
		let emptyField = 0;
		$.each($('.pwdf'),function(){
			if ($(this).val() == '') {
				$(this).focus().change();
	    	emptyField ++;
			}
	  });

	  if (emptyField == 0) {
	  	$('.password-box').removeClass('d-none');
			$(this).attr('type', 'submit');
	  }

	});
</script>
<?php $this->endSection()?>

<?= $this->extend('user-template')?>