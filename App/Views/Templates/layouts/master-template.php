<!DOCTYPE html>
<html lang="en">
<head>
	<title><?= $this->section('title')?></title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, maximum-scale=1">
	<?php if ($this->hasSection('metatags')) { ?>
		<?= $this->section('metatags')?>
		<meta name="robots" content="index, follow">
		<meta name="author" content="<?= config('app.name')?>">
	<?php } ?>
	<?php if ($this->hasSection('favicon')) { ?>
		<?= $this->section('favicon')?>
	<?php } else { ?>
	<link rel="icon" href="<?= assets(app('icon'))?>" type="image/png" />
	<link rel="shortcut icon" href="<?= assets(app('icon'))?>" type="image/png" />
	<?php } ?>
	<?= $this->section('head')?>
	<link rel="preconnect" href="<?= url()?>">
	<link rel="preload" href="<?= assets('fonts/InterDisplay-Medium.woff2')?>" as="font" type="font/woff2" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="<?= assets('css/style.css')?>">
	<link rel="stylesheet" type="text/css" href="<?= assets('css/animate.css')?>">
	<link rel="stylesheet" type="text/css" href="<?= assets('css/fontawesome.min.css')?>">
	<?= $this->section('css')?>
	<style type="text/css">
		@font-face {
		    font-family: "Inter";
		     src: url('<?= assets('fonts/InterDisplay-Medium.woff2')?>') format('woff2');
		  	font-weight: 500;
		    font-style: normal;
		    font-display: swap;
		}

		body {
	        font-family: "Inter", sans-serif;
	    }
	    .fa-1-5x {
	    	font-size: 1.54em ;
	    }
	    .pointer {cursor: pointer;}
	</style>
</head>
<body>
	<?= $this->section('layout')?>
	<script src="<?= assets('js/script.js')?>"></script>
	<script src="<?= assets('js/flashmessage.js')?>"></script>
	<script type="text/javascript">
		$("form:not(.needs-confirmation)").submit(function(){
			$(this).find(":submit").attr('type', 'button').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing<span class="visually-hidden">Loading...</span>').addClass('disabled');
			$(':submit').attr('type', 'button').addClass('disabled', 'disabled');
			$('*').css('pointer-events', 'none');
		});

		$('.show-p').on('click', function(e){
			e.preventDefault();
			var target = $(this).data('field');

			if ($(target).attr('type') == 'text') {
				$(target).attr('type', 'password');
			} else {
				$(target).attr('type', 'text');
			}
		});
	</script>
	<?= $this->section('script')?>
</body>
</html>
