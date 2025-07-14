<?php

namespace middlewares;

use core\Middleware;
use core\Session;

class AuthMiddleware implements Middleware
{
  /**
   * Gestisce la richiesta verificando se l'utente è autenticato
   */
  public function handle(callable $next): void
  {
    $isAjaxRequest = $this->isAjaxRequest();

    if (!Session::isLoggedIn()) {
      $this->handleUnauthenticated($isAjaxRequest, 'Non autorizzato');
    }

    if (Session::isExpired()) {
      $this->handleUnauthenticated($isAjaxRequest, 'Sessione scaduta', true);
    }

    $next();
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
   * Gestisce il caso di utente non autenticato
   */
  private function handleUnauthenticated(bool $isAjaxRequest, string $message, bool $logout = false): void
  {
    if ($isAjaxRequest) {
      $this->sendJsonError($message);
    } else {
      $redirectUrl = $_SERVER['REQUEST_URI'] ?? '/';
      Session::set('redirect_url', $redirectUrl);

      if ($logout) {
        Session::logout(true);
      }

      header('Location: /login');
      exit;
    }
  }

  /**
   * Invia una risposta JSON con errore 403
   */
  private function sendJsonError(string $message): void
  {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden', 'message' => $message]);
    exit;
  }
}