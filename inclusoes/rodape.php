<?php
    function rodape($diretorio_base) {
        $dataRodape = '';
        if(date('Y')==='2015') {
            $dataRodape = '<span class="copyright">Copyright&nbsp;&copy;&nbsp;2015&nbsp;&nbsp;&nbsp;Instituto de Nutrição - Universidade Estadual do Rio de Janeiro. Todos os direitos reservados.</span>';
        } else {
            $dataRodape = '<span class="copyright">Copyright&nbsp;&copy;&nbsp;2015-' . date('Y') . '&nbsp;&nbsp;&nbsp;Instituto de Nutrição - Universidade Estadual do Rio de Janeiro. Todos os direitos reservados.</span>';
        }

        $rodape=
            '<div id="rodape">'
                . '<div id="rodape-envoltorio">'
                    . '<div class="bloco-1-por-3">'
                            . '<ul class="projetado">'
                                . '<li class="projetado-titulo">Projetado por</li>'
                                . '<li class="projetado-item">Yan Gabriel da Silva Machado</li>'
                            . '</ul>'
                        . '</div>'
                        . '<div class="bloco-1-por-3">'
                            . '<ul class="desenvolvido">'
                                . '<li class="desenvolvido-titulo">Desenvolvido por</li>'
                                . '<li class="desenvolvido-item">Felipe Ribas Coutinho</li>'
                                . '<li class="desenvolvido-item">Yan Gabriel da Silva Machado</li>'
                            . '</ul>'
                        . '</div>'
                        . '<div class="bloco-1-por-3">'
                            . '<ul class="mantido">'
                                . '<li class="mantido-item"><a href="http://www.nutricao.uerj.br" target="_blank"><img class="mantido-img" title="INU" src="' . $diretorio_base .'imagens/inu.png" alt="INU" width="60px" /></a></li>'
                                . '<li class="mantido-item"><a href="http://www.uerj.br" target="_blank"><img class="mantido-img" title="UERJ" src="' . $diretorio_base .'imagens/uerj.png" alt="UERJ" width="50px" /></a></li>'
                            . '</ul>'
                        . '</div>'
                    . '</div>'
                    . $dataRodape
                . '</div>';
        
        echo $rodape;
    }