document.addEventListener('DOMContentLoaded', () => {
    const cancelOrderBtn = document.querySelector('#cancelOrderBtn');
    const markAsPaidBtn = document.querySelector('#markAsPaidBtn');
    // Se il bottone non è dispobile, allora non si esegue lo script
    if (cancelOrderBtn) {
        const modalElement = document.getElementById('cancelOrderConfirmationModal');
        const modal = new bootstrap.Modal(modalElement);
        const confirmBtn = modalElement.querySelector('.confirmBtn');
        cancelOrderBtn.addEventListener('click', () => {
            modal.show();
        });
        confirmBtn.addEventListener('click', async () => {
            const orderId = cancelOrderBtn.dataset.orderId;
            await fetch('/dettaglio-ordine/' + orderId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            // redirect verso storico-ordini 
            window.location.href = '/storico-ordini';
        });
    }
    if (markAsPaidBtn) {
        markAsPaidBtn.addEventListener('click', async () => {
            const orderId = markAsPaidBtn.dataset.orderId;
            await fetch('/dettaglio-ordine/' + orderId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            // redirect verso storico-ordini 
            window.location.href = '/storico-ordini';
        });
    }
});