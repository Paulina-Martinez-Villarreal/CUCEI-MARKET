let productoID = null;
let vendedorID = null;

function abrirChat(producto_id, vendedor_id) {
    productoID = producto_id;
    vendedorID = vendedor_id;
    console.log("Chat abierto con producto ID:", productoID, "y vendedor ID:", vendedorID);
}

function enviarMensaje() {
    var mensaje = document.getElementById("mensaje").value;

    if (!productoID || !vendedorID) {
        alert("Error: No se ha seleccionado un producto o vendedor.");
        return;
    }

    fetch("enviar_mensaje.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `mensaje=${encodeURIComponent(mensaje)}&producto_id=${productoID}&destinatario_id=${vendedorID}`
    })
    .then(response => response.text())
    .then(data => {
        console.log("Respuesta del servidor:", data);
        document.getElementById("mensaje").value = ""; // Limpiar el input
        alert("Mensaje enviado correctamente.");
    })
    .catch(error => console.error("Error al enviar mensaje:", error));
}



setInterval(() => {
    fetch("verificar_mensajes.php")
        .then(response => response.text())
        .then(data => {
            if (data > 0) {
                alert("Tienes mensajes nuevos.");
            }
        });
}, 5000); 
