<?php

namespace core;

class Request
{
  private $params = [];
  private $body = null;

  public function __construct()
  {
    $this->params = array_merge($_GET, $_POST);
  }

  /**
   * Ottiene il percorso URL della richiesta
   */
  public function getPath()
  {
    return $_SERVER['REQUEST_URI'];
  }

  /**
   * Ottiene il metodo HTTP della richiesta
   */
  public function getMethod()
  {
    return $_SERVER['REQUEST_METHOD'];
  }

  /**
   * Ottiene un valore di un parametro della richiesta
   */
  public function getParam($name, $default = null)
  {
    return isset($this->params[$name]) ? $this->params[$name] : $default;
  }

  /**
   * Imposta un parametro della richiesta
   */
  public function setParam($name, $value)
  {
    $this->params[$name] = $value;
    return $this;
  }

  /**
   * Ottiene il corpo della richiesta come array associativo
   * Supporta JSON e form-data
   */
  public function getBody(): array
  {
    if ($this->body !== null) {
      return $this->body;
    }

    if ($this->getMethod() === 'GET') {
      return $this->body = [];
    }

    $input = file_get_contents('php://input');
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (stripos($contentType, 'application/json') !== false) {
      $this->body = json_decode($input, true) ?? [];
    } elseif (!empty($_POST)) {
      $this->body = $_POST;
    } else {
      parse_str($input, $this->body);
    }

    return $this->body;
  }

  /**
   * Verifica se la richiesta è una chiamata AJAX
   */
  public function isAjax(): bool
  {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
  }
}

