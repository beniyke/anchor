<?php
use Helpers\File\Paths;

?>
<?php $this->startSection('layout')?>
<main class="d-flex w-100">
	<div class="container d-flex flex-column">
		<div class="row vh-100">
			<div class="col-11 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
				<div class="d-table-cell align-middle">
					<div class="text-center mb-4">
			      		<a href="<?= url()?>"><img loading="lazy" style="width:45px;" alt="<?= app('name')?>" class="rounded-circle" src="<?= assets(app('logo')['default'])?>"></a>
			    	</div>
					<div class="my-4 text-center">
						<h2 class="fw-medium"><?= $this->section('heading')?></h2>
						<p class="text-secondary"><?= $this->section('subheading')?></p>
					</div>
					<?= $this->inc('message', path: Paths::layoutPath())?>
					<?= $this->section('content')?>
				</div>
			</div>
		</div>
	</div>
</main>
<?php $this->endSection()?>

<?= $this->extend('master-template')?>