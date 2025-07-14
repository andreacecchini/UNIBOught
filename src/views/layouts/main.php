<?php
use core\Session;
$isLoggedIn = Session::isLoggedIn();
$user = Session::get('user');
$isVendor = $user['isVendor'] ?? false;
$homeRoute = $isVendor ? '/dashboard-venditore' : '/';
// Pagina corrente per evidenziare il link attivo
$currentPage = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>

<!DOCTYPE html>
<html lang="it">

<head>
  <?php require_once SOURCE_DIR . '/views/partials/head.php' ?>
</head>

<body class="bg-light">
  <!-- Intestazione della pagina -->
  <header class="container-fluid text-center mt-0 pt-3 pb-3 border-bottom bg-primary text-white sticky-top">
    <!-- Logo  -->
    <a href="<?= $homeRoute ?>" class="mt-2 py-0 text-white text-decoration-none">UNIBOught</a>
    <!-- Form di ricerca -->
    <div class="container">
      <?php require_once SOURCE_DIR . '/views/components/searchProdocutForm.php' ?>
    </div>
  </header>
  <!-- Notifiche -->
  <aside id="notification-toast-container" class="position-fixed top-0 start-50 translate-middle-x p-3 w-100" style="z-index: 1050;">
    <div id="notification-toast" class="toast w-100" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header text-bg-dark p-3">
        <strong class="me-auto fs-5">Nuova notifica</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Chiudi"></button>
      </div>
      <div class="toast-body text-bg-light p-3 fs-6"></div>
    </div>
  </aside>
  <!-- Contenuto principale della pagina -->
  <main class="container-md mt-3">
    <?php require_once SOURCE_DIR . '/views/components/flash-message.php' ?>
    <?= $content ?>
  </main>
  <!-- Footer con navbar -->
  <footer class="fixed-bottom mt-3">
    <?php require_once SOURCE_DIR . '/views/partials/navbar.php' ?>
  </footer>
  <script src=" /js/showAlertAsFlashMessage.js">
  </script>
</body>

</html>