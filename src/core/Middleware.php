<?php

namespace core;

interface Middleware
{
  /**
   * Esegue la logica del middleware passando poi il controllo alla prossima funzione
   * middleware della catena.
   */
  public function handle(callable $next): void;
}