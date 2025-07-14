<h1 class="mb-4">Profilo Utente</h1>

<form action="/user-profile/modifica" method="POST" id="profiloForm">
    <fieldset class="form-group mb-4 border border-2 shadow rounded p-3 bg-white">
        <legend>Dati Personali</legend>
        <div class="mb-3">
            <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nome" name="name"
                value="<?= htmlspecialchars($user['name']); ?>" disabled required />
        </div>

        <div class="mb-3">
            <label for="cognome" class="form-label">Cognome <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="cognome" name="surname"
                value="<?= htmlspecialchars($user['surname']); ?>" disabled required />
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Istituzionale <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email"
                value="<?= htmlspecialchars($user['email']); ?>" disabled required />
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Numero di Telefono</label>
            <input type="tel" class="form-control" id="telefono" name="telephone" maxlength="10" minlength="10"
                value="<?= htmlspecialchars($user['telephone_number']); ?>" disabled />
        </div>
    </fieldset>

    <fieldset id="passwordFields" class="d-none border border-2 shadow rounded p-3 bg-white">
        <legend>Modifica Password</legend>
        <div class="mb-3">
            <label for="oldPassword" class="form-label">Vecchia Password</label>
            <input type="password" class="form-control" id="oldPassword" name="oldPassword" minlength="8" />
        </div>
        <div class="mb-3">
            <label for="newPassword" class="form-label">Nuova Password <small class="d-block text-muted">La nuova
                    password deve contenere almeno 8 caratteri,di cui almeno una lettera, un numero
                    e un carattere speciale.</small></label>
            <input type="password" class="form-control" id="newPassword" name="newPassword" minlength="8"
                maxlength="128" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$" />
        </div>

        <div class="mb-3">
            <label for="confirmPassword" class="form-label">Conferma Nuova Password</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" minlength="8" />
        </div>
    </fieldset>

    <div class="mt-4" id="defaultButtons">
        <button type="button" class="btn btn-primary border-2 shadow fs-2 mb-4 w-100" id="modificaBtn">
            <span class="fas fa-edit me-2"></span>Modifica Account</button>
        <?php if (!$user['isVendor']): ?>
            <a href="/storico-ordini" class="btn btn-outline-primary border-2 shadow fs-2 w-100 mb-4" id="storicoBtn">
                <span class="fas fa-history me-2"></span>
                Storico Ordini</a>
        <?php endif; ?>
        <a href="/logout" class="btn btn-danger border-2 shadow fs-2 w-100" id="logoutBtn">
            <span class="fas fa-sign-out-alt me-2"></span>
            Logout</a>
    </div>

    <div class="mt-4 d-none" id="editButtons">
        <button type="submit" class="btn btn-success border-2 shadow fs-2 mb-3 w-100" id="salvaBtn">
            <span class="fas fa-check me-2"></span>Salva Modifiche</button>
        <button type="button" class="btn btn-outline-secondary border-2 shadow fs-2 w-100" id="annullaBtn">
            <span class="fas fa-times me-2"></span>Annulla</button>
    </div>
</form>

<script src="/js/compute-hash.js"></script>
<script src="/js/user-profile-handler.js"></script>