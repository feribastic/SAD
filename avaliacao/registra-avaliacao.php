<?php
    setlocale(LC_ALL, 'pt_BR', "pt_BR.iso-8859-1", 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    
    header('Content-Type: text/html; charset=UTF-8');
    /* OCULTAR ERROS MOSTRADOS PELO PHP
    * error_reporting(E_ALL); 
    * ini_set('display_errors', 0);
    */ 
    
    if(filter_input(INPUT_SERVER, 'REQUEST_METHOD')=='POST') {
        require '../inclusoes/db/config.php';
        $con = mysqli_connect(HOST, USER, PASSWORD, DB);
        mysqli_set_charset($con, CHARSET);
        
        $dataAtual = date('Y-m-d H:i:s');
        $comandoSQL = 'SELECT id_avaliacao FROM ' . PREFIXO_TABELAS . 'avaliacoes WHERE "' . $dataAtual . '" BETWEEN inicio AND termino';
        $resultado = mysqli_query($con, $comandoSQL);
        
        if(mysqli_num_rows($resultado)>0) {
            $jsonString = filter_input(INPUT_POST, 'jsonString');
            $jsonObj = json_decode($jsonString);

            $disciplinas = $jsonObj->disciplinas;
            $avaliacao = $jsonObj->avaliacao;
            $disponibilidade = $jsonObj->disponibilidade;

            $sql = 'INSERT INTO ' . PREFIXO_TABELAS . 'respostas(id_resposta, id_avaliacao, id_disciplina, id_pergunta, id_marcador, id_alternativa, resposta_textual, id_usuario, data) VALUES ';

            $comandoUsuario = 'INSERT INTO ' . PREFIXO_TABELAS . 'usuarios(id_usuario, apelido, nome, email, senha, token, nivel_acesso, data_ultimo_acesso, data_cadastro) VALUES'
             . '(NULL, "", "", "", "", "", 0, "' . $dataAtual . '", "' . $dataAtual . '");';
            
            /*  ANTIGO TOKEN[INICIO] 
            
                    md5(uniqid(rand(), true));
            
                ANTIGO TOKEN[FIM]
             */
            
            $usuario='';
            if(mysqli_query($con, $comandoUsuario)) {
                $usuario = mysqli_insert_id($con);
            } else {
                die('Erro ao capturar Usuário: ' . mysqli_error($con));
            }

            foreach($avaliacao as $a) {
                $marcador = (!property_exists($a, 'marcador'))?1:$a->marcador;
                if(!empty($disciplinas)) {
                    if(!property_exists($a, 'disciplina')) {
                        foreach($disciplinas as $disciplina) {
                            if($a->inteiro) {
                                $respostas = $a->resposta;
                                foreach($respostas as $resposta) {
                                    $sql.='(null, ' . $disponibilidade . ', ' . $disciplina . ', ' . $a->pergunta . ', ' . $marcador . ', ' . $resposta . ', ' . '"", ' . $usuario . ', "' . $dataAtual . '"), ';
                                }
                            } else {
                                $respostas_textuais = $a->resposta;
                                foreach($respostas_textuais as $resposta_textual) {
                                    $sql.='(null, ' . $disponibilidade . ', ' . $disciplina . ', ' . $a->pergunta . ', ' .$marcador . ', 1, "'. $resposta_textual . '", ' . $usuario . ', "' . $dataAtual . '"), ';
                                }
                            }
                        }
                    } else {
                        if($a->inteiro) {
                            $respostas = $a->resposta;
                            foreach($respostas as $resposta) {
                                $sql.='(null, ' . $disponibilidade . ', ' . $a->disciplina . ', ' . $a->pergunta . ', ' . $marcador . ', ' . $resposta . ', ' . '"", ' . $usuario . ', "' . $dataAtual . '"), ';
                            }
                        } else {
                            $respostas_textuais = $a->resposta;
                            foreach($respostas_textuais as $resposta_textual) {
                                $sql.='(null, ' . $disponibilidade . ', ' . $a->disciplina . ', ' . $a->pergunta . ', ' . $marcador . ', 1, "'. $resposta_textual . '", ' . $usuario . ', "' . $dataAtual . '"), ';
                            }
                        }
                    }
                } else {
                    if($a->inteiro) {
                        $respostas = $a->resposta;
                        foreach($respostas as $resposta) {
                            $sql.='(null, ' . $disponibilidade . ', ' . $a->disciplina . ', ' . $a->pergunta . ', ' . $marcador . ', ' . $resposta . ', ' . '"", ' . $usuario . ', "' . $dataAtual . '"), ';
                        }
                    } else {
                        $respostas_textuais = $a->resposta;
                        foreach($respostas_textuais as $resposta_textual) {
                            $sql.='(null, ' . $disponibilidade . ', ' . $a->disciplina . ', ' . $a->pergunta . ', ' . $marcador . ', 1, "'. $resposta_textual . '", ' . $usuario . ', "' . $dataAtual . '"), ';
                        }
                    }
                }
            }

            $comandoSql = substr_replace($sql, ';', strrpos($sql, ','));

            if(!$con) {
                echo '<div id="avaliacao-concluida"><span class="titulo">Avaliação concluída com Erro: ' . mysqli_connect_error($con) . '</span><span class="descricao">Clique no botão abaixo para ser redirecionado(a) para a página principal.</span><button type="button" title="Ok" id="botao-avaliacao-concluida" class="botao botao-primario" onclick="confirmarConclusaoAvaliacao();">Ir para a página principal</button></div>';
            } else {
                if(mysqli_query($con, $comandoSql)) {
                    echo '<div id="avaliacao-concluida"><span class="titulo">Avaliação concluída com sucesso!</span><span class="descricao">Clique no botão abaixo para ser redirecionado(a) para a página principal.</span><button type="button" title="Ir para a página principal" id="botao-avaliacao-concluida" class="botao botao-primario" onclick="confirmarConclusaoAvaliacao();">Ir para a página principal</button></div>';
                } else {
                    echo '<div id="avaliacao-concluida"><span class="titulo">Avaliação concluída com Erro: ' . mysqli_error($con) . '</span><span class="descricao">Clique no botão abaixo para ser redirecionado(a) para a página principal.</span><button type="button" title="Ir para a página principal" id="botao-avaliacao-concluida" class="botao botao-primario" onclick="confirmarConclusaoAvaliacao();">Ir para a página principal</button></div>';
                } 
            }
        } else {
            echo '<div id="avaliacao-concluida"><span class="titulo">Desculpe mas o período de avaliação está encerrado.</span><span class="descricao">Clique no botão abaixo para ser redirecionado(a) para a página principal.</span><button type="button" title="Ir para a página principal" id="botao-avaliacao-concluida" class="botao botao-primario" onclick="confirmarConclusaoAvaliacao();">Ir para a página principal</button></div>';
        } 
        
        mysqli_close($con);
    } else {
        header('Location: ../index.php');
        exit;
    }