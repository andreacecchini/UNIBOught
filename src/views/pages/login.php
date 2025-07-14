<?php
use core\Session;
?>
<div
    class="container border rounded-3 p-5 col-lg-6 col-xl-4 d-flex justify-content-center align-items-center align-self-center bg-white">
    <div class="card-body">
        <?php require_once SOURCE_DIR . '/views/components/flash-message.php' ?>
        <form action="<?= $route ?>" method="post" class="need-validation">
            <div class="text-center">
                <a href="/" class="navbar-brand my-0 py-0 logo-animated-dark fs-1">UNIBOught</a>
            </div>

            <div class="form-group my-4">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 text-bg-primary"><span class="fas fa-envelope"></span></span>
                    <input type="email" name="email" id="email" class="form-control border-start-0" required />
                </div>
            </div>

            <div class="form-group my-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 text-bg-primary"><span class="fas fa-lock"></span></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0"
                        minlength="8" maxlength="128" required />
                </div>
            </div>


            <button type="submit" class="btn btn-block btn-primary mb-4 w-100">Accedi</button>
            <?php if (!$isVendor): ?>
                <p class="text-center text-muted my-4">Non hai un account?
                    <a href="signup" class="text-decoration-none">Registrati subito</a>
                </p>
                <p class="text-center text-muted my-4 py-4 border-top">Sei un venditore?
                    <a href="/login-vendor" class="text-decoration-none">Accedi come venditore</a>
                </p>
            <? endif; ?>
        </form>
    </div>
</div>

<script src="/js/compute-hash.js"></script>
<script src="/js/login.js"></script>