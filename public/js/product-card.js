/**
 * Gestisce le richieste AJAX al carrello
 */
async function sendCartRequest(endpoint, productId, onSuccess = (data) => { }) {
    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ product_id: productId })
        });
        const data = await response.json();
        if (data.success) {
            onSuccess(data)
        } else {
            showError(data.message || 'Operazione fallita');
        }
    } catch (error) {
        showError('Errore di connessione');
    }
}

/**
 * Rimuove un prodotto dal carrello
 */
function removeItemFromCart(productId) {
    sendCartRequest('/carrello/remove-product', productId, () => {
        location.reload();
    });
}

/**
 * Aggiunge un prodotto al carrello
 */
function addItemToCart(productId) {
    sendCartRequest('/carrello/add-product', productId, (data) => { showSuccess(data.message); });
}