<?php
    $entrar = <<<HTML
    <div id="entrar">
                <span class="titulo">Administração</span>
                <span class="descricao">Digite seu Email e Senha abaixo para ter acesso a Administração SAD</span>
                <form action="admin/index.php" method="post">
                    <input id="login" class="campo-primario" type="text" name="login" placeholder="Email" autofocus="autofocus" />
                    <input id="senha" class="campo-primario" type="password" name="senha" placeholder="Senha" />
                    <button type="button" id="entrar-botao-cacelar" class="botao botao-terciario">Cancelar</button>
                    <button type="submit" class="botao botao-secundario">Entrar</button>
                    <span title="Clique para recuperar sua Senha" style="width:100%; float:left;"><a href="#" class="">Esqueci minha <span class="">senha?</span></a></span>
                    <button title="Clique aqui para FECHAR ou pressione a tecla ESC" id="entrar-botao-fechar" class="botao botao-fechar" type="button">&times;</button>
                </form>
            </div>
HTML;
    
    function gerarEntrar($entrar) {
        echo $entrar;
    }
    
    gerarEntrar($entrar);