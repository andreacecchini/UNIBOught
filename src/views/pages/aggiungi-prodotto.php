<h1 class="my-4"><?= $context === "add" ? "Aggiungi Prodotto" : "Aggiorna Prodotto" ?></h1>
<form action="<?= $context === "add" ? "/aggiungi-prodotto" : "/modifica-prodotto/" . $product['id'] ?>"
  id="aggiungiProdotto" method="post" enctype="multipart/form-data">
  <div class="mb-4">
    <label for="fileInput" class="form-label">Immagine del
      prodotto<?= $context === "add" ? ' <span class="text-danger">*</span>' : '' ?>
      <small class="form-text text-muted d-block" id="imageHelp">
        Formati supportati: JPEG, PNG, WebP, GIF. Dimensione massima: 5MB
      </small>
    </label>
    <input type="file" class="form-control" accept="image/*" id="fileInput" name="image" <?= $context === "add" ? "required" : "" ?> />
    <section class="p-4 text-center<?= $context !== "edit" ? " visually-hidden" : "" ?> " id="imageUpload">
      <h2 class="visually-hidden">Anteprima immagine</h2>
      <figure class="mb-0">
        <?php if ($context === "edit"): ?>
          <img src="/images/<?= htmlspecialchars($product['image_name']) ?>" alt="Anteprima del prodotto"
            class="img-fluid object-fit-contain" />
          <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">Immagine corrente</small>
          </div>
        <?php endif; ?>
      </figure>
    </section>
  </div>

  <div class="mb-3">
    <label for="productName" class="form-label">Nome prodotto <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="productName" name="name"
      value="<?= $context === "edit" ? htmlspecialchars($product['name']) : '' ?>" required />
  </div>

  <div class="mb-3">
    <label for="description" class="form-label">Descrizione <span class="text-danger">*</span></label>
    <textarea class="form-control" id="description" name="description" rows="4" maxlength="256"
      required><?= $context === "edit" ? htmlspecialchars($product['description']) : '' ?></textarea>
    <div class="form-text text-end"><small
        id="charCount"><?= $context === "edit" ? strlen($product['description']) : 0 ?>/256</small></div>
  </div>

  <div class="row g-2 mb-3">
    <div class="col-md-6">
      <label for="price" class="form-label">Prezzo <span class="text-danger">*</span></label>
      <div class="input-group">
        <span class="input-group-text">€</span>
        <input type="number" class="form-control" id="price" name="price" min="0" max="600" step="0.01" 
          value="<?= $context === "edit" ? number_format($product['price'], 2, '.', '') : '' ?>" required />
      </div>
    </div>
    <div class="col-md-6">
      <label for="quantity" class="form-label">Quantità <span class="text-danger">*</span></label>
      <input type="number" class="form-control" id="quantity" name="quantity" min="1"
        value="<?= $context === "edit" ? $product['quantity'] : '1' ?>" required />
    </div>
  </div>

  <div class="mb-3">
    <label for="category" class="form-label">Categoria <span class="text-danger">*</span></label>
    <select class="form-select" id="category" name="category" required>
      <option value="">Seleziona...</option>
      <?php foreach ($categories as $category): ?>
        <option value="<?= $category['id']; ?>" <?= ($context === "edit" && in_array($category['id'], array_column($product['categories'], 'id'))) ? 'selected' : '' ?>>
          <?= htmlspecialchars($category['name']); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <button type="submit" class="btn btn-primary w-100">
    <?php if ($context === "add"): ?>
      <span class="fas fa-plus me-2"></span>Aggiungi
    <?php else: ?>
      <span class="fas fa-edit me-2"></span>Aggiorna
    <?php endif; ?>
  </button>
  <a href="/dashboard-venditore" class="btn btn-outline-danger w-100 mt-2">
    <span class="fas fa-times me-2"></span>Annulla
  </a>
  
</form>

<script src="/js/add-product-handler.js"></script>