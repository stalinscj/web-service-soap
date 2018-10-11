<?php

    class EstudianteDAO{
        
        private $dataBase;
        
        function __construct($dataBase) {
            require_once "DataBaseClass.php";
            require_once "EstudianteClass.php";
            $this->dataBase = $dataBase;
            $this->dataBase->conectar();
        }
        
        function getDataBase() {
            return $this->dataBase;
        }

        function setDataBase($dataBase) {
            $this->dataBase = $dataBase;
        }
        
        function insertar($cedula, $nombre, $apellido, $copias, $clave, $sede) {
            $sql = "INSERT INTO estudiantes 
                    VALUES('$cedula', '$nombre', '$apellido', $copias, '$clave', '$sede');";
            
            $resultado = $this->dataBase->consultar($sql);

            return $resultado;
        }

        function buscar($cedula){
            $sql = "SELECT * 
                    FROM estudiantes 
                    WHERE cedula='$cedula'";
            
            $resultado = $this->dataBase->consultar($sql);
            
            return $resultado;
        }
        
        function borrar($cedula){
            $sql = "DELETE 
                    FROM estudiantes 
                    WHERE cedula='$cedula'";
            
            $resultado = $this->dataBase->consultar($sql);
            
            return $resultado;
        }

        function actualizar($cedula, $nombre, $apellido, $clave, $sede){
            
            $sql = "UPDATE estudiantes 
                    SET
                        nombre  ='$nombre', 
                        apellido='$apellido',
                        sede='$sede' 
                    WHERE cedula='$cedula' AND clave='$clave'";
            
            $resultado = $this->dataBase->consultar($sql);

            return $resultado;
            
        }

        function validar($cedula, $clave){
            $sql = "SELECT *
                   FROM estudiantes
                   WHERE cedula='$cedula' AND clave='$clave'";
            
            $resultado = $this->dataBase->consultar($sql);
            
            return $resultado;
        }

        function renovarCopias(){

            $sql = "UPDATE  estudiantes AS e
                    SET copias=250
                    WHERE copias>=0";

            $rs1 = $this->dataBase->consultar($sql);

            $sql = "UPDATE  estudiantes AS e
                    SET copias=250+copias
                    WHERE copias<0";

            $rs2 = $this->dataBase->consultar($sql);

            return ($rs1 && $rs2);
        }

        function consumirCopias($cedula, $clave, $cantidad){

            $estudiante = $this->buscar($cedula);
            if($estudiante!=null){
                $estudiante = $estudiante->fetch_assoc();
                $copias = $estudiante["copias"];
                $copias -= $cantidad;

                $sql = "UPDATE estudiantes
                        SET copias=$copias
                        WHERE cedula='$cedula' AND clave='$clave'";
                $resultado = $this->dataBase->consultar($sql);

                return $resultado;
            }else{
                return null;
            }
        }

        function estudiantesSinCopias(){

            $sql = "SELECT cedula, nombre, apellido
                    FROM estudiantes
                    WHERE copias=0
                    ORDER BY nombre";

            $resultado = $this->dataBase->consultar($sql);
            return $resultado;
        }

        function estudiantesInactivos(){

            $sql = "SELECT cedula, nombre, apellido
                    FROM estudiantes 
                    WHERE copias=250
                    ORDER BY nombre";
            $resultado = $this->dataBase->consultar($sql);
            return $resultado;
        }

        function actualizarCopias($cedula, $copias){
            
            $sql = "UPDATE estudiantes 
                    SET
                        copias=$copias, 
                    WHERE cedula=$cedula";
            
            $resultado = $this->dataBase->consultar($sql);
            
            return $resultado;
        }

        function insertarPendiente($cedula, $nombre, $apellido, $copias, $clave, $sede, $accion) {
            $sql = "INSERT INTO pendientes 
                    VALUES(null,0,'$accion', '$cedula', '$nombre', '$apellido', $copias, '$clave', '$sede');";
            
            $resultado = $this->dataBase->consultar($sql);

            return $resultado;
        }

        function borrarPendiente($idPendiente){
            $sql = "DELETE 
                    FROM pendientes
                    WHERE id_pendiente='$idPendiente'";
            
            $resultado = $this->dataBase->consultar($sql);
            
            return $resultado;
        }

    }
?>
