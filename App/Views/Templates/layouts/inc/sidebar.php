<nav id="sidebar" class="sidebar js-sidebar">
	<div class="sidebar-content js-simplebar">
		<a class="sidebar-brand" href="<?= url(route_name('home')) ?>">
			<img loading="lazy" style="width:25px;" alt="<?= app('name') ?>" src="<?= assets(app('logo.default')) ?>"> <span class="fw-bold"><?= app('name') ?></span>
		</a>

		<?php if ($layout->getMenu()): ?>
			<ul class="sidebar-nav">
				<?php foreach ($layout->getMenu()->getItems() as $item): ?>
					<li class="sidebar-item <?= $item['is_active'] ? 'active' : '' ?> <?= $item['has_submenu'] ? 'dropdown' : '' ?>">
						<?php if ($item['has_submenu']): ?>
							<a data-bs-target="<?= $item['url'] ?>" data-bs-toggle="collapse" class="sidebar-link <?= $item['is_dropdown_open'] ? '' : 'collapsed' ?>">
								<i class="align-middle <?= $item['icon'] ?>"></i>
								<span class="align-middle"><?= $item['title'] ?></span>
							</a>

							<ul id="<?= str_replace('#', '', $item['url']) ?>" class="sidebar-dropdown list-unstyled <?= $item['is_dropdown_open'] ? '' : 'collapse' ?>" data-bs-parent="#sidebar">
								<?php foreach ($item['submenu'] as $submenu): ?>
									<li class="sidebar-item <?= $submenu['is_active'] ? 'active' : '' ?>">
										<a class='sidebar-link' href="<?= url($submenu['url']) ?>">
											<?= $submenu['title'] ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php else: ?>
							<a class='sidebar-link' href="<?= url($item['url']) ?>">
								<i class="align-middle <?= $item['icon'] ?>"></i>
								<span class="align-middle"><?= $item['title'] ?></span>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</nav>