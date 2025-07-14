<?php

namespace controllers;
use core\Controller;
use core\Database;
use core\Session;
use Exception;
use models\User;

class SignUpController extends Controller
{
  public function index()
  {
    $this
      ->setLayout('no-navbar')
      ->setViewData([
        'title' => 'Registrazione',
        'header' => 'Registrati',
      ])
      ->render('signup');
  }
  public function signup()
  {
    try {
      $body = $this->request->getBody();

      // Validazione input
      $requiredFields = ['name', 'surname', 'email', 'password'];

      foreach ($requiredFields as $field) {
        if (empty($body[$field])) {
          throw new Exception("Il campo '$field' è obbligatorio");
        }
      }

      if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Email non valida");
      }

      if (strlen($body['password']) < 8) {
        throw new Exception("La password deve essere di almeno 8 caratteri");
      }

      if ($this->emailExists($body['email'])) {
        throw new Exception(message: "Email già registrata, usane un'altra.");
      }

      $newUser = User::create(
        $body['name'],
        $body['surname'],
        $body['email'],
        $body['password'],
        $body['number']
      );

      $this->saveUser($newUser);

      Session::setFlash('success_message', 'Registrazione completata con successo!', 'success');
      header("Location: /login");
      exit;

    } catch (Exception $e) {
      error_log("Errore durante la registrazione: " . $e->getMessage());

      Session::setFlash('error', $e->getMessage(), 'error');

      $this
        ->setLayout('no-navbar')
        ->setViewData([
          'title' => 'Registrazione',
          'header' => 'Registrati'
        ])
        ->render('signup');
    }
  }

  private function emailExists($email)
  {
    $db = Database::getIstance();
    $sql = "SELECT COUNT(id) AS count FROM users WHERE email = ?";
    $results = $db->query($sql, [$email], "s");
    return $results[0]['count'] > 0;
  }


  private function saveUser($user)
  {
    $db = Database::getIstance();
    $db->transaction(function () use ($user, $db) {
      if (!$user->save()) {
        throw new Exception("Errore durante la creazione dell'utente");
      }
      $sql = "INSERT INTO clients (user_id) VALUES (?)";
      $params = [$user->id];
      if (!$db->query($sql, $params, "i")) {
        throw new Exception("Errore durante l'inserimento del cliente");
      }
    });
  }

}