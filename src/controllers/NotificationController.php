<?php

namespace controllers;
use core\Controller;
use core\Session;
use models\Notification;

class NotificationController extends Controller
{
  public function index()
  {
    $this
      ->setViewData(['title' => 'Notifiche'])
      ->render('notifiche');
  }

  public function fetchNotifications()
  {
    $notifications = array_map(fn($n) => $n->toArray(), Notification::findByUserId(Session::get('user')['id']));
    $this->renderJson($notifications);
  }

    public function markAsRead()
    {
      $notificationId = $this->request->getParam("id");
      $notification = Notification::findById($notificationId);
    
      if (!$notification) {
        $this->renderJson(['error' => 'Notifica non trovata'], 404);
        return;
      }
    
      $notification->markAsRead();
    
      $this->renderJson([
        'status' => 'success',
        'id' => $notificationId
      ]);
      
    }

  public function markAllAsRead()
  {
    Notification::markAllAsReadByUserId(Session::get('user')['id']);

    $this->renderJson([
      'status' => 'success',
      'message' => 'Tutte le notifiche sono state segnate come lette'
    ]);
  }

  public function deleteNotification() {
    $notificationId = $this->request->getParam("id");
    $notification = Notification::findById($notificationId);
    if (!$notification) {
      $this->renderJson(['error' => 'Notifica non trovata'], 404);
      return;
    }

    $notification->delete();
    
    $this->renderJson([
      'status' => 'success',
      'message' => 'Notifica eliminata con successo'
    ]);
  }

  public function getUnreadCount() {
    $unreadCount = Notification::countUnreadByUserId(Session::get('user')['id']);
    if ($unreadCount === 0) {
      $this->renderJson(['unreadCount' => 0]);
      return;
    }
    $n =  Notification::findByUserId(Session::get('user')['id'])[0];
    $message = $n->message;
    $reference = $n->reference; 
    $this->renderJson([
      'unreadCount' => $unreadCount,
      'message' => $message,
      'reference' => $reference 
    ]);
  }
}
