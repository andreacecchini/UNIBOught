<?php

namespace controllers;
use core\Controller;
use core\Session;
use models\Product;

class HomeController extends Controller
{
  public function index(): void
  {
    $isVendor = Session::get('user')['isVendor'] ?? false;
    $context = $isVendor ? "vendor" : "list";

    $filters = [
      'nomeProdotto' => $this->request->getParam('nomeProdotto'),
      'categoria' => $this->request->getParam('categoria'),
      'prezzoMax' => $this->request->getParam('prezzoMax'),
    ];

    $products = array_filter(
      array_map(fn($p) => $p->toArray(), Product::findAllValid()),
      fn($product) => $this->applyFilters($product, $filters)
    );

    $removedProducts = [];
    if ($isVendor) {
      $removedProducts = array_filter(
        array_map(fn($p) => $p->toArray(), Product::findAllRemoved()),
        fn($product) => $this->applyFilters($product, $filters)
      );
    }

    $this->setViewData(["title" => "Home", "products" => $products, "removedProducts" => $removedProducts, 'context' => $context])
      ->setLayout("main")
      ->render("home");
  }

  private function applyFilters(array $product, array $filters): bool
  {
    if (!empty($filters['nomeProdotto']) && stripos($product['name'], $filters['nomeProdotto']) === false) {
      return false;
    }
    if (!empty($filters['categoria']) && !$this->matchesCategory($product['categories'], $filters['categoria'])) {
      return false;
    }
    if ($filters['prezzoMax'] !== null && $product['price'] > $filters['prezzoMax']) {
      return false;
    }
    return true;
  }

  private function matchesCategory(array $categories, string $categoryId): bool
  {
    foreach ($categories as $category) {
      if (is_array($category) && isset($category['id']) && $category['id'] == $categoryId) {
        return true;
      }
    }
    return false;
  }
}
