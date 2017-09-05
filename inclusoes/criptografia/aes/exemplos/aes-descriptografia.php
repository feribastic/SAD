<?php
    $id=filter_input(INPUT_GET, 'id');

    include '../classe-aes.php';
    
    $aes = new AES("86c179f7d0a1a39716e41243f80380a5");
    echo (int)$aes->decrypt(base64_decode(filter_input(INPUT_GET, 'id')));