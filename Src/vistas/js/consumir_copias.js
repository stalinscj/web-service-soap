function procesarRespuesta(respuesta) {
    inicio = respuesta.indexOf("error")
    fin = respuesta.lastIndexOf("error")
    error = respuesta.substring(inicio+9, fin-5);

    inicio = respuesta.indexOf("msj")
    fin = respuesta.lastIndexOf("msj")
    msj = respuesta.substring(inicio+7, fin-5);

    inicio = respuesta.indexOf("cedula")
    fin = respuesta.lastIndexOf("cedula")
    cedula = respuesta.substring(inicio+10, fin-5);

    inicio = respuesta.indexOf("nombre")
    fin = respuesta.lastIndexOf("nombre")
    nombre = respuesta.substring(inicio+10, fin-5);

    inicio = respuesta.indexOf("apellido")
    fin = respuesta.lastIndexOf("apellido")
    apellido = respuesta.substring(inicio+12, fin-5);

    inicio = respuesta.indexOf("copias")
    fin = respuesta.lastIndexOf("copias")
    copias = respuesta.substring(inicio+10, fin-5);

    inicio = respuesta.indexOf("sede")
    fin = respuesta.lastIndexOf("sede")
    sede = respuesta.substring(inicio+8, fin-5);
    
    if(error==1 || msj.length>1)
    	alert("Error: "+msj);
    else{
        alert("Al estudiante: "+cedula+" le quedan "+copias+" copias.");
        limpiarCampos();
    }

}

function enviarPeticion() {
    $.ajax({
        url: "http://192.168.0.100:3000/uneg",
        type: "POST",
        crossDomain: true,
        dataType: 'html',
        data:{
            metodo : "consumirCopias",
            cedula : document.getElementById("txt_cedula").value,
            nombre : 0,
            apellido: 0,
            copias : document.getElementById("txt_copias").value,
            clave: document.getElementById("txt_clave").value,
            sede : 0
        }
    
    }).done( function(respuesta){
            procesarRespuesta(respuesta);
        }
    );
}

function limpiarCampos () {
    document.getElementById("txt_cedula").value = "";
    document.getElementById("txt_copias").value = "";
    document.getElementById("txt_clave").value =   "";
}

window.onload = function() {
	document.getElementById("btn_consumir").onclick = enviarPeticion;
    document.getElementById("btn_limpiar").onclick = limpiarCampos;
}
