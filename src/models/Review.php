<?php

namespace models;

use core\BaseModel;
use core\Database;

class Review extends BaseModel
{
  public $id;
  public $client_id;
  public $product_id;
  public $title;
  public $content;
  public $rating;
  public $review_date;

  /**
   * Costruttore che inizializza un oggetto Review da un array associativo
   */
  protected function __construct(array $data = [])
  {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        if (($key === "id" || $key === "client_id" || $key === "product_id") && is_string($value)) {
          $this->$key = (int) $value;
        } elseif ($key === "rating" && is_string($value)) {
          $this->$key = (int) $value; // In DB è TINYINT (non float)
        } else {
          $this->$key = $value;
        }
      }
    }
  }

  /**
   * Crea una nuova istanza di recensione con i parametri forniti
   */
  public static function create(
    int $clientId,
    string $productId,
    string $title,
    string $content,
    int $rating
  ) {
    if ($clientId <= 0 || $productId <= 0) {
      throw new \Exception("ID cliente e prodotto sono obbligatori");
    }

    if (empty($title)) {
      throw new \Exception("Il titolo della recensione è obbligatorio");
    }

    if (empty($content)) {
      throw new \Exception(message: "Il contenuto della recensione è obbligatorio");
    }

    if ($rating < 1 || $rating > 5) {
      throw new \Exception("La valutazione deve essere compresa tra 1 e 5");
    }

    $review = new self();
    $review->client_id = $clientId;
    $review->product_id = $productId;
    $review->title = $title;
    $review->content = $content;
    $review->rating = $rating;
    $review->review_date = date('Y-m-d H:i:s');

    return $review;
  }

  /**
   * @inheritDoc
   */
  protected static function tableName(): string
  {
    return 'reviews';
  }

  /**
   * Trova recensioni per un prodotto specifico
   */
  public static function findByProductId(string $productId): array
  {
    return self::findWhere(['product_id' => $productId], 'review_date', 'DESC');
  }

  /**
   * Trova recensioni di un cliente specifico
   */
  public static function findByClientId(int $clientId): array
  {
    return self::findWhere(['client_id' => $clientId], 'review_date', 'DESC');
  }

  /**
   * Trova una recensione di un cliente per un prodotto specifico
   */
  public static function findByClientAndProductId(int $clientId, string $productId): ?Review
  {
    $result = self::findWhere(['client_id' => $clientId, 'product_id' => $productId]);
    return $result ? $result[0] : null;
  }
  /**
   * Salva la recensione nel database
   */
  public function save(): bool
  {
    if (!isset($this->id) || empty($this->id)) {
      return $this->insert();
    }
    $existingReview = self::findById($this->id);
    if (!$existingReview) {
      return $this->insert();
    }
    return $this->update();
  }

  /**
   * Inserisce una nuova recensione nel database
   */
  private function insert(): bool
  {
    if (
      !isset($this->client_id) || !isset($this->product_id) || !isset($this->rating) ||
      !isset($this->title) || !isset($this->content)
    ) {
      return false;
    }
    error_log("Inserting review: " . print_r($this, true));
    $db = Database::getIstance();
    $sql = "INSERT INTO reviews (client_id, product_id, title, content, rating, review_date) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $params = [
      $this->client_id,
      $this->product_id,
      $this->title,
      $this->content,
      $this->rating,
      $this->review_date ?? date('Y-m-d H:i:s')
    ];
    $types = "isssds";

    $success = $db->query($sql, $params, $types);

    if ($success) {
      $this->id = $db->getLastInsertId();
      return true;
    }
    return false;
  }

  /**
   * Aggiorna una recensione esistente nel database
   */
  private function update(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "UPDATE reviews 
            SET title = ?, content = ?, rating = ?, review_date = NOW()
            WHERE id = ?";
    $params = [
      $this->title,
      $this->content,
      $this->rating,
      $this->id
    ];
    $types = "ssdi";

    return $db->query($sql, $params, $types);
  }

  /**
   * Elimina la recensione dal database
   */
  public function delete(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "DELETE FROM reviews WHERE id = ?";
    $params = [$this->id];
    $types = "i";

    return $db->query($sql, $params, $types);
  }

  /**
   * Ottiene il prodotto associato a questa recensione
   */
  public function getProduct(): ?Product
  {
    if (!$this->product_id) {
      return null;
    }

    return Product::findById($this->product_id);
  }

  /**
   * Ottiene il cliente che ha scritto questa recensione
   */
  public function getClient(): ?User
  {
    if (!$this->client_id) {
      return null;
    }

    return Client::findById($this->client_id);
  }

  /**
   * Converte la recensione in array associativo
   */
  public function toArray(bool $includeRelations = false): array
  {

    $client = $this->getClient();

    $result = [
      "id" => $this->id,
      "username" => $client->name . " " . $client->surname,
      "title" => $this->title,
      "content" => $this->content,
      "rating" => (int) $this->rating,
      "review_date" => date('d/m/Y', strtotime($this->review_date))
    ];

    return $result;
  }
}