<?php

namespace controllers;
use core\Controller;
use core\Session;
use models\Client;
use models\Vendor;

class LoginController extends Controller
{

  public function indexClient()
  {
    $this
      ->setLayout('no-navbar')
      ->setViewData([
        'title' => 'Accedi come cliente',
        'header' => 'Login',
        'route' => '/login',
        'isVendor' => false
      ])
      ->render('login');
  }

  public function indexVendor()
  {
    $this
      ->setLayout('no-navbar')
      ->setViewData([
        'title' => 'Accedi come venditore',
        'header' => 'Login',
        'route' => '/login-vendor',
        'isVendor' => true
      ])
      ->render('login');
  }

  public function loginAsClient()
  {
    $client = Client::authenticate($this->request->getParam('email'), $this->request->getParam('password'));
    if ($client) {
      Session::regenerate(deleteOldSession: true);
      Session::setUser(userData: ['id' => $client->id, 'name' => $client->name, 'surname' => $client->surname, 'email' => $client->email, 'telephone' => $client->telephone_number, 'isVendor' => false]);
      // Recupera l'URL a cui reindirizzare se presente, altrimenti porta alla home
      $redirectUrl = Session::get('redirect_url', '/');
      Session::remove('redirect_url');
      // Merge tra il carrello in sessione e quello nel database
      if (Session::has(key: 'cart')) {
        $client->mergeCartItems(Session::get('cart'));
        Session::remove('cart');
      }
      header(header: "Location: $redirectUrl");
      exit;
    } else {
      Session::setFlash('credenziali_errate', 'Credenziali errate. Riprova.', 'error');
      header("Location: /login");
      exit;
    }
  }

  public function loginAsVendor()
  {
    $vendor = Vendor::authenticate($this->request->getParam('email'), $this->request->getParam('password'));
    if ($vendor) {
      Session::regenerate(deleteOldSession: true);
      Session::setUser(userData: ['id' => $vendor->id, 'name' => $vendor->name, 'surname' => $vendor->surname, 'email' => $vendor->email, 'telephone' => $vendor->telephone_number, 'isVendor' => true]);
      // Recupera l'URL a cui reindirizzare se presente, altrimenti porta alla home
      $redirectUrl = Session::get('redirect_url', '/dashboard-venditore');
      // Rimuovi l'URL di reindirizzamento dalla sessione
      Session::remove('redirect_url');
      header(header: "Location: $redirectUrl");
      exit;
    } else {
      Session::setFlash('error', 'Credenziali errate. Riprova.', 'error');
      header("Location: /login-vendor");
      exit;
    }
  }
}