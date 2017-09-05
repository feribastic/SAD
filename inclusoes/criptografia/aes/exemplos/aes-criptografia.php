<?php
    header('Content-Type: text/html; charset=UTF-8');
    
    include '../classe-aes.php';
    
    /* Gera a chave que está no "$aes=new AES($chave);" abaixo:
    * $chave = AES::keygen(AES::KEYGEN_256_BITS, "yan");
    */
    
    $aes = new AES("86c179f7d0a1a39716e41243f80380a5");
    $encode = $aes->encrypt("15");
    
    echo '<a href="aes-descriptografia.php?id=' . base64_encode($encode) . '">Testar Criptografia</a>';
    
    echo '<br />' . $aes->decrypt($encode);