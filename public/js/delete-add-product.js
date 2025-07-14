document.addEventListener('DOMContentLoaded', () => {
    const deleteBtn = document.querySelector('#deleteBtn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', () => {
            const productId = deleteBtn.dataset.productId;
            showConfirmDeletionModal(productId);
        });
    }
    const republicBtn = document.querySelector('#republicBtn');
    if (republicBtn) {
        republicBtn.addEventListener('click', () => {
            const productId = republicBtn.dataset.productId;
            showConfirmRepublicModal(productId);
        });
    }
});

function showConfirmDeletionModal(productId) {
    const modal = new bootstrap.Modal(document.getElementById('removeProductConfirmationModal'));
    const modalElement = modal._element;
    const confirmBtn = modalElement.querySelector('.confirmBtn');
    const cancelBtn = modalElement.querySelector('.cancelBtn');
    confirmBtn.addEventListener('click', () => {
        deleteProduct(productId);
        modal.hide();
    });
    cancelBtn.addEventListener('click', () => {
        modal.hide();
    });
    modal.show();
}

function showConfirmRepublicModal(productId) {
    const modal = new bootstrap.Modal(document.getElementById('republicProductConfirmationModal'));
    const modalElement = modal._element;
    const confirmBtn = modalElement.querySelector('.confirmBtn');
    const cancelBtn = modalElement.querySelector('.cancelBtn');
    confirmBtn.addEventListener('click', () => {
        republicProduct(productId);
        modal.hide();
    });
    cancelBtn.addEventListener('click', () => {
        modal.hide();
    });
    modal.show();
}

function deleteProduct(productId) {
    fetch(`/cancella-prodotto/${productId}`, {
        method: 'DELETE'
    }).then(_ => {
        // redirect alla home page
        window.location.href = '/';
    });
}

function republicProduct(productId) {
    fetch(`/ricarica-prodotto/${productId}`, {
        method: 'POST'
    }).then(_ => {
        // redirect alla home page
        window.location.href = '/';
    });
}