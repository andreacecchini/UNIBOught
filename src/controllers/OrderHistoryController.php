<?php

namespace controllers;
use core\Controller;
use core\Session;
use models\Notification;
use models\Order;

class OrderHistoryController extends Controller
{
  public function index()
  {
    $userId = Session::get('user')['id'];
    if (Session::get('user')['isVendor']) {
      $orders = Order::findAllNotCancelled();
    } else {
      $orders = Order::findByClientId($userId);
    }
    $enrichedOrders = [];
    foreach ($orders as $order) {
      $orderArray = $order->toArray(true);
      $enrichedOrders[$order->id] = $orderArray;
    }
    $this
      ->setData('title', 'Storico Ordini')
      ->setData('isVendor', Session::get('user')['isVendor'])
      ->setData('orders', $enrichedOrders)
      ->render('storico-ordini');
  }

  public function updateStatus()
  {
    $body = $this->request->getBody();
    $orderId = $body['orderId'] ?? null;
    $newStatus = $body['status'] ?? null;
    $order = Order::findById($orderId);
    if (!$order) {
      $this->renderJson([
        'success' => false,
        'message' => 'Ordine non trovato.']);
      return;
    }
    if ($order->updateStatus($newStatus)) {
      Notification::notifyOrderStatusUpdate($order->client_id, $orderId, $newStatus);
      $this->renderJson([
        'success' => true,
        'message' => 'Stato dell\'ordine aggiornato con successo.',
        'orderId' => $orderId,
        'newStatus' => $orderId,
      ]);
    } else {
      $this->renderJson([
        'success' => false,
        'message' => 'Errore durante l\'aggiornamento dello stato dell\'ordine.'
      ], 500);
    }
  }
}