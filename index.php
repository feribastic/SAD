<?php
    setlocale(LC_ALL, 'pt_BR', "pt_BR.iso-8859-1", 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    
    session_start();
    
    if(isset($_SESSION['avaliacao-iniciada'])) {
        unset($_SESSION['avaliacao-iniciada']);
        header('Location: index.php');
        exit();
    }
    
    function strip($html) {
        return trim(preg_replace('/<!--(.|\s)*?-->/', '',preg_replace('/\s+/', ' ', $html)));
    }
    
    ob_start('strip');
    
    ini_set('memory_limit', '-1');
    set_time_limit(60);
    
    require 'inclusoes/db/config.php';
    $con = mysqli_connect(HOST, USER, PASSWORD, DB)
    or die('Erro ao estabelecer conexão com o banco de dados: ' . mysqli_connect_error());
    mysqli_set_charset($con, CHARSET);
    
    $dataAtual = date('Y-m-d H:i:s');
    $comandoSQLData = 'SELECT id_avaliacao, nome FROM ' . PREFIXO_TABELAS . 'avaliacoes WHERE "' 
        . $dataAtual . '" BETWEEN inicio AND termino';
    $resultadoCSQLData = mysqli_query($con, $comandoSQLData);
    
    $resultado = $preAvaliacao = $disponibilidade = $nomeAvaliacao = '';
    
    function ordenarPeriodos($a, $b) {
        if($a->periodo===$b->periodo) {
            return 0; 
        }
        
        return ($a->periodo<$b->periodo)?-1:1;
    }
    
    if(mysqli_num_rows($resultadoCSQLData)>0) {
        while($linha = mysqli_fetch_array($resultadoCSQLData)) {
            $disponibilidade = $linha['id_avaliacao'];
            $nomeAvaliacao = $linha['nome'];
        }
        
        $comandoSql = 'SELECT b.id_disciplina, b.disciplina, b.periodo, b.e_eletiva FROM ' 
            . PREFIXO_TABELAS . 'disciplinas_perguntas a INNER JOIN ' . PREFIXO_TABELAS 
            . 'disciplinas b ON a.id_disciplina=b.id_disciplina INNER JOIN ' . PREFIXO_TABELAS 
            . 'perguntas c ON c.id_pergunta=a.id_pergunta WHERE b.esta_disponivel=true ' 
            . 'ORDER BY b.disciplina';
        $consulta = mysqli_query($con, $comandoSql);

        $periodos = []; 
        $disciplinas = []; 
        $existe = true; 
        $existeD = true;

        if(mysqli_num_rows($consulta)>0) {
            while($linha = mysqli_fetch_array($consulta)) {
                if(count($periodos)===0) {
                    $periodos[] = (object) [
                        'periodo'=>(int)$linha['periodo'], 
                        'e_eletiva'=>(int)$linha['e_eletiva']
                    ];
                } else {
                    foreach($periodos as $p) {
                        if($p->periodo!==(int)$linha['periodo']) {
                            $existe = false;
                        } else {
                            $existe = true; 
                            break;
                        }
                    }  

                    if(!$existe) {
                        $periodos[] = (object) [
                            'periodo'=>(int)$linha['periodo'], 
                            'e_eletiva'=>(int)$linha['e_eletiva']
                        ];
                    } 
                }

                if(count($disciplinas)===0) {
                    $disciplinas[] = (object) [
                        'id_disciplina'=>(int)$linha['id_disciplina'], 
                        'disciplina'=>$linha['disciplina'], 
                        'periodo'=>(int)$linha['periodo']
                    ];
                } else {
                    foreach($disciplinas as $d) {
                        if($d->id_disciplina!==(int)$linha['id_disciplina']) {
                            $existeD = false;
                        } else {
                            $existeD = true; 
                            break;
                        }
                    }

                    if(!$existeD) {
                        $disciplinas[] = (object) [
                            'id_disciplina'=>(int)$linha['id_disciplina'], 
                            'disciplina'=>$linha['disciplina'], 
                            'periodo'=>(int)$linha['periodo']
                        ];
                    }
                }
            }
        } 
        
        usort($periodos, 'ordenarPeriodos');
        
        if(count($disciplinas)>0) {
            $resultado.='<form name="pre-avaliacao" action="avaliacao/index.php" method="post">';
            $preAvaliacao = '<div id="pre-avaliacao"><span id="pre-avaliacao-titulo">' 
                . 'Sistema de Avaliação de Disciplina</span><span id="pre-avaliacao-descricao">' 
                . 'Selecione as Disciplinas através dos Períodos abaixo</span>';

            $numeroDeBlocos = count($periodos); 
            $nD = 1; 

            foreach($periodos as $p) {
                if($numeroDeBlocos===1) {
                    if(!$p->e_eletiva) {
                        $preAvaliacao.='<div class="bloco-4-por-4">'
                            . '<input id="p' . $p->periodo . '" data-periodo="' . $p->periodo 
                            . '" type="checkbox" class="checkbox periodos" value="' . $p->periodo . '" />'
                            . '<label title="Disciplinas do ' . $p->periodo 
                            . '° Período" class="checkbox-rotulo checkbox-especial checkbox-rotulo-secundario" for="p' 
                            . $p->periodo . '">'
                            . $p->periodo . '° Período'
                            . '</label>'
                            . '<span class="ver-disciplinas" data-disciplinas="' 
                            . $p->periodo . '">Ver</span></div>';
                    } else {
                        $preAvaliacao.='<div class="bloco-4-por-4">'
                            . '<input id="p' . $periodos[$w]->periodo . '" data-periodo="' 
                            . $periodos[$w]->periodo . '" type="checkbox" class="checkbox periodos" value="' . $periodos[$w]->periodo . '" />'
                            . '<label title="Disciplinas Eletivas" class="checkbox-rotulo checkbox-especial checkbox-rotulo-secundario" for="p' 
                            . $periodos[$w]->periodo . '">'
                            . 'Disciplinas Eletivas'
                            . '</label>'
                            . '<span class="ver-disciplinas" data-disciplinas="' . $periodos[$w]->periodo . '">Ver</span></div>';
                    }
                } else if($numeroDeBlocos===2) {
                    if(!$p->e_eletiva) {
                        $preAvaliacao.='<div class="bloco-2-por-4">'
                            . '<input id="p' . $p->periodo . '" data-periodo="' . $p->periodo 
                            . '" type="checkbox" class="checkbox periodos" value="' . $p->periodo . '" />'
                            . '<label title="Disciplinas do ' . $p->periodo 
                            . '° Período" class="checkbox-rotulo checkbox-especial checkbox-rotulo-secundario" for="p' . $p->periodo . '">'
                            . $p->periodo . '° Período'
                            . '</label>'
                            . '<span class="ver-disciplinas" data-disciplinas="' 
                            . $p->periodo . '">Ver</span></div>';
                    } else {
                        $preAvaliacao.='<div class="bloco-2-por-4">'
                            . '<input id="p' . $periodos[$w]->periodo . '" data-periodo="' . $periodos[$w]->periodo 
                            . '" type="checkbox" class="checkbox periodos" value="' . $periodos[$w]->periodo . '" />'
                            . '<label title="Disciplinas Eletivas" class="checkbox-rotulo checkbox-especial checkbox-rotulo-secundario" for="p' 
                            . $periodos[$w]->periodo . '">'
                            . 'Disciplinas Eletivas'
                            . '</label>'
                            . '<span class="ver-disciplinas" data-disciplinas="' 
                            . $periodos[$w]->periodo . '">Ver</span></div>';
                    }
                } else if($numeroDeBlocos===3) {
                    if(!$p->e_eletiva) {
                        $preAvaliacao.='<div class="bloco-1-por-3">'
                            . '<input id="p' . $p->periodo . '" data-periodo="' . $p->periodo 
                            . '" type="checkbox" class="checkbox periodos" value="' . $p->periodo . '" />'
                            . '<label title="Disciplinas do ' . $p->periodo 
                            . '° Período" class="checkbox-rotulo checkbox-especial checkbox-rotulo-secundario" for="p' 
                            . $p->periodo . '">' . $p->periodo . '° Período'
                            . '</label>'
                            . '<span class="ver-disciplinas" data-disciplinas="' 
                            . $p->periodo . '">Ver</span></div>';
                    } else {
                        $preAvaliacao.='<div class="bloco-1-por-3">'
                            . '<input id="p' . $periodos[$w]->periodo . '" data-periodo="' . $periodos[$w]->periodo 
                            . '" type="checkbox" class="checkbox periodos" value="' . $periodos[$w]->periodo . '" />'
                            . '<label title="Disciplinas Eletivas" class="checkbox-rotulo checkbox-especial checkbox-rotulo-secundario" for="p' 
                            . $periodos[$w]->periodo . '">'
                            . 'Disciplinas Eletivas'
                            . '</label>'
                            . '<span class="ver-disciplinas" data-disciplinas="' 
                            . $periodos[$w]->periodo . '">Ver</span></div>';
                    }
                } else if($numeroDeBlocos===4) {
                    if(!$p->e_eletiva) {
                        $preAvaliacao.='<div class="bloco-1-por-4">'
                            . '<input id="p' . $p->periodo . '" data-periodo="' . $p->periodo 
                            . '" type="checkbox" class="checkbox periodos" value="' . $p->periodo . '" />'
                            . '<label title="Disciplinas do ' . $p->periodo 
                            . '° Período" class="checkbox-rotulo checkbox-especial checkbox-rotulo-secundario" for="p' 
                            . $p->periodo . '">'
                            . $p->periodo . '° Período'
                            . '</label>'
                            . '<span class="ver-disciplinas" data-disciplinas="' . $p->periodo . '">Ver</span></div>';
                    } else {
                        $preAvaliacao.='<div class="bloco-1-por-4">'
                            . '<input id="p' . $periodos[$w]->periodo . '" data-periodo="' 
                                . $periodos[$w]->periodo . '" type="checkbox" class="checkbox periodos" value="' 
                                . $periodos[$w]->periodo . '" />'
                            . '<label title="Disciplinas Eletivas" class="checkbox-rotulo checkbox-especial checkbox-rotulo-secundario" for="p' 
                            . $periodos[$w]->periodo . '">'
                            . 'Disciplinas Eletivas'
                            . '</label>'
                            . '<span class="ver-disciplinas" data-disciplinas="' 
                            . $periodos[$w]->periodo . '">Ver</span></div>';
                    }
                } 
                
                if(!$p->e_eletiva) {
                    $resultado.='<div class="disciplinas" data-disciplinas="' . $p->periodo 
                        . '"><div class="disciplinas-conteudo"><span class="titulo">Disciplinas do ' . $p->periodo
                        . '° Período</span><span class="descricao descricao-secundaria">Selecione as suas disciplinas abaixo</span>';
                } else {
                    $resultado.='<div class="disciplinas" data-disciplinas="' . $p->periodo 
                        . '"><div class="disciplinas-conteudo"><span class="titulo">Disciplinas Eletivas</span>'
                        . '<span class="descricao descricao-secundaria">Selecione as suas disciplinas abaixo</span>';
                }
                
                $d = [];
                foreach($disciplinas as $dis) { 
                    if($dis->periodo===$p->periodo) {
                        $d[] = $dis;
                    }
                }

                $numeroDeDisciplinas = count($d);
                $qD = $numeroDeDisciplinas%2===0?$numeroDeDisciplinas/2:(($numeroDeDisciplinas-$numeroDeDisciplinas%2)/2)+1;
                $nDisc = array_chunk($d, $qD, true);

                for($w=0, $lw=count($nDisc); $w<$lw; $w++) {
                    $resultado.='<div class="bloco-2-por-4 disciplinas-bloco">';
                    foreach($nDisc[$w] as $di=>$vd) {
                        if($vd->periodo===$p->periodo) {
                            $resultado.='<input name="disciplinas[]" id="d' . $nD . '" type="checkbox" class="checkbox checkbox-disciplinas" value="' 
                                . $vd->id_disciplina . '" />'
                                . '<label title="' . $vd->disciplina . '" class="checkbox-rotulo checkbox-rotulo-primario" for="d' 
                                . $nD . '">' . $vd->disciplina . '</label>';
                        } 
                        
                        $nD++;
                    } 
                    
                    $resultado.='</div>';  
                } 
                
                $resultado.='<div class="bloco-4-por-4">' 
                . '<button type="button" class="botao botao-terciario botao-disciplinas-cancelar">' 
                . 'Cancelar</button><button type="button" class="botao botao-primario botao-disciplinas-ok">' 
                . 'Ok</button></div></div></div>';
            } 
            
            $resultado.='<input type="hidden" value="' . $disponibilidade . '" name="disponibilidade" /></form>';
            
            if($numeroDeBlocos!==1 && $numeroDeBlocos!==2 && $numeroDeBlocos!==3 && $numeroDeBlocos!==4 && $numeroDeBlocos%4>=0) {
                $numeroDePeriodos = count($periodos);
                $resto = $numeroDePeriodos%4; 
                $n = $numeroDePeriodos-$resto; 
                $q = $n/4;
                $blocos = [$q, $q, $q, $q]; 
                $b = count($blocos)-1;

                for($i=$resto; $i>0; $i--) {
                    $blocos[$b] = $blocos[$b]+1;
                    $b>0?$b--:$b++;
                } 
                
                $y = 0; 
                $w = 0;

                for($i=count($blocos)-1; $i>-1; $i--) {
                    $preAvaliacao.='<div class="bloco-1-por-4">';
                    while($y<$blocos[$i]) {
                        if(!$periodos[$w]->e_eletiva) {
                            $preAvaliacao.='<input id="p' . $periodos[$w]->periodo
                                . '" data-periodo="' . $periodos[$w]->periodo 
                                . '" type="checkbox" class="checkbox periodos" value="' 
                                . $periodos[$w]->periodo . '" />'
                                . '<label title="Disciplinas do ' . $periodos[$w]->periodo 
                                . '° Período" class="checkbox-rotulo checkbox-especial checkbox-rotulo-secundario" for="p' 
                                . $periodos[$w]->periodo . '">'
                                . $periodos[$w]->periodo . '° Período'
                                . '</label>'
                                . '<span class="ver-disciplinas" data-disciplinas="' 
                                . $periodos[$w]->periodo . '">Ver</span>'; 
                        } else {
                            $preAvaliacao.='<input id="p' . $periodos[$w]->periodo 
                                . '" data-periodo="' . $periodos[$w]->periodo 
                                . '" type="checkbox" class="checkbox periodos" value="' 
                                . $periodos[$w]->periodo . '" />'
                                . '<label title="Disciplinas Eletivas" class="checkbox-rotulo checkbox-especial checkbox-rotulo-secundario" for="p'
                                . $periodos[$w]->periodo . '">'
                                . 'Disciplinas Eletivas'
                                . '</label>'
                                . '<span class="ver-disciplinas" data-disciplinas="' 
                                . $periodos[$w]->periodo . '">Ver</span>';
                        }
                        
                        $y++; 
                        $w++;
                    } 
                    
                    $y = 0; 
                    $preAvaliacao.='</div>';
                } 
            } 
            
            $preAvaliacao.='<div id="pre-avaliacao-botao">'
            . '<button id="botao-pre-avaliacao" title="Iniciar Avaliação" type="button" class="botao">' 
            . 'Iniciar Avaliação</button></div></div>';
        } else {
            $preAvaliacao.='<div id="pre-avaliacao"><span id="pre-avaliacao-titulo">' 
            . 'Sistema de Avaliação de Disciplina</span><span id="pre-avaliacao-descricao">'
            . 'Nenhuma disciplina cadastrada.</span></div>';            
        }
    } else {
        $comandoSql='SELECT DATE_FORMAT(inicio, "%d/%m/%Y às %H:%i:%s") AS inicio, ' 
        . 'DATE_FORMAT(termino, "%d/%m/%Y às %H:%i:%s") AS termino FROM ' 
        . PREFIXO_TABELAS . 'avaliacoes WHERE inicio>"' . $dataAtual . '" AND e_manutencao=false LIMIT 1';
        $resultadoSQL=mysqli_query($con, $comandoSql);
        
        $previsao='';
        
        if(mysqli_num_rows($resultadoSQL)>0) {
            while($linha = mysqli_fetch_array($resultadoSQL)) {
                $previsao.='<span id="pre-avaliacao-previsao">'
                . 'A próxima avaliacão acontecerá entre <span class="pre-avaliacao-previsao-destaque">' 
                . $linha['inicio'] . '</span> e <span class="pre-avaliacao-previsao-destaque">' 
                . $linha['termino'] . '</span>.</span>';
            }
        }
        
        $preAvaliacao.='<div id="pre-avaliacao"><span id="pre-avaliacao-titulo">' 
        . 'Sistema de Avaliação de Disciplina</span><span id="pre-avaliacao-descricao">' 
        . 'O período de Avaliações está encerrado.</span>' . $previsao . '</div>';
    } 
    
    mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta name="robots" content="index, follow" />
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="keywords" content="SAD, Sistema, Avaliação, Disciplina, Sistema de Avaliação de Disciplina, INU, Instituto de Nutrição, UERJ, Universidade Estadual do Rio de Janeiro" />
        <meta name="description" content="Sistema de Avaliação de Disciplina" />
        <meta name="author" content="Yan Gabriel da Silva Machado" />
        <noscript><meta http-equiv="refresh" content="0; URL=sem-js.html" /></noscript>
        <link rel="stylesheet" type="text/css" href="css/base.css" />
        <!-- FAVICON[INICIO] -->
        <link rel="apple-touch-icon" sizes="57x57" href="imagens/favicon/apple-icon-57x57.png" />
        <link rel="apple-touch-icon" sizes="60x60" href="imagens/favicon/apple-icon-60x60.png" />
        <link rel="apple-touch-icon" sizes="72x72" href="imagens/favicon/apple-icon-72x72.png" />
        <link rel="apple-touch-icon" sizes="76x76" href="imagens/favicon/apple-icon-76x76.png" />
        <link rel="apple-touch-icon" sizes="114x114" href="imagens/favicon/apple-icon-114x114.png" />
        <link rel="apple-touch-icon" sizes="120x120" href="imagens/favicon/apple-icon-120x120.png" />
        <link rel="apple-touch-icon" sizes="144x144" href="imagens/favicon/apple-icon-144x144.png" />
        <link rel="apple-touch-icon" sizes="152x152" href="imagens/favicon/apple-icon-152x152.png" />
        <link rel="apple-touch-icon" sizes="180x180" href="imagens/favicon/apple-icon-180x180.png" />
        <link rel="icon" type="image/png" sizes="192x192"  href="imagens/favicon/android-icon-192x192.png" />
        <link rel="icon" type="image/png" sizes="32x32" href="imagens/favicon/favicon-32x32.png" />
        <link rel="icon" type="image/png" sizes="96x96" href="imagens/favicon/favicon-96x96.png" />
        <link rel="icon" type="image/png" sizes="16x16" href="imagens/favicon/favicon-16x16.png" />
        <link rel="manifest" href="imagens/favicon/manifest.json" />
        <meta name="msapplication-TileColor" content="#ffffff" />
        <meta name="msapplication-TileImage" content="imagens/favicon/ms-icon-144x144.png" />
        <meta name="theme-color" content="#ffffff" />
        <!-- FAVICON[FIM] -->
        <title>SAD</title>
        <!-- SCRIPTS[INICIO] -->
        <script>
            /* SEM SUPORTE[INICIO] */
            if(document.all) {
                window.location='sem-suporte.html';
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
            <a href="index.php" class="ligacao ligacao-logotipo logotipo-movel">SAD</a>
            <div id="navegacao-container">
                <ul class="menu" id="navegacao">
                    <li class="menu-item"><span class="menu-item-termos" id="menu-toggle-fechar">Fechar Menu</span></li>
                    <li class="menu-item"><a href="index.php" class="menu-link ligacao-logotipo">SAD</a></li>
                    <li class="menu-item"><a href="#iniciando-avaliacao" class="menu-link">Iniciando a Avaliação</a></li> 
                    <li class="menu-item"><a href="#sobre" class="menu-link">Sobre</a></li> 
                    <!--<li class="menu-item menu-item-especial" style="float:right"><button title="Entrar na Administração" id="botao-entrar" class="botao" type="button">Entrar</button></li>-->
                    <?php
                        if(!empty($nomeAvaliacao)) {
                            if(strlen($nomeAvaliacao)<51) {
                                echo '<li class="menu-item menu-item-movel" id="menu-item-avaliacao-nome">' 
                                . '<span id="menu-item-termos-avaliacao-nome" title="' 
                                . $nomeAvaliacao . '">' . $nomeAvaliacao . '</span></li>';
                            } else {
                                echo '<li class="menu-item menu-item-movel" id="menu-item-avaliacao-nome">' 
                                . '<span id="menu-item-termos-avaliacao-nome" title="' 
                                . $nomeAvaliacao . '">' . rtrim(substr($nomeAvaliacao, 0, 46)) . '...</span></li>';
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
        </div>
        <!-- CABECALHO[FIM] -->

        <!-- CONTEUDO[INICIO] -->
        <div id="conteudo">
            <?php
                if(!empty($nomeAvaliacao)) {
                    echo '<span id="avaliacao-nome" class="avaliacao-nome-excecao">' 
                    . $nomeAvaliacao . '</span>';
                }
            ?>
            <!-- PRE-AVALIACAO[INICIO] -->
            <?php
                echo $preAvaliacao . $resultado;
            ?>
            <!-- PRE-AVALIACAO[FIM] -->

            <div id="iniciando-avaliacao">
                <span id="iniciando-avaliacao-titulo">Iniciando a Avaliação</span>
                <span id="iniciando-avaliacao-descricao">Faça em apenas 3 Passos</span>
                <div class="bloco-1-por-3">
                    <div class="iniciando-avaliacao-conteudo">
                        <span class="iniciando-avaliacao-passos"><span class="iniciando-avaliacao-passos-destaque">1°</span> Passo</span>
                        <div class="iniciando-avaliacao-conteudo-termos">
                            <p>Selecione os Períodos correspondentes as suas Disciplinas.</p>
                        </div>
                    </div>
                </div>
                <div class="bloco-1-por-3">
                    <div class="iniciando-avaliacao-conteudo">
                        <span class="iniciando-avaliacao-passos"><span class="iniciando-avaliacao-passos-destaque">2°</span> Passo</span>
                        <div class="iniciando-avaliacao-conteudo-termos">
                            <p>Escolha as desejadas e clique em <b>Ok</b>.</p>
                        </div>
                    </div>
                </div>
                <div class="bloco-1-por-3">
                    <div class="iniciando-avaliacao-conteudo">
                        <!--<img src="imagens/inav3.png" />-->
                        <span class="iniciando-avaliacao-passos"><span class="iniciando-avaliacao-passos-destaque">3°</span> Passo</span>
                        <div class="iniciando-avaliacao-conteudo-termos">
                            <p>Quando estiver preparado(a) clique em <b>Iniciar Avaliação</b> e seja feliz!</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="sobre">
                <span id="sobre-titulo">SAD?</span>
                <div class="bloco-3-por-3">
                    <div class="sobre-conteudo">
                        <p>É; <b>SAD</b>: <b>S</b>istema de <b>A</b>valiação de <b>D</b>isciplina. Seu objetivo é gerar informações a partir das respostas dadas pelos alunos nos questionários e fornece-las ao <a href="http://www.nutricao.uerj.br" class="link" target="_blank" title="Instituto de Nutrição - Universidade Estadual do Rio de Janeiro">Instituto de Nutrição</a> assegurando confiabilidade e privacidade.</p>
                        <p>Portanto, sinta-se a vontade para dizer o que pensa pois suas respostas estarão seguras conosco. Saiba que elas são de extrema importância para aperfeiçoarmos a qualidade de ensino.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- CONTEUDO[FIM] -->

        <!-- RODAPE[INICIO] -->
        <?php 
            require 'inclusoes/rodape.php';
            rodape('');
        ?>
        <!-- RODAPE[FIM] -->

        <!-- FUNDOS[INICIO] -->
        <div id="fundo-primario"></div>
        <!-- FUNDOS[FIM] -->

        <!-- ENTRAR[INICIO] -->
        <?php //require 'inclusoes/entrar.php';?>
        <!-- ENTRAR[FIM] -->
        
        <!-- SCRIPTS[INICIO] -->
        <script src="js/base.js"></script>
        <!-- SCRIPTS[FIM] -->
    </body>
</html>
<?php
    ob_end_flush();