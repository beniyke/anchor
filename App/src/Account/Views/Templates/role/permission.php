<?php
$this->setSection('title', $user_permission_view_model->getPageTitle());

$this->setSection('heading', $user_permission_view_model->getHeading());

$this->setSection('back', $user_permission_view_model->getBackUrl());
?>

<?php $this->startSection('content') ?>
<form action="<?= $user_permission_view_model->getFormActionUrl() ?>" method="POST">
    <?= $this->importantFormFields('patch') ?>
    <div class="card">
    	<h4 class="card-header border-bottom">
    		<?=$user_permission_view_model->getUser()->getName()?> Permissions
    	</h4>
    	<div class="table-responsive border-bottom">
	    	<table class="table text-nowrap">
	    		<tbody>
		    		<?php foreach ($user_permission_view_model->getGroupedPermissions() as $group => $permissions): ?>
		    		<tr>
		    			<td>
			    			<h4 class="fw-bold"><?= ucwords($group) ?></h4>
				    			<?php foreach ($permissions as $permission): ?>
				    				<div class="mb-3">
				                        <div class="d-flex justify-content-between align-items-center mb-1">
				                            <div>
				                                <div class="fw-medium"><?= $permission->name ?></div>
				                            </div>
				                            <?php if ($user_permission_view_model->isInherited($permission)): ?>
				                                <span class="badge rounded-pill bg-light text-primary border border-primary">Inherited via Role</span>
				                            <?php endif; ?>
				                        </div>

				                        <div class="btn-group btn-group-md" role="group">
				                            <?php $state = $user_permission_view_model->getPermissionState($permission); ?>

				                            <input type="radio" class="btn-check" name="permissions[<?= $permission->slug ?>]" id="inherit_<?= $permission->id ?>" value="inherit" <?= $state === 'inherit' ? 'checked' : '' ?>>
				                            <label class="btn btn-outline-secondary" for="inherit_<?= $permission->id ?>">Inherit</label>

				                            <input type="radio" class="btn-check" name="permissions[<?= $permission->slug ?>]" id="grant_<?= $permission->id ?>" value="grant" <?= $state === 'grant' ? 'checked' : '' ?>>
				                            <label class="btn btn-outline-success" for="grant_<?= $permission->id ?>">Grant</label>

				                            <input type="radio" class="btn-check" name="permissions[<?= $permission->slug ?>]" id="deny_<?= $permission->id ?>" value="deny" <?= $state === 'deny' ? 'checked' : '' ?>>
				                            <label class="btn btn-outline-danger" for="deny_<?= $permission->id ?>">Deny</label>
				                        </div>
			                    	</div>
				                <?php endforeach; ?>
				            </div>
				        </td>
		    		</tr>
		    		<?php endforeach?>
		    	</tbody>
	    	</table>
	    </div>
    	<div class="card-footer">
    		 <?= component('submit')->content('Update Permission')->attributes(['class' => 'btn btn-primary btn-lg'])->render() ?>
    	</div>
    </div>
</form>
<?php $this->endSection() ?>

<?= $this->extend('user-template'); ?>