function prepareFormData() {
  const rows = document.querySelectorAll('#product-table tbody tr');
  let body = 'Pedido HellPrix:\n\n';
  let total = 0;

  rows.forEach(row => {
    const code = row.children[0].textContent;
    const name = row.children[2].textContent;
    const price = parseFloat(row.dataset.price) || 0;
    const qty = parseInt(row.querySelector('input').value);
    const subtotal = price * qty;

    if (qty > 0) {
      body += `${code} - ${name} | Cantidad: ${qty} | Subtotal: $${subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}\n`;
      total += subtotal;
    }
  });

  if (total < CONFIG.minOrderAmount) {
    alert(`El pedido no alcanza el mínimo de compra requerido ($${CONFIG.minOrderAmount.toLocaleString('es-AR')}). Por favor, agrega más productos.`);
    return false;
  }

  // Validar campos obligatorios del formulario
  const requiredFields = document.querySelectorAll('input[required]');
  let isValid = true;
  
  requiredFields.forEach(field => {
    if (!field.value.trim()) {
      field.style.borderColor = 'var(--error-color)';
      field.scrollIntoView({ behavior: 'smooth', block: 'center' });
      isValid = false;
    } else {
      field.style.borderColor = 'var(--border-color)';
    }
  });
  
  if (!isValid) {
    alert('Por favor complete todos los campos obligatorios marcados con *.');
    return false;
  }

  body += `\nTOTAL GENERAL: $${total.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
  
  // Agregar datos del formulario al cuerpo del mensaje
  body += '\n\nDatos del Cliente:\n';
  body += `Nombre: ${document.querySelector('input[name="nombre"]').value}\n`;
  body += `Apellido: ${document.querySelector('input[name="apellido"]').value}\n`;
  body += `DNI/CUIT: ${document.querySelector('input[name="dni_cuit"]').value}\n`;
  body += `Teléfono: ${document.querySelector('input[name="telefono"]').value}\n`;
  body += `Email: ${document.querySelector('input[name="email"]').value}\n`;
  body += `País: ${document.querySelector('input[name="pais"]').value}\n`;
  body += `Provincia: ${document.querySelector('input[name="provincia"]').value}\n`;
  body += `Localidad: ${document.querySelector('input[name="localidad"]').value}\n`;
  body += `Código Postal: ${document.querySelector('input[name="codigo_postal"]').value}\n`;
  body += `Dirección: ${document.querySelector('input[name="direccion"]').value}\n`;
  body += `Referencia: ${document.querySelector('input[name="referencia"]').value}\n`;
  
  const empresa = document.querySelector('input[name="empresa"]').value;
  if (empresa) {
    body += `Empresa: ${empresa}\n`;
  }
  
  document.getElementById('pedido-data').value = body;
  return true;
}