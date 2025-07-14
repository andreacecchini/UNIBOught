<?php

namespace controllers;
use core\Controller;
use core\Session;
use models\Product;

class ProductDetailController extends Controller
{
  public function index(): void
  {
    $isVendor = Session::get('user')['isVendor'] ?? false;
    $productId = $this->request->getParam("id");
    $product = $isVendor ? Product::findById($productId) : Product::findByIdValid($productId);
    
    if (!$product) {
      header(header: "Location: /404");
      exit();
    }

    $months = [
      1 => 'Gennaio',
      2 => 'Febbraio',
      3 => 'Marzo',
      4 => 'Aprile',
      5 => 'Maggio',
      6 => 'Giugno',
      7 => 'Luglio',
      8 => 'Agosto',
      9 => 'Settembre',
      10 => 'Ottobre',
      11 => 'Novembre',
      12 => 'Dicembre'
    ];

    $product = $product->toArray();
    $dataSpedizione = strtotime('+7 days');
    $day = date('j', $dataSpedizione);
    $month = $months[date('n', $dataSpedizione)];
    $year = date('Y', $dataSpedizione);
    $dataSpedizione = "$day $month $year";
    $this->setData("title", "Dettaglio prodotto")
      ->setData("isVendor", $isVendor)
      ->setData("isRemoved", value: $product['valid'] === 0)
      ->setData("id", $productId)
      ->setData("product", $product)
      ->setData("dataSpedizione", $dataSpedizione)
      ->setLayout("main")
      ->render("dettaglio-prodotto");
  }
}