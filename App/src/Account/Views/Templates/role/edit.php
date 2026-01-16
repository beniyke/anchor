<?php $this->setSection('title', $edit_role_view_model->getPageTitle())?>
<?php $this->setSection('heading', $edit_role_view_model->getHeading())?>

<?php $this->setSection('back', $edit_role_view_model->getBackUrl())?>

<?php $this->startSection('content')?>
<form method="POST" action="<?= $edit_role_view_model->getFormActionUrl()?>">
	<?= $this->importantFormFields('patch')?>
	<?= component('hidden')->attributes(['name' => 'type'])->content($edit_role_view_model->getTypeValue())->render()?>

	<div class="card">
		<div class="card-header border-bottom">
			<div class="fw-bold"><?= $edit_role_view_model->getCurrentTypeLabel()?> Role Permissions</div>
		</div>
		<div class="card-body">
			<div class="mb-3">
				<?= component('label')->content('Title')->attributes(['class' => 'form-label fw-bold'])->render()?>
				<?= component('input')->content($edit_role_view_model->getRoleTitle())->attributes(['class' => 'form-control form-control-lg', 'type' => 'text', 'name' => 'title', 'required' => true, 'placeholder' => 'Enter role title'])->render()?>
			</div>
		</div>
		<div class="table-responsive">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th style="width: 120px;">Menu</th>
						<th>Sub Menu</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($edit_role_view_model->getMenuConfig() as $menu) { ?>
						<?php if ($edit_role_view_model->isMenuAccessible($menu)) { ?>
							<tr>
								<td style="width: 200px;">
									<?php if ($menu['url'] == 'account/home') { ?>
										<?= component('hidden')->attributes(['name' => 'permission[menu][]'])->content(str_replace('/', '-', ($menu['url'])))->render()?>

										<span data-feather="check-square" class="me-2 text-primary"></span><span class="fw-bold"><?= $menu['title']?></span>
									<?php } else { ?>
									<div class="form-check">
									 	<?= component('checkbox')->attributes(array_merge(['class' => 'form-check-input p-2 me-2 pointer checkbox', 'name' => 'permission[menu][]', 'value' => str_replace('/', '-', ($menu['url'])), 'id' => $edit_role_view_model->getMenuId($menu['url']), 'data-children' => '.'.$edit_role_view_model->getSubmenuClass($menu['url'])], ($checked_attr = $edit_role_view_model->isMenuChecked($menu['url']) ? ['checked' => true] : [])))->render()?>
									 	<label class="form-check-label fw-medium p-1 pointer" for="<?= $edit_role_view_model->getMenuId($menu['url'])?>">
									  	<?= $menu['title']?>
									 	</label>
									</div>
									<?php }?>
								</td>
								<td>
									<?php if (! empty($menu['submenu'])) { ?>
										<?php foreach ($menu['submenu'] as $submenu) { ?>
											<?php if ($edit_role_view_model->isSubmenuAccessible($submenu)) { ?>
												<div class="form-check form-check-inline">
												 	<?= component('checkbox')->attributes(array_merge(['name' => 'permission[submenu][]', 'class' => 'form-check-input p-2 me-2 pointer '.$edit_role_view_model->getSubmenuClass($menu['url']), 'id' => $edit_role_view_model->getSubmenuId($submenu['url']), 'value' => str_replace('/', '-', ($menu['url'].'::'.$submenu['url']))], ($edit_role_view_model->isSubmenuChecked($menu['url'], $submenu['url']) ? ['checked' => true] : [])))->render()?>
												 <label class="form-check-label fw-medium p-1 pointer" for="<?= $edit_role_view_model->getSubmenuId($submenu['url'])?>"><?= $submenu['title']?></label>
												</div>
											<?php }?>
										<?php }?>
									<?php }?>
								</td>
							</tr>
						<?php }?>
					<?php }?>
				</tbody>
			</table>
		</div>
		<div class="card-footer">
			<?= component('submit')->content('Save')->attributes(['class' => 'btn btn-lg btn-primary'])->render()?>
		</div>
	</div>
</form>
<?php $this->endSection()?>

<?php $this->startSection('script')?>
<script type="text/javascript">
	$('.checkbox').on('click', function(){
		if (!$(this).prop('checked')) {
			$($(this).data('children')).prop('checked', false);
		}
	});
</script>
<?php $this->endSection()?>

<?= $this->extend('user-template'); ?>