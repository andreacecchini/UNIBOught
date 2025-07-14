<?php

namespace core;

use mysqli;
use Exception;

class Database
{
  private static $istance = null;
  private $connection;
  private $inTransaction = false;

  private function __construct()
  {
    $config = require_once SOURCE_DIR . "/config/database.php";
    try {
      $this->connection = new mysqli(
        $config["host"],
        $config["user"],
        $config["password"],
        $config["db"],
        $config["port"]
      );
      if ($this->connection->connect_error) {
        error_log("Database Connection Error: " . $this->connection->connect_error);
        die("Can't connect to the database");
      }
      if (!$this->connection->set_charset("utf8mb4")) {
        error_log(message: "Error loading character set utf8mb4: " . $this->connection->error);
      }
    } catch (Exception $e) {
      error_log("Database Connection Exception: " . $e->getMessage());
      die("Can't connect to the database");
    }
  }

  public function __destruct()
  {
    if ($this->connection) {
      $this->connection->close();
    }
  }

  public static function getIstance(): Database
  {
    if (self::$istance === null) {
      self::$istance = new self();
    }
    return self::$istance;
  }

  public function query(string $sql, array $params = [], string $types = "")
  {
    $stmt = $this->connection->prepare($sql);
    if (!$stmt) {
      throw new Exception("Prepare failed: " . $this->connection->error);
    }

    if ($params) {
      $stmt->bind_param($types, ...$params);
    }

    // Indica il sucesso dell'esecuzione della query
    $success = $stmt->execute();
    // false se la query non ha restituito risultati, altrimenti conterrà il risultato
    $result = $stmt->get_result();
    $stmt->close();

    return $result
      ? $result->fetch_all(MYSQLI_ASSOC)
      : $success;
  }

  public function generateUuid(): string
  {
    $sql = "SELECT UUID() AS uuid";
    $result = $this->query($sql);
    return $result[0]['uuid'] ?? '';
  }
  
  public function getLastInsertId()
  {
    return $this->connection->insert_id;
  }

  /**
   * Inizia una transazione SQL
   */
  private function beginTransaction(): bool
  {
    if ($this->inTransaction) {
      throw new Exception("Una transazione è già in corso");
    }

    $started = $this->connection->begin_transaction();

    if ($started) {
      $this->inTransaction = true;
    } else {
      throw new Exception("Impossibile iniziare la transazione: " . $this->connection->error);
    }

    return $started;
  }

  /**
   * Conferma la transazione corrente
   */
  private function commit(): bool
  {
    if (!$this->inTransaction) {
      throw new Exception("Nessuna transazione in corso");
    }

    $committed = $this->connection->commit();

    if ($committed) {
      $this->inTransaction = false;
    } else {
      throw new Exception("Impossibile eseguire il commit della transazione: " . $this->connection->error);
    }

    return $committed;
  }

  /**
   * Annulla la transazione corrente
   */
  private function rollback(): bool
  {
    if (!$this->inTransaction) {
      throw new Exception("Nessuna transazione in corso");
    }

    $rolledBack = $this->connection->rollback();

    if ($rolledBack) {
      $this->inTransaction = false;
    } else {
      error_log("Impossibile eseguire il rollback della transazione: " . $this->connection->error);
    }

    return $rolledBack;
  }

  /**
   * Verifica se è in corso una transazione
   */
  public function isInTransaction(): bool
  {
    return $this->inTransaction;
  }

  /**
   * Esegue l'insieme delle operazioni all'interno di una transazione
   */
  public function transaction(callable $callback)
  {
    try {
      $this->beginTransaction();
      $result = $callback($this);
      $this->commit();
      return $result;
    } catch (Exception $e) {
      if ($this->isInTransaction()) {
        $this->rollback();
      }
      error_log("Transazione annullata a causa di un'eccezione: " . $e->getMessage());
      throw $e;
    }
  }
}