// Función principal para cargar productos
async function loadProducts() {
  const statusElement = document.getElementById('loading-status');
  statusElement.textContent = 'Cargando listado de productos...';
  statusElement.className = 'loading';
  
  try {
    const productos = await cargarExcel();
    const productosConImagenes = await Promise.all(
      productos.map(async (producto) => {
        const imageUrl = `${CONFIG.imagesPath}${producto.sku.toLowerCase()}.jpg`;
        const imageExists = await checkImageExists(imageUrl);
        return {
          ...producto,
          imagen: imageExists ? imageUrl : CONFIG.defaultImage
        };
      })
    );
    
    generarTablaProductos(productosConImagenes);
    statusElement.textContent = `Listado actualizado (${productos.length} productos) - ${new Date().toLocaleTimeString()}`;
  } catch (error) {
    console.error('Error al cargar productos:', error);
    statusElement.textContent = `Error al cargar productos: ${error.message}`;
    statusElement.className = 'error';
    document.querySelector('#product-table tbody').innerHTML = '<tr><td colspan="6">No se pudieron cargar los productos</td></tr>';
  }
}

// Función para cargar y procesar el Excel
async function cargarExcel() {
  const response = await fetch(CONFIG.excelFile);
  if (!response.ok) throw new Error('No se pudo cargar el archivo Excel');
  
  const arrayBuffer = await response.arrayBuffer();
  const data = new Uint8Array(arrayBuffer);
  const workbook = XLSX.read(data, { type: 'array' });
  
  const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
  const jsonData = XLSX.utils.sheet_to_json(firstSheet);
  
  return jsonData.map(item => {
    if (!item.SKU) throw new Error('El archivo Excel no tiene columna SKU');
    if (!item.NOMBRE) throw new Error('El archivo Excel no tiene columna NOMBRE');
    if (!item.PRECIO) throw new Error('El archivo Excel no tiene columna PRECIO');
    
    return {
      sku: item.SKU.toString().trim(),
      nombre: item.NOMBRE.toString().trim(),
      precio: parseFloat(item.PRECIO) || 0,
      imagen: ''
    };
  });
}

// Función para manejar la tecla Enter
function handleEnterKey(event) {
  if (event.key === 'Enter') {
    event.preventDefault();
    updateSubtotal(event.target);
    return false;
  }
  return true;
}

// Función para generar la tabla de productos
function generarTablaProductos(productos) {
  const tbody = document.querySelector('#product-table tbody');
  tbody.innerHTML = '';
  
  if (productos.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6">No hay productos disponibles</td></tr>';
    return;
  }
  
  productos.forEach(producto => {
    const row = document.createElement('tr');
    row.dataset.sku = producto.sku;
    row.dataset.price = producto.precio;
    
    row.innerHTML = `
      <td>${producto.sku}</td>
      <td>
        <div class="img-container">
          <img src="${producto.imagen}" alt="${producto.nombre}" 
               onclick="expandImage(this)">
        </div>
      </td>
      <td>${producto.nombre}</td>
      <td>
        <div class="qty-buttons">
          <button type="button" onclick="changeQty(this, -1)">-</button>
          <input type="number" value="0" min="0" onchange="updateSubtotal(this)" onkeydown="return handleEnterKey(event)">
          <button type="button" onclick="changeQty(this, 1)">+</button>
        </div>
      </td>
      <td class="price">$${producto.precio.toLocaleString('es-AR', {minimumFractionDigits: 2})}</td>
      <td class="subtotal">$0</td>
    `;
    
    tbody.appendChild(row);
  });
  
  updateGrandTotal();
}

// Funciones para manejar cantidades
function changeQty(btn, delta) {
  const input = btn.parentElement.querySelector('input');
  let value = parseInt(input.value) || 0;
  value += delta;
  if (value < 0) value = 0;
  input.value = value;
  updateSubtotal(input);
  
  // Evitar que se active el teclado en móviles
  input.blur();
}

function updateSubtotal(input) {
  const row = input.closest('tr');
  const price = parseFloat(row.dataset.price) || 0;
  const qty = parseInt(input.value) || 0;
  const subtotal = price * qty;
  row.querySelector('.subtotal').textContent = `$${subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
  updateGrandTotal();
}

function updateGrandTotal() {
  const rows = document.querySelectorAll('#product-table tbody tr');
  let total = 0;
  rows.forEach(row => {
    const qty = parseInt(row.querySelector('input').value) || 0;
    const price = parseFloat(row.dataset.price) || 0;
    total += qty * price;
  });
  
  const formattedTotal = `$${total.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
  document.getElementById('grand-total').textContent = formattedTotal;
  document.getElementById('mobile-grand-total').textContent = formattedTotal;
  

  // Habilitar/deshabilitar botones según el mínimo de compra
  const desktopButton = document.getElementById('desktop-submit-button');
  const mobileButton = document.getElementById('mobile-submit-button');
  const minOrderMessage = document.getElementById('min-order-message');
  
  if (total >= CONFIG.minOrderAmount) {
    desktopButton.disabled = false;
    mobileButton.disabled = false;
    minOrderMessage.style.display = 'none';
  } else {
    desktopButton.disabled = true;
    mobileButton.disabled = true;
    minOrderMessage.style.display = 'block';
  }
}