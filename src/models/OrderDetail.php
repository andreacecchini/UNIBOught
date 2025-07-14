<?php

namespace models;

use core\BaseModel;
use core\Database;
use Exception;

class OrderDetail extends BaseModel
{
  public $order_id;
  public $product_id;
  public $quantity;
  public $purchase_unit_price;

  /**
   * Costruttore che inizializza un oggetto OrderDetail da un array associativo
   */
  protected function __construct(array $data = [])
  {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        if (($key === "order_id" || $key === "quantity") && is_string($value)) {
          $this->$key = (int) $value;
        } elseif ($key === "purchase_unit_price" && is_string($value)) {
          $this->$key = (float) $value;
        } else {
          $this->$key = $value;
        }
      }
    }
  }

  /**
   * Crea una nuova istanza di dettaglio ordine con i parametri forniti
   */
  public static function create(
    int $orderId,
    string $productId,
    int $quantity,
    float $purchaseUnitPrice
  ) {
    if ($orderId == null || $productId == null) {
      throw new Exception("ID ordine e prodotto sono obbligatori");
    }

    if ($quantity <= 0) {
      throw new Exception("La quantità deve essere maggiore di zero");
    }

    if ($purchaseUnitPrice <= 0) {
      throw new Exception("Il prezzo unitario deve essere maggiore di zero");
    }

    $detail = new self();
    $detail->order_id = $orderId;
    $detail->product_id = $productId;
    $detail->quantity = $quantity;
    $detail->purchase_unit_price = $purchaseUnitPrice;

    return $detail;
  }

  /**
   * @inheritDoc
   */
  protected static function tableName(): string
  {
    return 'order_details';
  }

  /**
   * Trova tutti i dettagli di un ordine
   */
  public static function findByOrderId(int $orderId): array
  {
    return self::findWhere(['order_id' => $orderId]);
  }

  /**
   * Salva lo stato del dettaglio ordine nel database
   */
  public function save(): bool
  {
    // Verifica se esiste già questo dettaglio (chiave composta)
    $existing = self::findOne([
      'order_id' => $this->order_id,
      'product_id' => $this->product_id
    ]);

    if ($existing) {
      return $this->update();
    } else {
      return $this->insert();
    }
  }

  /**
   * Inserisce il dettaglio dell'ordine nel database
   */
  private function insert(): bool
  {
    if (
      !isset($this->order_id) || !isset($this->product_id) ||
      !isset($this->quantity) || !isset($this->purchase_unit_price)
    ) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "INSERT INTO order_details (order_id, product_id, quantity, purchase_unit_price) 
            VALUES (?, ?, ?, ?)";
    $params = [
      $this->order_id,
      $this->product_id,
      $this->quantity,
      $this->purchase_unit_price
    ];
    $types = "isid";

    return $db->query($sql, $params, $types);
  }

  /**
   * Aggiorna il dettaglio dell'ordine
   */
  private function update(): bool
  {
    if (!isset($this->order_id) || !isset($this->product_id)) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "UPDATE order_details 
            SET quantity = ?, purchase_unit_price = ? 
            WHERE order_id = ? AND product_id = ?";
    $params = [
      $this->quantity,
      $this->purchase_unit_price,
      $this->order_id,
      $this->product_id
    ];
    $types = "idis";

    return $db->query($sql, $params, $types);
  }

  /**
   * Elimina il dettaglio ordine dal database
   */
  public function delete(): bool
  {
    if (!isset($this->order_id) || !isset($this->product_id)) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "DELETE FROM order_details WHERE order_id = ? AND product_id = ?";
    $params = [$this->order_id, $this->product_id];
    $types = "is";

    return $db->query($sql, $params, $types);
  }

  /**
   * Ottiene il prodotto associato a questo dettaglio ordine
   */
  public function getProduct(): ?Product
  {
    if (!$this->product_id) {
      return null;
    }
    // Per ottenere anche i prodotti eliminati logicalmente non viene usata la funzione findById
    return Product::findWhere(['id' => $this->product_id])[0] ?? null;
  }

  /**
   * Ottiene l'ordine associato a questo dettaglio
   */
  public function getOrder(): ?Order
  {
    if (!$this->order_id) {
      return null;
    }

    return Order::findById($this->order_id);
  }

  /**
   * Calcola il subtotale di questo dettaglio (prezzo × quantità)
   */
  public function getSubtotal(): float
  {
    return $this->purchase_unit_price * $this->quantity;
  }

  /**
   * Converte il dettaglio ordine in array associativo
   */
  public function toArray(bool $includeProduct = false): array
  {
    $result = [
      "order_id" => $this->order_id,
      "product_id" => $this->product_id,
      "quantity" => $this->quantity,
      "purchase_unit_price" => (float) $this->purchase_unit_price,
      "subtotal" => $this->getSubtotal()
    ];

    if ($includeProduct) {
      $product = $this->getProduct();
      if ($product) {
        $result["product"] = [
          "id" => $product->id,
          "name" => $product->name,
          "image_name" => $product->image_name,
          "image_alt" => $product->image_alt,
          "average_rating" => $product->getAverageRating()
        ];
      }
    }

    return $result;
  }

  private static function findOne(array $conditions): ?OrderDetail
  {
    $results = self::findWhere($conditions);
    return $results[0] ?? null;
  }
}
