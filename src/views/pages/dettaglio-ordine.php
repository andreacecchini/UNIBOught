<h1>Dettaglio Ordine</h1>

<section class="border-bottom border-2 pt-1 my-3">
  <h2 class="border-bottom border-2 pb-2">Informazioni</h2>
  <div class="d-flex justify-content-between">
    <p>N° Ordine</p>
    <strong><?= $id ?></strong>
  </div>
  <div class="d-flex justify-content-between">
    <p>Cliente</p>
    <strong><?= $order['client']['name'] . " " . $order['client']['surname'] ?></strong>
  </div>
  <div class="d-flex justify-content-between">
    <p>Ordine piazzato</p>
    <strong><?= date('d/m/Y', strtotime($order['order_date'])) ?></strong>
  </div>
  <div class="d-flex justify-content-between">
    <p>Disponibile al ritiro</p>
    <?php if ($order['status'] === 'cancelled'): ?>
      <strong>Non disponibile</strong>
    <?php else: ?>
      <strong><?= date('d/m/Y', strtotime($order['expected_pickup_date'])) ?></strong>
    <?php endif; ?>
  </div>
  <div class="d-flex justify-content-between">
    <p>Pagamento</p>
    <?php if ($order['status'] === 'cancelled'): ?>
      <strong>Non disponibile</strong>
    <?php else: ?>
      <strong><?= $order['isPaid'] ? 'Pagato' : 'Non pagato' ?></strong>
    <?php endif; ?>
  </div>
</section>

<section>
  <h2 class="h3">Totale (IVA inclusa): €<?= number_format($order['total'], 2, ',', '.') ?></h2>
  <?php
  $statuses = [
    'pending' => 'In attesa',
    'processing' => 'In lavorazione',
    'shipped' => 'Spedito',
    'completed' => 'Completato',
    'cancelled' => 'Annullato',
  ];
  ?>
  <p>Stato: <strong data-order-status="<?= $order['status'] ?>"
      class="<?= $order['status'] === 'cancelled' ? 'text-danger' : '' ?>"><?= $statuses[$order['status']] ?></strong>
  </p>
  <div class="row">
    <?php if (isset($order['items']) && !empty($order['items'])): ?>
      <?php foreach ($order['items'] as $item): ?>
        <div class="col-lg-6 col-xl-4 mb-3">
          <?php
          // Adattiamo i contenuti di $item dell'ordine per la product-card
          $product = $item;
          if (isset($item['product'])) {
            $product = array_merge($product, $item['product']);
            $product['price'] = $item['purchase_unit_price'];
          } else {
            $product['price'] = $item['purchase_unit_price'] ?? $item['price'] ?? 0;
            $product['quantity'] = $item['quantity'] ?? 1;
          }
          require SOURCE_DIR . '/views/components/product-card.php';
          ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <p class="text-muted">Nessun prodotto trovato per questo ordine.</p>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php if (!$isVendor && $order['status'] === 'pending'): ?>
  <button type="button" class="btn btn-danger fs-2 w-100 mt-2" id="cancelOrderBtn" data-order-id="<?= $id ?>">
    Annulla ordine
  </button>
  <?php
  $modalId = 'cancelOrderConfirmationModal';
  $modalTitle = 'Conferma annullamento ordine';
  $modalBody = "<strong>Confermi di voler annullare l'ordine?</strong>";
  require SOURCE_DIR . '/views/components/modal.php'
    ?>
<?php endif; ?>

<?php if ($isVendor && !$order['isPaid']): ?>
  <button type="button" class="btn btn-primary fs-2 w-100 mt-2" id="markAsPaidBtn" data-order-id="<?= $id ?>">
    Segna come pagato
  </button>
  <?php
  $modalId = 'markAsPaidConfirmationModal';
  $modalTitle = 'Conferma pagamento';
  $modalBody = "<strong>Confermi di voler segnare l'ordine come pagato?</strong>";
  require SOURCE_DIR . '/views/components/modal.php'
    ?>
<?php endif; ?>


<script src="/js/order-detail.js"></script>