#!/usr/bin/php -q
<?php
	
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	require_once "../modelos/DataBaseClass.php";
	require_once "../modelos/EstudianteDAO.php";
	require_once "../modelos/FotocopiaDAO.php";

	$dbA = new DataBase("atlantico");
	$estA = new EstudianteDAO($dbA);
	$dbU = new DataBase("upata");
	$estU = new EstudianteDAO($dbU);

	$msjs[]="Log:";

	if($dbA->getConexion()!=null && $dbU->getConexion()!=null){
		$sql="SELECT *
			  FROM pendientes";
		$listaPendientes = $dbA->consultar($sql);

		if($listaPendientes->num_rows>0){
			while ($filaPendiente = $listaPendientes->fetch_assoc()) {
				$id_pendiente = $filaPendiente["id_pendiente"];
				$id_copia = $filaPendiente["id_copia"];
				$accion = $filaPendiente["accion"];
				$cedula = $filaPendiente["cedula"];
				$nombre = $filaPendiente["nombre"];
				$apellido = $filaPendiente["apellido"];
				$copias = $filaPendiente["copias"];
				$clave = $filaPendiente["clave"];
				$sede = $filaPendiente["sede"];

				switch ($accion) {
					case 'insertar':{
						if($sede=="upata"){
							if($estA->buscar($cedula)->num_rows==0){
								$rs = $estU->insertar($cedula, $nombre, $apellido, $copias, $clave, $sede);
								if($rs!=null){
									$rs = $estA->borrarPendiente($id_pendiente);
									$msjs[]= "Estudiante insertado con exito";
								}else{
									$msjs[]= "error al insertar estudiante";
								}
							}else{
								$msjs[]= "El estudiante ya se encuentra registrado en la otra sede";
							}
						}else{
							if($sede=="atlantico"){
								if($estU->buscar($cedula)->num_rows==0){
									$rs = $estA->insertar($cedula, $nombre, $apellido, $copias, $clave, $sede);
									if($rs!=null){
										$rs = $estA->borrarPendiente($id_pendiente);
										$msjs[]= "Estudiante insertado con exito";
									}else{
										$msjs[]= "error al insertar estudiante";
									}
								}else{
									$msjs[]= "El estudiante ya se encuentra registrado en la otra sede";
								}
							}else{
								$msjs[]= "La se sede no existe";
							}
						}
					}break;

					case 'actualizar':{
						if($sede=="atlantico" || $sede=="upata"){

							if($sede=="upata"){
								$resultado = $estA->buscar($cedula);
								if($resultado->num_rows==0){
									$cambio=false;
								}else{
									$cambio=true;
									$resultado = $resultado->fetch_assoc();
		                    		$copias = $resultado["copias"];
								}

								if($cambio){
									$estA->borrar($cedula);
									$resultado = $estU->insertar($cedula, $nombre, $apellido, $copias, $clave, $sede);
									
									if($resultado!=null){
										$rs = $estA->borrarPendiente($id_pendiente);
										$error=0;
									}else{
										$msjs[]="Error al actualizar estudiante";
									}
								}else{
									$valido = $estU->validar($cedula, $clave);
									if($valido->num_rows==0){
										$msjs[]="Combinación de Cédula/Clave incorrecta.";
									}else{
										$rs = $estU->actualizar($cedula, $nombre, $apellido, $clave, $sede);
										
									  	if($rs){
									  		$rs = $estA->borrarPendiente($id_pendiente);
									  		$error=0;
										}else{
										   	$msjs[]= "No se pudo Actualizar el estudiante.";
										}
									}  
								}
							}else{
								$resultado = $estU->buscar($cedula);
								if($resultado->num_rows==0){
									$cambio=false;
								}else{
									$cambio=true;
									$resultado = $resultado->fetch_assoc();
		                    		$copias = $resultado["copias"];
								}

								if($cambio){
									$estU->borrar($cedula);
									$resultado = $estA->insertar($cedula, $nombre, $apellido, $copias, $clave, $sede);
									
									if($resultado!=null){
										$rs = $estA->borrarPendiente($id_pendiente);
										$error=0;
									}else{
										$msjs[]="Error al actualizar estudiante";
									}
								}else{
									$valido = $estA->validar($cedula, $clave);
									if($valido->num_rows==0){
										$msjs[]="Combinación de Cédula/Clave incorrecta.";
									}else{
										$rs = $estA->borrarPendiente($id_pendiente);
										$rs = $estA->actualizar($cedula, $nombre, $apellido, $clave, $sede);
										
									  	if($rs){
									  		$error=0;
										}else{
										   	$msjs[]= "No se pudo Actualizar el estudiante.";
										}
									}  
								}  
							}
						}else{
							$msjs[]= "La sede no existe";
						}
					}break;

					case 'copias':{

						$estudiante = $estA->buscar($cedula);
						if($estudiante->num_rows>0){
							$estudiante = $estudiante->fetch_assoc();
							if($clave==$estudiante["clave"]){
								$copiasRestantes = $estudiante["copias"];
								if($copiasRestantes<$copias){
									$msjs[]="Copias insuficientes ($copiasRestantes)";
								}
								$rs=$estA->consumirCopias($cedula, $clave, $copias);
								if($rs){
									$copiasRestantes-=$copias;
									$fotocopiaDAO = new FotocopiaDAO($dbA);
			    					$rs= $fotocopiaDAO->insertar($cedula, $copias);
			    					if($rs){
										$error=0;
										$rs = $estA->borrarPendiente($id_pendiente);
			    					}else{
			    						$msjs[]="error al facturar las fotocopias";
			    					}
								}else{
									$msjs[]="No se pudo consumir las copias";
								}
							}else{
								$msjs[] = "Combinación de Cédula/Clave incorrecta.";
							}
						}else{
							$estudiante = $estU->buscar($cedula);
							if($estudiante->num_rows>0){
								$estudiante = $estudiante->fetch_assoc();
								if($clave==$estudiante["clave"]){
									$copiasRestantes = $estudiante["copias"];
									if($copiasRestantes<$copias){
										$msjs[]="Copias insuficientes ($copiasRestantes)";
									}
									$rs=$estU->consumirCopias($cedula, $clave, $copias);
									if($rs){
										$rs = $estA->borrarPendiente($id_pendiente);
										$copiasRestantes-=$copias;
										$fotocopiaDAO = new FotocopiaDAO($dbA);
				    					$rs= $fotocopiaDAO->insertar($cedula, $copias);
				    					if($rs){
											$error=0;
				    					}else{
				    						$msjs[]="error al facturar las fotocopias";
				    					}

									}else{
										$msjs[]="No se pudo consumir las copias";
									}
								}else{
									$msjs[]= "Combinación de Cédula/Clave incorrecta.";
								}
							}else{
								$msjs[]= "El estudiante no se encuentra registrado ni en la sede principal ni en la sede de upata.";
							}
						}

					}break;

					case 'renovar':{
						$rsU = $estU->renovarCopias();

						if($rsU){
							$error=0;
							$rs = $estA->borrarPendiente($id_pendiente);
						}else{
							$msjs[]="Error al renovar las copias.";
						}
					}break;


					
					default:{
						$msjs[]= "accion no encontrada ($accion)";
					}break;
				}

			}
		}else{
			$msjs[]= "Lista de pendientes vacía.";
		}
		$dbA->desconectar();
		$dbU->desconectar();
	}else{
		$msjs[]="No se ha podido acceder a los servidores.";
	}


	$archivo = "/var/www/html/log/pendientes_log.txt";
	$open = fopen($archivo,"a");

	$tiempo = date("G:i:s");
	if ( $open ) {
		$fila = "--------$tiempo----------.\n";
		fwrite($open, $fila);
		foreach ($msjs as $clave => $valor) {
			$fila = "[$clave]: $valor.\n";
			fwrite($open, $fila);
		}
	}
	fclose($open);
?>
