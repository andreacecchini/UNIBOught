// Mappa per tradurre gli stati dall'inglese all'italiano
const statusMap = {
  'pending': 'In attesa',
  'processing': 'In lavorazione',
  'shipped': 'Spedito',
  'completed': 'Completato'
};

document.addEventListener('DOMContentLoaded', () => {
  // Aggiungere l'event listener a tutti i select per il cambio di stato
  document.querySelectorAll('.order-status').forEach(select => {
    select.addEventListener('change', function () {
      const orderId = this.id.split('-')[2];
      showStatusConfirmModal(orderId, this.value, this);
    });
  });
});

function showStatusConfirmModal(orderId, newStatus, selectEl) {
  const article = document.getElementById(`order-${orderId}`);
  const currentStatus = article.dataset.currentStatus;
  const modalEl = document.getElementById('changeStatusConfirmationModal');
  const bsModal = new bootstrap.Modal(modalEl);
  
  // Popolazione del modal con i dati dell'ordine
  modalEl.querySelector('.order-id').textContent = orderId;
  modalEl.querySelector('.current-status').textContent = statusMap[currentStatus];
  modalEl.querySelector('.new-status').textContent = statusMap[newStatus];
  
  // Rimuovere i vecchi bottoni di conferma e cancellazione
  const confirmBtn = modalEl.querySelector('.confirmBtn');
  const newConfirmBtn = confirmBtn.cloneNode(true);
  confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
  const cancelBtn = modalEl.querySelector('.cancelBtn');
  const newCancelBtn = cancelBtn.cloneNode(true);
  cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
  
  newConfirmBtn.addEventListener('click', function () {
    updateOrderStatus(orderId, newStatus)
      .then(() => {
        bsModal.hide();
      });
  });
  
  newCancelBtn.addEventListener('click', function () {
    selectEl.value = currentStatus;
    bsModal.hide();
  });
  bsModal.show();
}


const updateOrderStatus = (orderId, newStatus) => {
  const article = document.getElementById(`order-${orderId}`);
  return fetch('/orders/update-status', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({ orderId, status: newStatus })
  })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        throw new Error(data.message);
      }
      article.dataset.currentStatus = newStatus;
      showSuccess(`Ordine ${orderId} aggiornato a "${statusMap[newStatus]}"`);
    })
    .catch(e => showError(`Errore: ${e.message}`));
};
