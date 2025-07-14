<?php
namespace models;

use core\BaseModel;
use core\Database;
use Exception;

class Order extends BaseModel
{
  public $id;
  public $client_id;
  public $order_date;
  public $expected_pickup_date;
  public $status;
  public $isPaid;

  /**
   * Costruttore della classe Order
   */
  public function __construct(array $data = [])
  {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        if (($key === "id" || $key === "client_id") && is_string($value)) {
          $this->$key = (int) $value;
        } else {
          $this->$key = $value;
        }
      }
    }
  }

  /**
   * Crea un nuovo ordine con i parametri specificati
   */
  public static function create(
    int $clientId,
    string $status = 'pending',
    bool $isPaid = false
  ) {
    if ($clientId <= 0) {
      throw new Exception("L'ID del cliente è obbligatorio");
    }

    $order = new self();
    $order->client_id = $clientId;
    $order->order_date = date('Y-m-d H:i:s');
    $order->expected_pickup_date = date('Y-m-d H:i:s', strtotime('+7 days'));
    $order->status = $status;
    $order->isPaid = $isPaid === true ? 1 : 0;

    return $order;
  }

  /**
   * @inheritDoc
   */
  protected static function tableName(): string
  {
    return 'orders';
  }

  public static function findAll(): array
  {
    return self::findWhere([], 'order_date', 'DESC');
  }

  public static function findAllNotCancelled(): array
  {
    return self::findWhere([
      'status' => [
        'operator' => '!=',
        'value' => 'cancelled'
      ]
    ], 'order_date', 'DESC');
  }

  /**
   * Trova ordini per cliente
   * @param int $clientId ID del cliente
   * @return Order[] Array di oggetti Order
   */
  public static function findByClientId(int $clientId): array
  {
    return self::findWhere(['client_id' => $clientId], 'order_date', 'DESC');
  }

  /**
   * Salva l'ordine nel database
   * @return bool True in caso di successo, False altrimenti
   */
  public function save(): bool
  {
    if (!isset($this->id) || empty($this->id)) {
      return $this->insert();
    }
    $existingOrder = self::findById($this->id);
    if (!$existingOrder) {
      return $this->insert();
    }
    return $this->update();
  }

  /**
   * Inserisce un nuovo ordine nel database
   * @return bool True in caso di successo, False altrimenti
   */
  private function insert(): bool
  {
    if (!isset($this->client_id) || $this->client_id <= 0) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "INSERT INTO orders (client_id, order_date, expected_pickup_date, status, isPaid) 
            VALUES (?, ?, ?, ?, ?)";

    $params = [
      $this->client_id,
      $this->order_date ?? date('Y-m-d H:i:s'),
      $this->expected_pickup_date,
      $this->status ?? 'pending',
      $this->isPaid ? 1 : 0
    ];

    $types = "isssi";

    $success = $db->query($sql, $params, $types);

    if ($success) {
      $this->id = $db->getLastInsertId();
      return true;
    }

    return false;
  }

  /**
   * Aggiorna un ordine esistente nel database
   */
  private function update(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "UPDATE orders 
            SET client_id = ?, expected_pickup_date = ?, status = ?, isPaid = ?
            WHERE id = ?";

    $params = [
      $this->client_id,
      $this->expected_pickup_date,
      $this->status,
      $this->isPaid ? 1 : 0,
      $this->id
    ];

    $types = "issii";

    return $db->query($sql, $params, $types);
  }

  /**
   * Elimina l'ordine dal database
   * @return bool True in caso di successo, False altrimenti
   */
  public function delete(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "UPDATE orders SET status = 'cancelled' WHERE id = ?";
    $params = [$this->id];
    $types = "i";

    return $db->query($sql, $params, $types);
  }

  /**
   * Aggiorna lo stato dell'ordine
   * @param string $newStatus Nuovo stato dell'ordine
   * @return bool True in caso di successo, False altrimenti
   */
  public function updateStatus(string $newStatus): bool
  {
    $validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

    if (!in_array($newStatus, $validStatuses)) {
      return false;
    }

    $this->status = $newStatus;
    return $this->save();
  }

  /**
   * Ottiene i dettagli dell'ordine (prodotti)
   * @return OrderDetail[] Array di oggetti OrderDetail
   */
  public function getOrderDetails(): array
  {
    if (!$this->id) {
      return [];
    }

    return OrderDetail::findByOrderId($this->id);
  }

  /**
   * Calcola il totale dell'ordine
   * @return float Importo totale dell'ordine
   */
  public function getTotal(): float
  {
    if (!$this->id) {
      return 0.0;
    }

    $details = $this->getOrderDetails();
    $total = 0.0;

    foreach ($details as $detail) {
      $total += $detail->purchase_unit_price * $detail->quantity;
    }
    $total += $total * 0.22;
    return $total;
  }

  /**
   * Ottiene il cliente associato all'ordine
   * @return User|null L'utente cliente
   */
  public function getClient(): ?User
  {
    if (!$this->client_id) {
      return null;
    }

    return User::findById($this->client_id);
  }

  /**
   * Aggiunge un prodotto all'ordine
   */
  public function addProduct(string $productId, int $quantity, float $price): bool
  {
    if (!$this->id || empty($productId) || $quantity <= 0 || $price <= 0) {
      return false;
    }
    try {
      $detail = OrderDetail::create(
        $this->id,
        $productId,
        $quantity,
        $price
      );

      return $detail->save();
    } catch (Exception $e) {
      return false;
    }
  }

  public function markAsPaid(): bool
  {
    if (!$this->id) {
      return false;
    }

    $this->isPaid = true;
    return $this->save();
  }


  /**
   * Converte l'ordine in un array associativo
   * @param bool $includeDetails Se includere i dettagli dei prodotti
   * @return array<string,mixed> Dati dell'ordine in formato array
   */
  public function toArray(bool $includeDetails = false): array
  {
    $orderArray = [
      "id" => $this->id,
      "client_id" => $this->client_id,
      "order_date" => $this->order_date,
      "expected_pickup_date" => $this->expected_pickup_date,
      "status" => $this->status,
      "isPaid" => (bool) $this->isPaid,
      "total" => $this->getTotal()
    ];

    if ($includeDetails) {
      $items = [];
      foreach ($this->getOrderDetails() as $detail) {
        $items[] = $detail->toArray(true);
      }
      $orderArray["items"] = $items;

      $client = $this->getClient();
      if ($client) {
        $orderArray["client"] = [
          "id" => $client->id,
          "name" => $client->name,
          "surname" => $client->surname,
          "email" => $client->email
        ];
      }
    }

    return $orderArray;
  }
}
