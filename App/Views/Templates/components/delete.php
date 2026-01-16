<?php
$url = $delete['url'];
$importantFormFields = $delete['important-fields'];
$actionTitle = $delete['value'];
$attributes = $delete['attributes'];
?>
<form class="needs-confirmation" action="<?= $url?>" method="POST" novalidate>
	<?= $importantFormFields?>
	<?= component('submit')->content($actionTitle)->attributes($attributes)->render()?>
</form>