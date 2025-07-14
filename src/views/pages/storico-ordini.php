<h1 class="mb-4">Storico Ordini</h1>

<!-- Searchbox -->
<section class="row">
    <h2 class="visually-hidden">Filtra Ordini</h2>
    <!-- Filtro per prodotto o per id dell'ordine -->
    <div class="<?= $isVendor ? '' : 'col-md-8' ?>">
        <div class="input-group mb-3 shadow rounded">
            <label for="cerca-ordine" class="visually-hidden">Cerca ordine</label>
            <input class="form-control" type="text" name="cerca-ordine" id="cerca-ordine"
                placeholder="Cerca per numero ordine o prodotto" />
        </div>
    </div>
    <!-- Filtro per cliente, solo per venditore-->
    <?php if ($isVendor): ?>
        <div class="col-md-8">
            <div class="input-group mb-3 shadow rounded">
                <label for="nome" class="input-group-text text-bg-primary">Cliente</label>
                <input class="form-control" type="text" name="nome" id="nome"
                    placeholder="Cerca per numero ordine o prodotto" />
            </div>
        </div>
    <?php endif; ?>
    <!-- Filtro per stato -->
    <div class="col-md-4">
        <div class="input-group mb-3 shadow rounded">
            <label class="input-group-text text-bg-primary" for="status-filter">Stato</label>
            <select class="form-select" id="status-filter">
                <option value="all">Tutti gli stati</option>
                <option value="pending">In attesa</option>
                <option value="processing">In lavorazione</option>
                <option value="shipped">Spedito</option>
                <option value="completed">Completato</option>
                <?php if (!$isVendor): ?>
                    <option value="cancelled">Annullato</option>
                <?php endif; ?>
            </select>
        </div>
    </div>
</section>

<!-- Lista degli ordini passati -->
<section id="orders-list">
    <h2 class="visually-hidden">Lista Ordini</h2>
    <?php if (empty($orders)): ?>
        <p class="alert alert-info" role="alert">
            <?= $isVendor ? 'Non ci sono ancora ordini.' : 'Nessun ordine effettuato.' ?>
        </p>
    <?php else: ?>
        <div class="accordion">
            <?php foreach ($orders as $orderId => $order): ?>
                <?php require SOURCE_DIR . '/views/components/order-accordion-card.php' ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Modal -->
<?php
$modalId = 'changeStatusConfirmationModal';
$modalTitle = 'Modifica stato';
$modalBody = "Confermi di voler modificare lo stato dell'ordine <strong class=\"order-id\"></strong> da
        \"<strong class=\"current-status\"></strong>\" a \"<strong class=\"new-status\"></strong>\"?";
require SOURCE_DIR . '/views/components/modal.php'
    ?>


<!-- Script per il filtro degli ordini -->
<script src="/js/order-search.js"></script>

<!-- Script per modificare lo stato di un ordine, solo per venditore -->
<?php if ($isVendor): ?>
    <script src="/js/order-status.js"></script>
<?php endif; ?>