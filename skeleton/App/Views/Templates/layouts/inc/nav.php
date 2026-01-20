<nav class="navbar navbar-expand navbar-light navbar-bg">
	<a class="sidebar-toggle js-sidebar-toggle">
		<i class="hamburger align-self-center"></i>
	</a>
	<div class="fw-bold text-muted">
		<div class="row align-items-center">
			<div class="col-2">
				<img loading="lazy" class="rounded-circle me-3" style="width:25px;" alt="<?= app('name') ?>" src="<?= assets(app('logo.default')) ?>">
			</div>
			<div class="col">
				<div><span class="fw-bold"><?= config('app.name') ?></span></div>
			</div>
		</div>
	</div>
	<div class="navbar-collapse collapse">
		<ul class="navbar-nav navbar-align">
			<li class="nav-item dropdown">
				<a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown">
					<div class="position-relative">
						<i class="fas fa-bell fa-fw"></i>
						<?php if ($layout->getUser()?->hasNotification()): ?>
							<span class="indicator"><?= $layout->getUser()->getNotificationCount() ?></span>
						<?php endif; ?>
					</div>
				</a>
				<div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
					<div class="dropdown-menu-header">
						<?php if ($layout->getUser()?->hasNotification()): ?>
							<?= $layout->getUser()->getNotificationCount() ?> New <?= inflect('Notification', $layout->getUser()->getNotificationCount()) ?>
						<?php else: ?>
							No Notification Found
						<?php endif; ?>
					</div>
					<?php if ($layout->getUser()?->hasNotification()): ?>
						<div class="list-group">
							<?php foreach ($layout->getUser()->getNotifications() as $notification): ?>
								<div class="list-group-item">
									<div class="row g-0 align-items-center">
										<div class="col-2 text-center text-md-left">
											<i class="fas fa-bell fa-2x"></i>
										</div>
										<div class="col-10">
											<div class="text-dark"><?= ucfirst($notification->getLabel()) ?></div>
											<div class="text-muted small mt-1"><?= $notification->getMessage() ?></div>
											<div class="text-muted small mt-1"><?= $notification->getTimeAgo() ?></div>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="dropdown-menu-footer">
							<a href="<?= url(route_name('notification')) ?>" class="text-muted">Show all notifications</a>
						</div>
					<?php endif; ?>
				</div>
			</li>

			<li class="nav-item dropdown">
				<a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
					<i class="align-middle" data-feather="settings"></i>
				</a>

				<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
					<?php if ($layout->getUser()?->hasPhoto()): ?>
						<img loading="lazy" src="<?= $layout->getUser()->getAvatar() ?>" class="avatar img-fluid rounded-circle me-1" alt="<?= $layout->getUser()->getName() ?>" />
					<?php else: ?>
						<i class="align-middle me-1" data-feather="user"></i>
					<?php endif; ?>
					<span class="text-dark me-1"><?= $layout->getUser()?->getShortName() ?></span>
				</a>
				<div class="dropdown-menu dropdown-menu-end">
					<a class="dropdown-item <?= (route() == route_name('profile') ? 'active' : '') ?>" href="<?= url(route_name('profile')) ?>"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item <?= (route() == route_name('activity') ? 'active' : '') ?>" href="<?= url(route_name('activity')) ?>"><i class="align-middle me-1" data-feather="activity"></i> Activity</a>
					<a class="dropdown-item <?= (route() == route_name('notification') ? 'active' : '') ?>" href="<?= url(route_name('notification')) ?>"><i class="align-middle me-1" data-feather="bell"></i> Notification</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="<?= url(route_name('logout')) ?>"><i class="align-middle me-1" data-feather="log-out"></i> Log out</a>
				</div>
			</li>
		</ul>
	</div>
</nav>