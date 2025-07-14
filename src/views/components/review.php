<?php
/**
 * Componente per mostrare una recensione
 *
 * Parametri:
 * @param array $review - Dati della recensione
 */
?>
<article class="border border-2 p-3 mt-4 bg-white shadow">
  <header class="mb-3">
    <h3 class="d-flex align-items-center gap-2 mb-2 h4">
      <span class="bi bi-person-circle fs-4"></span>
      <span class="fw-semibold"><?= $review['username'] ?></span>
    </h3>
    <?php
    $n = $review['rating'];
    require SOURCE_DIR . '/views/components/review-stars.php';
    unset($n);
    ?>
    <p class="mt-2 text-muted"><?= $review['review_date'] ?></p>
  </header>
  <section>
    <h3><?= htmlspecialchars($review['title']) ?></h3>
    <p><?= htmlspecialchars($review['content']) ?></p>
  </section>
</article>