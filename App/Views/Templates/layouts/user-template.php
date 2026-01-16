<?php $this->startSection('layout')?>
<div class="wrapper">
	<?= $this->layout()->inc('sidebar')?>
  	<div class="main">
  		<?= $this->inc('nav')?>
  		<?php if ($layout->getUser()?->hasIncompleteProfile()) { ?>
  			<?= $this->layout()->inc('incomplete-profile')?>
  		<?php }?>
  		<?= $this->layout()->inc('message')?>
  		<main class="content">
			<div class="container-fluid p-0">
				<?php if ($this->hasSection('back')) { ?>
				<div class="mb-2 mb-xl-3">
					<a href="<?= $this->section('back')?>" class="fw-bold">
						<i class="fas fa-fw fa-arrow-left"></i> Back
					</a>
				</div>
				<?php }?>

				<div class="row mb-2 mb-xl-3">
			    	<?php if ($this->hasSection('heading')) { ?>
					<div class="col-auto">
						<h1 class="h3 mb-3"><?= $this->section('heading')?></h1>
					</div>
					<?php }?>

					<div class="col-auto ms-auto text-end mt-n1">
						<?= $this->section('action')?>
					</div>
				</div>
				<?= $this->section('content')?>
			</div>
		</main>
    	<?= $this->inc('footer')?>
  	</div>
</div>
<?php $this->endSection()?>

<?php $this->startSection('script')?>
<?= $this->section('script')?>
<script src="<?= assets('autosize/autosize.js')?>"></script>
<script type="text/javascript">
	autosize($('textarea'));
</script>
<?php $this->endSection()?>

<?= $this->extend('master-template')?>