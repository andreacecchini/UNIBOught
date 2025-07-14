<?php

namespace middlewares;

use core\Middleware;
use core\Session;
use models\User;
use models\Vendor;

class CheckVendorMiddleware implements Middleware
{
  /**
   * Gestisce la richiesta verificando se l'utente è un venditore
   */
  public function handle(callable $next): void
  {
    $user = User::findById(Session::get('user')['id']);
    if (!$user || !$user->isVendor()) {
      $this->sendUnauthorizedResponse();
    }
    $next();
  }

  /**
   * Invia una risposta di errore 403 (non autorizzato)
   */
  private function sendUnauthorizedResponse(): void
  {
    $isAjaxRequest = $this->isAjaxRequest();

    if ($isAjaxRequest) {
      $this->sendJsonErrorResponse();
    } else {
      $this->redirectToErrorPage();
    }
  }

  /**
   * Verifica se la richiesta è una chiamata AJAX
   */
  private function isAjaxRequest(): bool
  {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
  }

  /**
   * Invia una risposta JSON con errore 403
   */
  private function sendJsonErrorResponse(): void
  {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode([
      'error' => 'Forbidden',
      'message' => 'Non autorizzato'
    ]);
    exit;
  }

  /**
   * Reindirizza alla pagina di errore 403
   */
  private function redirectToErrorPage(): void
  {
    http_response_code(403);
    header('Location: /403');
    exit;
  }
}