<?php

namespace models;

use core\Database;

class Vendor extends User
{
  protected function insert(): bool
  {
    $db = Database::getIstance();
    $success = $db->transaction(function () use ($db) {
      parent::insert();
      $sql = "INSERT INTO vendor (user_id) VALUES (?)";
      $success = $db->query($sql, [$this->id], "i");
      return $success;
    });
    return $success;
  }

  /**
   * Autentica il venditore mediante email e password
   */
  public static function authenticate(string $email, string $password_hash)
  {
    $vendor = self::findByEmail($email);
    if (!$vendor || !$vendor->isVendor()) {
      return false;
    }
    if (!$vendor || !$vendor->verifyPassword($password_hash)) {
      return false;
    }
    return $vendor;
  }
}