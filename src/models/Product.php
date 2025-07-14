<?php

namespace models;

use core\BaseModel;
use core\Database;
use Exception;

class Product extends BaseModel
{
  public $id;
  public $vendor_id;
  public $name;
  public $description;
  public $price;
  public $image_name;
  public $image_alt;
  public $quantity;
  public $valid;

  /**
   * Costruttore che inizializza un oggetto Product da un array associativo
   */
  protected function __construct(array $data = [])
  {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        if ($key === "price" && is_string($value)) {
          $this->$key = (float) $value;
        } elseif (
          ($key === "vendor_id" || $key === "quantity") &&
          is_string($value)
        ) {
          $this->$key = (int) $value;
        } else {
          $this->$key = $value;
        }
      }
    }
  }

  /**
   * Crea una nuova istanza di prodotto con i parametri forniti
   */
  public static function create(
    int $vendor_id,
    string $name,
    float $price,
    string $description,
    string $image_name,
    ?string $image_alt = null,
    ?int $quantity = null
  ) {
    if (empty($name) || $vendor_id <= 0 || $price <= 0) {
      throw new Exception(
        "Campi obbligatori del prodotto mancanti o non validi (nome, vendor_id, prezzo)."
      );
    }

    $product = new self();
    $product->vendor_id = $vendor_id;
    $product->name = $name;
    $product->price = $price;
    $product->description = $description;
    $product->image_name = $image_name;
    $product->image_alt = $image_alt ?? $name;
    $product->quantity = $quantity ?? 0;

    return $product;
  }

  /**
   * @inheritDoc
   */
  protected static function tableName(): string
  {
    return 'products';
  }

  /**
   * @inheritDoc
   */
  protected static function primaryKey(): string
  {
    return 'id';
  }

  /**
   * @inheritDoc
   */
  protected static function primaryKeyType(): string
  {
    return 's';
  }

  public static function findAllRemoved(): array
  {
    return self::findWhere(['valid' => 0], 'name', 'ASC');
  }

  public static function findAllValid(): array
  {
    return self::findWhere(['valid' => 1], 'name', 'ASC');
  }

  public static function findByIdValid(string $id): ?self
  {
    return self::findWhere(['id' => $id, 'valid' => 1], '', '')[0] ?? null;
  }

  public static function findAll(): array
  {
    return self::findWhere([], 'name', 'ASC');
  }

  /**
   * Salva il prodotto nel database
   */
  public function save(): bool
  {
    if (!isset($this->id) || empty($this->id)) {
      return $this->insert();
    }
    $existingProduct = self::findById($this->id);
    if ($this->quantity < 10) {
      Notification::notifyProductShortage($this->id);
    }
    if (!$existingProduct) {
      return $this->insert();
    }
    return $this->update();
  }

  /**
   * Inserisce un nuovo prodotto nel database
   */
  private function insert(): bool
  {
    $db = Database::getIstance();
    $uuid = $db->generateUuid();
    $sql = "INSERT INTO products (id, vendor_id, name, description, price, image_name, image_alt, quantity, valid) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [
      $uuid,
      $this->vendor_id,
      $this->name,
      $this->description,
      $this->price,
      $this->image_name,
      $this->image_alt,
      $this->quantity,
      $this->valid ?? 1,
    ];
    $types = "sissdssii";
    $success = $db->query($sql, $params, $types);

    if ($success) {
      $this->id = $uuid;
      return true;
    }
    return false;
  }

  /**
   * Aggiorna un prodotto esistente nel database
   */
  private function update(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "UPDATE products 
            SET vendor_id = ?, name = ?, description = ?, price = ?, 
                image_name = ?, image_alt = ?, quantity = ?, valid = ? 
            WHERE id = ?";
    $params = [
      $this->vendor_id,
      $this->name,
      $this->description,
      $this->price,
      $this->image_name,
      $this->image_alt,
      $this->quantity,
      $this->valid,
      $this->id,
    ];
    $types = "issdssiis";

    return $db->query($sql, $params, $types);
  }

  /**
   * Elimina il prodotto dal database
   */
  public function delete(): bool
  {
    if (!$this->id) {
      return false;
    }
    $db = Database::getIstance();
    // Esegui l'eliminazione del prodotto in transazione
    return $db->transaction(function () {
      // Rimuovi l'immagine del prodotto dal filesystem del server
      // $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/images/' . $this->image_name;
      // if (file_exists($imagePath)) {
      //   if (!unlink(filename: $imagePath)) {
      //     throw new Exception("Errore nella rimozione dell'immagine del prodotto.");
      //   }
      // }
      return $this->deleteProduct();
    });
  }

  private function deleteProduct(): bool
  {
    $db = Database::getIstance();
    // Eliminazione logica del prodotto
    // questo permette di non far visualizzare il prodotto nel catalogo
    // ma di non eliminarlo fisicamente dal database.
    $sql = "UPDATE products SET valid = '0' WHERE id = ?";
    $params = [$this->id];
    $types = "s";
    return $db->query($sql, $params, $types);
  }

  /**
   * Ottiene tutte le categorie associate a questo prodotto
   */
  public function getCategories(): array
  {
    if (!$this->id) {
      return [];
    }

    $db = Database::getIstance();
    $sql = "SELECT c.id FROM categories c 
            JOIN product_category pc ON c.id = pc.category_id 
            WHERE pc.product_id = ?";
    $results = $db->query($sql, [$this->id], "i");

    $categories = [];
    foreach ($results as $result) {
      $category = Category::findById($result['id']);
      if ($category) {
        $categories[] = $category;
      }
    }

    return $categories;
  }

  /**
   * Aggiunge una categoria a questo prodotto
   */
  public function addCategory(int $categoryId): bool
  {
    if (!$this->id || $categoryId <= 0) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "INSERT INTO product_category (product_id, category_id) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE product_id=product_id";

    return $db->query($sql, [$this->id, $categoryId], "si");
  }

  /**
   * Rimuove una categoria da questo prodotto
   */
  public function removeCategory(int $categoryId): bool
  {
    if (!$this->id || $categoryId <= 0) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "DELETE FROM product_category 
            WHERE product_id = ? AND category_id = ?";

    return $db->query($sql, [$this->id, $categoryId], "ii");
  }

  /**
   * Ottiene le recensioni associate a questo prodotto
   */
  public function getReviews(): array
  {
    if (!$this->id) {
      return [];
    }

    return Review::findByProductId($this->id);
  }

  /**
   * Calcola il rating medio del prodotto
   */
  public function getAverageRating(): float
  {
    if (!$this->id) {
      return 0.0;
    }

    $db = Database::getIstance();
    $sql = "SELECT ROUND(AVG(rating), 1) as average_rating 
            FROM reviews 
            WHERE product_id = ?";
    $results = $db->query($sql, [$this->id], "s");

    if (empty($results) || !isset($results[0]["average_rating"])) {
      return 0.0;
    }

    return (float) $results[0]["average_rating"];
  }

  /**
   * Ottiene il venditore di questo prodotto
   */
  public function getVendor(): ?User
  {
    if (!$this->vendor_id) {
      return null;
    }

    return Vendor::findById($this->vendor_id);
  }

  /**
   * Converte il prodotto in un array associativo
   */
  public function toArray(bool $includeRelations = true): array
  {
    $result = [
      "id" => $this->id,
      "vendor_id" => $this->vendor_id,
      "name" => $this->name,
      "description" => $this->description,
      "price" => number_format($this->price, 2, '.', ''),
      "image_name" => $this->image_name,
      "image_alt" => $this->image_alt,
      "quantity" => $this->quantity,
      "average_rating" => $this->getAverageRating(),
      "valid" => $this->valid,
    ];

    if ($includeRelations) {

      // Converte gli oggetti Category in array
      $categories = [];
      foreach ($this->getCategories() as $category) {
        $categories[] = $category->toArray();
      }
      $result["categories"] = $categories;

      // Converte gli oggetti Review in array
      $reviews = [];
      foreach ($this->getReviews() as $review) {
        $reviews[] = $review->toArray(true);
      }
      $result["reviews"] = $reviews;
    }
    return $result;
  }
}