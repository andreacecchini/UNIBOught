<?php

namespace models;

use core\BaseModel;
use core\Database;
use Exception;

class Category extends BaseModel
{
  public $id;
  public $name;

  /**
   * Costruttore che inizializza un oggetto Category da un array associativo
   */
  protected function __construct(array $data = [])
  {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        if ($key === "id" && is_string($value)) {
          $this->$key = (int) $value;
        } else {
          $this->$key = $value;
        }
      }
    }
  }

  /**
   * Crea una nuova istanza di categoria con i parametri forniti
   */
  public static function create(string $name): self
  {
    if (empty($name)) {
      throw new Exception("Il nome della categoria è obbligatorio");
    }

    $category = new self();
    $category->name = $name;

    return $category;
  }

  /**
   * @inheritDoc
   */
  protected static function tableName(): string
  {
    return 'categories';
  }

  /**
   * Salva la categoria nel database
   */
  public function save(): bool
  {
    if (!isset($this->id) || empty($this->id)) {
      return $this->insert();
    }
    $existingCategory = self::findById($this->id);
    if (!$existingCategory) {
      return $this->insert();
    }
    return $this->update();
  }

  /**
   * Inserisce una nuova categoria nel database
   */
  private function insert(): bool
  {
    $db = Database::getIstance();
    $sql = "INSERT INTO categories (name) VALUES (?)";
    $params = [$this->name];
    $types = "s";

    $success = $db->query($sql, $params, $types);

    if ($success) {
      $this->id = $db->getLastInsertId();
      return true;
    }
    return false;
  }

  /**
   * Aggiorna una categoria esistente nel database
   */
  private function update(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "UPDATE categories SET name = ? WHERE id = ?";
    $params = [$this->name, $this->id];
    $types = "si";

    return $db->query($sql, $params, $types);
  }

  /**
   * Elimina la categoria dal database
   */
  public function delete(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "DELETE FROM categories WHERE id = ?";
    $params = [$this->id];
    $types = "i";

    return $db->query($sql, $params, $types);
  }

  /**
   * Ottiene tutti i prodotti associati a questa categoria
   * @return Product[] Array di oggetti Product
   */
  public function getProducts(): array
  {
    if (!$this->id) {
      return [];
    }

    $db = Database::getIstance();
    $sql = "SELECT p.id FROM products p 
            JOIN product_category pc ON p.id = pc.product_id 
            WHERE pc.category_id = ? 
            AND p.valid = 1";
    $results = $db->query($sql, [$this->id], "i");

    $products = [];
    foreach ($results as $result) {
      $product = Product::findById($result['id']);
      if ($product) {
        $products[] = $product;
      }
    }

    return $products;
  }

  /**
   * Converte la categoria in array associativo
   */
  public function toArray(bool $includeProducts = false): array
  {
    $result = [
      "id" => $this->id,
      "name" => $this->name
    ];

    if ($includeProducts) {
      $productsArray = [];
      foreach ($this->getProducts() as $product) {
        $productsArray[] = $product->toArray(false);
      }
      $result["products"] = $productsArray;
    }

    return $result;
  }
}