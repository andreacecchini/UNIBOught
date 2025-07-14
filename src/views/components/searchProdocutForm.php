<?php
use models\Category;

$categories = array_map(
  fn($c) => $c->toArray(),
  Category::findAll()
);
?>
<!-- Form per ricerca prodotto -->
<form action="/" class="d-flex flex-column flex-grow-1 mt-3" role="search" id="search-form">
  <!-- Ricerca base -->
  <fieldset class="input-group">
    <legend class="visually-hidden">Ricerca semplice</legend>
    <label for="navbar-search" class="visually-hidden">Cerca prodotti</label>
    <input type="search" id="navbar-search" name="nomeProdotto" class="form-control border-white"
      placeholder="Cerca prodotti" />
    <button class="btn btn-light border-white" type="submit" aria-label="Cerca prodotti">
      <span class="fas fa-search text-primary" aria-hidden="true"></span>
      <span class="visually-hidden">Cerca</span>
    </button>
    <button class="btn btn-secondary border-white" type="button" data-bs-toggle="collapse" data-bs-target="#filtroRicerca"
      aria-expanded="false" aria-controls="filtroRicerca" aria-label="Mostra filtri avanzati">
      <span class="fas fa-filter text-white" aria-hidden="true"></span>
      <span class="visually-hidden">Filtra</span>
    </button>
  </fieldset>
  <!-- Ricerca avanzata  -->
  <fieldset class="collapse mt-3" id="filtroRicerca">
    <legend class="h5 mb-3 text-white">Ricerca Avanzata</legend>
    <div class="card card-body px-4 bg-white">
      <div class="row">
        <div class="col-md-6 mb-3">
          <!-- Filtro per categoria -->
          <label for="categoria" class="form-label fw-bold text-dark">Categoria</label>
          <select class="form-select" id="categoria" name="categoria" aria-describedby="categoria-help">
            <!-- Tutte le categorie -->
            <option selected value="">Tutte le categorie</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <div id="categoria-help" class="form-text">Seleziona una categoria per filtrare i prodotti</div>
        </div>
        <div class="col-md-6 mb-3">
          <!-- Filtro per prezzo massimo -->
          <label for="prezzoMax" class="form-label fw-bold text-dark">Prezzo massimo: <span id="prezzo-value">€500</span></label>
          <div class="d-flex align-items-center">
            <input type="range" class="form-range me-2" min="100" max="600" step="50" value="600" id="prezzoMax"
              name="prezzoMax" aria-describedby="prezzo-help" oninput="document.getElementById('prezzo-value').textContent = this.value + '€'" />
          </div>
          <div class="d-flex justify-content-between text-muted">
            <span class="small">€100</span>
            <span class="small">€200</span>
            <span class="small">€300</span>
            <span class="small">€400</span>
            <span class="small">€500</span>
            <span class="small">€600</span>
          </div>
          <div id="prezzo-help" class="form-text">Trascina per impostare il prezzo massimo</div>
        </div>
      </div>
      <!-- Bottoni submit e reset -->
      <div class="row g-2">
        <div class="col-md-6">
          <button type="submit" class="btn btn-primary w-100 mb-3">
            <span class="fas fa-search me-2" aria-hidden="true"></span>
            Applica filtri
          </button>
        </div>
        <div class="col-md-6">
          <button type="reset" class="btn btn-outline-secondary w-100" onclick="document.getElementById('prezzo-value').textContent = '500€'">
            <span class="fas fa-undo me-2" aria-hidden="true"></span>
            Reimposta filtri
          </button>
        </div>
      </div>
    </div>
  </fieldset>
</form>