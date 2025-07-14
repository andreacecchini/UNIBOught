<?php
// Configurazione stati ordine
$statuses = [
    'pending' => 'In attesa',
    'processing' => 'In lavorazione',
    'shipped' => 'Spedito',
    'completed' => 'Completato',
    'cancelled' => 'Annullato'
];

$currentStatus = $order['status'] ?? 'pending';
?>

<article class="accordion-item order-item mt-3 border-top shadow border-2"
    data-current-status="<?= htmlspecialchars($currentStatus) ?>" id='order-<?= htmlspecialchars($orderId) ?>'>
    <h2 class="visually-hidden">Ordine</h2>
    <!-- Header dell'accordion -->
    <div class="row border-bottom align-items-center mx-0">

        <!-- Titolo ordine -->
        <section class="col-6 col-sm-9  ps-0">
            <h3 class="accordion-header mb-0">
                <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse-<?= $orderId ?>" aria-expanded="false"
                    aria-controls="collapse-<?= $orderId ?>">
                    <span class="fw-bold">
                        Ordine #<strong class="order-id"><?= htmlspecialchars($orderId) ?></strong>
                    </span>
                </button>
            </h3>
        </section>

        <!-- Stato dell'ordine -->
        <section class="col-6 col-sm-3  py-2">
            <h3 class="visually-hidden">Stato dell'ordine</h3>
            <?php if ($isVendor): ?>
                <!-- Select per venditori -->
                <label for="order-status-<?= htmlspecialchars(string: $orderId) ?>" class="visually-hidden">
                    Stato ordine #<?= htmlspecialchars($orderId) ?>
                </label>
                <select class="form-select border-0 form-select-sm order-status py-2 text-center fw-semibold"
                    id="order-status-<?= htmlspecialchars($orderId) ?>">
                    <?php foreach ($statuses as $statusKey => $statusView): ?>
                        <?php if ($statusKey !== 'cancelled'): ?>
                            <?php if ($statusKey === 'completed' && !$order['isPaid']): ?>
                            <?php else: ?>
                                <option value="<?= htmlspecialchars($statusKey) ?>" <?= $statusKey === $currentStatus ? 'selected' : '' ?>
                                    class="text-dark">
                                    <?= htmlspecialchars($statusView) ?>
                                </option>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <!-- Span per clienti -->
                <span
                    class="form-select form-select-sm status border-0 pe-3 py-2 me-3 d-inline-block text-center fw-semibold <?= $currentStatus === 'cancelled' ? 'text-danger' : '' ?>"
                    style="background-image: none">
                    <?= htmlspecialchars($statuses[$currentStatus]) ?>
                </span>
            <?php endif; ?>
        </section>

    </div>
    <!-- Contenuto dell'ordine -->
    <div id="collapse-<?= $orderId ?>" class="accordion-collapse collapse" data-bs-parent="#orderAccordion">

        <section class="accordion-body">
            <h3 class="h5 text-dark mb-3">Dettaglio prodotti</h3>
            <!-- Lista prodotti -->
            <?php if (!empty($order['items'])): ?>
                <!-- Singolo prodotto -->
                <?php foreach ($order['items'] as $item): ?>
                    <article class="card mb-3 order-product border-2 shadow">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <!-- Immagine prodotto -->
                                <div class="col-4 col-sm-3 col-md-2 col-lg-1 text-center">
                                    <img src="/images/<?= htmlspecialchars($item['product']['image_name']) ?>"
                                        alt="<?= htmlspecialchars($item['product']['image_alt']) ?>" class="img-fluid" />
                                </div>

                                <!-- Dettagli prodotto -->
                                <div class="col-8 col-sm-9 col-md-10 col-lg-11">
                                    <h4 class="h6 mb-1">
                                        <a href="/dettaglio-prodotto/<?= htmlspecialchars($item['product']['id']) ?>"
                                            class="text-decoration-none fw-bold product-name text-primary">
                                            <?= htmlspecialchars($item['product']['name']) ?>
                                        </a>
                                    </h4>
                                    <small class="text-dark fw-medium">
                                        €<?= number_format($item['purchase_unit_price'], 2, ',', '.') ?> ×
                                        <?= $item['quantity'] ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Nessun prodotto trovato in questo ordine.</p>
            <?php endif; ?>
            <!-- Informazioni cliente (solo per venditori) -->
            <?php if ($isVendor): ?>
                <p class="text-dark mb-0">
                    Ordine di: <strong class="customer-name">
                        <?= htmlspecialchars($order['client']['name'] . " " . $order['client']['surname']) ?>
                    </strong>
                </p>
            <?php endif; ?>
        </section>

        <!-- Footer con link e totale -->
        <footer class="border-top p-3">
            <div class="row align-items-center">
                <div class="col-6">
                    <a href="/dettaglio-ordine/<?= htmlspecialchars($orderId) ?>" class="link fw-medium">
                        Vedi Dettagli Ordine
                    </a>
                </div>
                <div class="col-6 text-end">
                    <span>Totale ordine:</span>
                    <strong class="fs-6">€<?= number_format($order['total'], 2, ',', '.') ?></strong>
                </div>
            </div>
        </footer>
    </div>
</article>