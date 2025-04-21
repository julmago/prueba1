// Elementos del modal
const modal = document.getElementById("imageModal");
const modalImg = document.getElementById("expandedImg");
const captionText = document.getElementById("caption");
const span = document.getElementsByClassName("close")[0];

// Cerrar modal
span.onclick = function() {
  closeModal();
}

// Cerrar al hacer clic fuera
modal.onclick = function(event) {
  if (event.target === modal) {
    closeModal();
  }
}

// Cerrar con tecla ESC
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape' && modal.style.display === "block") {
    closeModal();
  }
});

// Cerrar con gesto táctil (swipe down)
let touchStartY = 0;
modal.addEventListener('touchstart', function(e) {
  touchStartY = e.touches[0].clientY;
}, {passive: true});

modal.addEventListener('touchmove', function(e) {
  const touchY = e.touches[0].clientY;
  const diff = touchY - touchStartY;
  
  if (diff > 50) { // Swipe hacia abajo
    closeModal();
    e.preventDefault();
  }
}, {passive: false});

function closeModal() {
  modal.classList.remove("show");
  setTimeout(() => {
    modal.style.display = "none";
  }, 300);
}

// Mostrar imagen ampliada con efecto
function expandImage(imgElement) {
  modal.style.display = "block";
  setTimeout(() => {
    modal.classList.add("show");
  }, 10);
  
  const sku = imgElement.closest('tr').dataset.sku;
  const largeImageUrl = `${CONFIG.largeImagesPath}${sku.toLowerCase()}.jpg`;
  
  // Mostrar imagen pequeña primero
  modalImg.src = imgElement.src;
  captionText.innerHTML = imgElement.alt;
  
  // Intentar cargar imagen grande
  const tempImg = new Image();
  tempImg.onload = function() {
    modalImg.src = this.src;
  };
  tempImg.onerror = function() {
    // Si falla la imagen grande, mantener la pequeña
  };
  tempImg.src = largeImageUrl;
}

// Función para verificar si una imagen existe
async function checkImageExists(imageUrl) {
  return new Promise((resolve) => {
    const img = new Image();
    img.onload = () => resolve(true);
    img.onerror = () => resolve(false);
    img.src = imageUrl;
  });
}