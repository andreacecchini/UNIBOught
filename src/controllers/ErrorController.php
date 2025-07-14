<?php

namespace controllers;
use core\Controller;

class ErrorController extends Controller
{
  public function error404()
  {
    $this
      ->setLayout(false)
      ->render('404');
  }

  public function error403()
  {
    $this
      ->setLayout(false)
      ->render('403');
  }
}