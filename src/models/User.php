<?php
namespace models;

use core\BaseModel;
use core\Database;
use Exception;

class User extends BaseModel
{
  public $id;
  public $name;
  public $surname;
  public $email;
  public $password_hash;
  public $telephone_number;

  /**
   * Costruttore della classe User
   */
  protected function __construct($data = [])
  {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        if ($key === "id") {
          $this->$key = (int) $value;
        } else {
          $this->$key = $value;
        }
      }
    }
  }

  /**
   * Crea un nuovo utente con i parametri forniti
   */
  public static function create(
    string $name,
    string $surname,
    string $email,
    string $password,
    ?string $telephone = null
  ) {

    $user = new self();
    $user->name = $name;
    $user->surname = $surname;
    $user->email = $email;
    $user->password_hash = self::hashPassword($password);
    $user->telephone_number = $telephone;

    return $user;
  }

  /**
   * Ritorna il nome della tabella
   */
  protected static function tableName(): string
  {
    return 'users';
  }

  /**
   * Trova un utente tramite email
   */
  public static function findByEmail(string $email): ?User
  {
    return self::findWhere(['email' => $email])[0] ?? null;
  }

  public static function hashPassword(string $password): string
  {
    return password_hash($password, PASSWORD_DEFAULT);
  }

  /**
   * Verifica la password dell'utente
   */
  public function verifyPassword(string $password): bool
  {
    if (!$this->password_hash) {
      return false;
    }

    return password_verify($password, $this->password_hash);
  }

  /**
   * Salva l'utente nel database
   */
  public function save(): bool
  {
    if (!isset($this->id) || empty($this->id)) {
      return $this->insert();
    }
    $existingUser = self::findById($this->id);
    if (!$existingUser) {
      return $this->insert();
    }
    return $this->update();
  }

  /**
   * Inserisce un nuovo utente nel database
   */
  protected function insert(): bool
  {
    if (!isset($this->name) || !isset($this->surname) || !isset($this->email) || !isset($this->password_hash)) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "INSERT INTO users (name, surname, email, password_hash, telephone_number) 
            VALUES (?, ?, ?, ?, ?)";

    $params = [
      $this->name,
      $this->surname,
      $this->email,
      $this->password_hash,
      $this->telephone_number ?? null
    ];

    $types = "sssss";

    $success = $db->query($sql, $params, $types);

    if ($success) {
      $this->id = $db->getLastInsertId();
      return true;
    }

    return false;
  }

  /**
   * Aggiorna un utente esistente nel database
   */
  private function update(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "UPDATE users SET name = ?, surname = ?, email = ?, telephone_number = ? 
            WHERE id = ?";

    $params = [
      $this->name,
      $this->surname,
      $this->email,
      $this->telephone_number,
      $this->id
    ];

    $types = "ssssi";
    return $db->query($sql, $params, $types);
  }

  /**
   * Aggiorna la password dell'utente
   */
  public function updatePassword(string $password_hash): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
    $params = [$password_hash, $this->id];
    $types = "si";

    $success = $db->query($sql, $params, $types);

    if ($success) {
      $this->password_hash = $password_hash;
      return true;
    }

    return false;
  }

  /**
   * Elimina l'utente dal database
   */
  public function delete(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "DELETE FROM users WHERE id = ?";
    $params = [$this->id];
    $types = "i";

    return $db->query($sql, $params, $types);
  }

  public function isClient(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "SELECT EXISTS(SELECT 1 FROM clients WHERE user_id = ?) as is_client";
    $params = [$this->id];
    $types = "i";

    $result = $db->query($sql, $params, $types);
    return $result && isset($result[0]['is_client']) && (bool) $result[0]['is_client'];
  }

  public function isVendor(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "SELECT EXISTS(SELECT 1 FROM vendors WHERE user_id = ?) as is_vendor";
    $params = [$this->id];
    $types = "i";

    $result = $db->query($sql, $params, $types);
    return $result && isset($result[0]['is_vendor']) && (bool) $result[0]['is_vendor'];
  }



  /**
   * Converte l'utente in un array associativo
   */
  public function toArray(): array
  {
    return [
      "id" => $this->id,
      "name" => $this->name,
      "surname" => $this->surname,
      "email" => $this->email,
      "telephone_number" => $this->telephone_number,
      "isVendor" => $this->isVendor(),
    ];
  }
}