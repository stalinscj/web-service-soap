<?php

    class FotocopiaDAO{
        
        private $dataBase;
        
        function __construct($dataBase) {
            require_once "DataBaseClass.php";
            $this->dataBase = $dataBase;
            $this->dataBase->conectar();
        }
        
        function getDataBase() {
            return $this->dataBase;
        }

        function setDataBase($dataBase) {
            $this->dataBase = $dataBase;
        }
        
        function insertar($cedula, $copias) {
            $sql = "INSERT INTO fotocopias
                    VALUES(null,'$cedula', $copias, null);";
            
            $resultado = $this->dataBase->consultar($sql);

            return $resultado;
        }

        function buscar($cedula){
            $sql = "SELECT * 
                    FROM fotocopias 
                    WHERE cedula='$cedula'";
            
            $resultado = $this->dataBase->consultar($sql);
            
            return $resultado;
        }
        
        function borrar($id){
            $sql = "DELETE 
                    FROM fotocopias 
                    WHERE id_fotocopia='$id'";
            
            $resultado = $this->dataBase->consultar($sql);
            
            return $resultado;
        }
    } 
?>
