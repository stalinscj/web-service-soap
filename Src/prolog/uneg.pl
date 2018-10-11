:- module(uneg,[ buscar_estudiante/2, insertar_estudiante/7, actualizar_estudiante/7, renovar_copias/3, consumir_copias/4 ]).

:- set_prolog_flag(double_quotes, chars).


:- use_module(library(wsdl)).
:- use_module(library(xpath)).
:- use_module(library(soap)).

:- use_module(library(http/thread_httpd)).
:- use_module(library(http/http_dispatch)).
:- use_module(library(http/http_parameters)).
:- use_module(library(http/html_write)).
:- use_module(library(http/http_header)).

:- http_handler(root(uneg), uneg_servicios, []).

:- http_server(http_dispatch, [port(3000)]).

:- debug(soap).

:- multifile sgml_write:xmlns/2.

sgml_write:xmlns(uneg, 'http://192.168.0.100').    %Debe quedarse con la ip donde corre prolog


:- initialization wsdl_read('uneg.wsdl').

uneg_servicios(Request):-
	http_parameters(
		Request,
		[
			metodo(Metodo, []),
			cedula(Cedula, []),
			nombre(Nombre, []),
			apellido(Apellido, []),
			copias(Copias, []),
			clave(Clave, []),
			sede(Sede, [])
        ]
    ),
    (  Metodo == 'buscarEstudiante' ->
    	buscar_estudiante(Cedula, Reply),
    	reply_html_page(title('Respuesta'), [ Reply ])
    ;
    	(  Metodo == 'insertarEstudiante' ->
	    	insertar_estudiante(Cedula, Nombre, Apellido, Copias, Clave, Sede, Reply),
	    	reply_html_page(title('Respuesta'), [ h1(Reply) ])
	    ;
	    	(  Metodo == 'actualizarEstudiante' ->
		    	actualizar_estudiante(Cedula, Nombre, Apellido, Copias, Clave, Sede, Reply),
		    	reply_html_page(title('Respuesta'), [ h1(Reply) ])
		    ;
		    	(  Metodo == 'validarEstudiante' ->
			    	reply_html_page(title('Respuesta'), [ h1('Validar eliminado') ])
			    ;
			    	(  Metodo == 'renovarCopias' ->
				    	renovar_copias(Nombre, Clave, Reply),
				    	reply_html_page(title('Respuesta'), [ h1(Reply) ])
				    ;
				    	(  Metodo == 'consumirCopias' ->
					    	consumir_copias(Cedula, Clave, Copias, Reply),
					    	reply_html_page(title('Respuesta'), [ h1(Reply) ])
					    ;
					    	reply_html_page(title('Respuesta'), [ h1('Metodo no encontrado') ])
						)
					)
				)
			)
		)
	)
.

buscar_estudiante(Cedula, Reply) :-
	Operation = ('http://192.168.0.110':unegSoap) /
	            ('http://192.168.0.110':'buscarEstudiante'),
	soap_call(Operation,
		  [ 'cedula'=Cedula
		  ],
		  Reply)
.

insertar_estudiante(Cedula, Nombre, Apellido, Copias, Clave, Sede, Reply) :-
	Operation = ('http://192.168.0.110':unegSoap) /
	            ('http://192.168.0.110':'insertarEstudiante'),
	soap_call(Operation,
		  [ 'cedula'=Cedula,
		  	'nombre'=Nombre,
		  	'apellido'=Apellido,
		  	'copias'=Copias,
		  	'clave'=Clave,
		  	'sede' =Sede
		  ],
		  Reply)
.

actualizar_estudiante(Cedula, Nombre, Apellido, Copias, Clave, Sede, Reply) :-
	Operation = ('http://192.168.0.110':unegSoap) /
	            ('http://192.168.0.110':'actualizarEstudiante'),
	soap_call(Operation,
		  [ 'cedula'=Cedula,
		  	'nombre'=Nombre,
		  	'apellido'=Apellido,
		  	'copias'=Copias,
		  	'clave'=Clave,
		  	'sede' =Sede
		  ],
		  Reply)
.


renovar_copias(Nombre, Clave, Reply) :-
	Operation = ('http://192.168.0.110':unegSoap) /
	            ('http://192.168.0.110':'renovarCopias'),
	soap_call(Operation,
		  [
		  	'nombre'=Nombre,
		  	'clave'=Clave
		  ],
		  Reply)
.

consumir_copias(Cedula, Clave, Copias, Reply) :-
	Operation = ('http://192.168.0.110':unegSoap) /
	            ('http://192.168.0.110':'consumirCopias'),
	soap_call(Operation,
		  [ 'cedula'=Cedula,
		  	'clave' =Clave,
		  	'copias'=Copias
		  ],
		  Reply)
.





