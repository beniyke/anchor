<?php if (isset($error['error_message']) && is_array($error['error_message'])): ?>
    <div class="invalid-feedback">
        <ul class="p-0 m-0 list-unstyled">
            <?php foreach ($error['error_message'] as $message): ?>
                <li><?= htmlspecialchars($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>