<?php
    require '../inclusoes/db/config.php';
    require '../inclusoes/classes/db/classe-conexao.php';
    require '../inclusoes/criptografia/aes/classe-aes.php';
    require '../inclusoes/classes/classe-usuario.php';
    
    $informacoes = Usuario::entrar('yansilvagabriel@gmail.com', 'root');
    
    var_dump($informacoes);