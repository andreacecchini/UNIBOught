<?php

namespace controllers;
use core\Controller;
use core\Session;
use models\Client;
use models\Product;
use Exception;

class ShopCartController extends Controller
{
  public function index()
  {
    $productsInCart = [];
    if (Session::isLoggedIn()) {
      // DB
      $user = Client::findById(Session::get('user')['id']);
      $productsInCart = $user->getCartItems();
    } else {
      // Sesssion
      $productsInCart = Session::get('cart', []);
    }
    $this
      ->setViewData(['title' => 'Carrello', 'context' => 'cart', 'products' => $productsInCart])
      ->render(viewPath: 'carrello');
  }

  public function updateQuantity()
  {
    try {
      $input = $this->request->getBody();
      if (!$input || !isset($input['product_id'], $input['quantity'])) {
        $this->renderJson(['success' => false, 'message' => 'Dati mancanti'], 200);
        return;
      }
      $productId = $input['product_id'];
      $quantity = (int) $input['quantity'];
      if ($quantity < 1) {
        $this->renderJson(['success' => false, 'message' => 'Quantità non valida'], 200);
        return;
      }
      if (Session::isLoggedIn()) {
        $user = Client::findById(Session::get('user')['id']);
        $success = $user->updateCartItemQuantity($productId, $quantity);
      } else {
        $success = $this->updateSessionCartQuantity($productId, $quantity);
      }
      $message = $success ? 'Quantità aggiornata' : 'Errore nell\'aggiornamento';
      $this->renderJson(['success' => $success, 'message' => $message]);
    } catch (Exception $e) {
      $this->renderJson(['success' => false, 'message' => 'Errore del server: ' . $e->getMessage()], 500);
    }
  }

  public function addProduct()
  {
    try {
      $isAjax = $this->request->isAjax();
      $input = $this->request->getBody();
      if (!$input || !isset($input['product_id'])) {
        return $this->handleError($isAjax, 'Dati mancanti', 400);
      }
      $productId = $input['product_id'];
      $quantity = isset($input['quantity']) ? (int) $input['quantity'] : 1;
      if ($quantity < 1) {
        return $this->handleError($isAjax, 'Quantità non valida', 400);
      }
      if (Session::isLoggedIn()) {
        $user = Client::findById(Session::get('user')['id']);
        $success = $user->addProductToCart($productId, $quantity);
      } else {
        $success = $this->addToSessionCart($productId, $quantity);
      }
      return $this->handleSuccess($isAjax, $success, 'Prodotto aggiunto al carrello', 'Errore nell\'aggiunta');
    } catch (Exception $e) {
      error_log("Errore in addProduct: " . $e->getMessage());
      return $this->handleError($this->request->isAjax(), 'Errore l server: ' . $e->getMessage(), 500);
    }
  }

  public function removeProduct()
  {
    try {
      // Ottengo i dati contenuti nel body della richiesta
      $input = $this->request->getBody();
      if (!$input || !isset($input['product_id'])) {
        $this->renderJson(['success' => false, 'message' => 'Dati mancanti']);
        return;
      }
      $productId = $input['product_id'];
      if (Session::isLoggedIn()) {
        // Se l'utente è loggato allora rimuovo il prodotto dal carrello memorizzato nel database
        $user = Client::findById(Session::get('user')['id']);
        $success = $user->removeProductFromCart($productId);
      } else {
        // Altrimenti lo rimuovo dalla sessione
        $success = $this->removeFromSessionCart($productId);
      }
      $message = $success ? 'Prodotto rimosso dal carrello' : 'Errore nella rimozione';
      $this->renderJson(['success' => $success, 'message' => $message]);
    } catch (Exception $e) {
      $this->renderJson(['success' => false, 'message' => 'Errore del server: ' . $e->getMessage()], 500);
    }
  }

  private function handleError($isAjax, $message, $statusCode = 400)
  {
    if ($isAjax) {
      $this->response->setHeader('Content-Type: application/json');
      $this->renderJson(['success' => false, 'message' => $message], $statusCode);
    } else {
      // Se la richiesta non è AJAX, reindirizza alla home con un messaggio di errore (possibilemente un messaggio flash in sessione)
      $this->response->setStatusCode($statusCode);
      header('Location: /');
      exit;
    }
  }

  private function handleSuccess($isAjax, $success, $successMessage, $errorMessage)
  {
    if ($isAjax) {
      // Se la richiesta è AJAX, restituisce un JSON con la conferma del successo
      $message = $success ? $successMessage : $errorMessage;
      $this->renderJson(data: ['success' => $success, 'message' => $message]);
    } else {
      // Se la richiesta viene da un form di una pagina web, allora si viene reindirizzati alla home
      Session::setFlash('add_to_cart', $success ? $successMessage : $errorMessage, $success ? 'success' : 'error');
      header('Location: /');
      exit;
    }
  }

  private function addToSessionCart($productId, $quantity = 1)
  {
    $cart = Session::get('cart', []);
    // Controlla se il prodotto è già nel carrello
    foreach ($cart as &$item) {
      if ($item['id'] == $productId) {
        $item['quantity'] += $quantity;
        Session::set('cart', $cart);
        return true;
      }
    }
    // Se non è nel carrello, lo si aggiunge
    $product = Product::findById($productId);
    if (!$product) {
      return false;
    }
    $cart[] = [
      'id' => $productId,
      'quantity' => $quantity,
      'name' => $product->name,
      'price' => $product->price,
      'image_name' => $product->image_name,
      'image_alt' => $product->image_alt,
      'average_rating' => $product->getAverageRating(),
    ];
    Session::set('cart', $cart);
    return true;
  }

  private function removeFromSessionCart($productId)
  {
    $cart = Session::get('cart', []);
    foreach ($cart as $key => $item) {
      if ($item['id'] === $productId) {
        unset($cart[$key]);
        Session::set('cart', $cart);
        return true;
      }
    }
    return false;
  }

  private function updateSessionCartQuantity($productId, $quantity)
  {
    $cart = Session::get('cart', []);
    foreach ($cart as &$item) {
      if ($item['id'] === $productId) {
        // Aggiorna la quantità del prodotto nel carrello sovrascrivendone il valore
        $item['quantity'] = $quantity;
        Session::set('cart', $cart);
        return true;
      }
    }
    return false;
  }

  public function countProductsInCart()
  {
    $cart = Session::isLoggedIn() ?
      Client::findById(Session::get('user')['id'])->getCartItems() :
      Session::get('cart', []);
    $this->renderJson([
      'totalCount' => array_reduce($cart, function ($acc, $item) {
        return $acc += $item['quantity'];
      }, 0)
    ]);
  }
}