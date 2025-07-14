<?php
/**
 * Componente stelle recensione
 * 
 * Parametri:
 * @param float $n - Il numero di stelle da visualizzare (da 0 a 5)
 * @param bool $showText - Indica se mostrare il testo con il numero di stelle
 */
?>
<div class="row align-items-center">
    <div class="col-auto">
        <div class="row g-1">
            <?php
            $fullStars = floor($n);
            $hasHalfStar = $n - $fullStars > 0;
            $showText = isset($showText) ? $showText : false;

            for ($i = 1; $i <= 5; $i++): ?>
                <div class="col-auto">
                    <?php if ($i <= $fullStars): ?>
                        <span class="bi bi-star-fill"></span>
                    <?php elseif ($hasHalfStar && $i == $fullStars + 1): ?>
                        <span class="bi bi-star-half"></span>
                    <?php else: ?>
                        <span class="bi bi-star"></span>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <?php if ($showText): ?>
        <div class="col-auto">
            <p class="text-muted mb-0"><?= $n ?> stelle su 5</p>
        </div>
    <?php endif; ?>
</div>