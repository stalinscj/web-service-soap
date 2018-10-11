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
    
    if(error!=0)
    	alert("Error: "+msj);
    else{
		document.getElementById("txt_cedula").value = cedula;
		document.getElementById("txt_nombre").value = nombre;
		document.getElementById("txt_apellido").value = apellido;
		document.getElementById("txt_copias").value = copias;
		document.getElementById("txt_sede").value =   sede;
        document.getElementById("txt_cedula_buscar").value ="";
    }

}

function enviarPeticion() {
    $.ajax({
        url: "http://192.168.0.100:3000/uneg",
        type: "POST",
        crossDomain: true,
        dataType: 'html',
        data:{
            cedula : document.getElementById("txt_cedula_buscar").value,
            metodo : "buscarEstudiante",
            nombre : 0,
            apellido: 0,
            copias : 0,
            clave: 0,
            sede : 0 
        }
    
    }).done( function(respuesta){
            procesarRespuesta(respuesta);
        }
    );
}

function limpiarCampos () {
    document.getElementById("txt_cedula").value = "";
    document.getElementById("txt_nombre").value = "";
    document.getElementById("txt_apellido").value = "";
    document.getElementById("txt_copias").value = "";
    document.getElementById("txt_sede").value =   "";
    document.getElementById("txt_cedula_buscar").value ="";
}

window.onload = function() {
	document.getElementById("btn_buscar").onclick = enviarPeticion;
    document.getElementById("btn_limpiar").onclick = limpiarCampos;
}
