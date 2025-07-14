<?php

namespace controllers;
use core\Controller;
use core\Session;
use models\Notification;
use models\User;

class UserProfileController extends Controller
{
  public function index()
  {
    $user = User::findById(Session::get('user')['id'])->toArray();
    $this
      ->setData('title', 'User profile')
      ->setData('user', $user)
      ->render('user-profile');
  }
  public function updateProfile()
  {
    $body = $this->request->getBody();
    $user = User::findById(Session::get('user')['id']);
    // Aggiorna i dati del profilo
    $user->name = $body['name'];
    $user->surname = $body['surname'];
    $user->email = $body['email'];
    $user->telephone_number = $body['telephone'];

    // Gestisce il cambio password se richiesto
    if ($this->changePasswordRequested($body['oldPassword'], $body['newPassword'])) {
      if (!$user->verifyPassword($body['oldPassword'])) {
        Session::setFlash('password-errata', 'La password attuale non è corretta.', 'error');
        header("Location: /user-profile");
        exit;
      }
      $user->updatePassword(User::hashPassword($body['newPassword']));
      $user->save();
      Session::logout();
      header('Location: /login');
      exit;
    }

    // Salva le modifiche e reindirizza
    if ($user->save()) {
      // Aggiorna i dati della sessione
      Session::setUser($user->toArray());
      Session::setFlash('modifica-profilo-effettuata', 'Modifica del profilo effettuata con successo.', 'success');
    }
    header('Location: /user-profile');
    exit;
  }

  private function changePasswordRequested($old, $new)
  {
    return !empty($old) && !empty($new);
  }

}