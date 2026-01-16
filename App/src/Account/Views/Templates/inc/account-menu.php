<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item" role="presentation">
		<a class="nav-link <?= route() == route_name('profile') ? 'active' : ''?>" href="<?= url(route_name('profile'))?>">
			<i class="align-middle me-1" data-feather="user"></i><span class="align-middle">Profile</span>
		</a>
	</li>
	<li class="nav-item" role="presentation">
		<a class="nav-link <?= route() == route_name('change-password') ? 'active' : ''?>" href="<?= url(route_name('change-password'))?>">
			<i class="align-middle me-1" data-feather="lock"></i><span class="align-middle">Change Password</span>
		</a>
	</li>
	<li class="nav-item" role="presentation">
		<a class="nav-link <?= route() == route_name('change-photo') ? 'active' : ''?>" href="<?= url(route_name('change-photo'))?>">
			<i class="align-middle me-1" data-feather="camera"></i><span class="align-middle">Change Photo</span>
		</a>
	</li>
</ul>