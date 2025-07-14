<?php
use core\Session;

$flashMessages = Session::getAllFlash(); ?>

<div id="flash-message-container">
    <?php if (!empty($flashMessages)): ?>
        <?php foreach ($flashMessages as $key => $flash): ?>
            <?php
            $type = $flash['type'];
            $message = $flash['message'];
            $alertClass = match ($type) {
                'success' => 'alert-success',
                'error' => 'alert-danger',
                'warning' => 'alert-warning',
                'info' => 'alert-info',
                default => 'alert-secondary'
            };
            ?>
            <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>