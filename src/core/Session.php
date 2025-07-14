<?php

namespace core;

class Session
{
  /**
   * Inizializza la sessione se non è già attiva
   */
  public static function start(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        // solo se HTTPS è abilitato
        'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'use_strict_mode' => true,
        'use_only_cookies' => true,
      ]);
    }
  }

  /**
   * Rigenera l'ID di sessione per prevenire session hijacking
   */
  public static function regenerate(bool $deleteOldSession = false): bool
  {
    return session_regenerate_id($deleteOldSession);
  }

  /**
   * Imposta un valore nella sessione
   */
  public static function set(string $key, $value): void
  {
    $_SESSION[$key] = $value;
  }

  /**
   * Ottiene un valore dalla sessione
   */
  public static function get(string $key, $default = null)
  {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
  }

  /**
   * Verifica se una chiave esiste nella sessione
   */
  public static function has(string $key): bool
  {
    return isset($_SESSION[$key]);
  }

  /**
   * Rimuove un valore dalla sessione
   */
  public static function remove(string $key): void
  {
    if (isset($_SESSION[$key])) {
      unset($_SESSION[$key]);
    }
  }

  /**
   * Imposta un messaggio flash che sarà disponibile solo per la prossima richiesta
   */
  public static function setFlash(string $key, $value, string $type = 'info'): void
  {
    if (!isset($_SESSION['flash'])) {
      $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][$key] = [
      'message' => $value,
      'type' => $type
    ];
  }

  /**
   * Ottiene un messaggio flash e lo rimuove dalla sessione
   */
  public static function getFlash(string $key, $default = null)
  {
    $value = $default;
    if (isset($_SESSION['flash'][$key])) {
      $value = $_SESSION['flash'][$key];
      unset($_SESSION['flash'][$key]);
    }
    return $value;
  }

  public static function getAllFlash(): array
  {
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flash;
  }

  /**
   * Distrugge la sessione corrente
   */
  public static function destroy(): bool
  {
    if (session_status() === PHP_SESSION_ACTIVE) {
      // Pulisce tutte le variabili di sessione
      $_SESSION = [];

      // Elimina anche il cookie di sessione
      if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
          session_name(),
          '',
          time() - 42000,
          $params["path"],
          $params["domain"],
          $params["secure"],
          $params["httponly"]
        );
      }

      // Distrugge la sessione
      return session_destroy();
    }
    return false;
  }

  /**
   * Imposta l'utente nella sessione
   */
  public static function setUser(array $userData): void
  {
    self::set(key: 'user', value: $userData);
    self::set('start_session', time());
  }

  /**
   * Verifica se l'utente è loggato
   */
  public static function isLoggedIn(): bool
  {
    return isset($_SESSION['user']);
  }

  /**
   * Logout dell'utente
   */
  public static function logout(bool $destroySession = false): void
  {
    if ($destroySession) {
      // Distrugge completamente la sessione
      self::destroy();
    } else {
      // Rimuove solo i dati dell'utente
      self::remove('user');
      self::remove('start_session');
      // Rigenera l'ID di sessione
      self::regenerate(deleteOldSession: true);
    }
  }

  /**
   * Aggiorna il timestamp dell'ultima attività
   */
  public static function updateActivity(): void
  {
    self::set('last_activity', time());
  }

  /**
   * Verifica se la sessione è scaduta
   */
  public static function isExpired(int $maxLifetime = 1800): bool
  {
    $lastActivity = self::get('start_session', 0);
    return (time() - $lastActivity) > $maxLifetime;
  }
}