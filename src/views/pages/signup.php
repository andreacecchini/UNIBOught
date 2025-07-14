<div
    class="container border rounded-3 p-5 col-lg-7 col-xl-5 d-flex justify-content-center align-items-center align-self-center bg-white">
    <div class="card-body">
        <?php require_once SOURCE_DIR . '/views/components/flash-message.php' ?>
        <form action="/signup" method="POST" class="need-validation">
            <div class="text-center">
                <a href="/" class="navbar-brand my-0 py-0 logo-animated-dark fs-1">UNIBOught</a>
            </div>

            <div class="row my-4">
                <div class="col-md my-2">
                    <div class="form-group">
                        <label for="name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 text-bg-primary"><span
                                    class="fas fa-user"></span></span>
                            <input type="text" name="name" id="name" class="form-control border-start-0" required />
                        </div>
                    </div>
                </div>

                <div class="col-md my-2">
                    <div class="form-group">
                        <label for="surname" class="form-label">Cognome <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0 text-bg-primary"><span
                                    class="fas fa-user"></span></span>
                            <input type="text" name="surname" id="surname" class="form-control border-start-0"
                                required />
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group my-4">
                <label for="email" class="form-label">Email istituzionale <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 text-bg-primary"><span
                            class="fas fa-envelope"></span></span>
                    <input type="email" name="email" id="email" class="form-control border-start-0" required />
                </div>
            </div>

            <div class="form-group my-4">
                <label for="number" class="form-label">Numero di telefono</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 text-bg-primary"><span
                            class="fas fa-phone"></span></span>
                    <input type="tel" name="number" id="number" class="form-control border-start-0"
                        pattern="[0-9]{3}[0-9]{3}[0-9]{4}" minlength="10" maxlength="10" />
                </div>
                <small class="form-text text-muted">Formato: 1234567890</small>
            </div>

            <div class="form-group my-4">
                <label for="password" class="form-label">Password <span class="text-danger">*</span><small
                        class="d-block text-muted">La nuova password deve contenere almeno 8 caratteri, di cui almeno una lettera, un numero
                        e un carattere speciale.</small></label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 text-bg-primary"><span class="fas fa-lock"></span></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0"
                        pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$" minlength="8"
                        maxlength="128" required />
                </div>
            </div>

            <div class="form-group my-4">
                <label for="check-password" class="form-label">Conferma password <span
                        class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 text-bg-primary"><span class="fas fa-lock"></span></span>
                    <input type="password" name="check-password" id="check-password" class="form-control border-start-0"
                        minlength="8" required />
                </div>
            </div>

            <button type="submit" class="btn btn-block btn-primary mb-4 w-100">Registrati</button>
            <p class="text-center text-muted my-4">Hai già un account?
                <a href="login" class="text-decoration-none">Accedi</a>
            </p>
        </form>
    </div>
</div>

<script src="/js/compute-hash.js"></script>
<script src="/js/signup.js"></script>