<?php
    header('Content-Type: text/html; charset=UTF-8');

    include 'config.php';
    $con = mysqli_connect(HOST, USER, PASSWORD, DB) or die('Erro na conexão!');
    
    $sql = 'SELECT id_pergunta FROM sad_perguntas ORDER BY id_pergunta';
    
    $resultado = mysqli_query($con, $sql);
    if(mysqli_num_rows($resultado)>0) {
        $sql='';
        $perguntas=[];
        
        while($linha = mysqli_fetch_assoc($resultado)) {
            array_push($perguntas, $linha['id_pergunta']);
        }
        
        if(count($perguntas)>0) {
            foreach($perguntas as $pergunta) {
                $sql.="(NULL, {$pergunta}, 1, 1),";
            }
            
            $sql = 'INSERT INTO sad_perguntas_marcadores VALUES' . substr($sql, 0, -1) . ';';
            echo $sql;
            if(mysqli_query($con, $sql)) {
                echo 'Modificações feitas com Sucesso!';
            } else {
                echo 'Erro na inserção!';
            }
        }
    } else {
        echo 'Erro na consulta!';
    }
    
    mysqli_close($con);