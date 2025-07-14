<?php
/**
 * Componente card prodotto
 *
 * Parametri:
 * @param array $product - Dati del prodotto
 * @param string $context - Contesto di visualizzazione ('list', 'cart', ecc.)
 * @param bool $showActions - Mostra i pulssanti di azione
 */
$context = isset($context) ? $context : 'list';
$showActions = isset($showActions) ? $showActions : true;
?>

<article class="card <?= $context === 'cart' ? 'product-card-tall' : 'product-card-medium' ?> h-100 border-2 shadow">
    <h2 class="visually-hidden">Card del prodotto</h2>
    <div class="row g-0 h-100">
        <!-- Colonna dell'immagine -->
        <section class="col-5 h-100">
            <h3 class="visually-hidden">Immagine prodotto</h3>
            <div class="border-end border-2 h-100 text-center d-flex align-items-center justify-content-center">
                <a href="/dettaglio-prodotto/<?= $product['id'] ?>" class="text-decoration-none h-100 d-flex align-items-center justify-content-center">
                    <img src="/images/<?= $product['image_name'] ?>" alt="<?= $product['image_alt'] ?>"
                        class="img-fluid p-2">
                </a>
            </div>
        </section>
        <!-- Colonna del contenuto della card -->
        <section class="col-7 card-body h-100">
            <!-- Titolo della card -->
            <h3 class="card-title h6">
                <a href="/dettaglio-prodotto/<?= $product['id'] ?>"
                    class="link-dark text-decoration-none">
                    <?= strlen($product['name']) > 25 ? substr($product['name'], 0, 25) . '...' : $product['name'] ?>
                </a>
            </h3>
            <!-- Componente per visualizzare il numero di stelle di un prodotto -->
            <?php
            $n = $product['average_rating'] ?? 0;
            $showText = false;
            require SOURCE_DIR . '/views/components/review-stars.php';
            ?>
            <p class="fw-bold product-price">€<?= $product['price'] ?></p>
            <!-- Se il contesto è il dettaglio ordine, 
                         allora viene visualizzata la quantità acquistata nell'ordine -->
            <?php if ($context === 'orderDetail'): ?>
                <p>Qty. <?= $product['quantity'] ?></p>
            <?php endif; ?>
            <!-- Se il contesto è il carrello, 
                         allora viene visualizzato il componente per incrementare e decrementare la quantità
                         nel carrello  -->
            <?php if ($context === 'cart'): ?>
                <div class="mb-3">
                    <?php
                    $inputId = 'quantita-' . $product['id'];
                    $initialQuantity = $product['quantity'];
                    require SOURCE_DIR . '/views/components/quantity-input.php';
                    ?>
                </div>

            <?php endif; ?>
            <!-- Zona bottoni -->
            <?php if ($showActions): ?>
                <div class="mt-5 mb-2">
                    <!-- Se il contesto è il carrello, allora viene visualizzato il bottone "Rimuovi" -->
                    <?php if ($context === 'cart'): ?>
                        <button type="button" id="removeBtn'<?= $product['id'] ?>'"
                            onclick="removeItemFromCart('<?= $product['id'] ?>')" class="btn btn-danger w-100 shadow"
                            aria-label="Rimuovi prodotto dal carrello">
                            <span class="bi bi-trash me-2"></span> <span class="d-none d-sm-inline">Rimuovi</span>
                        </button>
                        <!-- Se il contesto è quello del venditore, allora viene visualizzato il bottone "Modifica" -->
                    <?php elseif ($context === 'vendor'): ?>
                        <a href="/modifica-prodotto/<?= $product['id'] ?>" class="btn btn-secondary w-100 shadow"
                            aria-label="Modifica prodotto">
                            <span class="bi bi-pencil me-2"></span> Modifica</a>
                    <?php else: ?>
                        <!-- Altrimenti, viene visualizzato il bottone "Aggiungi al carrello" -->
                        <button type="button" id="addBtn'<?= $product['id'] ?>'" class="btn btn-primary cart-action w-100 shadow"
                            aria-label="Aggiungi prodotto al carrello" onclick="addItemToCart('<?= $product['id'] ?>')">
                            <span class="bi bi-cart-plus me-2"></span> <span class="d-none d-sm-inline">Aggiungi</span>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</article>