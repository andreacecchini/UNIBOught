<?php
define('ROOT_DIR', dirname(__DIR__));
define('SOURCE_DIR', ROOT_DIR . '/src');
// Configura il fuso orario per l'Italia
date_default_timezone_set('Europe/Rome');
// Abilita debuggin
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Autoloading per includere automaticamente le classi quando vengono utilizzate
spl_autoload_register(function ($class) {
  // Mappare il namespace della classe al percorso del file
  $file = SOURCE_DIR . '/' . str_replace('\\', '/', $class) . '.php';
  // Se il file esiste, includilo
  if (file_exists($file)) {
    require_once $file;
  }
});

// Inizializza l'applicazione
$app = new core\App();
// Esegui l'applicazione per processare la richiesta
$app->run();