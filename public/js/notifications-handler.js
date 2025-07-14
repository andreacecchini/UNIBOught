import { updateNotificationBadge } from './navbar-handler.js';

function hyperlinkMessage(message, reference) {
    //regex per trovare l'id dell'ordine
    const orderRegex = /ordine #(\d+)/;
    if (orderRegex.test(message)) {
        //sostituisco il regex con un link avente come testo il regex stesso
        return message.replace(orderRegex, (match) => {
            return `<a href="/dettaglio-ordine/${reference}" aria-label="Vai alla pagina dettaglio ordine per il tuo ordine numero ${reference}">${match}</a>`;
        });
    }

    //regex per trovare il nome del prodotto e l'id
    const productRegex = /"([^"]+)"/;
    if (productRegex.test(message)) {
        //sostituisco il regex con un link avente come testo il nome del prodotto
        return message.replace(productRegex, (match, productName) => {
            return `<a href="/dettaglio-prodotto/${reference}" aria-label="Vai alla pagina prodotto ${productName} con id: ${reference}">${productName}</a> `;
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('lista-notifiche');
    const markAllAsRead = document.getElementById('mark-all-as-read');
    //funzione che fetcha le notifiche
    function fetchNotifications() {
        fetch('/notifiche/fetch')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Errore nel fetch delle notifiche');
                }
                return response.json();
            })
            .then(data => {
                if (!container) {
                    console.error('Elemento con id "lista-notifiche" non trovato');
                    return;
                }
                if (!Array.isArray(data)) {
                    console.error('I dati fetchati non sono array', data);
                    return;
                }
                //controllo che ci sia almeno una notifica
                if (data.length === 0) {
                    container.innerHTML = '<div class="text-center alert alert-info"><p class="m-0 p-0">Nessuna notifica ricevuta</p></div>';
                    return;
                }
                //ordino le notifiche per is_read (le non lette prima)
                data.sort((a, b) => a.is_read - b.is_read);
                container.innerHTML = '';

                data.forEach(notif => {
                    const isReadClass = notif.is_read ? 'text-secondary' : 'text-primary';
                    const isReadIcon = notif.is_read ? 'bi-bell' : 'bi-bell-fill';

                    //calcolo il tempo trascorso dalla data di invio della notifica e ora
                    const sentDate = new Date(notif.sent_date);
                    const now = Date.now();
                    const diffInMs = now - sentDate.getTime();
                    const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60));
                    const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
                    const diffInSeconds = Math.floor(diffInMs / 1000);               
                    const timeAgo = diffInSeconds < 60
                        ? `${diffInSeconds} secondi fa`
                        : diffInMinutes < 60
                            ? `${diffInMinutes} minuti fa`
                            : diffInHours < 24
                                ? `${diffInHours} ore fa`
                                : `${Math.floor(diffInHours / 24)} giorni fa`;
                    //converto parte della notifica in link se necessario
                    const messageWithLink = hyperlinkMessage(notif.message, notif.reference) || notif.message;
                    const notifHTML = `
                    <li class="list-group-item mb-2 rounded border border-2 shadow" data-id="${notif.id}">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="bi ${isReadIcon} fs-3 ${isReadClass}"></i>
                        </div>
                        <div class="col">
                            <div>${messageWithLink}</div>
                            <div class="text-muted small">${timeAgo}</div>
                            ${!notif.is_read ? `
                            <div class="col-sm-auto">
                                <button class="btn btn-sm btn-outline-primary mt-2 p-1 mark-as-read">Segna come letta</button>
                            </div>` : `
                            <div class="col-sm-auto">
                                <button class="btn btn-sm btn-outline-danger mt-2 p-1 delete">Elimina</button>
                            </div>`}
                        </div>
                    </div>
                </li>`;
                    container.innerHTML += notifHTML;
                });

                updateNotificationBadge();
            })
            .catch(error => console.error('Errore nel fetch delle notifiche:', error));
    }

    container.addEventListener('click', (event) => {
        if (event.target.classList.contains('mark-as-read')) {
            const notifId = event.target.closest('li').getAttribute('data-id');
            fetch(`/notifiche/mark-as-read/${notifId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failato nel segnare la notifica come letta');
                    }
                    return response.json();
                })
                .then(data => {
                    fetchNotifications();
                })
                .catch(error => console.error('Errore nel segnare la notifica come letta:', error));
        } else if (event.target.classList.contains('delete')) {
            const notifId = event.target.closest('li').getAttribute('data-id');
            fetch(`/notifiche/delete/${notifId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failato nell\'eliminare la notifica');
                    }
                    return response.json();
                })
                .then(data => {
                    fetchNotifications();
                })
                .catch(error => console.error('Errore nell\'eliminare la notifica:', error));
        }
    });

    markAllAsRead.addEventListener('click', () => {
        fetch('/notifiche/mark-all-as-read', {
            method: 'POST'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failato nel segnare tutte le notifiche come lette');
                }
                return response.json();
            })
            .then(data => {
                fetchNotifications();
            })
            .catch(error => console.error('Errore nel segnare tutte le notifiche come lette:', error));
    });

    setInterval(fetchNotifications, 3000);
    fetchNotifications();
});
