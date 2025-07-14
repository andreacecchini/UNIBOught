<?php
namespace core;

use Exception;

class View
{
  private $view;

  private $data;

  private $layout;

  public function __construct($view, $layout, $data = [])
  {
    $this->data = $data;
    $this->view = $view;
    $this->layout = $layout;
  }

  /**
   * Renderizza la view con i dati già associati
   */
  public function render()
  {
    // Mappa $this->data['field'] => $field
    extract($this->data);
    // Si apre il buffer di output per catturare il contenuto del file HTML
    ob_start();
    $viewFile = $this->getViewPath($this->view);
    if (file_exists($viewFile)) {
      require $viewFile;
    } else {
      throw new Exception("View file not found: $viewFile");
    }
    // Si chiude il buffer di output e si cattura il contenuto del file HTML
    $content = ob_get_clean();
    if ($this->layout === false) {
      return $content;
    }
    return $this->renderWithLayout($content);
  }

  /**
   * Renderizza la view all'interno del layout
   */
  protected function renderWithLayout($content)
  {
    $this->data['content'] = $content;
    extract($this->data);
    ob_start();
    $layoutFile = $this->getLayoutPath($this->layout);
    if (file_exists($layoutFile)) {
      require $layoutFile;
    } else {
      throw new Exception("Layout file not found: $layoutFile");
    }
    return ob_get_clean();
  }

  /**
   * Ottiene il percorso completo del file della view
   */
  protected function getViewPath($view)
  {
    return SOURCE_DIR . '/views/pages/' . $view . '.php';
  }

  /**
   * Ottiene il percorso completo del file del layout
   */
  protected function getLayoutPath($layout)
  {
    return SOURCE_DIR . '/views/layouts/' . $layout . '.php';
  }

}