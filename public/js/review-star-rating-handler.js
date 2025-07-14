document.addEventListener('DOMContentLoaded', () => {
    const textarea = document.getElementById('recensione');
    const charCount = document.getElementById('charCount');
    const stars = document.querySelectorAll('#starRating span');
    const ratingInput = document.getElementById('valutazione');
    ratingInput.value = '1'; 

    textarea.addEventListener('input', () => {
        charCount.textContent = `${textarea.value.length}/256`;
    });

    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            updateRating(index + 1);
        });

        star.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                updateRating(index + 1);
            }
        });
    });

    function updateRating(rating) {
        ratingInput.value = rating;

        stars.forEach((s, i) => {
            s.classList.toggle('bi-star-fill', i < rating);
            s.classList.toggle('bi-star', i >= rating);
        });
    }
});