<?php
namespace core;

abstract class Controller
{
  protected $request;

  protected $response;

  private $layout = 'main';

  private $viewData = [];

  public function __construct(Request $request, Response $response)
  {
    $this->request = $request;
    $this->response = $response;
  }

  /**
   * Imposta il layout da utilizzare per la view
   */
  public function setLayout($layout)
  {
    $this->layout = $layout;
    return $this;
  }

  /**
   * Imposta un singolo dato da passare alla view
   * @return self
   */
  public function setData($name, $value)
  {
    $this->viewData[$name] = $value;
    return $this;
  }

  /**
   * Imposta più dati contemporaneamente da passare alla view
   */
  public function setViewData(array $data)
  {
    $this->viewData = array_merge($this->viewData, $data);
    return $this;
  }

  /**
   * Ottiene i dati inviati con la richiesta
   */
  public function getRequestData($param)
  {
    return $this->request->getParam($param);
  }

  /**
   * Renderizza una view con il layout e i dati già impostati
   */
  public function render($viewPath)
  {
    $view = new View($viewPath, $this->layout, $this->viewData);
    $content = $view->render();
    $this->response->setContent($content);
  }

  /**
   * Restituisce una risposta JSON
   */
  public function renderJson($data, $statusCode = 200)
  {
    $encoded = json_encode($data);
    $this->response->setHeader('Content-Type: application/json');
    if ($encoded === false) {
      $this->response->setStatusCode(500);
      $this->response->setContent(json_encode(["error" => "Errore durante la codifica JSON dei dati."]));
    } else {
      $this->response->setStatusCode($statusCode);
      $this->response->setContent($encoded);
    }
  }
}