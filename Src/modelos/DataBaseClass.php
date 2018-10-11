<?php 
	class DataBase{
        private $hostname;
        private $username;
        private $password;
        private $dbname;
        private $conexion;
        private $dbConfig;

        public function __construct($sede){
            if($sede=='atlantico'){
                $this->dbConfig = require "../config/dataBaseConfig.php";
            }else{
                if($sede=='upata'){
                    $this->dbConfig = require "../config/dataBaseConfig2.php";
                }else{
                    die("No existe la sedee.");
                }
            }
            $this->hostname = $this->dbConfig["hostname"];
            $this->username = $this->dbConfig["username"];
            $this->password = $this->dbConfig["password"];
            $this->dbname   = $this->dbConfig["dbname"];
            $this->conexion = null;
        }

        function getHostname() {
            return $this->hostname;
        }

        function getUsername() {
            return $this->username;
        }

        function getPassword() {
            return $this->password;
        }

        function getDbname() {
            return $this->dbname;
        }

        function getConexion() {
            return $this->conexion;
        }

        function setHostname($hostname) {
            $this->hostname = $hostname;
        }

        function setUsername($username) {
            $this->username = $username;
        }

        function setPassword($password) {
            $this->password = $password;
        }

        function setDbname($dbname) {
            $this->dbname = $dbname;
        }

        function setConexion($conexion) {
            $this->conexion = $conexion;
        }

        function getDbConfig() {
            return $this->dbConfig;
        }

        function setDbConfig($dbConfig) {
            $this->dbConfig = $dbConfig;
        }
                
        function conectar(){
            $conexion = new mysqli($this->hostname, $this->username, $this->password, $this->dbname);
            
            if($conexion->connect_errno){
                $this->conexion = null;
            }else{
                $this->conexion = $conexion;
            }
            return $this->conexion;
        }


        function conectar2(){
            $conexion = new mysqli($this->hostname, $this->username, $this->password, $this->dbname);
            
            if($conexion->connect_errno){
                $this->conexion = null;
                return "Error: ".$conexion->connect_errno. " == ".$conexion->connect_error;
            }else{
                $this->conexion = $conexion;
            }
            return $this->conexion;
        }

        function consultar($sql){
            $resultado = $this->conexion->query($sql);
            
            if(!$resultado){
                return null;
            }else{
                return $resultado;
            }
        }
        
        function desconectar(){
            $this->conexion->close();
        }

    }
?>