<?php

namespace core;

use Exception;

abstract class BaseModel implements Model
{
  /**
   * Ritorna il nome della tabella per questo modello
   */
  abstract protected static function tableName(): string;

  /**
   * Ritorna la colonna primaria per questo modello
   */
  protected static function primaryKey(): string
  {
    return 'id';
  }

  /**
   * Ritorna il tipo della chiave primaria per questo modello
   */
  protected static function primaryKeyType(): string
  {
    return 'i';
  }

  /**
   * Trova tutte le istanze del modello.
   */
  public static function findAll(): array
  {
    $db = Database::getIstance();
    $tableName = static::tableName();
    $results = $db->query("SELECT * FROM {$tableName}");

    $models = [];
    if ($results && is_array($results)) {
      foreach ($results as $row) {
        $models[] = new static($row);
      }
    }
    return $models;
  }

  /**
   * Trova un'istanza del modello tramite il suo ID.
   */
  public static function findById($id)
  {
    $db = Database::getIstance();
    $tableName = static::tableName();
    $primaryKey = static::primaryKey();
    $primaryKeyType = static::primaryKeyType();
    $results = $db->query(
      "SELECT * FROM {$tableName} WHERE {$primaryKey} = ?",
      [$id],
      $primaryKeyType
    );
    if ($results && count($results) === 1) {
      return new static($results[0]);
    }
    return null;
  }

  /**
   * Trova entità in base a condizioni specifiche
   */
  public static function findWhere(array $conditions, string $orderBy = '', string $order = 'ASC'): array
  {
    $db = Database::getIstance();
    $tableName = static::tableName();

    $sql = "SELECT * FROM {$tableName}";
    $params = [];
    $types = "";

    if (!empty($conditions)) {
      $sql .= " WHERE ";
      $whereClauses = [];

      foreach ($conditions as $key => $value) {
        if (is_array(value: $value) && isset($value['operator']) && isset($value['value'])) {
          // Supporto per operatori personalizzati
          $operator = $value['operator'];
          $actualValue = $value['value'];
          $whereClauses[] = "{$key} {$operator} ?";
          $params[] = $actualValue;
          $types .= is_int($actualValue) ? "i" : "s";
        } else {
          // Comportamento standard (=)
          $whereClauses[] = "{$key} = ?";
          $params[] = $value;
          $types .= is_int($value) ? "i" : "s";
        }
      }

      $sql .= implode(" AND ", $whereClauses);
    }

    if (!empty($orderBy)) {
      $sql .= " ORDER BY {$orderBy} {$order}";
    }

    $results = $db->query($sql, $params, $types);

    $models = [];
    if ($results && is_array($results)) {
      foreach ($results as $row) {
        $models[] = new static($row);
      }
    }
    return $models;
  }

}