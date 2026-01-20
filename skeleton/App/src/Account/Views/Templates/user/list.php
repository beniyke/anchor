<?php $this->setSection('title', $user_list_view_model->getPageTitle())?>
<?php $this->setSection('heading', $user_list_view_model->getHeading())?>

<?php if ($user_list_view_model->isSearching()): ?>
    <?php $this->setSection('back', $user_list_view_model->getBackUrl())?>
<?php endif?>

<?php if ($user_list_view_model->hasUsers()) :?>
    <?php $this->startSection('action')?>
        <?= component('link')->content('<i class="align-middle" data-feather="plus"></i>  Create User')->data(['href' => $user_list_view_model->getCreateActionUrl()])->attributes(['class' => 'btn btn-primary btn-lg'])->render()?>
    <?php $this->endSection()?>
<?php endif?>

<?php $this->startSection('content')?>
<div class="card">
    <div class="div p-2 border-bottom d-print-none">
        <form method="GET" action="<?= $user_list_view_model->getSearchFormAction()?>">
            <div class="input-group mb-2">
                <?= component('input')->content($user_list_view_model->getSearchValue() ?? '')->attributes(['name' => 'search', 'type' => 'text', 'class' => 'form-control form-control-lg', 'required' => true, 'placeholder' => 'Search by name or email...'])->render()?>
                <?= component('submit')->attributes(['class' => 'btn btn-outline-primary btn-lg'])->content('Go!')->render()?>
            </div>
        </form>
        <?php if ($user_list_view_model->isSearching()): ?>
        <div class="text-muted p-2">Search result for <strong><?= $user_list_view_model->getSearchValue()?></strong></div>
        <?php endif?>
    </div>
    <?php if (! $user_list_view_model->hasUsers()): ?>
        <?= component('no-result')->data($user_list_view_model->getNoResultComponentData())->render()?>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped text-nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>&nbsp;</th>
                        <th class="text-end">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sn = $user_list_view_model->getUsers()->getOffset(); ?>
                    <?php foreach ($user_list_view_model->getUsersItems() as $user) { ?>
                        <tr>
                            <td><?= ($sn += 1)?>.</td>
                            <td>
                                <?php if ($user->hasPhoto()): ?>
                                <?= component('image')->data(['src' => $user->getAvatar(), 'alt' => $user->getName()])->attributes(['class' => 'avatar rounded-circle me-1', 'width' => 300, 'height' => 300, 'loading' => 'lazy'])->render()?>
                                <?php else: ?>
                                <i class="align-middle me-1 fas fa-fw fa-2x fa-user-circle"></i>
                                <?php endif?>
                            </td>
                            <td class="fw-bold"><?= $user->getName()?></td>
                            <td><?= $user->getEmail()?></td>
                            <td class="fw-medium text-primary"><?= ucfirst($user->getRoleName() ?? '')?></td>
                            <td><span class="fw-medium text-capitalize badge bg-<?= $user->getStatusColor()?>"><?= $user->getStatus()?></span></td>
                            <td class="table-action">
                                <div class="dropdown position-relative">
                                   <button type="button" class="btn btn-outline-primary btn-lg dropdown-toggle fw-bold" data-bs-toggle="dropdown" aria-expanded="false">
                                      Action
                                   </button>
                                   <ul class="dropdown-menu">
                                         <li><a class="dropdown-item text-primary" href="<?= $user_list_view_model->getEditUrl($user->getRefid())?>">Edit</a></li>
                                        <li>
                                            <?= component('delete')->content('Delete')->data(['url' => $user_list_view_model->getDeleteUrl($user->getRefid()), 'important-fields' => $this->importantFormFields('delete')])->attributes(['class' => 'dropdown-item text-danger'])->render()?>
                                        </li>

                                        <?php if ($user->isPending()): ?>
                                        <li><a class="dropdown-item text-dark needs-confirmation-link" data-message="Are you sure you want to resend link?" href="<?= $user_list_view_model->getResendUrl($user->getRefid())?>">Resend Link</a></li>
                                        <?php endif?>
                                    </ul>
                                </div>
                            </td>
                            <td class="text-end">
                                 <?= $user->getFormattedUpdatedAt()?>
                                <div class="small fw-medium">Last updated</div>
                            </td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <?= component('pagination')->data(['paginator' => $user_list_view_model->getUsers(), 'with' => request()->only(['search'])])->render()?>
        </div>
    <?php endif?>
</div>
<?= $this->layout()->modal('confirm'); ?>
<?= $this->layout()->modal('are-you-sure'); ?>
<?php $this->endSection()?>

<?php $this->startSection('script')?>
<script src="<?= assets('js/confirm.js')?>"></script>
<script src="<?= assets('js/areyousure.js')?>"></script>
<?php $this->endSection()?>

<?= $this->extend('user-template'); ?>