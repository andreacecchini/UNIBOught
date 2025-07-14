<?php
/**
 * Componente controllo della quantità
 * 
 * Parametri:
 * @param string $inputId  ID dell'input (default: 'quantita' o generato da $productId se presente)
 * @param int $initialQuantity  Valore iniziale (default: 1)
 * @param string $productId  ID del prodotto per generare un ID univoco (opzionale)
 * @param bool $makeAjaxRequest  Indica se alla modifica della quantità corrisponde una richiesta AJAX al server per modificare i dati.  (default: true)
 */
$inputId = isset($inputId) ? $inputId : 'quantita';
$initialQuantity = isset($initialQuantity) ? $initialQuantity : 1;
$makeAjaxRequest = isset($makeAjaxRequest) ? $makeAjaxRequest : true;
?>

<button type="button" onclick="decrementQuantity('<?= $inputId ?>')"
  class="btn btn-outline-secondary rounded-circle inc-dec-button cart-action">-</button>
<span class="product-quantity mx-2 fw-bold" id="<?= $inputId ?>"><?= $initialQuantity ?></span>
<button type="button" onclick="incrementQuantity('<?= $inputId ?>')"
  class="btn btn-primary rounded-circle inc-dec-button cart-action">+</button>