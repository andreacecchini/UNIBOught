<?php

namespace middlewares;

use core\Middleware;
use core\Session;

class ClearRedirectMiddleware implements Middleware
{
  /**
   * Gestisce la richiesta verificando se l'utente è autenticato
   */
  public function handle(callable $next): void
  {
    Session::remove('redirect_url');
    $next();
  }
}