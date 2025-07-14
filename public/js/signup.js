document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form');
  const password = document.getElementById('password');
  const checkPassword = document.getElementById('check-password');

  function checkPasswords() {
    if (checkPassword.value && password.value !== checkPassword.value) {
      checkPassword.setCustomValidity('Le password non coincidono');
    } else {
      checkPassword.setCustomValidity('');
    }
  }

  password?.addEventListener('input', checkPasswords);
  checkPassword?.addEventListener('input', checkPasswords);

  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    checkPasswords();
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    try {
      const hashHex = await computeHash(password.value);

      const hashInput = document.createElement('input');
      hashInput.type = 'hidden';
      hashInput.name = 'password';
      hashInput.value = hashHex;
      form.appendChild(hashInput);

      password.removeAttribute('name');
      checkPassword.removeAttribute('name');

      form.submit();
    } catch (error) {
      console.error('Errore durante il calcolo dell\'hash:', error);
      alert('Si è verificato un errore. Riprova più tardi.');
    }
  });
});
