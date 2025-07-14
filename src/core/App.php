<?php

namespace core;


class App
{
  private $router;
  private $request;
  private $response;

  public function __construct()
  {
    // Avvia la sessione all'inizio dell'applicazione
    Session::start();

    $this->request = new Request();
    $this->response = new Response();
    $this->router = new Router($this->response, $this->request);
  }

  public function run()
  {
    $this->router->dispatch();
    $this->response->send();
  }
}