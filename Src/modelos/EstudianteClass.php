<?php 
    class Estudiante {
        private $cedula;
        private $nombre;
        private $apellido;
        private $copias;
        private $clave;
        private $sede;
        
        function __construct($cedula, $nombre, $apellido, $copias, $clave, $sede) {
            $this->cedula = $cedula;
            $this->nombre = $nombre;
            $this->apellido = $apellido;
            $this->copias = $copias;
            $this->clave = $clave;
            $this->sede = $sede;
        }
        
        public function getCedula() {
            return $this->cedula;
        }
        
        public function getNombre() {
            return $this->nombre;
        }

        public function getApellido() {
            return $this->apellido;
        }

        public function getCopias() {
            return $this->copias;
        }

        public function getClave() {
            return $this->clave;
        }

        public function getSede() {
            return $this->sede;
        }

        public function setCedula($cedula) {
            $this->cedula = $cedula;
        }

        public function setNombre($nombre) {
            $this->nombre = $nombre;
        }

        public function setApellido($apellido) {
            $this->apellido = $apellido;
        }

        public function setCopias($copias) {
            $this->copias = $copias;
        }

        public function setClave($clave) {
            $this->clave = $clave;
        }

        public function setSede($sede) {
            $this->sede = $sede;
        } 
    }
?>