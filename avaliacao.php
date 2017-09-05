<?php
    setlocale(LC_ALL, 'pt_BR', "pt_BR.iso-8859-1", 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    session_start();

    if(isset($_SESSION['avaliacao-iniciada'])) {
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['avaliacao-iniciada']=true;
    }
    
    header('Content-Type:text/html; charset=UTF-8');
    
    $dis=[]; $comandoSql=''; $conteudo='<form name="avaliacao" action="" method="post">';
    
    if(filter_input(INPUT_SERVER, 'REQUEST_METHOD')=='POST') {
        if(isset($_POST['disciplinas'])) {
            foreach($_POST['disciplinas'] as $d=>$v) {
                $dis[] = (int)$v;
            }
        } sort($dis);
        
        $disponibilidade = filter_input(INPUT_POST, 'disponibilidade', FILTER_SANITIZE_NUMBER_INT);
        
        include 'inclusoes/db/config.php';
        
        $conexao=mysqli_connect(HOST, USER, PASSWORD, DB);
        $comandoSql.='SELECT a.ordem AS ordem_disciplinas_pergunta, b.id_disciplina, b.disciplina, c.id_pergunta, c.pergunta, c.texto_ajuda, c.tipo_entrada, c.e_alternativa, c.e_obrigatoria, d.prioridade AS ordem_perguntas_alternativa, e.id_alternativa, e.alternativa, f.id_grupo, f.ordem AS ordem_grupo, f.grupo FROM '
                    . 'disciplinas_perguntas a INNER JOIN disciplinas b ON a.id_disciplina=b.id_disciplina '
                    . 'INNER JOIN perguntas c ON a.id_pergunta=c.id_pergunta INNER JOIN perguntas_alternativas d ON c.id_pergunta=d.id_pergunta '
                    . 'INNER JOIN alternativas e ON d.id_alternativa=e.id_alternativa '
                    . 'INNER JOIN grupos f ON c.id_grupo=f.id_grupo WHERE b.id_disciplina>1 AND (';

        $primeiroAcesso=true; $grupos=$disciplinas=$ordemPerguntas=$perguntas=$perguntasAlternativas=$campos=[];
        $existeGrupo=$existeOrdemPergunta=$existePergunta=$existePerguntasAlternativas=$existeCampo=true;
        $existeDisciplina=true;
        
        /* $ordemPerguntas É PARA GRUPOS DIFERENTES DO PADRÃO QUE POR SUA VEZ POSSUI ID=1 */
        
        foreach($dis as $d) {
            if($primeiroAcesso) {
                $comandoSql.='a.id_disciplina=' . $d; $primeiroAcesso=false;
            } else {
                $comandoSql.=' OR a.id_disciplina=' . $d;
            }
        }
        
        $comandoSql.=');';

        mysqli_set_charset($conexao, 'utf8');
        $consulta = mysqli_query($conexao, $comandoSql);

        if(mysqli_num_rows($consulta)>0) {
            while($linha = mysqli_fetch_array($consulta)) {
                if(count($grupos)===0) {
                    $grupos[]=(object)['idGrupo'=>(int)$linha['id_grupo'], 'ordem_grupo'=>(int)$linha['ordem_grupo'], 'grupo'=>$linha['grupo']];
                } else {
                    foreach($grupos as $grupo) {
                        if($grupo->idGrupo!==(int)$linha['id_grupo']) {
                            $existeGrupo=false;
                        } else {
                            $existeGrupo=true; break;
                        }
                    }

                    if($existeGrupo===false) {
                        $grupos[]=(object)['idGrupo'=>(int)$linha['id_grupo'], 'ordem_grupo'=>(int)$linha['ordem_grupo'], 'grupo'=>$linha['grupo']];
                    }
                }

                if(count($disciplinas)===0) {
                    $disciplinas[]=(object)['idDisciplina'=>(int)$linha['id_disciplina'], 'disciplina'=>$linha['disciplina'], 'idGrupo'=>(int)$linha['id_grupo']];
                } else {
                    foreach($disciplinas as $disciplina) {
                        if($disciplina->idDisciplina!==(int)$linha['id_disciplina']) {
                            $existeDisciplina=false;
                        } else {
                            $existeDisciplina=true; break;
                        }
                    }

                    if($existeDisciplina===false) {
                        $disciplinas[]=(object)['idDisciplina'=>(int)$linha['id_disciplina'], 'disciplina'=>$linha['disciplina'], 'idGrupo'=>(int)$linha['id_grupo']];
                    }
                }

                if(count($ordemPerguntas)===0) {
                    $ordemPerguntas[]=(object)['idDisciplina'=>(int)$linha['id_disciplina'], 'id_pergunta'=>(int)$linha['id_pergunta'], 'ordem_disciplinas_pergunta'=>(int)$linha['ordem_disciplinas_pergunta'], 'idGrupo'=>(int)$linha['id_grupo']];
                } else {
                    foreach($ordemPerguntas as $ordemPergunta) {
                        if($ordemPergunta->idGrupo===(int)$linha['id_grupo']) { 
                            if($ordemPergunta->idDisciplina===(int)$linha['id_disciplina']) {
                                    if($ordemPergunta->id_pergunta!==(int)$linha['id_pergunta']) {
                                        $existeOrdemPergunta=false;
                                    } else {
                                        $existeOrdemPergunta=true; break;
                                    }
                            } else {
                                if($ordemPergunta->id_pergunta!==(int)$linha['id_pergunta']) {
                                    $existeOrdemPergunta=false;
                                } else {
                                    if($ordemPergunta->idGrupo===1) {
                                       $existeOrdemPergunta=false; 
                                    } else {
                                        $existeOrdemPergunta=true; break;
                                    }
                                }
                            } 
                        } else {
                            if($ordemPergunta->id_pergunta!==(int)$linha['id_pergunta']) {
                                $existeOrdemPergunta=false;
                            } else {
                                $existeOrdemPergunta=true; break;
                            }
                        }
                    }

                    if($existeOrdemPergunta===false) {
                        $ordemPerguntas[]=(object)['idDisciplina'=>(int)$linha['id_disciplina'], 'id_pergunta'=>(int)$linha['id_pergunta'], 'ordem_disciplinas_pergunta'=>(int)$linha['ordem_disciplinas_pergunta'], 'idGrupo'=>(int)$linha['id_grupo']];
                    }
                }

                if(count($perguntas)===0) {
                    $perguntas[]=(object)['id_pergunta'=>(int)$linha['id_pergunta'], 'pergunta'=>$linha['pergunta'], 'texto_ajuda'=>$linha['texto_ajuda'], 'e_obrigatoria'=>(int)$linha['e_obrigatoria'], 'tipo_entrada'=>$linha['tipo_entrada']];
                } else {
                    foreach($perguntas as $pergunta) {
                        if($pergunta->id_pergunta!==(int)$linha['id_pergunta']) {
                            $existePergunta=false;
                        } else {
                            $existePergunta=true; break;
                        }
                    }

                    if($existePergunta===false) {
                        $perguntas[]=(object)['id_pergunta'=>(int)$linha['id_pergunta'], 'pergunta'=>$linha['pergunta'], 'texto_ajuda'=>$linha['texto_ajuda'], 'e_obrigatoria'=>(int)$linha['e_obrigatoria'], 'tipo_entrada'=>$linha['tipo_entrada']];
                    }
                }

                if(count($perguntasAlternativas)===0) {
                    $perguntasAlternativas[]=(object)['id_pergunta'=>(int)$linha['id_pergunta'], 'id_alternativa'=>(int)$linha['id_alternativa'], 'ordem_perguntas_alternativa'=>(int)$linha['ordem_perguntas_alternativa']];
                } else {
                    foreach($perguntasAlternativas as $perguntasAlternativa) {
                        if($perguntasAlternativa->id_pergunta===(int)$linha['id_pergunta']) {
                            if($perguntasAlternativa->id_alternativa!==(int)$linha['id_alternativa']) {
                                $existePerguntasAlternativas=false;
                            } else {
                                $existePerguntasAlternativas=true; break;
                            }
                        } else {
                            $existePerguntasAlternativas=false;
                        }
                    }

                    if($existePerguntasAlternativas===false) {
                        $perguntasAlternativas[]=(object)['id_pergunta'=>(int)$linha['id_pergunta'], 'id_alternativa'=>(int)$linha['id_alternativa'], 'ordem_perguntas_alternativa'=>(int)$linha['ordem_perguntas_alternativa']];
                    }
                }

                if(count($campos)===0) {
                    $campos[]=(object)['id_pergunta'=>(int)$linha['id_pergunta'], 'id_alternativa'=>(int)$linha['id_alternativa'], 'alternativa'=>$linha['alternativa'], 'e_alternativa'=>(int)$linha['e_alternativa'], 'e_obrigatoria'=>(int)$linha['e_obrigatoria'], 'tipo_entrada'=>$linha['tipo_entrada']];
                } else {
                    foreach($campos as $campo) {
                        /*if($campo->id_pergunta!==(int)$linha['id_pergunta'] && $campo->id_alternativa!==(int)$linha['id_alternativa']) {
                            $existeCampo=false;
                        } else {
                            $existeCampo=true; break;
                        }*/
                        if($campo->id_pergunta===(int)$linha['id_pergunta']) {
                            if($campo->id_alternativa!==(int)$linha['id_alternativa']) {
                                $existeCampo=false;
                            } else {
                                $existeCampo=true; break;
                            }
                        } else {
                            $existeCampo=false;
                        }
                    }

                    if($existeCampo===false) {
                        $campos[]=(object)['id_pergunta'=>(int)$linha['id_pergunta'], 'id_alternativa'=>(int)$linha['id_alternativa'], 'alternativa'=>$linha['alternativa'], 'e_alternativa'=>(int)$linha['e_alternativa'], 'e_obrigatoria'=>(int)$linha['e_obrigatoria'], 'tipo_entrada'=>$linha['tipo_entrada']];
                    }
                }
            }
        }

        /* ORDENAR GRUPOS VELHO[INICIO]
        function ordenarGrupos($a, $b) {
            if($a->ordem_grupo===$b->ordem_grupo) {
                return 0;
            }

            return ($a->ordem_grupo<$b->ordem_grupo)?-1:1;
        }
        * ORDENAR GRUPOS VELHO[FIM] */
        
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
        
        $primeiroGrupoImpresso=false; $campoRadio=$campoCheckbox=$campoTextArea=$campoText=$idCampo=1; $ultimoGrupo=count($grupos); $divisoesDoFormulario=0;
        $ultimoGrupoDisciplinas=count($disciplinas);
        $conteudoD='';
        $divisoesDoFormularioDisciplinas=0;
        $numeroDaPergunta=1;
        $porcentagemPerguntasGeral=0;
        $blocosQtdPerguntas=[];
        $blocosPorcentagem=[];
        
        foreach($grupos as $grupo) {
            if($grupo->idGrupo>1) {
                if($primeiroGrupoImpresso===false) {
                    $conteudo.='<div class="avaliacao-perguntas avaliacao-perguntas-aparentes">';
                } else {
                    $conteudo.='<div class="avaliacao-perguntas">';
                }

                $conteudo.='<span class="titulo">' . $grupo->grupo . '</span>
        <span class="descricao">Você responderá questões referentes a(ao) ' . $grupo->grupo . '</span><span class="dica"><span class="dica-destaque">*</span>&nbsp;Resposta Obrigatória</span>';
                
                
                $qtdPerguntas=0;
                foreach($ordemPerguntas as $ordemPergunta) {
                    if($grupo->idGrupo===$ordemPergunta->idGrupo) {
                        foreach($perguntas as $pergunta) {
                            if($ordemPergunta->id_pergunta===$pergunta->id_pergunta) {
                                $perguntaObrigatoria=$pergunta->e_obrigatoria?'<span style="color:red;">&nbsp;*</span>':'';
                                $conteudo.='<div class="avaliacao-linha">';
                                if($pergunta->e_obrigatoria===1) {
                                    $conteudo.='<div class="avaliacao-coluna"><span class="avaliacao-obrigatorio">Resposta Obrigatória</span></div>';
                                }
                                $conteudo.='<div class="avaliacao-coluna"><span class="avaliacao-pergunta-termos">' . $numeroDaPergunta . '. ' . $pergunta->pergunta . $perguntaObrigatoria . '</span></div>';
                                
                                if(empty($pergunta->texto_ajuda)==false) {
                                    $conteudo.='<div class="avaliacao-coluna"><span class="avaliacao-pergunta-texto-ajuda">' . $pergunta->texto_ajuda . '</span></div>';
                                }
                                
                                $obrigatoria=$pergunta->e_obrigatoria?'obrigatorio':'';
                                
                                foreach($perguntasAlternativas as $perguntasAlternativa) {
                                    if($perguntasAlternativa->id_pergunta===$pergunta->id_pergunta) {
                                        foreach($campos as $campo) {
                                            if($campo->id_pergunta===$perguntasAlternativa->id_pergunta && $campo->id_alternativa===$perguntasAlternativa->id_alternativa) {
                                                $conteudo.='<div class="avaliacao-coluna">';
                                                if($campo->tipo_entrada==='radio') {
                                                    $conteudo.='<label class="radio" for="r' . $idCampo . '">
                    <span class="radio-circulo"><span class="radio-circulo-marcado"></span></span>
                    <span class="radio-termos">' . $campo->alternativa . '</span>
                </label><input id="r' . $idCampo . '" type="radio" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="0" class="radio-item ' 
                                                            . $obrigatoria
                                                            . '" name="r' . $campoRadio . '" value="' . $campo->id_alternativa . '" />';
                                                } else if($campo->tipo_entrada==='checkbox') {
                                                    $conteudo.='<input id="c' . $idCampo . '" type="checkbox" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="0" class="checkbox ' 
                                                            . $obrigatoria
                                                            . '" name="cc' . $campoCheckbox . '[]" value="' . $campo->id_alternativa . '" />'
                                                            . '<label class="checkbox-rotulo checkbox-rotulo-primario" for="c' . $idCampo . '">' 
                                                            . $campo->alternativa . '</label>';
                                                } else if($campo->tipo_entrada==='text') {
                                                    $conteudo.='<input id="t' . $idCampo . '" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="0" class="campo-secundario ' . $obrigatoria . '" name="t' . $campoText . '" />';
                                                } else if($campo->tipo_entrada==='textarea') {
                                                    $conteudo.='<textarea id="ta' . $idCampo . '" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="0" class="campo-textarea-primario ' . $obrigatoria . '" name="ta' . $campoTextArea . '"></textarea>';
                                                }

                                                $campoTextArea++; $campoText++; $idCampo++;
                                                $conteudo.='</div>';
                                            }
                                        } 
                                    }
                                } 


                                $campoRadio++; $campoCheckbox++; $numeroDaPergunta++; $qtdPerguntas++;
                                $conteudo.='</div>';
                            }
                        }
                    }
                } $divisoesDoFormulario++;
                
                array_push($blocosQtdPerguntas, $qtdPerguntas);
                
                $conteudo.='<div class="avaliacao-linha"><div class="avaliacao-coluna"><div class="porcentagem">
			<div class="porcentagem-preenchimento"><div class="porcentagem-preenchida"></div></div>
			<div class="porcentagem-valor">
				<span class="porcentagem-valor-termos"><span class="porcentagem-valor-destaque"></span>&nbsp;concluída</span>
			</div>
		</div></div></div>';
                
                if($divisoesDoFormulario===1 && $ultimoGrupo>1) {
                    $conteudo.='<div class="avaliacao-linha">
                <div class="avaliacao-coluna">
                    <button class="botao botao-primario botao-avaliacao-continuar" type="button">Continuar</button>
                </div></div>';
                } else if($divisoesDoFormulario>1 && $divisoesDoFormulario<$ultimoGrupo) {
                    $conteudo.='<div class="avaliacao-linha">
                <div class="avaliacao-coluna">
                    <button class="botao botao-primario botao-avaliacao-voltar" type="button">Voltar</button>
                    <button class="botao botao-primario botao-avaliacao-continuar" type="button">Continuar</button>
                </div></div>';
                } else if($divisoesDoFormulario===$ultimoGrupo && $ultimoGrupo>1) {
                    $conteudo.='<div class="avaliacao-linha">
                <div class="avaliacao-coluna">
                    <button class="botao botao-primario botao-avaliacao-voltar" type="button">Voltar</button>
                    <button id="botao-avaliacao-enviar" class="botao botao-primario" type="button">Enviar</button>
                </div></div>';
                } else if($divisoesDoFormulario===$ultimoGrupo && $ultimoGrupo===1) {
                    $conteudo.='<div class="avaliacao-linha">
                <div class="avaliacao-coluna">
                    <button id="botao-avaliacao-enviar" class="botao botao-primario" type="button">Enviar</button>
                </div></div>';
                } $conteudo.='</div>';

            } else if($grupo->idGrupo===1) {
                foreach($disciplinas as $disciplina) {
                    $qtdPerguntas=0;
                    if($primeiroGrupoImpresso===false) {
                        $conteudoD.='<div class="avaliacao-perguntas avaliacao-perguntas-aparentes">';
                    } else {
                        $conteudoD.='<div class="avaliacao-perguntas">';
                    }

                    $conteudoD.='<span class="titulo">' . $disciplina->disciplina . '</span>
            <span class="descricao">Você responderá questões referentes a Disciplina ' . $disciplina->disciplina . '</span>'
            . '<span class="dica"><span class="dica-destaque">*</span>&nbsp;Resposta Obrigatória</span>';

                    foreach($ordemPerguntas as $ordemPergunta) {
                        if($grupo->idGrupo===$ordemPergunta->idGrupo) {
                            foreach($perguntas as $pergunta) {
                                if($ordemPergunta->id_pergunta===$pergunta->id_pergunta && $ordemPergunta->idDisciplina===$disciplina->idDisciplina) {
                                    $perguntaObrigatoria=$pergunta->e_obrigatoria===1?'<span style="color:red;">&nbsp;*</span>':'';
                                    $conteudoD.='<div class="avaliacao-linha">';
                                    if($pergunta->e_obrigatoria===1) {
                                        $conteudoD.='<div class="avaliacao-coluna"><span class="avaliacao-obrigatorio">Resposta Obrigatória</span></div>';
                                    }
                                    
                                    $conteudoD.='<div class="avaliacao-coluna"><span class="avaliacao-pergunta-termos">' . $numeroDaPergunta . '. ' . $pergunta->pergunta . $perguntaObrigatoria . '</span></div>';
                                    if(empty($pergunta->texto_ajuda)==false) {
                                        $conteudoD.='<div class="avaliacao-coluna"><span class="avaliacao-pergunta-texto-ajuda">' . $pergunta->texto_ajuda . '</span></div>';
                                    }
                                    
                                    $obrigatoria=$pergunta->e_obrigatoria?'obrigatorio':'';
                                    
                                    
                                    foreach($perguntasAlternativas as $perguntasAlternativa) {
                                        if($perguntasAlternativa->id_pergunta===$pergunta->id_pergunta) {
                                            foreach($campos as $campo) {
                                                if($campo->id_pergunta===$perguntasAlternativa->id_pergunta && $campo->id_alternativa===$perguntasAlternativa->id_alternativa) {
                                                    $conteudoD.='<div class="avaliacao-coluna">';
                                                    if($campo->tipo_entrada==='radio') {
                                                        $conteudoD.='<label class="radio" for="r' . $idCampo . '">
                        <span class="radio-circulo"><span class="radio-circulo-marcado"></span></span>
                        <span class="radio-termos">' . $campo->alternativa . '</span>
                    </label><input id="r' . $idCampo . '" type="radio" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="' . $disciplina->idDisciplina . '" class="radio-item ' 
                                                                . $obrigatoria
                                                                . '" name="r' . $campoRadio . '" value="' . $campo->id_alternativa . '" />';
                                                    } else if($campo->tipo_entrada==='checkbox') {
                                                        $conteudoD.='<input id="c' . $idCampo . '" type="checkbox" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="' . $disciplina->idDisciplina . '" class="checkbox ' 
                                                                . $obrigatoria
                                                                . '" name="cc' . $campoCheckbox . '[]" value="' . $campo->id_alternativa . '" /><label class="checkbox-rotulo checkbox-rotulo-primario" for="c' . $idCampo . '">
                                                        ' . $campo->alternativa . '</label>';
                                                        //echo $pergunta->id_pergunta;
                                                        //echo $disciplina->idDisciplina;
                                                        //echo '<br />';
                                                        //var_dump($disciplina);
                                                    } else if($campo->tipo_entrada==='text') {
                                                        $conteudoD.='<input id="t' . $idCampo . '" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="' . $disciplina->idDisciplina . '" class="campo-secundario ' . $obrigatoria . '" name="t' . $campoText . '" />';
                                                    } else if($campo->tipo_entrada==='textarea') {
                                                        $conteudoD.='<textarea id="ta' . $idCampo . '" data-pergunta="' . $pergunta->id_pergunta . '" data-disciplina="' . $disciplina->idDisciplina . '" class="campo-textarea-primario ' . $obrigatoria . '" name="ta' . $campoTextArea . '"></textarea>';
                                                    }

                                                    $campoTextArea++; $campoText++; $idCampo++;
                                                    $conteudoD.='</div>';
                                                }
                                            } 
                                        }
                                    } 

                                    $campoRadio++; $campoCheckbox++; $numeroDaPergunta++; $qtdPerguntas++;
                                    $conteudoD.='</div>';
                                }
                            }
                        }
                    } $divisoesDoFormularioDisciplinas++;
                    
                    array_push($blocosQtdPerguntas, $qtdPerguntas);

                    $conteudoD.='<div class="avaliacao-linha"><div class="avaliacao-coluna"><div class="porcentagem">
                            <div class="porcentagem-preenchimento"><div class="porcentagem-preenchida"></div></div>
                            <div class="porcentagem-valor">
                                    <span class="porcentagem-valor-termos"><span class="porcentagem-valor-destaque"></span>&nbsp;concluída</span>
                            </div>
                    </div></div></div>';
                    

                    if($divisoesDoFormulario===0 && $divisoesDoFormularioDisciplinas===1 && $ultimoGrupoDisciplinas>1) {
                        $conteudoD.='<div class="avaliacao-linha">
                    <div class="avaliacao-coluna">
                        <button class="botao botao-primario botao-avaliacao-continuar" type="button">Continuar</button>
                    </div></div>';
                    } else if($divisoesDoFormularioDisciplinas>=1 && $divisoesDoFormularioDisciplinas<$ultimoGrupoDisciplinas) {
                        $conteudoD.='<div class="avaliacao-linha">
                    <div class="avaliacao-coluna">
                        <button class="botao botao-primario botao-avaliacao-voltar" type="button">Voltar</button>
                        <button class="botao botao-primario botao-avaliacao-continuar" type="button">Continuar</button>
                    </div></div>';
                    } else if($divisoesDoFormulario>=1 && $divisoesDoFormularioDisciplinas===$ultimoGrupoDisciplinas && $ultimoGrupoDisciplinas>=1) {
                        $conteudoD.='<div class="avaliacao-linha">
                    <div class="avaliacao-coluna">
                        <button class="botao botao-primario botao-avaliacao-voltar" type="button">Voltar</button>
                        <button id="botao-avaliacao-enviar" class="botao botao-primario" type="button">Enviar</button>
                    </div></div>';
                    } else if($divisoesDoFormulario===0 && $divisoesDoFormularioDisciplinas===$ultimoGrupoDisciplinas && $ultimoGrupoDisciplinas===1) {
                        $conteudoD.='<div class="avaliacao-linha">
                    <div class="avaliacao-coluna">
                        <button id="botao-avaliacao-enviar" class="botao botao-primario" type="button">Enviar</button>
                    </div></div>';
                    } $conteudoD.='</div>';            
                }
            } $primeiroGrupoImpresso=true;
        } $conteudo = $conteudo . $conteudoD . '</form>';
        
        $qtdTotalPerguntas = array_sum($blocosQtdPerguntas); $qtdPorcentagem=0;
        
        for($i=0, $l=count($blocosQtdPerguntas); $i<$l; $i++) {
            $qtdPorcentagem += ceil(($blocosQtdPerguntas[$i]*100)/$qtdTotalPerguntas);
            array_push($blocosPorcentagem, $qtdPorcentagem);
        } 
        
        $blocosPorcentagem[count($blocosPorcentagem)-1]=100;
    } else {
        header('Location: index.php');
        exit();
    }
    //echo '<br />Disciplinas:';
    //var_dump($disciplinas);/*
    //var_dump($campos);
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="author" content="Yan Gabriel da Silva Machado" />
        <link rel="stylesheet" type="text/css" href="css/base.css" />
        <title>SAD</title>
    </head>
    <body>
        <div id="sem-js">
            <div id="sem-js-titulo">
                <img id="sem-js-titulo-img" src="imagens/alert-64.png" />
                <span id="sem-js-titulo-termos">SAD - Aviso</span>
            </div>
            <div id="sem-js-conteudo">
                <p>Seu navegador não oferece suporte ou está com o Javascript desativado.</p> 
                <p>Por favor atualize seu navegador ou ative esse recurso para utilizar o sistema</p>
            </div>
        </div>
        <div id="sem-suporte">
            <div id="sem-suporte-titulo">
                <img id="sem-suporte-titulo-img" src="imagens/alert-64.png" />
                <span id="sem-suporte-titulo-termos">SAD - Aviso</span>
            </div>
            <div id="sem-suporte-conteudo">
                <p>Seu navegador não possui os recursos necessários para o correto funcionamento do sistema.</p> 
                <p>Para utiliza-lo, por favor, atualize seu navegador.</p>
            </div>
        </div>
        <script>
            document.getElementById('sem-js').style.display='none';
            if(!document.all) {
                document.getElementById('sem-suporte').style.display='none';
                document.body.style.overflow='auto';
            }
        </script>
        
        <div id="com-js">
            <!-- CABECALHO[INICIO] -->
            <div id="cabecalho">
                <!-- MENU[INICIO] -->
                <a href="index.php" class="ligacao ligacao-logotipo logotipo-movel">SAD</a>
                <div id="navegacao-container">
                    <ul class="menu" id="navegacao">
                        <li class="menu-item"><span class="menu-item-termos" id="menu-toggle-fechar">Fechar Menu</span></li>
                        <li class="menu-item"><a href="index.php" class="menu-link ligacao-logotipo">SAD</a></li>
                        <li class="menu-item"><a href="index.php#iniciando-avaliacao" class="menu-link">Iniciando a Avaliação</a></li> 
                        <li class="menu-item"><a href="index.php#sobre" class="menu-link">Sobre</a></li> 
                        <!--<li class="menu-item menu-item-especial" style="float:right"><button title="Entrar na Administração" id="botao-entrar" class="botao" type="button">Entrar</button></li>-->
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
            <div id="conteudo" class="conteudo-secundario">
                <!-- AVALIACAO[INICIO] -->
                <div id="localizacao">Avaliação</div>
                <?php 
                    echo $conteudo;
                ?>
                <!-- AVALIACAO[FIM] -->
            </div>
            <!-- CONTEUDO[FIM] -->
            <div id="carregamento"></div>

            <!-- RODAPE[INICIO] -->
            <?php include 'inclusoes/rodape.php';?>
            <!-- RODAPE[FIM] -->

            <!-- FUNDOS[INICIO] -->
            <div id="fundo-primario"></div>
            <!-- FUNDOS[FIM] -->

            <!-- ENTRAR[INICIO] -->
            <?php //include 'inclusoes/entrar.php';?>
            <!-- ENTRAR[FIM] -->
        
        <!-- SCRIPT[INICIO] -->
        <script>
            var comJS = document.getElementById('com-js');
            if(comJS!==null) {
                comJS.style.display='block';
            }
        </script>
        <script src="js/base.js"></script>
        <?php
            $disc='';
            foreach($dis as $idDisciplina) {
                $disc.=$idDisciplina . ', ';
            }
            
            if(empty($disc)===false) {
                $disc= substr_replace($disc, '', strripos($disc, ','));
                $disc='{"disciplinas":[' . $disc . ']';
            }
        ?>
        <script>
        <?php 
            $jsPorcentagem='';
            
            foreach($blocosPorcentagem as $p) {
                $jsPorcentagem.=$p.', ';
            }
               
            if(empty($jsPorcentagem)===false) {
                $jsPorcentagem = substr_replace($jsPorcentagem, '', strripos($jsPorcentagem, ','));
                echo $jsPorcentagem = 'var porcentagemBlocosAvaliacao=[' . $jsPorcentagem . '];'; 
            }
        ?>
            atribuirPorcentagemBlocosAvaliacao();
        </script>
        <script>
            /* ATIVA BOTAO AVALIACAO ENVIAR[INICIO] */
            var botaoAvaliacaoEnviar = document.getElementById('botao-avaliacao-enviar');
            if(botaoAvaliacaoEnviar) {
                botaoAvaliacaoEnviar.onclick=function(){enviarAvaliacao(this, '<?php 
                echo $disc;?>', '<?php echo $disponibilidade;?>');};
            }
            /* ATIVA BOTAO AVALIACAO ENVIAR[FIM] */
        </script>
        <!-- SCRIPT[FIM] -->
    </body>
</html>