<?php    
    setlocale(LC_ALL, 'pt_BR', "pt_BR.iso-8859-1", 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    
    /*
    session_start();
    if(isset($_SESSION['avaliacao-iniciada'])) {
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['avaliacao-iniciada'] = true;
    }
    */
    
    function strip($html) {
        return trim(preg_replace('/<!--(.|\s)*?-->/', '',preg_replace('/\s+/', ' ', $html)));
    }
    
    ob_start('strip');
    
    ini_set('memory_limit', '-1');
    set_time_limit(120);
    
    $dis = []; 
    $comandoSql = ''; 
    $conteudo = '<form name="avaliacao" action="" method="post">';
    
    if(filter_input(INPUT_SERVER, 'REQUEST_METHOD')=='POST') {
        require '../inclusoes/db/config.php';
        $conexao = mysqli_connect(HOST, USER, PASSWORD, DB);
        mysqli_set_charset($conexao, CHARSET);
        
        $dataAtual = date('Y-m-d H:i:s');
        $comandoSQLData = 'SELECT id_avaliacao, nome FROM ' . PREFIXO_TABELAS . 'avaliacoes WHERE "' . $dataAtual . '" BETWEEN inicio AND termino';
        $resultadoCSQLData = mysqli_query($conexao, $comandoSQLData);
        
        if(mysqli_num_rows($resultadoCSQLData)>0) {
            $nomeAvaliacao = '';
            while($linha = mysqli_fetch_array($resultadoCSQLData)) {
                $nomeAvaliacao = $linha['nome'];
            }
            
            if(isset($_POST['disciplinas'])) {
                foreach($_POST['disciplinas'] as $d=>$v) {
                    $dis[] = (int)$v;
                }
            } sort($dis);

            $disponibilidade = (int)filter_input(INPUT_POST, 'disponibilidade', FILTER_SANITIZE_NUMBER_INT);

            $comandoSql.='SELECT a.ordem AS ordem_disciplinas_pergunta, b.id_disciplina, b.disciplina, c.id_pergunta, c.pergunta, c.texto_ajuda, c.tipo_entrada, c.e_alternativa, c.e_obrigatoria, d.prioridade AS ordem_perguntas_alternativa, e.id_alternativa, e.alternativa, f.id_grupo, f.ordem AS ordem_grupo, f.grupo, g.id_marcador, h.marcador FROM '
                . PREFIXO_TABELAS . 'disciplinas_perguntas a INNER JOIN ' . PREFIXO_TABELAS . 'disciplinas b ON a.id_disciplina=b.id_disciplina '
                . 'INNER JOIN ' . PREFIXO_TABELAS . 'perguntas c ON a.id_pergunta=c.id_pergunta INNER JOIN ' . PREFIXO_TABELAS . 'perguntas_alternativas d ON c.id_pergunta=d.id_pergunta '
                . 'INNER JOIN ' . PREFIXO_TABELAS . 'alternativas e ON d.id_alternativa=e.id_alternativa '
                . 'INNER JOIN ' . PREFIXO_TABELAS . 'grupos f ON c.id_grupo=f.id_grupo INNER JOIN ' . PREFIXO_TABELAS . 'perguntas_marcadores g ON c.id_pergunta=g.id_pergunta INNER JOIN ' . PREFIXO_TABELAS . 'marcadores h ON g.id_marcador=h.id_marcador WHERE ';

            $primeiroAcesso = $existeDisciplina = $existeMarcador = true; 
            $grupos = $disciplinas = $ordemPerguntas = $perguntas = $perguntasAlternativas = $marcadores = $campos = [];
            $existeGrupo = $existeOrdemPergunta = $existePergunta = $existePerguntasAlternativas = $existeCampo = true;
            
            /* $ordemPerguntas E PARA GRUPOS DIFERENTES DO PADRAO. O GRUPO PADRAO POSSUI ID=1 */
            foreach($dis as $d) {
                if($primeiroAcesso) {
                    $comandoSql.='a.id_disciplina=' . $d; 
                    $primeiroAcesso = false;
                } else {
                    $comandoSql.=' OR a.id_disciplina=' . $d;
                }
            }

            $comandoSql.=';';

            $consulta = mysqli_query($conexao, $comandoSql);

            if(mysqli_num_rows($consulta)>0) {
                while($linha = mysqli_fetch_array($consulta)) {
                    if(count($grupos)===0) {
                        $grupos[] = (object) [
                            'idGrupo'=>(int)$linha['id_grupo'], 
                            'ordem_grupo'=>(int)$linha['ordem_grupo'], 
                            'grupo'=>$linha['grupo']
                        ];
                    } else {
                        foreach($grupos as $grupo) {
                            if($grupo->idGrupo!==(int)$linha['id_grupo']) {
                                $existeGrupo = false;
                            } else {
                                $existeGrupo = true; 
                                break;
                            }
                        }

                        if(!$existeGrupo) {
                            $grupos[] = (object) [
                                'idGrupo'=>(int)$linha['id_grupo'], 
                                'ordem_grupo'=>(int)$linha['ordem_grupo'], 
                                'grupo'=>$linha['grupo']
                            ];
                        }
                    }

                    if(count($disciplinas)===0) {
                        $disciplinas[] = (object) [
                            'idDisciplina'=>(int)$linha['id_disciplina'], 
                            'disciplina'=>$linha['disciplina'], 
                            'idGrupo'=>(int)$linha['id_grupo']
                        ];
                    } else {
                        foreach($disciplinas as $disciplina) {
                            if($disciplina->idDisciplina!==(int)$linha['id_disciplina']) {
                                $existeDisciplina = false;
                            } else {
                                $existeDisciplina = true; 
                                break;
                            }
                        }

                        if(!$existeDisciplina) {
                            $disciplinas[] = (object) [
                                'idDisciplina'=>(int)$linha['id_disciplina'], 
                                'disciplina'=>$linha['disciplina'], 
                                'idGrupo'=>(int)$linha['id_grupo']
                            ];
                        }
                    }

                    if(count($ordemPerguntas)===0) {
                        $ordemPerguntas[] = (object) [
                            'idDisciplina'=>(int)$linha['id_disciplina'], 
                            'id_pergunta'=>(int)$linha['id_pergunta'], 
                            'ordem_disciplinas_pergunta'=>(int)$linha['ordem_disciplinas_pergunta'], 
                            'idGrupo'=>(int)$linha['id_grupo']
                        ];
                    } else {
                        foreach($ordemPerguntas as $ordemPergunta) {
                            if($ordemPergunta->idGrupo===(int)$linha['id_grupo']) { 
                                if($ordemPergunta->idDisciplina===(int)$linha['id_disciplina']) {
                                        if($ordemPergunta->id_pergunta!==(int)$linha['id_pergunta']) {
                                            $existeOrdemPergunta = false;
                                        } else {
                                            $existeOrdemPergunta = true; 
                                            break;
                                        }
                                } else {
                                    if($ordemPergunta->id_pergunta!==(int)$linha['id_pergunta']) {
                                        $existeOrdemPergunta = false;
                                    } else {
                                        if($ordemPergunta->idGrupo===1) {
                                           $existeOrdemPergunta = false; 
                                        } else {
                                            $existeOrdemPergunta = true; 
                                            break;
                                        }
                                    }
                                } 
                            } else {
                                if($ordemPergunta->id_pergunta!==(int)$linha['id_pergunta']) {
                                    $existeOrdemPergunta = false;
                                } else {
                                    $existeOrdemPergunta = true; 
                                    break;
                                }
                            }
                        }

                        if(!$existeOrdemPergunta) {
                            $ordemPerguntas[] = (object) [
                                'idDisciplina'=>(int)$linha['id_disciplina'], 
                                'id_pergunta'=>(int)$linha['id_pergunta'], 
                                'ordem_disciplinas_pergunta'=>(int)$linha['ordem_disciplinas_pergunta'], 
                                'idGrupo'=>(int)$linha['id_grupo']
                            ];
                        }
                    }

                    if(count($perguntas)===0) {
                        $perguntas[] = (object) [
                            'id_pergunta'=>(int)$linha['id_pergunta'], 
                            'pergunta'=>$linha['pergunta'], 
                            'texto_ajuda'=>$linha['texto_ajuda'], 
                            'e_obrigatoria'=>(int)$linha['e_obrigatoria'], 
                            'tipo_entrada'=>$linha['tipo_entrada']
                        ];
                    } else {
                        foreach($perguntas as $pergunta) {
                            if($pergunta->id_pergunta!==(int)$linha['id_pergunta']) {
                                $existePergunta = false;
                            } else {
                                $existePergunta = true; 
                                break;
                            }
                        }

                        if(!$existePergunta) {
                            $perguntas[] = (object) [
                                'id_pergunta'=>(int)$linha['id_pergunta'], 
                                'pergunta'=>$linha['pergunta'], 
                                'texto_ajuda'=>$linha['texto_ajuda'], 
                                'e_obrigatoria'=>(int)$linha['e_obrigatoria'], 
                                'tipo_entrada'=>$linha['tipo_entrada']
                            ];
                        }
                    }

                    if(count($perguntasAlternativas)===0) {
                        $perguntasAlternativas[] = (object) [
                            'id_pergunta'=>(int)$linha['id_pergunta'], 
                            'id_alternativa'=>(int)$linha['id_alternativa'], 
                            'ordem_perguntas_alternativa'=>(int)$linha['ordem_perguntas_alternativa']
                        ];
                    } else {
                        foreach($perguntasAlternativas as $perguntasAlternativa) {
                            if($perguntasAlternativa->id_pergunta===(int)$linha['id_pergunta']) {
                                if($perguntasAlternativa->id_alternativa!==(int)$linha['id_alternativa']) {
                                    $existePerguntasAlternativas = false;
                                } else {
                                    $existePerguntasAlternativas = true; 
                                    break;
                                }
                            } else {
                                $existePerguntasAlternativas = false;
                            }
                        }

                        if(!$existePerguntasAlternativas) {
                            $perguntasAlternativas[] = (object) [
                                'id_pergunta'=>(int)$linha['id_pergunta'], 
                                'id_alternativa'=>(int)$linha['id_alternativa'], 
                                'ordem_perguntas_alternativa'=>(int)$linha['ordem_perguntas_alternativa']
                            ];
                        }
                    }

                    if(count($campos)===0) {
                        $campos[] = (object) [
                            'id_pergunta'=>(int)$linha['id_pergunta'], 
                            'id_alternativa'=>(int)$linha['id_alternativa'], 
                            'alternativa'=>$linha['alternativa'], 
                            'e_alternativa'=>(int)$linha['e_alternativa'], 
                            'e_obrigatoria'=>(int)$linha['e_obrigatoria'], 
                            'tipo_entrada'=>$linha['tipo_entrada'],
                            'id_marcador'=>(int)$linha['id_marcador']
                        ];
                    } else {
                        foreach($campos as $campo) {
                            if($campo->id_pergunta===(int)$linha['id_pergunta']) {
                                if($campo->id_alternativa!==(int)$linha['id_alternativa']) {
                                    $existeCampo = false;
                                } else {
                                    $existeCampo = true; 
                                    break;
                                }
                            } else {
                                $existeCampo = false;
                            }
                        }

                        if(!$existeCampo) {
                            $campos[] = (object) [
                                'id_pergunta'=>(int)$linha['id_pergunta'], 
                                'id_alternativa'=>(int)$linha['id_alternativa'], 
                                'alternativa'=>$linha['alternativa'], 
                                'e_alternativa'=>(int)$linha['e_alternativa'], 
                                'e_obrigatoria'=>(int)$linha['e_obrigatoria'], 
                                'tipo_entrada'=>$linha['tipo_entrada'],
                                'id_marcador'=>(int)$linha['id_marcador']
                            ];
                        }
                    }
                    
                    if(count($marcadores)===0) {
                        $marcadores[] = (object) [
                            'id_marcador'=>(int)$linha['id_marcador'],
                            'id_pergunta'=>(int)$linha['id_pergunta'],
                            'marcador'=>$linha['marcador']
                        ];
                    } else {
                        foreach($marcadores as $marcador) {
                            if($marcador->id_marcador===(int)$linha['id_marcador']) {
                                if($marcador->id_pergunta!==(int)$linha['id_pergunta']) {
                                    $existeMarcador = false;
                                } else {
                                    $existeMarcador = true; 
                                    break;
                                }
                            } else {
                                $existeMarcador = false;
                            }
                        }
                        
                        if(!$existeMarcador) {
                            $marcadores[] = (object) [
                                'id_marcador'=>(int)$linha['id_marcador'],
                                'id_pergunta'=>(int)$linha['id_pergunta'],
                                'marcador'=>$linha['marcador']
                            ];
                        }
                    } 
                }
            }

            function ordenarGrupos($a, $b) {
                if($a->ordem_grupo===$b->ordem_grupo) {
                    return 0;
                }

                if($a->ordem_grupo<$b->ordem_grupo && $a->ordem_grupo===0) {
                    return 1;
                } else {
                    return ($a->ordem_grupo>$b->ordem_grupo && $b->ordem_grupo>0)?1:-1;
                }
            }

            function ordenarPerguntasRespostasPadrao($a, $b) {
                if($a->ordem_perguntas_alternativa===$b->ordem_perguntas_alternativa) {
                    return 0;
                }

                return ($a->ordem_perguntas_alternativa<$b->ordem_perguntas_alternativa)?-1:1;
            }

            function ordenarOrdemPerguntas($a, $b) {
                if($a->ordem_disciplinas_pergunta===$b->ordem_disciplinas_pergunta) {
                    return 0;
                }

                return ($a->ordem_disciplinas_pergunta<$b->ordem_disciplinas_pergunta)?-1:1;
            }

            function ordenarDisciplinas($a, $b) {
                return strcmp($a->disciplina, $b->disciplina);
            }

            usort($grupos, 'ordenarGrupos');
            usort($ordemPerguntas, 'ordenarOrdemPerguntas');
            usort($disciplinas, 'ordenarDisciplinas');
            usort($perguntasAlternativas, 'ordenarPerguntasRespostasPadrao');

            $primeiroGrupoImpresso = false; 
            $campoRadio = $campoCheckbox = $campoTextArea = $campoText = $idCampo = 1; 
            $conteudoD = '';
            $numeroDaPergunta = 1;
            $blocosQtdPerguntas = [];
            $blocosPorcentagem = [];
            $ordemObrigatorio = 1;

            $totalGrupos = count($grupos);
            $totalDisciplinas = count($disciplinas);

            $divisoesFormulario = $totalDisciplinas+($totalGrupos-1);
            $divisaoAtual = 1;
            $idAviso = 1;

            foreach($grupos as $grupo) {
                if($grupo->idGrupo>1) {
                    if(!$primeiroGrupoImpresso) {
                        $conteudo .= '<div class="avaliacao-perguntas avaliacao-perguntas-aparentes">';
                        $primeiroGrupoImpresso = true;
                    } else {
                        $conteudo.='<div class="avaliacao-perguntas">';
                    }

                    $conteudo.='<span class="titulo">' . $grupo->grupo . '</span>'
                        . '<span class="descricao">Você responderá questões referentes a(ao) ' . $grupo->grupo . '</span>'
                        . '<span class="dica"><span class="dica-destaque">*</span>&nbsp;Resposta Obrigatória</span>';

                    $qtdPerguntas = 0;
                    foreach($ordemPerguntas as $ordemPergunta) {
                        if($grupo->idGrupo===$ordemPergunta->idGrupo) {
                            foreach($perguntas as $pergunta) {
                                if($ordemPergunta->id_pergunta===$pergunta->id_pergunta) {
                                    $perguntaObrigatoria = $pergunta->e_obrigatoria?'<span style="color:red;">&nbsp;*</span>':'';
                                    $conteudo.='<div class="avaliacao-linha">';
                                    if($pergunta->e_obrigatoria===1) {
                                        $conteudo.='<div class="avaliacao-coluna"><span id="aviso' . $idAviso . '" class="avaliacao-obrigatorio" data-ordem="' . $ordemObrigatorio . '">Resposta Obrigatória</span></div>';
                                        $ordemObrigatorio++;
                                    }
                                    
                                    $conteudo.='<div class="avaliacao-coluna"><span class="avaliacao-pergunta-termos">' . $numeroDaPergunta . '. ' . $pergunta->pergunta . $perguntaObrigatoria . '</span></div>';

                                    if(!empty($pergunta->texto_ajuda)) {
                                        $conteudo.='<div class="avaliacao-coluna"><span class="avaliacao-pergunta-texto-ajuda">' . $pergunta->texto_ajuda . '</span></div>';
                                    }

                                    $obrigatoria = $pergunta->e_obrigatoria?'obrigatorio':'';
                                    $camposMarcadores = [];
                                    $incCampoRadio = true;
                                        
                                    foreach($perguntasAlternativas as $perguntasAlternativa) {
                                        if($perguntasAlternativa->id_pergunta===$pergunta->id_pergunta) {
                                            foreach($campos as $campo) {
                                                if($campo->id_pergunta===$perguntasAlternativa->id_pergunta && $campo->id_alternativa===$perguntasAlternativa->id_alternativa) {
                                                    if($campo->tipo_entrada!=='tabela') {
                                                        $conteudo.='<div class="avaliacao-coluna">';
                                                    }
                                                    
                                                    if($campo->tipo_entrada==='radio') {
                                                        $conteudo.='<input data-id-aviso="aviso' . $idAviso . '" id="r' . $idCampo . '" type="radio" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="0" data-marcador="' . $campo->id_marcador . '" class="radio ' 
                                                            . $obrigatoria
                                                            . '" name="r' . $campoRadio . '" data-correspondentes="' . $divisaoAtual . '" value="' . $campo->id_alternativa . '" /><label class="radio-rotulo" for="r' . $idCampo . '">'
                                                            . '<span class="radio-rotulo-circulo"><span class="radio-rotulo-circulo-marcado"></span></span>'
                                                            . '<span class="radio-rotulo-termos">' . $campo->alternativa . '</span>'
                                                            . '</label>';
                                                    } else if($campo->tipo_entrada==='checkbox') {
                                                        $conteudo.='<input data-id-aviso="aviso' . $idAviso . '" id="c' . $idCampo . '" type="checkbox" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="0" data-marcador="' . $campo->id_marcador . '" class="checkbox ' 
                                                            . $obrigatoria
                                                            . '" name="cc' . $campoCheckbox . '[]" data-correspondentes="' . $divisaoAtual . '" value="' . $campo->id_alternativa . '" />'
                                                            . '<label class="checkbox-rotulo checkbox-rotulo-primario" for="c' . $idCampo . '">' 
                                                            . $campo->alternativa . '</label>';
                                                    } else if($campo->tipo_entrada==='text') {
                                                        $conteudo.='<input type="text" maxlength="1000" data-id-aviso="aviso' . $idAviso . '" id="t' . $idCampo . '" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="0" data-marcador="' . $campo->id_marcador . '" class="campo-secundario ' . $obrigatoria . '" name="t' . $campoText . '" data-correspondentes="' . $divisaoAtual . '" />';
                                                    } else if($campo->tipo_entrada==='textarea') {
                                                        $conteudo.='<textarea maxlength="2000" data-id-aviso="aviso' . $idAviso . '" id="ta' . $idCampo . '" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="0" data-marcador="' . $campo->id_marcador . '" class="campo-textarea-primario ' . $obrigatoria . '" name="ta' . $campoTextArea . '" data-correspondentes="' . $divisaoAtual . '"></textarea>';
                                                    } else if($campo->tipo_entrada==='tabela') {
                                                        $camposMarcadores[] = (object) [
                                                            'id_pergunta'=>$campo->id_pergunta, 
                                                            'id_alternativa'=>$campo->id_alternativa, 
                                                            'alternativa'=>$campo->alternativa, 
                                                            'e_alternativa'=>$campo->e_alternativa, 
                                                            'e_obrigatoria'=>$campo->e_obrigatoria, 
                                                            'tipo_entrada'=>$campo->tipo_entrada,
                                                            'id_marcador'=>$campo->id_marcador
                                                        ];
                                                            
                                                        $incCampoRadio = false;
                                                    }
                                                        
                                                    if($campo->tipo_entrada!=='tabela') {
                                                        $campoTextArea++; 
                                                        $campoText++; 
                                                        $idCampo++;
                                                        $conteudo .= '</div>';
                                                    }
                                                }
                                            } 
                                        }
                                    } 
                                        
                                    if(count($camposMarcadores)>0) {
                                        $alternativasImpressas = false;
                                        $conteudo.='<div class="avaliacao-coluna"><div class="tabela-wrapper">'
                                            . '<table class="tabela tabela-primaria">';
                                        $pTabelaBase = '<thead><tr><th></th>';
                                        foreach($marcadores as $marcador) {
                                            if($marcador->id_pergunta==$pergunta->id_pergunta) {
                                                if(!$alternativasImpressas) {
                                                    foreach($camposMarcadores as $camposMarcador) {
                                                        $pTabelaBase.= '<th>' .$camposMarcador->alternativa . '</th>';
                                                    }

                                                    $pTabelaBase.='</tr></thead><tbody><tr><th>' . $marcador->marcador . '</th>';

                                                    foreach($camposMarcadores as $camposMarcador) {
                                                        $pTabelaBase.='<td><input data-id-aviso="aviso' . $idAviso . '" id="tr' . $idCampo . '" type="radio" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="' . $disciplina->idDisciplina . '" data-marcador="' . $marcador->id_marcador . '" class="radio ' 
                                                            . $obrigatoria
                                                            . '" name="tr' . $campoRadio . '" data-correspondentes="' . $divisaoAtual . '" value="' . $camposMarcador->id_alternativa . '" />'
                                                            . '<label class="radio-rotulo" for="tr' . $idCampo . '">'
                                                            . '<span class="radio-rotulo-circulo"><span class="radio-rotulo-circulo-marcado"></span></span>'
                                                            . '</label></td>';
                                                        $idCampo++;
                                                    }

                                                    $pTabelaBase.='</tr>';
                                                    $alternativasImpressas = true;
                                                } else {
                                                    $pTabelaBase.= '<tr><th>' . $marcador->marcador . '</th>';

                                                    foreach($camposMarcadores as $camposMarcador) {
                                                        $pTabelaBase.='<td><input data-id-aviso="aviso' . $idAviso . '" id="tr' . $idCampo . '" type="radio" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="' . $disciplina->idDisciplina . '" data-marcador="' . $marcador->id_marcador . '" class="radio ' 
                                                            . $obrigatoria
                                                            . '" name="tr' . $campoRadio . '" data-correspondentes="' . $divisaoAtual . '" value="' . $camposMarcador->id_alternativa . '" />'
                                                            . '<label class="radio-rotulo" for="tr' . $idCampo . '">'
                                                            . '<span class="radio-rotulo-circulo"><span class="radio-rotulo-circulo-marcado"></span></span>'
                                                            . '</label></td>';
                                                        $idCampo++;
                                                    }

                                                    $pTabelaBase.='</tr>';
                                                }

                                                $campoRadio++;
                                            }
                                        }
                                            
                                        $conteudo.= $pTabelaBase . '</tbody></table></div></div>';
                                    }
                                        
                                    if($incCampoRadio) {
                                        $campoRadio++; 
                                    }
                                    
                                    $idAviso++; 
                                    $campoRadio++; 
                                    $campoCheckbox++; 
                                    $numeroDaPergunta++; 
                                    $qtdPerguntas++;
                                    $conteudo .= '</div>';
                                }
                            }
                        }
                    } 
                    
                    array_push($blocosQtdPerguntas, $qtdPerguntas);

                    $conteudo.='<div class="avaliacao-linha"><div class="avaliacao-coluna"><div class="porcentagem">'
                        . '<div class="porcentagem-preenchimento"><div class="porcentagem-preenchida"></div></div>'
                        . '<div class="porcentagem-valor">'
                        . '<span class="porcentagem-valor-termos"><span class="porcentagem-valor-destaque"></span>&nbsp;concluída</span>'
                        . '</div>'
                        . '</div></div></div>';

                    if($divisaoAtual===1 && $divisoesFormulario>1) {
                        $conteudo.='<div class="avaliacao-linha">'
                        . '<div class="avaliacao-coluna">'
                        . '<button class="botao botao-primario botao-avaliacao-continuar" data-correspondentes="' . $divisaoAtual . '" type="button">Continuar</button>'
                        . '</div></div>';
                    } else if($divisaoAtual>1 && $divisoesFormulario>$divisaoAtual) {
                        $conteudo.='<div class="avaliacao-linha">'
                        . '<div class="avaliacao-coluna">'
                        . '<button class="botao botao-primario botao-avaliacao-voltar" type="button">Voltar</button>'
                        . '<button class="botao botao-primario botao-avaliacao-continuar" data-correspondentes="' . $divisaoAtual . '" type="button">Continuar</button>'
                        . '</div></div>';
                    } else if($divisaoAtual>1 && $divisoesFormulario===$divisaoAtual) {
                        $conteudo.='<div class="avaliacao-linha">'
                        . '<div class="avaliacao-coluna">'
                        . '<button class="botao botao-primario botao-avaliacao-voltar" type="button">Voltar</button>'
                        . '<button id="botao-avaliacao-enviar" class="botao botao-primario botao-avaliacao-enviar" data-correspondentes="' . $divisaoAtual . '" type="button">Enviar</button>'
                        . '</div></div>';
                    } else if($divisaoAtual===1 && $divisoesFormulario===1) {
                        $conteudo.='<div class="avaliacao-linha">'
                        . '<div class="avaliacao-coluna">'
                        . '<button id="botao-avaliacao-enviar" class="botao botao-primario botao-avaliacao-enviar" data-correspondentes="' . $divisaoAtual . '" type="button">Enviar</button>'
                        . '</div></div>';
                    }

                    $conteudo.='</div>';
                    $divisaoAtual++;

                } else if($grupo->idGrupo===1) {
                    foreach($disciplinas as $disciplina) {
                        $qtdPerguntas = 0;
                        if(!$primeiroGrupoImpresso) {
                            $conteudoD.='<div class="avaliacao-perguntas avaliacao-perguntas-aparentes">';
                            $primeiroGrupoImpresso = true;
                        } else {
                            $conteudoD.='<div class="avaliacao-perguntas">';
                        }

                        $conteudoD.='<span class="titulo">' . $disciplina->disciplina . '</span>'
                        . '<span class="descricao">Você responderá questões referentes a Disciplina ' . $disciplina->disciplina . '</span>'
                        . '<span class="dica"><span class="dica-destaque">*</span>&nbsp;Resposta Obrigatória</span>';

                        foreach($ordemPerguntas as $ordemPergunta) {
                            if($grupo->idGrupo===$ordemPergunta->idGrupo) {
                                foreach($perguntas as $pergunta) {
                                    if($ordemPergunta->id_pergunta===$pergunta->id_pergunta && $ordemPergunta->idDisciplina===$disciplina->idDisciplina) {
                                        $perguntaObrigatoria=$pergunta->e_obrigatoria===1?'<span style="color:red;">&nbsp;*</span>':'';
                                        $conteudoD.='<div class="avaliacao-linha">';
                                        if($pergunta->e_obrigatoria===1) {
                                            $dataErros = $pergunta->tipo_entrada==='tabela'?'data-erros=""':'';
                                            $conteudoD.='<div class="avaliacao-coluna"><span id="aviso' . $idAviso . '" class="avaliacao-obrigatorio" data-ordem="' . $ordemObrigatorio . '" ' . $dataErros . '>Resposta Obrigatória</span></div>';
                                            $ordemObrigatorio++;
                                        }

                                        $conteudoD.='<div class="avaliacao-coluna"><span class="avaliacao-pergunta-termos">' . $numeroDaPergunta . '. ' . $pergunta->pergunta . $perguntaObrigatoria . '</span></div>';
                                        if(!empty($pergunta->texto_ajuda)) {
                                            $conteudoD.='<div class="avaliacao-coluna"><span class="avaliacao-pergunta-texto-ajuda">' . $pergunta->texto_ajuda . '</span></div>';
                                        }

                                        $obrigatoria = $pergunta->e_obrigatoria?'obrigatorio':'';
                                        
                                        $camposMarcadores = [];
                                        $incCampoRadio = true;

                                        foreach($perguntasAlternativas as $perguntasAlternativa) {
                                            if($perguntasAlternativa->id_pergunta===$pergunta->id_pergunta) {
                                                foreach($campos as $campo) {
                                                    if($campo->id_pergunta===$perguntasAlternativa->id_pergunta && $campo->id_alternativa===$perguntasAlternativa->id_alternativa) {
                                                        if($campo->tipo_entrada!=='tabela') {
                                                            $conteudoD.='<div class="avaliacao-coluna">';
                                                        }
                                                        
                                                        if($campo->tipo_entrada==='radio') {
                                                            $conteudoD.='<input data-id-aviso="aviso' . $idAviso . '" id="r' . $idCampo . '" type="radio" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="' . $disciplina->idDisciplina . '" data-marcador="' . $campo->id_marcador . '" class="radio ' 
                                                                . $obrigatoria
                                                                . '" name="r' . $campoRadio . '" data-correspondentes="' . $divisaoAtual . '" value="' . $campo->id_alternativa . '" />'
                                                                . '<label class="radio-rotulo" for="r' . $idCampo . '">'
                                                                . '<span class="radio-rotulo-circulo"><span class="radio-rotulo-circulo-marcado"></span></span>'
                                                                . '<span class="radio-rotulo-termos">' . $campo->alternativa . '</span></label>';
                                                        } else if($campo->tipo_entrada==='checkbox') {
                                                            $conteudoD.='<input data-id-aviso="aviso' . $idAviso . '" id="c' . $idCampo . '" type="checkbox" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="' . $disciplina->idDisciplina . '" data-marcador="' . $campo->id_marcador . '" class="checkbox ' 
                                                                . $obrigatoria
                                                                . '" name="cc' . $campoCheckbox . '[]" data-correspondentes="' . $divisaoAtual . '" value="' . $campo->id_alternativa . '" /><label class="checkbox-rotulo checkbox-rotulo-primario" for="c' . $idCampo . '">'
                                                                . $campo->alternativa . '</label>';
                                                        } else if($campo->tipo_entrada==='text') {
                                                            $conteudoD.='<input type="text" maxlength="1000" data-id-aviso="aviso' . $idAviso . '" id="t' . $idCampo . '" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="' . $disciplina->idDisciplina . '" data-marcador="' . $campo->id_marcador . '" class="campo-secundario ' . $obrigatoria . '" name="t' . $campoText . '" data-correspondentes="' . $divisaoAtual . '" />';
                                                        } else if($campo->tipo_entrada==='textarea') {
                                                            $conteudoD.='<textarea maxlength="2000" data-id-aviso="aviso' . $idAviso . '" id="ta' . $idCampo . '" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="' . $disciplina->idDisciplina . '" data-marcador="' . $campo->id_marcador . '" class="campo-textarea-primario ' . $obrigatoria . '" name="ta' . $campoTextArea . '" data-correspondentes="' . $divisaoAtual . '"></textarea>';
                                                        } else if($campo->tipo_entrada==='tabela') {
                                                            $camposMarcadores[] = (object) [
                                                                'id_pergunta'=>$campo->id_pergunta, 
                                                                'id_alternativa'=>$campo->id_alternativa, 
                                                                'alternativa'=>$campo->alternativa, 
                                                                'e_alternativa'=>$campo->e_alternativa, 
                                                                'e_obrigatoria'=>$campo->e_obrigatoria, 
                                                                'tipo_entrada'=>$campo->tipo_entrada,
                                                                'id_marcador'=>$campo->id_marcador
                                                            ];
                                                            
                                                            $incCampoRadio = false;
                                                        }
                                                        
                                                        if($campo->tipo_entrada!=='tabela') {
                                                            $campoTextArea++; 
                                                            $campoText++; 
                                                            $idCampo++;
                                                            $conteudoD .= '</div>';
                                                        }
                                                    }
                                                }
                                            }
                                        } 
                                        
                                        if(count($camposMarcadores)>0) {
                                            $alternativasImpressas = false;
                                            $conteudoD.='<div class="avaliacao-coluna"><div class="tabela-wrapper">'
                                                . '<table class="tabela tabela-primaria">';
                                            $pTabelaBase = '<thead><tr><th></th>';
                                            foreach($marcadores as $marcador) {
                                                if($marcador->id_pergunta==$pergunta->id_pergunta) {
                                                    if(!$alternativasImpressas) {
                                                        foreach($camposMarcadores as $camposMarcador) {
                                                            $pTabelaBase.= '<th>' .$camposMarcador->alternativa . '</th>';
                                                        }
                                                        
                                                        $pTabelaBase.='</tr></thead><tbody><tr><th>' . $marcador->marcador . '</th>';
                                                        
                                                        foreach($camposMarcadores as $camposMarcador) {
                                                            $pTabelaBase.='<td><input data-id-aviso="aviso' . $idAviso . '" id="tr' . $idCampo . '" type="radio" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="' . $disciplina->idDisciplina . '" data-marcador="' . $marcador->id_marcador . '" class="radio ' 
                                                                . $obrigatoria
                                                                . '" name="tr' . $campoRadio . '" data-correspondentes="' . $divisaoAtual . '" value="' . $camposMarcador->id_alternativa . '" />'
                                                                . '<label class="radio-rotulo" for="tr' . $idCampo . '">'
                                                                . '<span class="radio-rotulo-circulo"><span class="radio-rotulo-circulo-marcado"></span></span>'
                                                                . '</label></td>';
                                                            $idCampo++;
                                                        }
                                                        
                                                        $pTabelaBase.='</tr>';
                                                        $alternativasImpressas = true;
                                                    } else {
                                                        $pTabelaBase.= '<tr><th>' . $marcador->marcador . '</th>';
                                                        
                                                        foreach($camposMarcadores as $camposMarcador) {
                                                            $pTabelaBase.='<td><input data-id-aviso="aviso' . $idAviso . '" id="tr' . $idCampo . '" type="radio" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="' . $disciplina->idDisciplina . '" data-marcador="' . $marcador->id_marcador . '" class="radio ' 
                                                                . $obrigatoria
                                                                . '" name="tr' . $campoRadio . '" data-correspondentes="' . $divisaoAtual . '" value="' . $camposMarcador->id_alternativa . '" />'
                                                                . '<label class="radio-rotulo" for="tr' . $idCampo . '">'
                                                                . '<span class="radio-rotulo-circulo"><span class="radio-rotulo-circulo-marcado"></span></span>'
                                                                . '</label></td>';
                                                            $idCampo++;
                                                        }
                                                        
                                                        $pTabelaBase.='</tr>';
                                                    }
                                                    
                                                    $campoRadio++;
                                                }
                                            }
                                            
                                            $conteudoD.= $pTabelaBase . '</tbody></table></div></div>';
                                        }
                                        
                                        if($incCampoRadio) {
                                            $campoRadio++; 
                                        } 
                                        
                                        $idAviso++; 
                                        $campoCheckbox++; 
                                        $numeroDaPergunta++; 
                                        $qtdPerguntas++;
                                        $conteudoD.='</div>';
                                    }
                                }
                            }
                        }

                        array_push($blocosQtdPerguntas, $qtdPerguntas);

                        $conteudoD.='<div class="avaliacao-linha"><div class="avaliacao-coluna"><div class="porcentagem">'
                            . '<div class="porcentagem-preenchimento"><div class="porcentagem-preenchida"></div></div>'
                            . '<div class="porcentagem-valor">'
                            . '<span class="porcentagem-valor-termos"><span class="porcentagem-valor-destaque"></span>&nbsp;concluída</span>'
                            . '</div>'
                            . '</div></div></div>';

                        if($divisaoAtual===1 && $divisoesFormulario>1) {
                            $conteudoD.='<div class="avaliacao-linha">'
                            . '<div class="avaliacao-coluna">'
                            . '<button class="botao botao-primario botao-avaliacao-continuar" data-correspondentes="' . $divisaoAtual . '" type="button">Continuar</button>'
                            . '</div></div>';
                        } else if($divisaoAtual>1 && $divisoesFormulario>$divisaoAtual) {
                            $conteudoD.='<div class="avaliacao-linha">'
                            . '<div class="avaliacao-coluna">'
                            . '<button class="botao botao-primario botao-avaliacao-voltar" type="button">Voltar</button>'
                            . '<button class="botao botao-primario botao-avaliacao-continuar" data-correspondentes="' . $divisaoAtual . '" type="button">Continuar</button>'
                            . '</div></div>';
                        } else if($divisaoAtual>1 && $divisoesFormulario===$divisaoAtual) {
                            $conteudoD.='<div class="avaliacao-linha">'
                            . '<div class="avaliacao-coluna">'
                            . '<button class="botao botao-primario botao-avaliacao-voltar" type="button">Voltar</button>'
                            . '<button id="botao-avaliacao-enviar" class="botao botao-primario botao-avaliacao-enviar" data-correspondentes="' . $divisaoAtual . '" type="button">Enviar</button>'
                            . '</div></div>';
                        } else if($divisaoAtual===1 && $divisoesFormulario===1) {
                            $conteudoD.='<div class="avaliacao-linha">'
                            . '<div class="avaliacao-coluna">'
                            . '<button id="botao-avaliacao-enviar" class="botao botao-primario botao-avaliacao-enviar" data-correspondentes="' . $divisaoAtual . '" type="button">Enviar</button>'
                            . '</div></div>';
                        }

                        $conteudoD.='</div>';            
                        $divisaoAtual++;
                    }
                } 
            } 
            
            $conteudo = $conteudo . $conteudoD . '</form>';

            $qtdTotalPerguntas = array_sum($blocosQtdPerguntas); 
            $qtdPorcentagem = 0;

            for($i=0, $l=count($blocosQtdPerguntas); $i<$l; $i++) {
                $qtdPorcentagem += ($blocosQtdPerguntas[$i]*100)/$qtdTotalPerguntas;
                array_push($blocosPorcentagem, ceil($qtdPorcentagem));
            }

            $blocosPorcentagem[count($blocosPorcentagem)-1] = 100;
        } else {
            $conteudo='<div id="avaliacao-concluida"><span class="titulo">Desculpe mas o período de avaliação está encerrado.</span><span class="descricao">Clique no botão abaixo para ser redirecionado(a) para a página principal.</span><button type="button" title="Ir para a página principal" id="botao-avaliacao-concluida" class="botao botao-primario" onclick="confirmarConclusaoAvaliacao();">Ir para a página principal</button></div>';
        }
        
        mysqli_close($conexao);
    } else {
        header('Location: ../index.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta name="robots" content="noindex, nofollow" />
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="author" content="Yan Gabriel da Silva Machado" />
        <noscript><meta http-equiv="refresh" content="0; URL=../sem-js.html" /></noscript>
        <link rel="stylesheet" type="text/css" href="../css/base.css" />
        <!-- FAVICON[INICIO] -->
        <link rel="apple-touch-icon" sizes="57x57" href="../imagens/favicon/apple-icon-57x57.png" />
        <link rel="apple-touch-icon" sizes="60x60" href="../imagens/favicon/apple-icon-60x60.png" />
        <link rel="apple-touch-icon" sizes="72x72" href="../imagens/favicon/apple-icon-72x72.png" />
        <link rel="apple-touch-icon" sizes="76x76" href="../imagens/favicon/apple-icon-76x76.png" />
        <link rel="apple-touch-icon" sizes="114x114" href="../imagens/favicon/apple-icon-114x114.png" />
        <link rel="apple-touch-icon" sizes="120x120" href="../imagens/favicon/apple-icon-120x120.png" />
        <link rel="apple-touch-icon" sizes="144x144" href="../imagens/favicon/apple-icon-144x144.png" />
        <link rel="apple-touch-icon" sizes="152x152" href="../imagens/favicon/apple-icon-152x152.png" />
        <link rel="apple-touch-icon" sizes="180x180" href="../imagens/favicon/apple-icon-180x180.png" />
        <link rel="icon" type="image/png" sizes="192x192"  href="../imagens/favicon/android-icon-192x192.png" />
        <link rel="icon" type="image/png" sizes="32x32" href="../imagens/favicon/favicon-32x32.png" />
        <link rel="icon" type="image/png" sizes="96x96" href="../imagens/favicon/favicon-96x96.png" />
        <link rel="icon" type="image/png" sizes="16x16" href="../imagens/favicon/favicon-16x16.png" />
        <link rel="manifest" href="../imagens/favicon/manifest.json" />
        <meta name="msapplication-TileColor" content="#ffffff" />
        <meta name="msapplication-TileImage" content="../imagens/favicon/ms-icon-144x144.png" />
        <meta name="theme-color" content="#ffffff" />
        <!-- FAVICON[FIM] -->
        <title>Avaliação &#8250;&#8250; SAD</title>
        <!-- SCRIPTS[INICIO] -->
        <script>
            /* SEM SUPORTE[INICIO] */
            if(document.all) {
                window.location='../sem-suporte.html';
            }
            /* SEM SUPORTE[FIM] */
            
            /* EVITA QUE SEJA EXECUTADO EM UM IFRAME[INICIO] */
            if(window.self!==window.top) { 
                window.top.location.replace(window.location.href); 
            }
            /* EVITA QUE SEJA EXECUTADO EM UM IFRAME[FIM] */
        </script>
        <!-- SCRIPTS[FIM] -->
    </head>
    <body>
        <!-- CABECALHO[INICIO] -->
        <div id="cabecalho">
            <!-- MENU[INICIO] -->
            <a href="../index.php" class="ligacao ligacao-logotipo logotipo-movel">SAD</a>
            <div id="navegacao-container">
                <ul class="menu" id="navegacao">
                    <li class="menu-item"><span class="menu-item-termos" id="menu-toggle-fechar">Fechar Menu</span></li>
                    <li class="menu-item"><a href="../index.php" class="menu-link ligacao-logotipo">SAD</a></li>
                    <li class="menu-item"><a href="../index.php#iniciando-avaliacao" class="menu-link">Iniciando a Avaliação</a></li> 
                    <li class="menu-item"><a href="../index.php#sobre" class="menu-link">Sobre</a></li> 
                    <!--<li class="menu-item menu-item-especial" style="float:right"><button title="Entrar na Administração" id="botao-entrar" class="botao" type="button">Entrar</button></li>-->
                    <?php
                        if(!empty($nomeAvaliacao)) {
                            if(strlen($nomeAvaliacao)<51) {
                                echo '<li class="menu-item menu-item-movel" id="menu-item-avaliacao-nome"><span id="menu-item-termos-avaliacao-nome" title="' . $nomeAvaliacao . '">' . $nomeAvaliacao . '</span></li>';
                            } else {
                                echo '<li class="menu-item menu-item-movel" id="menu-item-avaliacao-nome"><span id="menu-item-termos-avaliacao-nome" title="' . $nomeAvaliacao . '">' . rtrim(substr($nomeAvaliacao, 0, 46)) . '...</span></li>';
                            }
                        }
                    ?>
                </ul>
            </div>
            <div class="menu-toggle" title="Menu" id="navegacao-toggle">
                <span class="menu-toggle-linha"></span>
                <span class="menu-toggle-linha"></span>
                <span class="menu-toggle-linha"></span>
            </div>
            <!-- MENU[FIM] -->
        </div>
        <!-- CABECALHO[FIM] -->

        <!-- CONTEUDO[INICIO] -->
        <div id="conteudo">
            <?php
                if(!empty($nomeAvaliacao)) {
                    echo '<span id="avaliacao-nome">' . $nomeAvaliacao . '</span>';
                }
            ?>
            <!-- AVALIACAO[INICIO] -->
            <?php 
                echo $conteudo;
            ?>
            <!-- AVALIACAO[FIM] -->
        </div>
        <!-- CONTEUDO[FIM] -->
        <div id="carregamento"></div>

        <!-- RODAPE[INICIO] -->
        <?php 
            require '../inclusoes/rodape.php';
            rodape('../');
        ?>
        <!-- RODAPE[FIM] -->

        <!-- FUNDOS[INICIO] -->
        <div id="fundo-primario"></div>
        <!-- FUNDOS[FIM] -->

        <!-- ENTRAR[INICIO] -->
        <?php //require '../inclusoes/entrar.php';?>
        <!-- ENTRAR[FIM] -->
        
        <!-- SCRIPTS[INICIO] -->
        <script src="../js/base.js"></script>
        <script>
        <?php
            /* ESCREVE ARRAY DISCIPLINAS PARA SER UTILIZADO NO JAVASCRIPT[INICIO] */
            $disc = '';
            foreach($dis as $idDisciplina) {
                $disc.=$idDisciplina . ', ';
            }
            
            if(!empty($disc)) {
                $disc = substr_replace($disc, '', strripos($disc, ','));
                $disc = '{"disciplinas":[' . $disc . ']';
            }
            /* ESCREVE ARRAY DISCIPLINAS PARA SER UTILIZADO NO JAVASCRIPT[FIM] */
            
            
            /* ESCREVE PORCENTAGENS PARA SER UTILIZADO NO JAVASCRIPT[INICIO] */
            $jsPorcentagem = '';
            
            foreach($blocosPorcentagem as $p) {
                $jsPorcentagem.= $p . ', ';
            }
               
            if(!empty($jsPorcentagem)) {
                $jsPorcentagem = substr_replace($jsPorcentagem, '', strripos($jsPorcentagem, ','));
                echo $jsPorcentagem = 'var porcentagemBlocosAvaliacao=[' . $jsPorcentagem . '];'; 
            }
            /* ESCREVE PORCENTAGENS PARA SER UTILIZADO NO JAVASCRIPT[FIM] */
        ?>
            atribuirPorcentagemBlocosAvaliacao();
            
            /* ATIVA BOTAO AVALIACAO ENVIAR[INICIO] */
            var botaoAvaliacaoEnviar = document.getElementById('botao-avaliacao-enviar');
            if(botaoAvaliacaoEnviar) {
                botaoAvaliacaoEnviar.onclick=function(){enviarAvaliacao(this, '<?php 
                echo $disc;?>', '<?php echo $disponibilidade;?>');};
            }
            /* ATIVA BOTAO AVALIACAO ENVIAR[FIM] */
        </script>
        <!-- SCRIPTS[FIM] -->
    </body>
</html>
<?php
    ob_end_flush();