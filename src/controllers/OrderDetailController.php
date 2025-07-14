<?php

namespace controllers;
use core\Controller;
use core\Session;
use models\Notification;
use models\Order;

class OrderDetailController extends Controller
{
  public function index()
  {
    $orderId = (int) $this->request->getParam("id");
    if (!$orderId) {
      header('Location: /404');
      exit;
    }
    $order = Order::findById($orderId);

    $orders = $order->toArray(true);

    $isVendor = Session::get('user')['isVendor'] ?? false;

    $this
      ->setData('title', 'Dettaglio ordine')
      ->setData('id', $orderId)
      ->setData('order', $orders)
      ->setData('isVendor', $isVendor)
      ->setData('context', 'orderDetail')
      ->setData('showActions', false)
      ->render('dettaglio-ordine');
  }

  public function cancelOrder()
  {
    $orderId = (int) $this->request->getParam("id");

    $order = Order::findById($orderId);
    if ($order && $order->delete()) {
      Session::setFlash('ordine_annullato', 'Ordine annullato con successo.', 'success');
      Notification::notifyOrderCancelled($orderId);
      exit;
    } else {
      Session::setFlash('ordine_annullato', 'Impossibile annullare l\'ordine. Riprova più tardi.', 'error');
      exit;
    }
  }

  public function markAsPaid()
  {
    $orderId = (int) $this->request->getParam("id");
    
    $order = Order::findById($orderId);
    if ($order && $order->markAsPaid()) {
      Session::setFlash('ordine_pagato', 'Ordine segnato come pagato.', 'success');
      exit;
    } else {
      Session::setFlash('ordine_non_pagato', 'Impossibile segnare l\'ordine come pagato. Riprova più tardi.', 'error');
      exit;
    }
  }
}