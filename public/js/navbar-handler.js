//funzione che fa l'update del badge delle notifiche
export function updateNotificationBadge() {
    const notificationBadge = document.querySelector('.notification-badge');

    if (!notificationBadge) {
        console.error('badge notifiche non trovato');
        return;
    }
    fetch('/notifiche/unread-count', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Errore risposta ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const unreadCount = data.unreadCount;
            const message = data.message;
            const previousUnreadCount = parseInt(localStorage.getItem('previousUnreadCount')) || 0;

            if (unreadCount > 0) {
                notificationBadge.textContent = data.unreadCount;
                notificationBadge.style.display = 'inline-block';

                if (unreadCount > previousUnreadCount) {
                    showNotificationToast(message);
                }
                localStorage.setItem('previousUnreadCount', unreadCount.toString());
            } else {
                localStorage.removeItem('previousUnreadCount');
                notificationBadge.style.display = 'none';
            }
        })
        .catch(error => console.error('Errore nel fetch delle notifiche non lette:', error));

}

//funzione di debounce per limitare la frequenza di chiamata 
function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

//funzione che fa l'update del badge del carrello
function updateCartBadge() {
    fetch('/carrello/count-products', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) throw new Error(`Errore risposta ${response.status}`);
            return response.json();
        })
        .then(data => {
            const cartBadge = document.querySelector('.cart-badge');
            if (!cartBadge) return;

            if (data.totalCount > 0) {
                cartBadge.textContent = data.totalCount;
                cartBadge.style.display = 'inline-block';
            } else {
                cartBadge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Errore nel fetch del carrello:', error);
        });
}

//debounce della funzione updateCartBadge
const debouncedUpdateCartBadge = debounce(updateCartBadge, 100);

function showNotificationToast(message = 'Nuova notifica') {
    const toastElement = document.getElementById('notification-toast');
    const toastBody = toastElement.querySelector('.toast-body');
    const previousUnreadCount = parseInt(localStorage.getItem('previousUnreadCount')) || 0;

    toastBody.textContent = `(${previousUnreadCount + 1}) ${message}`;

    toastElement.addEventListener('click', (event) => {
        if (event.target.closest('.btn-close')) {
            return;
        }
        window.location.href = '/notifiche';
    });

    const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 10000 });
    toastElement.style.display = 'block';
    toastElement.style.cursor = 'pointer';
    toast.show();
}

document.addEventListener('click', event => {
    const cartButton = event.target.closest('.cart-action');
    if (cartButton)
        debouncedUpdateCartBadge();
}
);

document.addEventListener('DOMContentLoaded', () => {
    if (isLoggedIn) {
        setInterval(updateNotificationBadge, 10000);
        if (!isVendor) {
            setInterval(updateCartBadge, 10000);
        }
        updateNotificationBadge();
    }
    if (!isVendor) {
        updateCartBadge();
    }
});