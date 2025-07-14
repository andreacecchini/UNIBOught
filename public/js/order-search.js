document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('#cerca-ordine');
    const nameFilter = document.querySelector('#nome');
    const statusFilter = document.querySelector('#status-filter')
    const orderItems = document.querySelectorAll('.order-item');
    searchInput.addEventListener('input', applyFilters);
    nameFilter?.addEventListener('input', applyFilters);
    statusFilter.addEventListener('change', applyFilters);

    function applyFilters() {
        document.querySelectorAll('.alert')
            .forEach(alert => alert.remove());

        const searchPattern = searchInput.value.toLowerCase().trim();
        const namePattern = nameFilter?.value.toLowerCase().trim();
        const status = statusFilter.value;
        hideOrders();

        filteredOrders = Array.from(orderItems)
            .filter(byPatternMatching)
            .filter(byStatus)
            .filter(byName);

        filteredOrders.length === 0 ? showAlert() : filteredOrders.forEach(show);

        function byPatternMatching(order) {
            return orderIdMatchesPattern(order) ||
                Array
                    .from(order.querySelectorAll('.order-product'))
                    .some(productNameMatchPattern);
        }
        function byStatus(order) {
            const orderStatus = order.dataset.currentStatus;
            return status === 'all' || orderStatus === status;
        }

        function orderIdMatchesPattern(order) {
            const orderId = order.querySelector('.order-id').textContent.toLowerCase().trim();
            return orderId.includes(searchPattern)
        }

        function byName(order) {
            // Se il campo "nome" è vuoto, allora non si filtra per nome
            if (namePattern === '' || namePattern === undefined) {
                return true;
            }
            const customerName = order.querySelector('.customer-name').textContent.toLowerCase().trim();
            return customerName.includes(namePattern);
        }

        function productNameMatchPattern(product) {
            const productName = product.querySelector('.product-name').textContent.toLowerCase().trim();
            return productName.includes(searchPattern);
        }
    }

    function hide(element) {
        element.style.display = 'none';
    }

    function show(element) {
        element.style.display = 'block';
    }

    function hideOrders() {
        orderItems.forEach(hide);
    }

    function showAlert() {
        const noResultsMessage = document.createElement('p');
        noResultsMessage.className = 'alert alert-info mt-3';
        noResultsMessage.textContent = 'Nessun ordine corrisponde ai filtri selezionati.';
        document.getElementById('orders-list').append(noResultsMessage);
    }
});