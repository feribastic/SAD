<?php
    header('Content-type: text/html; charset=UTF-8');

    include 'inclusoes/db/config.php';
    $con = mysqli_connect(HOST, USER, PASSWORD, DB);
        
    $con or die('Erro de Conexão');
        
    $resultado = mysqli_query($con, 'SELECT * FROM respostas');

    $respostas='<table class="table table-responsive tabela-primaria"><thead><tr><th>id_resposta</th><th>id_avaliacao</th><th>id_disciplina</th>
    <th>id_pergunta</th><th>id_alternativa</th><th>resposta_textual</th>
    <th>id_usuario</th><th>data</th></thead>
    </tr><tbody>';

    if(mysqli_num_rows($resultado)>0) {
        while($linha=mysqli_fetch_array($resultado)) {
            $respostas .="<tr><td>{$linha['id_resposta']}</td><td>{$linha['id_avaliacao']}</td>
            <td>{$linha['id_disciplina']}</td><td>{$linha['id_pergunta']}</td>
            <td>{$linha['id_alternativa']}</td><td>{$linha['resposta_textual']}</td>
            <td>{$linha['id_usuario']}</td><td>{$linha['data']}</td></tr>";
	}
    } else {
        echo 'Erro';
    }

    $respostas.='</tbody></table>';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Respostas</title>
        <link rel="stylesheet" type="text/css" href="respostas.css" />
    </head>
    <body>
        <?php
            echo $respostas;
	?>
    </body>
</html>