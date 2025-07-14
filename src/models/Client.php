<?php

namespace models;

use core\Database;
use Exception;

class Client extends User
{

  /**
   * Autentica un cliente mediante email e password
   */
  public static function authenticate(string $email, string $password)
  {
    // Cerco il cliente tramite email
    $client = self::findByEmail($email);

    // Controllo se il cliente è all'interno della tabella client
    if (!$client || !$client->isClient()) {
      return false;
    }

    // Se il cliente non esiste o la password non corrisponde
    if (!$client || !$client->verifyPassword($password)) {
      return false;
    }

    return $client;
  }

  /**
   * Ottiene il carrello dell'utente (se è un cliente)
   */
  public function getCartItems(): array
  {
    if (!$this->id) {
      return [];
    }
    $db = Database::getIstance();
    $sql = "SELECT ci.product_id as id, ci.quantity, p.name, p.price, p.image_name, p.image_alt,
          (SELECT AVG(rating) FROM reviews WHERE product_id = p.id) as average_rating
          FROM cart_items ci 
          JOIN products p ON ci.product_id = p.id 
          WHERE ci.client_id = ? AND p.valid = 1";

    $result = $db->query($sql, [$this->id], "i");

    if (!$result) {
      return [];
    }

    foreach ($result as &$item) {
      $item['average_rating'] = $item['average_rating'] ?? 0;
    }

    return $result;
  }

  /**
   * Effettua il merge tra i prodotti passati come argomento e il carrello del cliente (db)
   */
  public function mergeCartItems(array $cartItems): bool
  {
    if (!$this->id) {
      return false;
    }
    // L'operazione é riuscita solo se tutti gli elementi vengono aggiunti al carrello
    $success = true;
    foreach ($cartItems as $item) {
      // Aggiungo il prodotto nel carello con la quantitá specificata
      $success &= $this->addProductToCart($item['id'], $item['quantity']);
    }
    return $success;
  }

  /**
   * Ottiene gli ordini del cliente
   */
  public function getOrders(): array
  {
    if (!$this->id) {
      return [];
    }

    return Order::findWhere(['client_id' => $this->id], 'order_date', 'DESC');
  }

  /**
   * Aggiorna la quantita` di un prodotto nel carrello del cliente.
   */
  public function updateCartItemQuantity(string $productId, int $quantity): bool
  {
    if (!$this->id) {
      return false;
    }

    if ($quantity < 1) {
      return false;
    }

    $db = Database::getIstance();
    $sql = "UPDATE cart_items SET quantity = ? WHERE client_id = ? AND product_id = ?";
    $params = [$quantity, $this->id, $productId];
    return $db->query($sql, $params, "iis");
  }

  /**
   * Aggiunge un prodotto nel carrello del cliente
   */
  public function addProductToCart(string $productId, int $quantity = 1)
  {
    if (!$this->id) {
      return false;
    }
    $db = Database::getIstance();
    $sql = "INSERT INTO cart_items (client_id, product_id, quantity) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = quantity + ?";
    $params = [$this->id, $productId, $quantity, $quantity];
    return $db->query($sql, $params, "isii");
  }

  /**
   * Rimuove un prodotto dal carrello del cliente
   */
  public function removeProductFromCart(string $productId)
  {
    if (!$this->id) {
      return false;
    }
    $db = Database::getIstance();
    $sql = "DELETE FROM cart_items WHERE client_id = ? AND product_id = ?";
    return $db->query($sql, [$this->id, $productId], "is");
  }

  public function clearCart()
  {
    if (!$this->id) {
      return false;
    }
    $db = Database::getIstance();
    $sql = "DELETE FROM cart_items WHERE client_id = ?";
    return $db->query($sql, [$this->id], "i");
  }

  public function checkOrderOwnership(string $orderId)
  {
    if (!$this->id) {
      return false;
    }
    $db = Database::getIstance();
    $sql = "SELECT COUNT(*) as count FROM orders WHERE id = ? AND client_id = ?";
    $params = [$orderId, $this->id];
    $result = $db->query($sql, $params, "ii");
    return $result[0]['count'] > 0;
  }

  private function decrementProductQuantity(string $productId, int $quantity): bool
  {
    $db = Database::getIstance();
    // Mi asicuro che la quantità non scenda mai sotto lo zero.
    $sql = "UPDATE products SET quantity = GREATEST(quantity - ?, 0) WHERE id = ?";
    $params = [$quantity, $productId];
    $res = $db->query($sql, $params, "is");
    $product = Product::findById($productId);
    if ($product && $product->quantity < 10) {
      Notification::notifyProductShortage($productId);
    }
    return $res;
  }

  public function createOrderFromCart($isPaid = false): ?Order
  {
    if (!$this->id) {
      return null;
    }
    $cartItems = $this->getCartItems();
    if (empty($cartItems)) {
      return null;
    }
    $db = Database::getIstance();
    try {
      return $db->transaction(function () use ($db, $cartItems, $isPaid) {
        // Crea un nuovo ordine
        $order = Order::create($this->id, 'pending', $isPaid);
        if (!$order->save()) {
          throw new Exception('Errore durante il salvataggio dell\'ordine.');
        }
        // Aggiungi prodotti all'ordine
        foreach ($cartItems as $item) {
          $success = $order->addProduct(
            $item['id'],
            $item['quantity'],
            $item['price']
          );
          if (!$success) {
            return null;
          }
          // Svuota il carrello
          $this->clearCart();
          // Decrementa la quantità del prodotto disponibile nello store
          $this->decrementProductQuantity($item['id'], $item['quantity']);
        }
        return $order;
      });
    } catch (Exception $e) {
      return null;
    }
  }

  public function hasPurchased(string $productId): bool
  {
    if (!$this->id) {
      return false;
    }
    $db = Database::getIstance();
    $sql = "SELECT COUNT(*) as count
    FROM `orders` o
    INNER JOIN order_details od ON o.id = od.order_id
    WHERE o.client_id = ? AND od.product_id = ? AND o.status = 'completed'";
    $params = [
      $this->id,
      $productId
    ];
    $result = $db->query($sql, $params, "is");
    return !empty($result) && $result[0]['count'] > 0;
  }

  public function hasReviewed(string $productId): bool
  {
    if (!$this->id) {
      return false;
    }
    $db = Database::getIstance();
    $sql = "SELECT COUNT(*) as count FROM reviews WHERE client_id = ? AND product_id = ?";
    $params = [
      $this->id,
      $productId
    ];
    $result = $db->query($sql, $params, "is");
    return !empty($result) && $result[0]['count'] > 0;
  }

  public static function clientsWithProductInCart(string $productId): array
  {
    $db = Database::getIstance();
    $sql = "SELECT client_id
    FROM cart_items
    WHERE product_id = ?";
    $params = [$productId];
    $types = "s";

    $result = $db->query($sql, $params, $types);

    $clients = [];
    foreach ($result as $row) {
      $clients[] = Client::findById($row['client_id']);
    }

    return $clients;
  }

  protected function insert(): bool
  {
    $db = Database::getIstance();
    $success = $db->transaction(function () use ($db) {
      parent::insert();
      $sql = "INSERT INTO clients (user_id) VALUES (?)";
      $success = $db->query($sql, [$this->id], "i");
      return $success;
    });
    return $success;
  }
}