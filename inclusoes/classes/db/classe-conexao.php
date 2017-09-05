<?php
    class Conexao extends PDO {
        public $handle = null;
        
        public function __construct() {
            if($this->handle == null) {
                try {
                    $this->handle = parent::__construct(DSN, USER, PASSWORD, []);
                } catch(PDOException $e) {
                    echo $e->getMessage();
                }
            } else {
                echo 'Erro Interno. <br />Por favor entre em contato com o administrador do sistema.';
            }
        }
        
        public function __destruct() {
            $this->handle = null;
        }
    }