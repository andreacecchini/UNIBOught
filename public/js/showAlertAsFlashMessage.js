function showSuccess(message) {
  const flashMessageContainer = document.getElementById('flash-message-container');
  const alertBox = document.createElement('div');

  alertBox.textContent = message;
  alertBox.className = 'alert alert-success alert-dismissible fade show';

  const closeButton = document.createElement('button');
  closeButton.type = 'button';
  closeButton.className = 'btn-close';
  closeButton.setAttribute('data-bs-dismiss', 'alert');
  closeButton.setAttribute('aria-label', 'Chiudi');
  closeButton.addEventListener('click', () => flashMessageContainer.removeChild(alertBox));

  alertBox.appendChild(closeButton);
  flashMessageContainer.appendChild(alertBox);

  setTimeout(() => {
    if (flashMessageContainer.contains(alertBox)) {
      flashMessageContainer.removeChild(alertBox);
    }
  }, 3000);
}

function showError(message) {
  const flashMessageContainer = document.getElementById('flash-message-container');
  const alertBox = document.createElement('div');

  alertBox.textConstent = message;
  alertBox.className = 'alert alert-danger alert-dismissible fade show';

  const closeButton = document.createElement('button');
  closeButton.type = 'button';
  closeButton.className = 'btn-close';
  closeButton.setAttribute('data-bs-dismiss', 'alert');
  closeButton.setAttribute('aria-label', 'Chiudi');
  closeButton.addEventListener('click', () => flashMessageContainer.removeChild(alertBox));

  alertBox.appendChild(closeButton);
  flashMessageContainer.appendChild(alertBox);

  setTimeout(() => {
    if (flashMessageContainer.contains(alertBox)) {
      flashMessageContainer.removeChild(alertBox);
    }
  }, 3000);
}

function showInfo(message) {
  const flashMessageContainer = document.getElementById('flash-message-container');
  const alertBox = document.createElement('div');

  alertBox.textContent = message;
  alertBox.className = 'alert alert-info alert-dismissible fade show';

  const closeButton = document.createElement('button');
  closeButton.type = 'button';
  closeButton.className = 'btn-close';
  closeButton.setAttribute('data-bs-dismiss', 'alert');
  closeButton.setAttribute('aria-label', 'Chiudi');
  closeButton.addEventListener('click', () => flashMessageContainer.removeChild(alertBox));

  alertBox.appendChild(closeButton);
  flashMessageContainer.appendChild(alertBox);
}