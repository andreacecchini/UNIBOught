document.addEventListener('DOMContentLoaded', function () {
  const loginForm = document.querySelector('form');

  loginForm.addEventListener('submit', async function (e) {
    // Previene il comportamento di default del form
    e.preventDefault();
    const passwordField = document.getElementById('password');
    const password = passwordField.value;
    try {
      const hashHex = await computeHash(password);
      // Crea un campo nascosto per l'hash
      const hashInput = document.createElement('input');
      hashInput.type = 'hidden';
      hashInput.name = 'password'; // Usiamo lo stesso nome
      hashInput.value = hashHex;
      loginForm.appendChild(hashInput);
      // Rimuovi il name dal campo originale così non verrà inviato
      passwordField.removeAttribute('name');
      // Invia il form
      this.submit();
    } catch (error) {
      console.error('Errore durante il calcolo dell\'hash:', error);
      alert('Si è verificato un errore. Riprova più tardi...');
    }
  });

});