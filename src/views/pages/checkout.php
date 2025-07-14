<div class="px-4">
    <section class="border-bottom border-2 py-4">
        <h2 class="mb-3">Costo</h2>
        <div class="row mb-0">
            <p class="text-muted col-6 mb-2">Subtotale:</p>
            <p class="col-6 text-end mb-2"><?= $subtotal ?>€</p>

            <p class="text-muted col-6 mb-2">Aggiustamenti:</p>
            <p class="col-6 text-end mb-2"><?= $tax ?>€</p>

            <strong class="col-6 fs-3 mb-0">Totale:</strong>
            <strong class="col-6 fw-bold text-end fs-3 mb-0"><?= $total ?>€</strong>
        </div>
    </section>

    <section class="border-bottom border-2 py-4">
        <h2 class="mb-3">Carrello</h2>
        <?php foreach ($cartItems as $item): ?>
            <article class="card mb-3 order-product border-2 shadow">
                <div class="card-body p-3 row">
                    <!-- Immagine del prodotto -->
                    <div class="col-4 col-sm-3 col-md-2 col-lg-1">
                        <div class="d-flex align-items-center justify-content-center">
                            <?php
                            $imageName = $item['image_name'];
                            $imageAlt = $item['image_alt'];
                            ?>
                            <img src="/images/<?= htmlspecialchars($imageName) ?>" alt="<?= htmlspecialchars($imageAlt) ?>"
                                class="img-fluid" />
                        </div>
                    </div>
                    <!-- Dettagli del prodotto -->
                    <div class="col-8 col-sm-9 col-md-10 col-lg-11">
                        <div class="row">
                            <!-- Nome prodotto -->
                            <div class="col-12">
                                <h3 class="h6 mb-1">
                                    <?php
                                    $productId = $item['id'];
                                    $productName = $item['name'];
                                    ?>
                                    <a href="/dettaglio-prodotto/<?= htmlspecialchars($productId) ?>"
                                        class="text-decoration-none fw-bold product-name">
                                        <?= htmlspecialchars($productName) ?>
                                    </a>
                                </h3>
                            </div>
                            <!-- Prezzo e quantità nell'ordine -->
                            <div class="col-12">
                                <small class="text-muted">
                                    €<?= number_format($item['price'], 2, ',', '.') ?> ×
                                    <?= $item['quantity'] ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="border-bottom border-2 py-4 mb-4">
        <h2 class="mb-3">Ritiro</h2>
        <dl class="row mb-0">
            <dt class="col-sm-3 mb-2">Presso:</dt>
            <dd class="col-sm-9  text-sm-end mb-2">
                Università di Cesena, Via dell'Università 50
            </dd>

            <dt class="col-sm-3 mb-0">Disponibile il:</dt>
            <dd class="col-sm-9 text-sm-end mb-0"><?= $dataRitiro ?></dd>
        </dl>
    </section>

    <section>
        <h2 class="mb-3">Metodo di pagamento</h2>
        <ul class="nav nav-pills nav-justified" role="tablist">
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link active" id="tab_pagamento_carta" data-bs-toggle="tab"
                    data-bs-target="#pagamento_carta" type="button" role="tab" aria-controls="pagamento_carta"
                    aria-selected="true">Pagamento con carta</button>
            </li>
            <li class="nav-item ms-2" role="presentation">
                <button class="nav-link" id="tab_pagamento_ritiro" data-bs-toggle="tab"
                    data-bs-target="#pagamento_ritiro" type="button" role="tab" aria-controls="pagamento_ritiro"
                    aria-selected="false">Pagamento al
                    ritiro</button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active mt-4" id="pagamento_carta" role="tabpanel"
                aria-labelledby="tab_pagamento_carta">
                <p class="mb-0">Inserisci i dati della tua carta di credito per completare l'acquisto.</p>
                <form action="/checkout/payment/card" method="POST">
                    <fieldset class="border border-2 shadow bg-white p-3 mt-3">
                        <legend class="visually-hidden">Dati carta di credito</legend>
                        <div class="form-group">
                            <label for="numero_carta" class="form-label">Numero di carta <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0 text-bg-primary" aria-hidden="true">
                                    <span class="fas fa-credit-card"></span>
                                </span>
                                <input type="text" name="numero_carta" id="numero_carta"
                                    class="form-control border-start-0" placeholder="0000 0000 0000 0000" minlength="16"
                                    maxlength="16" pattern="\d{16}" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm mt-3">
                                <label for="data_scadenza" class="form-label">Data di scadenza <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0 text-bg-primary" aria-hidden="true">
                                        <span class="fas fa-calendar-alt"></span>
                                    </span>
                                    <input type="month" name="data_scadenza" id="data_scadenza"
                                        class="form-control border-start-0" required />
                                </div>
                            </div>
                            <div class="form-group col-sm mt-3">
                                <label for="cvv" class="form-label">CVV/CVC <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0 text-bg-primary" aria-hidden="true">
                                        <span class="fas fa-lock"></span>
                                    </span>
                                    <input type="text" name="cvv" id="cvv" class="form-control border-start-0"
                                        placeholder="123" minlength="3" maxlength="4" pattern="\d{3,4}" required />
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <button type="submit" class="btn btn-primary w-100 my-4">Procedi con il pagamento</button>
                </form>
            </div>
            <div class="tab-pane mt-4" id="pagamento_ritiro" role="tabpanel" aria-labelledby="tab_pagamento_ritiro">
                <p class="mb-0">Pagamento da effettuare al ritiro dell'ordine</p>
                <a href="/checkout/payment/delivery" class="btn btn-primary w-100 my-4">Procedi con l'ordine</a>
            </div>
        </div>
    </section>
</div>