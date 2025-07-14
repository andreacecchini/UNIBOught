/**
 * Diminuisce la quantità di un prodotto
 */
function decrementQuantity(inputId) {
  const input = document.getElementById(inputId);
  if (!input) return;

  const currentValue = parseInt(input.textContent);
  if (currentValue > 1) {
    const newValue = currentValue - 1;
    updateQuantity(inputId, newValue, currentValue);
  } else {
    const productId = inputId.replace('quantita-', '');
    removeItemFromCart(productId);
  }
}

/**
 * Aumenta la quantità di un prodotto
 */
function incrementQuantity(inputId) {
  const input = document.getElementById(inputId);
  if (!input) return;

  const currentValue = parseInt(input.textContent);
  if (currentValue < 100) {
    const newValue = currentValue + 1;
    updateQuantity(inputId, newValue, currentValue);
  }
}

/**
 * Aggiorna la quantità di un prodotto
 */
function updateQuantity(inputId, newValue, oldValue) {
  const input = document.getElementById(inputId);
  if (!input) return;

  // Aggiorna subito l'input per feedback immediato
  input.textContent = newValue;

  // Scatena l'evento 'change' in modo da aggiornare eventuali listener
  input.dispatchEvent(new Event('change'));

  sendQuantityUpdate(inputId, newValue, oldValue);
}

/**
 * Invia la richiesta di aggiornamento quantità al server
 */
async function sendQuantityUpdate(inputId, newQuantity, oldQuantity) {
  const input = document.getElementById(inputId);
  const productId = inputId.replace('quantita-', '');

  try {
    const response = await fetch('/carrello/update-quantity', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        product_id: productId,
        quantity: newQuantity
      })
    });

    const data = await response.json();

    if (!data.success) {
      // Ripristina il valore precedente se l'operazione fallisce
      input.textContent = oldQuantity;
      showError(data.message || 'Errore nell\'aggiornamento della quantità');
    }
  } catch (error) {
    // Ripristina il valore precedente in caso di errore di rete
    input.textContent = oldQuantity;
    showError('Errore di connessione durante l\'aggiornamento');
  }
}

/**
 * Mostra un messaggio di errore
 */
function showError(message) {
  console.error('Errore:', message);
  alert('Errore: ' + message);
}