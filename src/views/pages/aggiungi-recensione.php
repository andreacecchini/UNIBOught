<?php
use \models\Client;
use \core\Session;
use \models\Review;

$product_id = isset($_GET['product_id']) ? htmlspecialchars($_GET['product_id']) : null;
$client = Session::isLoggedIn() ? Client::findById(Session::get('user')['id']) : null;
if ($product_id && $client && $client->hasReviewed($product_id)) {
    $review = Review::findByClientAndProductId($client->id, $product_id)->toArray();
}
$reviewTitle = $review['title'] ?? '';
$reviewText = $review['content'] ?? '';
$reviewRating = $review['rating'] ?? 0;
?>

<div class="container">
    <h1 class="my-3">Aggiungi recensione</h1>

    <form id="reviewForm" method="post" action="/aggiungi-recensione">
        <div class="mb-3">
            <label for="titolo" class="form-label">Titolo</label>
            <input type="text" class="form-control" id="titolo" name="titolo" placeholder="Inserisci un titolo"
                value="<?php echo htmlspecialchars($reviewTitle); ?>" required />
        </div>

        <div class="mb-3">
            <label for="recensione" class="form-label">Recensione</label>
            <textarea class="form-control" id="recensione" name="recensione" maxlength="256"
                placeholder="Scrivi la tua recensione..."><?php echo htmlspecialchars($reviewText); ?></textarea>
            <div class="text-end"><small id="charCount"><?php echo strlen($reviewText); ?>/256</small></div>
        </div>

        <div class="mb-3 d-flex flex-column flex-sm-row justify-content-between">
            <p>Aggiungi una valutazione!</p>
            <div class="star-rating" id="starRating">
                <span class="bi <?php echo $i <= $reviewRating ? 'bi-star-fill' : 'bi-star'; ?> fs-2"
                    data-index="<?php echo $i; ?>" tabindex="0"></span>
                <?php for ($i = 2; $i <= 5; $i++): ?>
                    <span class="bi <?php echo $i <= $reviewRating ? 'bi-star-fill' : 'bi-star'; ?> fs-2"
                        data-index="<?php echo $i; ?>" tabindex="0"></span>
                <?php endfor; ?>
            </div>

            <input type="hidden" name="valutazione" id="valutazione" value="<?php echo $reviewRating; ?>" required
                min="1" />
        </div>

        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
        <button type="submit" class="btn btn-primary w-100">Invia recensione</button>
    </form>
</div>

<script src="/js/review-star-rating-handler.js"></script>