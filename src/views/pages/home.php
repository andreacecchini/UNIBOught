<h1 class="visually-hidden">Lista prodotti</h1>
<section>
  <h2 class="my-3 h1">Prodotti</h2>
  <div class="row">
    <?php if (empty($products)): ?>
      <p class="col-12 alert alert-info" role="alert">
        Nessun prodotto trovato.
      </p>
    <?php else: ?>
      <?php foreach ($products as $product): ?>
        <div class="col-lg-6 col-xl-4 mb-3">
          <?php require SOURCE_DIR . '/views/components/product-card.php' ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
<?php if ($context === 'vendor'): ?>
  <section class="mt-3">
    <h2 class="my-3 h1">Rimossi</h2>
    <div class="row">
      <?php if (empty($removedProducts)): ?>
        <p class="col-12 alert alert-info" role="alert">
          Nessun prodotto con queste caratteristiche è in quarantena.
        </p>
      <?php endif; ?>
      <?php foreach ($removedProducts as $product): ?>
        <div class="col-lg-6 col-xl-4 mb-3">
          <?php require SOURCE_DIR . '/views/components/product-card.php' ?>
        </div>
      <?php endforeach; ?>

    </div>
  </section>
<?php endif; ?>


<script src="/js/product-card.js"></script>