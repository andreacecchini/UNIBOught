<h1>Totale</h1>
<div class="row">
  <?php if (empty($products)): ?>
    <div class="col-12">
      <div class="alert alert-info text-center">
        <strong>Il tuo carrello è vuoto.</strong>
      </div>
    </div>
  <?php else: ?>
    <?php foreach ($products as $id => $product): ?>
      <div class="col-lg-6 col-xl-4 mb-3">
        <?php require SOURCE_DIR . '/views/components/product-card.php' ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="col-12 text-center">
    <a href="/checkout" class="btn btn-primary btn-lg w-100">
      <span class="fas fa-shopping-cart me-2"></span>
      Checkout
    </a>
  </div>
</div>

<script src="/js/compute-total.js"></script>
<script src="/js/product-card.js"></script>
<script src="/js/increment-decrement-quantity.js"></script>