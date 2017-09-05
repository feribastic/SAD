<?php
    setlocale(LC_ALL, 'pt_BR', "pt_BR.iso-8859-1", 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');    
    
    
    /**
     * Responsável por garantir a reescrita do acesso ao sistema para cada tipo de Usuário
     * 
     * @package poo
     * @author Yan Gabriel da Silva Machado <yansilvagabriel@gmail.com>
     * @version 0.1
     * @since 0.1.2
     */
    interface Autenticacao {
        public static function entrar($login, $senha);
    }
    
    /**
     * Define novos Usuários assim como suas configurações
     * 
     * @package poo
     * @author Yan Gabriel da Silva Machado <yansilvagabriel@gmail.com>
     * @version 0.1
     * @since 0.1.2
     */
    class Usuario implements Autenticacao {
        private $apelido;
        private $nome;
        private $email;
        private $senha;
        private $niveis;
        private $nivel;
        
        
        /**
         * Constrói o objeto Usuário a partir do parâmetros Nome e Nível de Acesso
         * 
         * @param type $nome
         * @param type $email
         * @param type $nivel
         * @return boolean
         */
        public function __construct($apelido, $nome, $email, $senha, $nivel=1) {
            $this->niveis = range(0, 10); 
            $this->nivel = (!array_search($nivel, $this->niveis))?NULL:$nivel;
            
            if(!is_null($this->nivel) && !empty($nome) && !empty($apelido) && !empty($email) && !empty($senha)) {
                if(Usuario::procurar($email, true)!=false) {
                    return false;
                } else {
                    $con = new Conexao();
                    $stm = $con->prepare('INSERT INTO ' . PREFIXO_TABELAS . 'usuarios(id_usuario, apelido, nome, email, senha, token, nivel_acesso, data_ultimo_acesso, data_cadastro) '
                    . 'VALUES(NULL, :apelido, :nome, :email, AES_ENCRYPT(:senha, "' . CRYPT_PASSWORD . '"), "", :nivel_acesso, NOW(), NOW())');
                    
                    $stm->bindParam(':apelido', $apelido, PDO::PARAM_STR);
                    $stm->bindParam(':nome', $nome, PDO::PARAM_STR);
                    $stm->bindParam(':email', $email, PDO::PARAM_STR);
                    $stm->bindParam(':senha', $senha, PDO::PARAM_STR);
                    $stm->bindParam(':nivel_acesso', $nivel, PDO::PARAM_INT);
                    $stm->execute();
                    if($stm->rowCount()>0) {
                        $this->apelido = $apelido;
                        $this->nome = $nome; 
                        $this->email = $email;
                        $this->senha = $senha;
                    }
                }
            } else {return false;}
        }
        
        
        /**
         * Retorna apelido do Usuário
         * 
         * @return string
         */
        public function getApelido() {
            return $this->apelido;
        }
        
        
        /**
         * Retorna nome do Usuário
         * 
         * @return string
         */
        public function getNome() {
            return $this->nome;
        }
        
        
        /**
         * Retorna senha do Usuário
         * 
         * @return string
         */
        public function getSenha() {
            return $this->senha;
        }
        
        
        /**
         * Retorna email do Usuário
         * 
         * @return string
         */
        public function getEmail() {
            return $this->email;
        }
        
        
        /**
         * Procura por Usuário
         * 
         * @param string $apelido
         * @param string $nome
         * @param string $email
         * @return array Retorna Array Associativo se encontrado
         * @return false Retorna False se não encontrado
         */
        public static function procurar($informacao, $buscaSegura=false) {
            if(!empty($informacao)) {
                $con = new Conexao();
                $comandoSQL = '';
                
                if($buscaSegura) {
                    $comandoSQL = 'SELECT apelido, nome, email FROM ' . PREFIXO_TABELAS 
                . 'usuarios WHERE BINARY apelido=:apelido OR BINARY nome=:nome OR BINARY email=:email';
                } else {
                    $comandoSQL = 'SELECT apelido, nome, email FROM ' . PREFIXO_TABELAS 
                . 'usuarios WHERE apelido=:apelido OR nome=:nome OR email=:email';
                }
                
                $stm = $con->prepare($comandoSQL);
                
                $stm->bindParam(':apelido', $informacao, PDO::PARAM_STR);
                $stm->bindParam(':nome', $informacao, PDO::PARAM_STR);
                $stm->bindParam(':email', $informacao, PDO::PARAM_STR);
                $stm->execute();
                if($stm->rowCount()>0) {
                    $usuarios = [];
                    $stm->setFetchMode(PDO::FETCH_ASSOC);
                    foreach($stm->fetchAll() as $linha) {
                        $usuarios[] = [
                            'apelido'=>$linha['apelido'], 
                            'nome'=>$linha['nome'], 
                            'email'=>$linha['email']
                        ];
                    }
                    
                    return count($usuarios)>0?$usuarios:false;
                } else {return false;}
                
            } else {return false;}
        }
        
        
        
        /**
         * Autentica Usuário
         * 
         * @param string $login Email
         * @param string $senha Senha
         * @return array Retorna Array Associativo com Token, Nível de Acesso e Apelido se autenticado
         * @return false Retorna False se não autenticado
         */
        public static function entrar($login, $senha) {
            $con = new Conexao();
            $stm = $con->prepare('SELECT id_usuario, apelido, email, AES_DECRYPT(senha, "' . CRYPT_PASSWORD . '") AS senha, nivel_acesso FROM ' 
            . PREFIXO_TABELAS . 'usuarios WHERE BINARY email=:login AND BINARY AES_DECRYPT(senha, "' 
            . CRYPT_PASSWORD . '")=:senha AND nivel_acesso>0 LIMIT 1');
            
            $stm->bindParam(':login', $login, PDO::PARAM_STR);
            $stm->bindParam(':senha', $senha, PDO::PARAM_STR);
            $stm->execute();
            if($stm->rowCount()>0) {
                $usuario = $apelido = $email = $senha = $token = $dataAtual = '';
                
                $stm->setFetchMode(PDO::FETCH_ASSOC);
                foreach($stm->fetchAll() as $linha) {
                    $usuario = $linha['id_usuario'];
                    $apelido = $linha['apelido'];
                    $email = $linha['email'];
                    $senha = $linha['senha'];
                    $token = md5(uniqid(rand(), true));
                    $niveAcesso = $linha['nivel_acesso'];
                    $dataAtual = date('Y-m-d H:i:s');
                }
                
                if(!empty($usuario) && !empty($email) && !empty($senha) 
                && !empty($token) && !empty($niveAcesso) && !empty($dataAtual)) {
                    $aes = new AES(AES_KEYGEN);
                    $usuario = $aes->encrypt($usuario);
                    $niveAcesso = $aes->encrypt($niveAcesso);
                    $apelido = $aes->encrypt($apelido);
                    
                    $stm2 = $con->prepare('UPDATE ' . PREFIXO_TABELAS . 'usuarios SET '
                    . 'token=:token, data_ultimo_acesso=:data WHERE BINARY email=:email AND BINARY AES_DECRYPT(senha, "' 
                    . CRYPT_PASSWORD . '")=:senha');
                    
                    $stm2->bindParam(':token', $token, PDO::PARAM_STR);
                    $stm2->bindParam(':data', $dataAtual, PDO::PARAM_STR);
                    $stm2->bindParam(':email', $email, PDO::PARAM_STR);
                    $stm2->bindParam(':senha', $senha, PDO::PARAM_STR);
                    unset($login, $email, $senha, $dataAtual);
                    
                    $stm2->execute();
                    return $stm2->rowCount()>0?['token'=>$token, 'usuario'=>$usuario, 'nivel_acesso'=>$niveAcesso, 'apelido'=>$apelido]:false;
                    
                } else {return false;}
                
            } else {return false;}
        }
        
        function __destruct() {
            unset($this->apelido, $this->nome, $this->email, $this->senha, $this->nivel, $this->niveis);
        }
    }