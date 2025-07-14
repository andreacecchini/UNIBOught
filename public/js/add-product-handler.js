document.addEventListener('DOMContentLoaded', () => {
  const imageSection = document.getElementById('imageUpload');
  const imageUpload = imageSection.querySelector('figure');
  const fileInput = document.getElementById('fileInput');
  const description = document.getElementById("description");
  const charCount = document.getElementById("charCount");
  const MAX_FILE_SIZE = 5 * 1024 * 1024;
  const ALLOWED_FILE_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

  description.addEventListener("input", () => {
    charCount.textContent = `${description.value.length}/256`;
  });

  fileInput.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
      if (!ALLOWED_FILE_TYPES.includes(file.type)) {
        alert('Formato file non supportato. Sono consentiti solo JPEG, PNG, WebP e GIF.');
        fileInput.value = '';
        imageSection.classList.add('visually-hidden');
        imageUpload.innerHTML = '';
        return;
      }
      if (file.size > MAX_FILE_SIZE) {
        alert('Il file caricato è troppo grande. La dimensione massima consentità è di 5MB.')
        fileInput.value = '';
        imageSection.classList.add('visually-hidden');
        imageUpload.innerHTML = '';
        return;
      }
      const imageSrc = URL.createObjectURL(file);
      showImagePreview(imageSrc, file.name, file.size);
    }
  });

  function showImagePreview(imageSrc, fileName, fileSize) {
    const fileSizeMB = (fileSize / 1024 / 1024).toFixed(2);
    const displayFileName = fileName.length > 20 ? fileName.substring(0, 20) + '...' : fileName;
    imageSection.classList.remove('visually-hidden');
    imageUpload.innerHTML = `
      <img src="${imageSrc}" alt="Anteprima dell'immagine caricata" class="rounded mb-2 shadow" style="max-height: 200px; object-fit: contain;">
      <figcaption class="text-center text-muted">Anteprima di: ${displayFileName} (${fileSizeMB}MB)</figcaption>
    `;
  }
});