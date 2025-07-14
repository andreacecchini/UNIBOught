<?php

namespace controllers;
use core\Controller;
use core\Session;
use models\Notification;
use models\Review;
use core\Request;
use core\Response;

class AddReviewController extends Controller
{

  public function index()
  {
    $this
      ->setData('title', 'Aggiungi recensione')
      ->setLayout('main')
      ->render('aggiungi-recensione');
  }

  public function addReview()
  {
    $clientId = Session::get('user')['id'];
    $body = $this->request->getBody();

    $productId = $body['product_id'];
    $rating = $body['valutazione'];
    $title = $body['titolo'];
    $content = $body['recensione'];

    $review = Review::findByClientAndProductId($clientId, $productId) ?? null;
    if ($review) {
      $review->title = $title;
      $review->content = $content;
      $review->rating = (int) $rating;
      Notification::notifyProductReviewEdited($clientId, $productId);
    } else {
      $review = Review::create(
        clientId: (int) $clientId,
        productId: $productId,
        title: $title,
        content: $content,
        rating: (int) $rating
      );
      Notification::notifyProductReviewAdded($clientId, $productId);
    }

    $review->save();
    header("Location: /dettaglio-prodotto/" . $productId);
    exit;
  }
}