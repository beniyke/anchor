<?php
$this->setSection('title', 'Access Denied');

$this->setSection('heading', 'Access Denied');

$this->setSection('back', url(route()));
?>

<?php $this->startSection('content')?>
<div class="card">
	<div class="card-body">
		<?= component('no-result')->path(template_path('components'))->data(['icon' => 'lock', 'heading' => 'Access Denied', 'subheading' => 'You do not have the required privileges to view this page; your current privilege level only grants access to certain pages'])->render()?>
	</div>
</div>
<?php $this->endSection()?>

<?= $this->extend('user-template'); ?>