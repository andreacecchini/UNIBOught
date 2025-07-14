<?php

namespace controllers;
use core\Controller;
use core\Session;
use models\Client;

class LogoutController extends Controller
{

  public function index()
  {
    Session::logout(true);
    header("Location: /");
    exit();
  }
}