document.addEventListener('DOMContentLoaded', () => {
  // Ottengo l'header dove mettere il totale
  const totalHeader = document.querySelector('h1');

  const extractPrice = element => {
    const priceElement = element.querySelector('.product-price');
    return parseFloat(priceElement.textContent.replace('€', ''));
  };

  const extractQuantity = element => {
    const quantityInput = element.querySelector('.product-quantity');
    return parseInt(quantityInput.textContent);
  };

  const calculateSubtotal = element => extractPrice(element) * extractQuantity(element);

  function computeTotal() {
    const total = Array.from(document.querySelectorAll('main .card'))
      .map(calculateSubtotal)
      .reduce((sum, subtotal) => sum + subtotal, 0);

    totalHeader.innerText = `Totale €${total.toFixed(2)}`;
  }

  computeTotal();

  // Aggiungo event listener per aggiornare il totale quanto cambia la quantità
  document.querySelectorAll('.product-quantity').forEach(input => {
    input.addEventListener('change', computeTotal);
  });

  document.querySelectorAll('.btn-outline-secondary').forEach(button => {
    button.addEventListener('click', () => {
      // Aspetto un attimo per permettere all'input di aggiornarsi
      setTimeout(computeTotal, 50);
    });
  });
});