<?php

namespace core;

class Response
{
  private $statusCode = 200;

  private $headers = [];

  private $content = "";

  /**
   * Ottiene il codice di stato HTTP corrente
   */
  public function getStatusCode()
  {
    return $this->statusCode;
  }

  /**
   * Imposta il codice di stato HTTP
   */
  public function setStatusCode($statusCode)
  {
    $this->statusCode = $statusCode;
    return $this;
  }


  /**
   * Imposta un'intestazione HTTP
   */
  public function setHeader($header)
  {
    $this->headers[] = $header;
    return $this;
  }

  /**
   * Imposta il contenuto della risposta
   */
  public function setContent($content)
  {
    $this->content = $content;
    return $this;
  }

  /**
   * Invia la risposta al client
   */
  public function send()
  {
    // Imposta il codice di stato HTTP
    http_response_code($this->statusCode);
    // Invia tutte le intestazioni
    foreach ($this->headers as $header) {
      header($header);
    }
    // Invia il contenuto
    echo $this->content;
  }
}