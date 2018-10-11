<?php

	require_once "../modelos/DataBaseClass.php";
	require_once "../modelos/EstudianteDAO.php";
	require_once "../modelos/FotocopiaDAO.php";

	if(!extension_loaded("soap")){
	      dl("php_soap.dll");
	}
	
	ini_set("soap.wsdl_cache_enabled","0");
	$server = new SoapServer('http://192.168.0.100/prolog/uneg.wsdl');

	 
	function buscarEstudiante($param){
		$error=1;
		$msj="";

		$cedula = $param->cedula;

		$dataBase = new DataBase("atlantico");
		$estudianteDAO = new EstudianteDAO($dataBase);

		if($dataBase->getConexion()==null){
			//Buscar en la otra sede
			$dataBase2 = new DataBase("upata");
			$estudianteDAO2 = new EstudianteDAO($dataBase2);

			if($dataBase2->getConexion()==null){
				$msj="No se pudo conectar a la Base de Datos de la sede principal ni a la de upata.";
			}else{
				$resultado = $estudianteDAO2->buscar($cedula);
			
				if($resultado->num_rows==0){

					$msj = "Estudiante no encontrado en la Base de Datos de upata y no se pudo conectar a la base de datos de la sede principal.";
				}else{
					$error = 0;
			    	$fila = $resultado->fetch_assoc();
		    	}
		    	$estudianteDAO2->getDataBase()->desconectar();
			}
		}else{
	    	$resultado = $estudianteDAO->buscar($cedula);

	    	if($resultado->num_rows==0){
				//Buscar en la otra sede
				$dataBase2 = new DataBase("upata");
				$estudianteDAO2 = new EstudianteDAO($dataBase2);

				if($dataBase2->getConexion()==null){
					$msj="El estudiante no se encuentra en la sede principal y no se pudo conectar a la Base de Datos de upata.";
				}else{
					$resultado = $estudianteDAO2->buscar($cedula);
				
					if($resultado->num_rows==0){

						$msj = "Estudiante no encontrado.2";
					}else{
						$error = 0;
				    	$fila = $resultado->fetch_assoc();
			    	}
			    	$estudianteDAO2->getDataBase()->desconectar();
				}
			}else{
				$error = 0;
		    	$fila = $resultado->fetch_assoc();
		    }
		    $estudianteDAO->getDataBase()->desconectar();
		}
	    
	    
	    $respuesta["buscarEstudianteResult"] =

	    	'<error>'.$error.'</error>
	    	<msj>'.$msj.'</msj>
	    	<Estudiante>
				  <cedula>'.$fila["cedula"].'</cedula>
				  <nombre>'.$fila["nombre"].'</nombre>
				  <apellido>'.$fila["apellido"].'</apellido>
				  <copias>'.$fila["copias"].'</copias>
				  <sede>'.$fila["sede"].'</sede>
			</Estudiante>';

	    return $respuesta;
	}

	function insertarEstudiante($param){

		$cedula = $param->cedula;
		$nombre = $param->nombre;
		$apellido = $param->apellido;
		$copias = $param->copias;
		$clave = $param->clave;
		$sede = $param->sede;

		$error = 1;
		$msj = "";

		if($sede=="atlantico" || $sede=="upata"){
			$existe = true;
			//Comprobando que no esté registrado en la otra sede
			if($sede=="atlantico"){
				$dbU = new DataBase("upata");
				$estU = new EstudianteDAO($dbU);

				if($dbU->getConexion()==null){
					$estado="1";
				}else{
					$estado="0";
					$rs = $estU->buscar($cedula);

		    		if($rs->num_rows==0){
		    			$existe=false;
		    		}
		    		$estU->getDataBase()->desconectar();
				}
				
			}else{
				if($sede=="upata"){
					$dbA = new DataBase("atlantico");
					$estA = new EstudianteDAO($dbA);

					if($dbA->getConexion()==null){
						$estado="2";
					}else{
						$estado="0";
						$rs = $estA->buscar($cedula);

			    		if($rs->num_rows==0){
			    			$existe=false;
			    		}
			    		$estA->getDataBase()->desconectar();
		    		}
				}
			}
			
			if($existe && $estado=="0"){
				$msj = "El estudiante ya está registrado en la otra sede.";
			}else{

				if($sede=="atlantico"){
					$dataBase = new DataBase($sede);
					$estudianteDAO = new EstudianteDAO($dataBase);

					if($dataBase->getConexion()==null){
						$msj = "Servidor principal caido.";
					}else{
						if($estado=="1"){
							$rs = $estudianteDAO->buscar($cedula);
							if($rs->num_rows==0){
								$resultado = $estudianteDAO->insertarPendiente($cedula, $nombre, $apellido, $copias, $clave, $sede, "insertar");
								$msj="Estudiante insertado la lista de espera, porque no se pudo conectar
					    				al servidor de upata para comprobar si ya está registrado allá.";
							}else{
								$msj="Estudiante ".$cedula." ya se encuentra registrado.";
							}
						}else{
				    		$resultado = $estudianteDAO->insertar($cedula, $nombre, $apellido, $copias, $clave, $sede);
						}
					    
					    if($resultado!=null){
					    	$error=0;
					   	}else{

					   		$msj = "No se pudo insertar el estudiante porque ya está registrado en la sede de atlántico.";
					   	}

						$estudianteDAO->getDataBase()->desconectar();
					}
				}else{
					
					$dataBase = new DataBase($sede);
					$estudianteDAO = new EstudianteDAO($dataBase);

					if($dataBase->getConexion()==null){
						$estado="1";
						$dataBase = new DataBase("atlantico");
						$estudianteDAO = new EstudianteDAO($dataBase);

						if($dataBase->getConexion()==null){
							$msj = "Servidor principal caido.";
						}else{
					    	$resultado = $estudianteDAO->insertarPendiente($cedula, $nombre, $apellido, $copias, $clave, $sede, "insertar");
						    
						    if($resultado!=null){
						    	$error=0;
						    	if($estado=="1"){
						    		$msj="Estudiante insertado la lista de espera, porque no se pudo conectar
						    				al servidor de upata para comprobar si ya está registrado allá.";
						    	}
						   	}else{

						   		$msj = "No se pudo insertar el estudiante.";
						   	}

							$estudianteDAO->getDataBase()->desconectar();
						}
					}else{

						if($estado=="0"){

				    		$resultado = $estudianteDAO->insertar($cedula, $nombre, $apellido, $copias, $clave, $sede);
					    
					    	if($resultado!=null){
					    		$error=0;
					   		}else{

					   			$msj = "No se pudo insertar el estudiante porque ya está registrado en la sede de upata.";
					   		}
						}else{
							$msj = "Servidor principal caído.";
						}

						$estudianteDAO->getDataBase()->desconectar();
					}
					
				}
			}
		}else{
			$msj = "No existe la sede";
		}

	    
	    $respuesta["insertarEstudianteResult"] = 
	    	'<error>'.$error.'</error>
	    	<msj>'.$msj.'</msj>
	    	<Estudiante>
				  <cedula>'.$cedula.'</cedula>
				  <nombre>'.$nombre.'</nombre>
				  <apellido>'.$apellido.'</apellido>
				  <copias>'.$copias.'</copias>
				  <sede>'.$sede.'</sede>
			</Estudiante>';

	    return $respuesta;
	}

	function actualizarEstudiante($param){

		$cedula = $param->cedula;
		$nombre = $param->nombre;
		$apellido = $param->apellido;
		$copias = $param->copias;
		$clave = $param->clave;
		$sede = $param->sede;

		$error = 1;
		$msj = "";

		if($sede=="atlantico" || $sede=="upata"){
			$dbA = new DataBase("atlantico");
			$dbU = new DataBase("upata");
			$estA = new EstudianteDAO($dbA);
			$estU = new EstudianteDAO($dbU);

			if($dbA->getConexion()==null && $dbU->getConexion()==null){
				$msj = "No se puedo conectar a la base de datos de la sede principal ni a la de upata.";
			}else{
				if($dbA->getConexion()!=null && $dbU->getConexion()==null){

					if($sede=="upata"){
						$resultado = $estA->insertarPendiente($cedula, $nombre, $apellido, $copias, $clave, $sede, "actualizar");
					    
					    if($resultado!=null){
					    	$error=0;
					    	$msj="Actualizacion guardada en lista de espera, porque no se pudo conectar
					    			al servidor de upata.";
					    	
					   	}else{
					   		$msj = "No se pudo Actualizar el estudiante.";
					   	}
					}else{
						$resultado = $estA->buscar($cedula);
						if($resultado->num_rows==0){
							$resultado = $estA->insertarPendiente($cedula, $nombre, $apellido, $copias, $clave, $sede, "actualizar");
					    
						    if($resultado!=null){
						    	$error=0;
						    	$msj="Actualizacion guardada en lista de espera, porque no se pudo conectar
						    			al servidor de upata para verificar si se encuentra registrado allá.";
						    	
						   	}else{
						   		$msj = "No se pudo Actualizar el estudiante.";
						   	}
						}else{
							$valido = $estA->validar($cedula, $clave);
							if($valido->num_rows==0){
								$msj="Combinación de Cédula/Clave incorrecta.";
							}else{
								$rs = $estA->actualizar($cedula, $nombre, $apellido, $clave, $sede);
								
							  	if($rs){
							  		$error=0;
								}else{
								   	$msj = "No se pudo Actualizar el estudiante.";
								}
							}    
						}   
					}
					$dbA->desconectar();
				}else{
					if($dbA->getConexion()==null && $dbU->getConexion()!=null){
						if($sede=="upata"){
							$resultado = $estU->buscar($cedula);
							if($resultado->num_rows==0){
							    $msj="No se encontró al estudiante registrado en upata y no se pudo conectar
							    		al servidor principal para verificar si se encuentra registrado allá.";
							
							}else{
								$valido = $estU->validar($cedula, $clave);
								if($valido->num_rows==0){
									$msj="Combinación de Cédula/Clave incorrecta.";
								}else{
									$rs = $estU->actualizar($cedula, $nombre, $apellido, $clave, $sede);
									
								  	if($rs){
								  		$error=0;
									}else{
									   	$msj = "No se pudo Actualizar el estudiante.";
									}
								}  
							}  
						}else{
							$msj = "No se pudo conectar al servidor principal";
						}
						$dbU->desconectar();
					}else{

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
									$error=0;
								}else{
									$msj="Error al actualizar estudiante";
								}
							}else{
								$valido = $estU->validar($cedula, $clave);
								if($valido->num_rows==0){
									$msj="Combinación de Cédula/Clave incorrecta.";
								}else{
									$rs = $estU->actualizar($cedula, $nombre, $apellido, $clave, $sede);
									
								  	if($rs){
								  		$error=0;
									}else{
									   	$msj = "No se pudo Actualizar el estudiante.";
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
									$error=0;
								}else{
									$msj="Error al actualizar estudiante";
								}
							}else{
								$valido = $estA->validar($cedula, $clave);
								if($valido->num_rows==0){
									$msj="Combinación de Cédula/Clave incorrecta.";
								}else{
									$rs = $estA->actualizar($cedula, $nombre, $apellido, $clave, $sede);
									
								  	if($rs){
								  		$error=0;
									}else{
									   	$msj = "No se pudo Actualizar el estudiante.";
									}
								}  
							}  
						}
						$dbA->desconectar();
						$dbU->desconectar();
					}
				}
			}
		}else{
			$msj="No existe la sede";
		}

	    
	    $respuesta["actualizarEstudianteResult"] = 
	    	'<error>'.$error.'</error>
	    	<msj>'.$msj.'</msj>
	    	<Estudiante>
				  <cedula>'.$cedula.'</cedula>
				  <nombre>'.$nombre.'</nombre>
				  <apellido>'.$apellido.'</apellido>
				  <copias>'.$copias.'</copias>
				  <sede>'.$fila["sede"].'</sede>
			</Estudiante>';

	    return $respuesta;
	}

	function renovarCopias($param){
		
		$nombre = $param->nombre;
		$clave =  $param->clave;

		$error = 1;
		$msj = "";
		
		if($nombre=="uneg" && $clave=="uneg"){
			$dbA = new DataBase("atlantico");
			$dbU = new DataBase("upata");
			$estA = new EstudianteDAO($dbA);
			$estU = new EstudianteDAO($dbU);

			if($dbA->getConexion()==null && $dbU->getConexion()==null){
				$msj = "No se puedo conectar a la base de datos de la sede principal ni a la de upata.";
			}else{
				if($dbA->getConexion()!=null && $dbU->getConexion()==null){
					$rsU = $estA->insertarPendiente("0", "0", "0", "0", "0", "upata", "renovar");
					$rs = $estA->renovarCopias();

					if($rs){
						$error=0;
						$msj="Solo se renovaron las copias de la sede principal porque no se puedo conectar a la sede de upata.";
					}else{
						$msj="Error al renovar las copias.";
					}

					$dbA->desconectar();
				}else{
					if($dbA->getConexion()==null && $dbU->getConexion()!=null){
						$msj="No se pudo conectar a la sede principal y se renovará upata cuando se logre conectar con la sede principal.";
						
						$dbU->desconectar();
					}else{
						$rsU = $estU->renovarCopias();
						$rsA = $estA->renovarCopias();

						if($rsU){
							$error=0;
							
						}else{
							$msj="Error al renovar las copias.";
						}

						$dbU->desconectar();
						$dbA->desconectar();
					}
				}
			}
		}else{
			$msj="Combinacion de Cédula/Clave de administrador incorrecta.";
		}
	    
	    $respuesta["renovarCopiasResult"] = 
	    	'<error>'.$error.'</error>
	    	<msj>'.$msj.'</msj>';

	    return $respuesta;
	}

	function consumirCopias($param){

		$cedula = $param->cedula;
		$clave 	= $param->clave;
		$copias = $param->copias;

		
		$dbA = new DataBase("atlantico");
		$dbU = new DataBase("upata");
		$estA = new EstudianteDAO($dbA);
		$estU = new EstudianteDAO($dbU);
		$copiasRestantes = 0;

		if($dbA->getConexion()==null && $dbU->getConexion()==null){
			$msj = "No se puedo conectar a la base de datos de la sede principal ni a la de upata.";
		}else{
			if($dbA->getConexion()!=null && $dbU->getConexion()==null){
				$estudiante = $estA->buscar($cedula);
				if($estudiante->num_rows>0){
					$estudiante = $estudiante->fetch_assoc();
					if($clave==$estudiante["clave"]){
						$copiasRestantes = $estudiante["copias"];
						if($copiasRestantes<$copias){
							$msj="Copias insuficientes ($copiasRestantes)";
						}else{
							$rs=$estA->consumirCopias($cedula, $clave, $copias);
							if($rs){
								$copiasRestantes-=$copias;
								$fotocopiaDAO = new FotocopiaDAO($dbA);
		    					$rs= $fotocopiaDAO->insertar($cedula, $copias);
		    					if($rs){
									$error=0;
		    					}else{
		    						$msj="error al facturar las fotocopias";
		    					}
							}else{
								$msj="No se pudo consumir las copias";
							}
						}
					}else{
						$msj = "Combinación de Cédula/Clave incorrecta.";
					}
				}else{
					$rs = $estA->insertarPendiente($cedula, "0", "0", $copias, $clave, "", "copias");
					if($rs){
						$error=0;
						$msj="Consumo de fotocopias pendiente hasta que se pueda conectar con la sede de upata.";
					}else{
						$msj="No se pudo insertar en la tabla de pendientes";
					}
				}

				$dbA->desconectar();
			}else{
				if($dbA->getConexion()==null && $dbU->getConexion()!=null){
					$estudiante = $estU->buscar($cedula);
					if($estudiante->num_rows>0){
						$estudiante = $estudiante->fetch_assoc();
						if($clave==$estudiante["clave"]){
							$copiasRestantes = $estudiante["copias"];
							if($copiasRestantes<$copias){
								$msj="Copias insuficientes ($copiasRestantes)";
							}else{
								$rs=$estU->consumirCopias($cedula, $clave, $copias);
								if($rs){
									$copiasRestantes-=$copias;
									$error=0;
								}else{
									$msj="No se pudo consumir las copias";
								}
							}
						}else{
							$msj = "Combinación de Cédula/Clave incorrecta.";
						}
					}else{
						$msj = "No se encontró el estudiante registrado en la sede de upata y no se puede conectar con la sede principal";
					}
					$dbU->desconectar();
				}else{
					$estudiante = $estA->buscar($cedula);
					if($estudiante->num_rows>0){
						$estudiante = $estudiante->fetch_assoc();
						if($clave==$estudiante["clave"]){
							$copiasRestantes = $estudiante["copias"];
							if($copiasRestantes<$copias){
								$msj="Copias insuficientes ($copiasRestantes)";
							}else{
								$rs=$estA->consumirCopias($cedula, $clave, $copias);
								if($rs){
									$copiasRestantes-=$copias;
									$fotocopiaDAO = new FotocopiaDAO($dbA);
			    					$rs= $fotocopiaDAO->insertar($cedula, $copias);
			    					if($rs){
										$error=0;
			    					}else{
			    						$msj="error al facturar las fotocopias";
			    					}
								}else{
									$msj="No se pudo consumir las copias";
								}
							}
						}else{
							$msj = "Combinación de Cédula/Clave incorrecta.";
						}
					}else{
						$estudiante = $estU->buscar($cedula);
						if($estudiante->num_rows>0){
							$estudiante = $estudiante->fetch_assoc();
							if($clave==$estudiante["clave"]){
								$copiasRestantes = $estudiante["copias"];
								if($copiasRestantes<$copias){
									$msj="Copias insuficientes ($copiasRestantes)";
								}else{
									$rs=$estU->consumirCopias($cedula, $clave, $copias);
									if($rs){
										$copiasRestantes-=$copias;
										$fotocopiaDAO = new FotocopiaDAO($dbA);
				    					$rs= $fotocopiaDAO->insertar($cedula, $copias);
				    					if($rs){
											$error=0;
				    					}else{
				    						$msj="error al facturar las fotocopias";
				    					}

									}else{
										$msj="No se pudo consumir las copias";
									}
								}
							}else{
								$msj = "Combinación de Cédula/Clave incorrecta.";
							}
						}else{
							$msj = "El estudiante no se encuentra registrado ni en la sede principal ni en la sede de upata.";
						}
					}

					$dbA->desconectar();
					$dbU->desconectar();
				}
			}
		}


	    $respuesta["consumirCopiasResult"] = 
	    	'<error>'.$error.'</error>
	    	<msj>'.$msj.'</msj>
	    	<Estudiante>
				  <cedula>'.$cedula.'</cedula>
				  <nombre>'.$nombre.'</nombre>
				  <apellido>'.$apellido.'</apellido>
				  <copias>'.$copiasRestantes.'</copias>
				  <sede>'.$fila["sede"].'</sede>
			</Estudiante>';

	    return $respuesta;
	}
	 
	$server->AddFunction("buscarEstudiante");
	$server->AddFunction("insertarEstudiante");
	$server->AddFunction("actualizarEstudiante");
	$server->AddFunction("renovarCopias");
	$server->AddFunction("consumirCopias");

	$server->handle();
?>
