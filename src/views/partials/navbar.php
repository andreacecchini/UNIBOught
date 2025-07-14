<nav class="navbar bg-white border-top border-2 shadow mt-3 py-2">
  <div class="container-fluid">
    <ul class="navbar-nav w-100 flex-row row m-0 p-0 text-center">
      <!-- Home -->
      <li class="col nav-item">
        <a class="nav-link <?= ($currentPage === $homeRoute) ? 'active text-primary' : 'text-dark' ?>"
          href="<?= $homeRoute ?>">
          <span class="fa fa-home"></span>
          <span class="ms-2 d-none d-md-inline">Home</span>
        </a>
      </li>
      <!-- Profilo -->
      <li class="col nav-item">
        <a class="nav-link <?= ($currentPage === '/user-profile') ? 'active text-primary' : 'text-dark' ?>"
          href="/user-profile">
          <span class="fa fa-user"></span>
          <span class="ms-2 d-none d-md-inline"><?= $isLoggedIn ? $user['name'] : "Accedi" ?></span>
        </a>
      </li>
      <?php if (!$isVendor): ?>
        <!-- Carrello (solo per clienti) -->
        <li class="col nav-item">
          <a class="nav-link <?= ($currentPage === '/carrello') ? 'active text-primary' : 'text-dark' ?>"
            href="/carrello">
            <span class="fa fa-shopping-cart position-relative">
              <span class="badge bg-danger position-absolute translate-middle cart-badge text-bg-primary"></span>
            </span>
            <span class="ms-2 d-none d-md-inline">Carrello</span>
          </a>
        </li>
      <?php endif; ?>
      <!-- Storico ordini -->
      <li class="col nav-item">
        <a class="nav-link <?= ($currentPage === '/storico-ordini') ? 'active text-primary
          ' : 'text-dark' ?>" href="/storico-ordini">
          <span class="fa fa-box"></span>
          <span class="ms-2 d-none d-md-inline">Ordini</span>
        </a>
      </li>
      <!-- Notifiche -->
      <li class="col nav-item">
        <a class="nav-link <?= ($currentPage === '/notifiche') ? 'active text-primary' : 'text-dark' ?>"
          href="/notifiche">
          <span class="fa fa-bell position-relative">
            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle notification-badge"
              style="display: none;"></span>
          </span>
          <span class="ms-2 d-none d-md-inline">Notifiche</span>
        </a>
      </li>
    </ul>
  </div>
</nav>

<script>
  const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
  const isVendor = <?php echo json_encode($isVendor); ?>;
</script>
<script type="module" src="/js/navbar-handler.js"></script>