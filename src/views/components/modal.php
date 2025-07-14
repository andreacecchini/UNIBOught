<div class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static" id="<?= $modalId ?>" tabindex="-1">
  <div class="modal-dialog">
    <article class="modal-content">
      <header class="modal-header text-bg-primary">
        <h2 class="modal-title"><?= $modalTitle ?></h2>
      </header>
      <p class="modal-body py-4 pt-4 pb-2">
        <?= $modalBody ?>
      </p>
      <footer class="modal-footer text-bg-light">
        <button type="button" class="btn btn-outline-secondary cancelBtn" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-primary confirmBtn">Conferma</button>
      </footer>
    </article>
  </div>
</div>