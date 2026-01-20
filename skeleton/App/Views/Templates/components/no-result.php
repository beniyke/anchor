<div class="d-flex justify-content-center">
    <div class="col-md-6 text-center my-5">
        <div class="mb-3">
            <?php if (! str($no_result['icon'] ?? '')->contains('fas fa')->get()): ?>
                <i class="align-middle text-muted" data-feather="<?= $no_result['icon'] ?? '' ?>" style="width:50px; height:50px;"></i>
            <?php else: ?>
                <i class="align-middle <?= $no_result['icon'] ?? '' ?> fa-3x fa-fw text-muted"></i>
            <?php endif; ?>
        </div>
        <?php if (! empty($no_result['heading'])): ?>
            <h4 class="mb-3 fw-bold text-muted"><?= $no_result['heading'] ?></h4>
        <?php endif; ?>

        <?php if (! empty($no_result['subheading'])): ?>
            <div class="card-subtitle text-muted mb-3"><?= $no_result['subheading'] ?></div>
        <?php endif; ?>

        <?php if (! empty($no_result['cta'])): ?>
            <div>
                <a class="btn btn-primary btn-lg" href="<?= $no_result['cta']['url'] ?? '#' ?>"><i class="fas fa-plus fa-fw"></i> <?= $no_result['cta']['content'] ?? '' ?></a>
            </div>
        <?php endif; ?>
    </div>
</div>