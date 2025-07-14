<?php

namespace controllers;

use core\Controller;
use core\Session;
use models\Client;
use models\Notification;

class CheckoutController extends Controller
{
  public function index()
  {
    $user = Session::get('user');
    $client = Client::findById($user['id']);
    $cartItems = $client->getCartItems();
    if (empty($cartItems)) {
      header('Location: /carrello');
      exit;
    }
    $subtotal = array_reduce($cartItems, function ($total, $item) {
      return $total + ($item['price'] * $item['quantity']);
    }, 0);
    // IVA
    $tax = round($subtotal * 0.22, 2);
    $total = $subtotal + $tax;
    $this->setLayout('no-navbar')
      ->setViewData([
        'title' => 'Checkout',
        'header' => 'Checkout',
        'cartItems' => $cartItems,
        'dataRitiro' => date('d/m/Y', strtotime('+7 days')),
        'context' => 'cart',
        'subtotal' => number_format($subtotal, 2),
        'tax' => number_format($tax, 2),
        'total' => number_format($total, 2)
      ])
      ->render('checkout');
  }

  public function paymentCard()
  {
    $cardNumber = $this->request->getParam('numero_carta');
    $expiryDate = $this->request->getParam('data_scadenza');
    $cvv = $this->request->getParam('cvv');

    if (empty($cardNumber) || empty($expiryDate) || empty($cvv)) {
      Session::setFlash('errore_pagamento', 'Tutti i campi sono obbligatori.', 'error');
      header('Location: /checkout');
      exit;
    }

    if (!preg_match('/^\d{16}$/', $cardNumber)) {
      Session::setFlash('errore_pagamento', 'Numero di carta non valido.', 'error');
      header('Location: /checkout');
      exit;
    }

    if (!preg_match('/^\d{3,4}$/', $cvv)) {
      Session::setFlash('errore_pagamento', 'CVV non valido.', 'error');
      header('Location: /checkout');
      exit;
    }

    $user = Session::get('user');
    $client = Client::findById($user['id']);
    $order = $client->createOrderFromCart(true);
    if ($order) {
      Notification::notifyOrderConfirmed($client->id, $order->id);
      header('Location: /dettaglio-ordine/' . $order->id);
    } else {
      Session::set('errore_creazione_ordine', 'Errore durante la creazione dell\'ordine. Riprova più tardi.');
      header('Location: /carrello');
    }
    exit;
  }

  public function paymentDelivery()
  {
    $user = Session::get('user');
    $client = Client::findById($user['id']);
    $order = $client->createOrderFromCart();
    if ($order) {
      Notification::notifyOrderConfirmed($client->id, $order->id);
      header('Location: /dettaglio-ordine/' . $order->id);
    } else {
      Session::setFlash('errore_creazione_ordine', 'Errore durante la creazione dell\'ordine. Riprova più tardi.', 'error');
      header('Location: /carrello');
    }
    exit;
  }
}