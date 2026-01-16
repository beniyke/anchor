<?php
$this->setSection('title', $activity_log_view_model->getPageTitle());
$this->setSection('heading', $activity_log_view_model->getHeading());
?>

<?php $this->startSection('content')?>
<div class="card">
	<?php if (! $activity_log_view_model->hasActivities()) { ?>
		<div class="card-body">
            <?= component('no-result')->data($activity_log_view_model->getNoResultComponentData())->render()?>
		</div>
	<?php } else { ?>
	<div class="list-group list-group-flush">
 		<?php foreach ($activity_log_view_model->getActivitiesItems() as $activity) { ?>
		  	<div class="list-group-item">
			   	<div class="row align-items-center">
			    	<div class="col-auto">
				     	<i class="align-middle" data-feather="activity"></i>
			    	</div>
				    <div class="col">
		         		<div class="text-dark fw-medium">You (<?= ucfirst($activity->getUser()->getName())?>)</div>
						<div class="text-muted mt-1"><?= ucfirst($activity->getDescription())?></div>
						<div class="text-muted fw-medium small mt-1"><?= $activity->getTimeAgo()?></div>
				    </div>
		   		</div>
		   	</div>
	   	<?php }?>
  	</div>
	<?php }?>
	<div class="card-footer border-top">
        <?= component('pagination')->data(['paginator' => $activity_log_view_model->getActivities()])->render()?>
  	</div>
</div>
<?php $this->endSection()?>

<?= $this->extend('user-template'); ?>