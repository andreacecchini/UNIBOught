document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("profiloForm");
    const nome = document.getElementById("nome");
    const cognome = document.getElementById("cognome");
    const email = document.getElementById("email");
    const telefono = document.getElementById("telefono");
    const passwordFields = document.getElementById("passwordFields");
    const oldPasswordField = document.getElementById("oldPassword");
    const newPasswordField = document.getElementById("newPassword");
    const confirmNewPasswordField = document.getElementById("confirmPassword");
    const defaultButtons = document.getElementById("defaultButtons");
    const editButtons = document.getElementById("editButtons");
    const modificaBtn = document.getElementById("modificaBtn");
    const salvaBtn = document.getElementById("salvaBtn");
    const annullaBtn = document.getElementById("annullaBtn");

    const inputFields = [nome, cognome, email, telefono];

    const originalValues = {
        nome: nome?.value || "",
        cognome: cognome?.value || "",
        email: email?.value || "",
        telefono: telefono?.value || ""
    };

    /**
     * Abilita e disabilita gli elementi del form
     */
    function toggleElementState(elements, disabled) {
        elements.forEach(element => {
            if (element) {
                element.disabled = disabled;
            }
        });
    }

    /**
     * Abilita e disabilità una classe css su un elemento
     */
    function toggleClass(element, className, add) {
        if (!element) return;

        if (add) {
            element.classList.add(className);
        } else {
            element.classList.remove(className);
        }
    }

    /**
     * Pulisce i campi della password quando l'utente non è in modifica
     */
    function clearPasswordFields() {
        const passwordInputs = document.querySelectorAll("#passwordFields input");
        passwordInputs.forEach(input => {
            input.value = "";
            input.setCustomValidity(''); // Clear any validation messages
        });
    }

    /**
     * Entra in modalità modifica abilitando i campi del form e mostrando i campi della password.
     */
    function enterEditMode() {
        toggleElementState(inputFields, false);
        toggleClass(passwordFields, "d-none", false);
        toggleClass(defaultButtons, "d-none", true);
        toggleClass(editButtons, "d-none", false);
        inModifica = true;
    }

    /**
     * Esci dalla modalità modifica
     */
    function exitEditMode() {
        toggleElementState(inputFields, true);
        toggleClass(passwordFields, "d-none", true);
        toggleClass(defaultButtons, "d-none", false);
        toggleClass(editButtons, "d-none", true);

        // Rimposta i valori originali 
        if (nome) nome.value = originalValues.nome;
        if (cognome) cognome.value = originalValues.cognome;
        if (email) email.value = originalValues.email;
        if (telefono) telefono.value = originalValues.telefono;

        // Pulisci i campi della password
        clearPasswordFields();
        inModifica = false;
    }

    /**
     * Validare gli input della password prima di submittare il form.
     */
    function validatePasswords() {
        if (!newPasswordField || !confirmNewPasswordField) return true;

        const newPassword = newPasswordField.value;
        const confirmPassword = confirmNewPasswordField.value;
        const oldPassword = oldPasswordField.value;
        if (oldPassword === "" && newPassword !== "") {
            oldPasswordField.setCustomValidity('Inserisci la password attuale per impostare una nuova password');
            return false;
        } else {
            oldPasswordField.setCustomValidity('');
        }
        if (newPassword === "" && confirmPassword !== "") {
            confirmNewPasswordField.setCustomValidity('Hai inserito una password di conferma senza una nuova password');
            return true;
        }
        if (newPassword !== confirmPassword) {
            confirmNewPasswordField.setCustomValidity('Le password non coincidono');
            return false;
        } else if (oldPassword === newPassword) {
            confirmNewPasswordField.setCustomValidity('La nuova password deve essere diversa dalla password attuale');
            return false;
        } else {
            confirmNewPasswordField.setCustomValidity('');
            return true;
        }
    }

    /**
     * Controlla se è richiesto il cambio password. 
     * Il cambio password è richiesto se entrambi i campi della password vecchia e nuova sono compilati
     */
    function isPasswordChangeRequested() {
        return oldPasswordField?.value.trim() !== "" && newPasswordField?.value.trim() !== "";
    }

    function validatePasswordChange() {
        if (!isPasswordChangeRequested()) return true;
        return validatePasswords();
    }

    /**
     * Creazione di input hidden per inviare le password hashate
     */
    function createHiddenInput(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }

    /**
     * Preparazione delle password per l'invio del form.
     */
    async function preparePasswordsForSubmission() {
        try {
            const oldPassword = oldPasswordField?.value.trim() || "";
            const newPassword = newPasswordField?.value.trim() || "";

            // Compute hashes
            const hashOld = oldPassword ? await computeHash(oldPassword) : "";
            const hashNew = newPassword ? await computeHash(newPassword) : "";

            // Create hidden inputs with hashes
            const hashOldInput = createHiddenInput('oldPassword', hashOld);
            const hashNewInput = createHiddenInput('newPassword', hashNew);

            form.appendChild(hashOldInput);
            form.appendChild(hashNewInput);

            // Remove name attributes from visible password fields to prevent plain text submission
            oldPasswordField?.removeAttribute('name');
            newPasswordField?.removeAttribute('name');
            confirmNewPasswordField?.removeAttribute('name');

        } catch (error) {
            console.error('Errore durante il calcolo dell\'hash:', error);
            throw new Error('Errore durante la preparazione delle password');
        }
    }

    // Event listeners
    modificaBtn?.addEventListener('click', enterEditMode);
    annullaBtn?.addEventListener('click', exitEditMode);
    confirmNewPasswordField?.addEventListener('input', validatePasswords);

    salvaBtn?.addEventListener('click', async (e) => {
        e.preventDefault();

        try {
            // Valida il cambio della password prima di procedere
            validatePasswordChange();

            // Segnala varie invalidità del form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // prepara le password per l'invio
            await preparePasswordsForSubmission();

            form.submit();

        } catch (error) {
            console.error('Errore durante il salvataggio:', error);
            alert('Si è verificato un errore durante il salvataggio. Riprova più tardi.');
        }
    });
});