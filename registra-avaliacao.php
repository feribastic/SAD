<?php
    setlocale(LC_ALL, 'pt_BR', "pt_BR.iso-8859-1", 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    
    header('Content-Type: text/html; charset=UTF-8');
    /* OCULTAR ERROS MOSTRADOS PELO PHP
    * error_reporting(E_ALL); 
    * ini_set('display_errors', 0);
    */ 
    //$jsonString = filter_input(INPUT_POST, 'jsonString');
    if(filter_input(INPUT_SERVER, 'REQUEST_METHOD')=='POST') {
        include 'inclusoes/db/config.php';
        $con = mysqli_connect(HOST, USER, PASSWORD, DB);
        mysqli_set_charset($con, 'utf8');
        
        $dataAtual = date('Y-m-d H:i:s');
        $comandoSQL = 'SELECT id_avaliacao FROM avaliacoes WHERE "' . $dataAtual . '" BETWEEN inicio AND termino';
        $resultado = mysqli_query($con, $comandoSQL);
        
        if(mysqli_num_rows($resultado)>0) {
            $jsonString = filter_input(INPUT_POST, 'jsonString');
            $jsonObj = json_decode($jsonString);

            $disciplinas = $jsonObj->disciplinas;
            $avaliacao = $jsonObj->avaliacao;
            $disponibilidade = $jsonObj->disponibilidade;

            $sql = 'INSERT INTO respostas(id_resposta, id_avaliacao, id_disciplina, id_pergunta, id_alternativa, resposta_textual, id_usuario, data) VALUES ';

            /* PENDENCIAS[INICIO]
             * 
             * DE YAN PARA YAN:
             * 
             * MUDAR DIA 05/07/2015 OS COMANDOS SQL NOW() 
             * POR UMA VARIÁVEL PHP COM A DATA FORMATADA 
             * PARA GARANTIR A DATA CERTA DE BRASÍLIA 
             * INDEPENDENTE DO HORÁRIO DO SERVIDOR 
             * 
             * PENDENCIAS[FIM] */

            $comandoToken = 'INSERT INTO usuarios VALUES(NULL, "", "", "", "", "' . md5(uniqid(rand(), true)) . '", 0, NOW());';
            $token='';
            if(mysqli_query($con, $comandoToken)) {
                $token=mysqli_insert_id($con);
            } else {
                die('Erro ao capturar Token: ' . mysqli_error($con));
            }

            foreach($avaliacao as $a) {
                if(empty($disciplinas)===false) {
                    if($a->disciplina===0) {
                        foreach($disciplinas as $disciplina) {
                            if($a->inteiro) {
                                $respostas = $a->resposta;
                                foreach($respostas as $resposta) {
                                    $sql.='(null, ' . $disponibilidade . ', ' . $disciplina . ', ' . $a->pergunta . ', ' . $resposta . ', ' . '"", "' . $token . '", now()), ';
                                }
                            } else {
                                $respostas_textuais = $a->resposta;
                                foreach($respostas_textuais as $resposta_textual) {
                                    $sql.='(null, ' . $disponibilidade . ', ' . $disciplina . ', ' . $a->pergunta . ', 1, "'. $resposta_textual . '", "' . $token . '", now()), ';
                                }
                            }
                        }
                    } else {
                        if($a->inteiro) {
                            $respostas = $a->resposta;
                            foreach($respostas as $resposta) {
                                $sql.='(null, ' . $disponibilidade . ', ' . $a->disciplina . ', ' . $a->pergunta . ', ' . $resposta . ', ' . '"", "' . $token . '", now()), ';
                            }
                        } else {
                            $respostas_textuais = $a->resposta;
                            foreach($respostas_textuais as $resposta_textual) {
                                $sql.='(null, ' . $disponibilidade . ', ' . $a->disciplina . ', ' . $a->pergunta . ', 1, "'. $resposta_textual . '", "' . $token . '", now()), ';
                            }
                        }
                    }
                } else {
                    if($a->inteiro) {
                        $respostas = $a->resposta;
                        foreach($respostas as $resposta) {
                            $sql.='(null, ' . $disponibilidade . ', ' . $a->disciplina . ', ' . $a->pergunta . ', ' . $resposta . ', ' . '"", "' . $token . '", now()), ';
                        }
                    } else {
                        $respostas_textuais = $a->resposta;
                        foreach($respostas_textuais as $resposta_textual) {
                            $sql.='(null, ' . $disponibilidade . ', ' . $a->disciplina . ', ' . $a->pergunta . ', 1, "'. $resposta_textual . '", "' . $token . '", now()), ';
                        }
                    }
                }
            }

            $comandoSql = substr_replace($sql, ';', strrpos($sql, ','));

            if(!$con) {
                echo '<div id="localizacao">Avaliação</div><div id="avaliacao-concluida"><span class="titulo">Avaliação concluída com Erro d: ' . mysqli_connect_error($con) . '</span><span class="descricao">Clique no botão abaixo para ser redirecionado(a) para a página principal.</span><button type="button" title="Ok" id="botao-avaliacao-concluida" class="botao botao-primario" onclick="confirmarConclusaoAvaliacao();">Ir para a página principal</button></div>';
            } else {
                if(mysqli_query($con, $comandoSql)) {
                    echo '<div id="localizacao">Avaliação</div><div id="avaliacao-concluida"><span class="titulo">Avaliação concluída com sucesso!</span><span class="descricao">Clique no botão abaixo para ser redirecionado(a) para a página principal.</span><button type="button" title="Ir para a página principal" id="botao-avaliacao-concluida" class="botao botao-primario" onclick="confirmarConclusaoAvaliacao();">Ir para a página principal</button></div>';
                } else {
                    echo '<div id="localizacao">Avaliação</div><div id="avaliacao-concluida"><span class="titulo">Avaliação concluída com Erro: ' . mysqli_error($con) . '</span><span class="descricao">Clique no botão abaixo para ser redirecionado(a) para a página principal.</span><button type="button" title="Ir para a página principal" id="botao-avaliacao-concluida" class="botao botao-primario" onclick="confirmarConclusaoAvaliacao();">Ir para a página principal</button></div>';
                } mysqli_close($con);
            }
        } else {
            echo '<div id="localizacao">Avaliação</div><div id="avaliacao-concluida"><span class="titulo">Desculpe mas o período de avaliação está encerrado.</span><span class="descricao">Clique no botão abaixo para ser redirecionado(a) para a página principal.</span><button type="button" title="Ir para a página principal" id="botao-avaliacao-concluida" class="botao botao-primario" onclick="confirmarConclusaoAvaliacao();">Ir para a página principal</button></div>';
        }
    } else {
        header('Location: index.php');
        exit;
    }