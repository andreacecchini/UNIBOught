<?php
use \models\Client;
use \core\Session;

?>
<h1 class="h4"><?= htmlspecialchars($product['name']) ?></h1>
<?php
$n = $product['average_rating'] ?? 0;
$showText = true;
require SOURCE_DIR . '/views/components/review-stars.php';
?>

<section class="mt-4">
    <h2 class="visually-hidden">Immagine odotto</h2>
    <figure class="d-flex justify-content-center align-items-center">
        <img src="<?= '/images/' . $product['image_name'] ?>" alt="<?= htmlspecialchars($product['image_alt']) ?>"
            class="img-fluid object-fit-contain">
    </figure>
</section>

<section class="mt-4">
    <h2 class="h4 mb-4">Informazioni</h2>
    <div class="row">
        <div class="col-6">
            <p class="h5">€<?= $product['price'] ?></p>
        </div>
        <div class="col-6 text-end">
            <p class="text-muted">Consegna: <?= $dataSpedizione ?></p>
        </div>
    </div>
    <?php
    $quantity = $product['quantity'];
    $coloreQ = $quantity >= 10 ? 'text-success' : ($quantity >= 4 && $quantity <= 9 ? 'text-warning' : 'text-danger');
    ?>
    <p class="<?= $coloreQ ?> mt-2">Disponibili all'acquisto: <?= $quantity ?> unità</p>
</section>

<section>
    <h2 class="h5 mb-3">Descrizione prodotto</h2>
    <p>
        <?= htmlspecialchars($product['description']) ?>
    </p>
</section>

<?php if ($isVendor): ?>
    <section class="mt4">
        <h2 class="visually-hidden">Gestione Prodotto</h2>
        <?php if ($isRemoved): ?>
            <a href="/modifica-prodotto/<?= $product['id'] ?>" class="btn btn-outline-secondary border-2 shadow w-100 mt-2">
                <span class="bi bi-pencil me-2"></span>Modifica</a>
            <button type="button" class="btn btn-primary border-2 shadow w-100 mt-2" id="republicBtn"
                data-product-id="<?= $product['id'] ?>"><span class="bi bi-arrow-clockwise me-2"></span>Ripubblica</button>
        <?php else: ?>
            <a href="/modifica-prodotto/<?= $product['id'] ?>" class="btn btn-outline-secondary border-2 shadow w-100 mt-2">
                <span class="bi bi-pencil me-2"></span>Modifica</a>
            <button type="button" class="btn btn-danger border-2 shadow w-100 mt-2" id="deleteBtn"
                data-product-id="<?= $product['id'] ?>"><span class="bi bi-trash me-2"></span>Rimuovi dallo store</button>
        <?php endif; ?>
    </section>
<?php else: ?>
    <section class="mt-4">
        <h2 id="purchase-form" class="visually-hidden">Modulo d'acquisto</h2>
        <form action="/carrello/add-product" method="POST" class="d-flex flex-column gap-3 align-items-center">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
            <div class="input-group border rounded shadow">
                <label for="quantity" class="input-group-text">Quantità</label>
                <input type="number" name="quantity" min="1" max="<?= $quantity ?>" value="1" id="quantity"
                    class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary w-100" <?= $quantity === 0 ? 'disabled' : '' ?>><span
                    class="bi bi-cart-plus me-2"></span> Aggiungi al
                carrello</button>
        </form>
    </section>
<?php endif; ?>

<section class="my-4">
    <h2>Recensioni</h2>
    <?php
    $client = Session::isLoggedIn() ? Client::findById(Session::get('user')['id']) : null;
    $hasReviewed = $client ? $client->hasReviewed($product['id']) : false;
    $linkText = $hasReviewed ? 'Modifica la tua recensione' : 'Aggiungi recensione';
    ?>
    <?php if ($client && $client->hasPurchased($product['id'])): ?>
        <a href="/aggiungi-recensione?product_id=<?= $product['id'] ?>" class="link-secondary"
            aria-label="<?= $linkText ?>">
            <?= $linkText ?>
        </a>
    <?php endif; ?>
    <?php if (empty($product['reviews'])): ?>
        <p class="text-muted mt-3">Questo prodotto non ha ancora recensioni.</p>
    <?php else: ?>
        <?php foreach ($product['reviews'] as $review): ?>
            <?php require SOURCE_DIR . '/views/components/review.php'; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<?php if ($isVendor): ?>
    <?php
    $modalId = 'removeProductConfirmationModal';
    $modalTitle = 'Conferma rimozione';
    $modalBody = "<strong>Confermi di voler rimuovere il prodotto dallo store?</strong><br />Il prodotto verrà reso <strong>indisponibile</strong> per l'acquisto, ma potrai <strong>ripubblicarlo</strong> in un secondo momento.";
    require SOURCE_DIR . '/views/components/modal.php'
        ?>
    <?php
    $modalId = 'republicProductConfirmationModal';
    $modalTitle = 'Conferma Ripubblicazione';
    $modalBody = "<strong>Confermi di voler ripubblicare il prodotto nello store?</strong>";
    require SOURCE_DIR . '/views/components/modal.php'
        ?>
<?php endif; ?>

<script src="/js/increment-decrement-quantity.js"></script>

<?php if ($isVendor): ?>
    <script src="/js/delete-add-product.js"></script>
<?php endif; ?>