<!DOCTYPE html>
<html lang="it">

<head>
  <?php require_once SOURCE_DIR . '/views/partials/head.php' ?>
</head>

<body class="bg-light">
  <!-- Header -->
  <header class="text-bg-primary">
    <h1 class="text-center pt-2 mb-4 pb-3"><?= $header ?></h1>
  </header>
  <!-- Main -->
  <main class="container-md mt-3">
    <?= $content ?>
  </main>
  <!-- Footer -->
  <footer class="footer mt-4 text-bg-dark text-center p-4 text-">
    <?php require_once SOURCE_DIR . '/views/partials/footer.php' ?>
  </footer>

</body>

</html>