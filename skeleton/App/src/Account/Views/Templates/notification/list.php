<?php
$this->setSection('title', $notification_log_view_model->getPageTitle());
$this->setSection('heading', $notification_log_view_model->getHeading());
?>

<?php $this->startSection('content')?>
<div class="card">
	<?php if (! $notification_log_view_model->hasNotifications()) { ?>
		<div class="card-body">
			<?= component('no-result')->data($notification_log_view_model->getNoResultComponentData())->attributes(['class' => 'btn btn-link fw-medium'])->render()?>
		</div>
	<?php } else { ?>
		<div class="card-header border-bottom">
			<?= component('delete')->content('Clear All')->data(['url' => url(route('destroy')), 'important-fields' => $this->importantFormFields('delete')])->attributes(['class' => 'btn btn-link'])->render()?>
		</div>
	 	<div class="list-group list-group-flush">
	 		<?php foreach ($notification_log_view_model->getNotificationsItems() as $notification) { ?>
			  <div class="list-group-item my-1">
			   	<div class="row align-items-center">
			    	<div class="col-auto <?= $notification->isRead() ? 'text-muted' : 'fw-bold text-primary'?>">
			    		<i class="align-middle" data-feather="bell"></i>
			    	</div>
				    <div class="col">
				     	<div class="<?= $notification->isRead() ? 'text-muted' : 'fw-bold text-primary'?>">
	                        <a href="<?= $notification->getUrl() ?? '#'?>" class="text-decoration-none text-reset"><?= ucfirst($notification->getLabel())?></a>
	                    </div>
						<div class="<?= $notification->isRead() ? 'text-muted' : 'text-dark'?> mt-1"><?= $notification->getMessage()?></div>
						<div class="text-muted small mt-1 fw-medium"><?= $notification->getTimeAgo()?></div>
				    </div>
		   		</div>
		   	</div>
		   	<?php } ?>
	  	</div>
	  	<div class="card-footer">
			<?= component('pagination')->data(['paginator' => $notification_log_view_model->getNotifications()])->render()?>
	  	</div>
  <?php } ?>
</div>
<?= $this->layout()->modal('confirm'); ?>
<?php $this->endSection()?>

<?php $this->startSection('script')?>
<script src="<?= assets('js/confirm.js')?>"></script>
<?php $this->endSection()?>

<?= $this->extend('user-template'); ?>