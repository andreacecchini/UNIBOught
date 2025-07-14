<?php
namespace models;

use core\BaseModel;
use core\Database;
use Exception;

class Notification extends BaseModel
{
  public $id;
  public $user_id;
  public $message;
  public $reference;
  public $sent_date;
  public $status;

  /**
   * Costruttore della classe Notification
   */
  protected function __construct($data = [])
  {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        if ($key === "id" || $key === "user_id") {
          $this->$key = (int) $value;
        } else {
          $this->$key = $value;
        }
      }
    }
  }

  /**
   * Ritorna il nome della tabella
   */
  protected static function tableName(): string
  {
    return 'notifications';
  }

  /**
   * Trova tutte le notifiche non lette per un determinato utente
   */
  public static function findUnreadByUserId(int $userId): array
  {
    return self::findWhere(
      ['user_id' => $userId, 'status' => 'unread'],
      'sent_date',
      'DESC'
    );
  }

  /**
   * Trova tutte le notifiche di un determinato utente
   */
  public static function findByUserId(int $userId): array
  {
    $result = self::findWhere(
      ['user_id' => $userId],
      'sent_date',
      'DESC'
    );
    return $result;
  }

  /**
   * Crea una nuova istanza di notifica con i parametri forniti.
   */
  public static function create(int $userId, string $message, string $status = 'unread', string $reference = null): Notification
  {
    $validStatuses = ['read', 'unread'];
    if (!in_array($status, $validStatuses)) {
      throw new Exception("Stato notifica non valido. Stati validi: " . implode(', ', $validStatuses));
    }

    // Utilizza il costruttore per inizializzare le proprietà
    $notification = new self([
      'user_id' => $userId,
      'message' => $message,
      'status' => $status,
      'reference' => $reference
    ]);
    // id e sent_date saranno null inizialmente. Vengono impostati dopo una chiamata save()->insert() con successo.

    return $notification;
  }

  /**
   * Salva la notifica nel database
   */
  public function save(): bool
  {
    if (!isset($this->id) || empty($this->id)) {
      return $this->insert();
    }
    $existingNotification = self::findById($this->id);
    if (!$existingNotification) {
      return $this->insert();
    }
    return $this->update();
  }

  /**
   * Inserisce una nuova notifica nel database
   */
  private function insert(): bool
  {
    $db = Database::getIstance();
    $sql = "INSERT INTO notifications (user_id, message, status, reference) 
            VALUES (?, ?, ?, ?)";

    $params = [
      $this->user_id,
      $this->message,
      $this->status,
      $this->reference
    ];

    $types = "isss";

    $success = $db->query($sql, $params, $types);

    if ($success) {
      $this->id = $db->getLastInsertId();

      // Recupera la data generata dal database
      $result = $db->query(
        "SELECT sent_date FROM notifications WHERE id = ?",
        [$this->id],
        "i"
      );

      if ($result && isset($result[0]["sent_date"])) {
        $this->sent_date = $result[0]["sent_date"];
      }

      return true;
    }

    return false;
  }

  /**
   * Aggiorna una notifica esistente nel database
   */
  private function update(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "UPDATE notifications SET user_id = ?, message = ?, status = ?, reference = ? 
            WHERE id = ?";

    $params = [
      $this->user_id,
      $this->message,
      $this->status,
      $this->reference,
      $this->id
    ];

    $types = "isssi";

    return $db->query($sql, $params, $types);
  }

  /**
   * Elimina la notifica dal database
   */
  public function delete(): bool
  {
    if (!$this->id) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "DELETE FROM notifications WHERE id = ?";
    $params = [$this->id];
    $types = "i";

    return $db->query($sql, $params, $types);
  }

  /**
   * Segna la notifica come letta
   */
  public function markAsRead(): bool
  {
    if (!$this->id) {
      return false;
    }
    $this->status = 'read';
    return $this->save();
  }

  /**
   * Segna tutte le notifiche di un utente come lette
   */
  public static function markAllAsReadByUserId(int $userId): bool
  {
    $db = Database::getIstance();
    $sql = "UPDATE notifications SET status = 'read' WHERE user_id = ? AND status = 'unread'";
    $params = [$userId];
    $types = "i";
    return $db->query($sql, $params, $types);
  }

  /**
   * Conta il numero di notifiche non lette per un utente
   */
  public static function countUnreadByUserId(int $userId): int
  {
    $db = Database::getIstance();
    $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND status = 'unread'";
    $result = $db->query($sql, [$userId], "i");

    return isset($result[0]['count']) ? (int) $result[0]['count'] : 0;
  }

  /**
   * Ottiene l'utente destinatario della notifica
   */
  public function getUser(): ?User
  {
    if (!$this->user_id) {
      return null;
    }
    return User::findById($this->user_id);
  }

  /**
   * Converte la notifica in un array associativo
   */
  public function toArray(): array
  {
    return [
      "id" => $this->id,
      "user_id" => $this->user_id,
      "message" => $this->message,
      "sent_date" => $this->sent_date,
      "status" => $this->status,
      "is_read" => ($this->status === 'read'),
      "reference" => $this->reference
    ];
  }

  /**
   * Invia una notifica all'utente
   */
  public static function notify(int $userId, string $message, string $status = 'unread', string $reference = null): bool
  {
    try {
      $notification = Notification::create($userId, $message, $status, $reference);
      return $notification->save();
    } catch (Exception $e) {
      error_log("Errore invio notifica: " . $e->getMessage());
      return false;
    }
  }

  public static function notifyOrderConfirmed(int $userId, int $orderId): bool
  {
    $vendorId = Order::findById($orderId)->getOrderDetails()[0]->getProduct()->vendor_id;
    $messageClient = "L'ordine #$orderId è stato effettuato.";
    $utenteNome = User::findById($userId)->name;
    $messageVendor = "Il cliente $utenteNome ha effettuato un ordine (ordine #$orderId)";
    return self::notify($userId, $messageClient, reference: $orderId) &&
      self::notify($vendorId, $messageVendor, reference: $orderId);
  }

  public static function notifyOrderStatusUpdate(int $userId, int $orderId, string $newStatus): bool
  {
    $statuses = [
      'pending' => "ora in attesa",
      'processing' => "ora in lavorazione",
      'shipped' => "in fase di consegna",
      'completed' => "stato completato",
      'cancelled' => "stato annullato",
    ];
    $message = "L'ordine #$orderId è $statuses[$newStatus].";
    return self::notify($userId, $message, reference: $orderId);
  }

  public static function notifyAccountEdited(int $userId): bool
  {
    $message = "Il tuo profilo è stato aggiornato con successo.";
    return self::notify($userId, $message);
  }

  public static function notifyProductReviewAdded(int $userId, string $productId): bool
  {
    $userName = User::findById($userId)->name;
    $prodotto = Product::findByIdValid($productId)->name;
    $message = "$userName ha aggiunto una nuova recensione per il prodotto \"$prodotto\".";
    return self::notify(Product::findById($productId)->vendor_id, $message, reference: $productId);
  }

  public static function notifyProductReviewEdited(int $userId, string $productId): bool
  {
    $userName = User::findById($userId)->name;
    $prodotto = Product::findByIdValid($productId)->name;
    $message = "$userName ha modificato la recensione per il prodotto \"$prodotto\".";
    return self::notify(Product::findById($productId)->vendor_id, $message, reference: $productId);
  }

  public static function notifyProductShortage(string $productId): bool
  {
    $product = Product::findById($productId);
    $vendorId = $product->vendor_id;

    if (!$product) {
      return false;
    }

    $clients = Client::clientsWithProductInCart($productId);

    foreach ($clients as $client) {
      $message = "\"{$product->name}\" nel tuo carrello è in esaurimento, sbrigati a comprarlo!";
      self::notify($client->id, $message, reference: $productId );
    }

    $message = "\"{$product->name}\" ha raggiunto il livello minimo di scorte.";
    return self::notify($vendorId, $message, reference: $productId);
  }


  public static function notifyProductQuarantine(string $productId): bool
  {
    $product = Product::findById($productId);
    if (!$product) {
      return false;
    }
    $clients = Client::clientsWithProductInCart($productId);
    $res = false;
    foreach ($clients as $client) {
      $message = "{$product->name} non è temporaneamente disponibile per l'acquisto. Si prega di controllare la disponibilità più tardi.";
      $res = self::notify($client->id, $message, reference: $productId);
    }
    return $res;
  }

  public static function notifyOrderCancelled(string $orderId): bool
  {
    $order = Order::findById($orderId);
    if (!$order) {
      return false;
    }
    $userId = $order->client_id;

    $vendorId = $order->getOrderDetails()[0]->getProduct()->vendor_id;
    $userName = User::findById($userId)->name;
    $vendorMessage = "$userName ha annullato l'ordine #$orderId.";

    return self::notify($vendorId, $vendorMessage, reference: $orderId);
  }
}