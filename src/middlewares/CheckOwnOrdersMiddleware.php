<?php
namespace middlewares;

use core\Middleware;
use core\Session;
use models\Client;

class CheckOwnOrdersMiddleware implements Middleware
{
  /**
   * Gestisce la richiesta verificando se l'utente è un venditore
   */
  public function handle(callable $next): void
  {
    $userSession = Session::get('user');
    if (!$userSession['isVendor']) {
      // Prendo l'id dell'ordine dal url
      $orderId = basename($_SERVER['REQUEST_URI']);
      $client = Client::findById($userSession['id']);
      if (!$client->checkOrderOwnership($orderId)) {
        // 403
        header("Location: /404");
        exit;
      }
    }
    $next();
  }
}