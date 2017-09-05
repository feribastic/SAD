function eAnoBissexto(ano) {
    return ((ano%4===0 && ano%100!==0) || ano%400===0)?true:false;
};

function verificaTecla(e, numerica) {
    var tecla = new Number();
    if(window.event) {
        tecla = e.keyCode;
    } else if(e.wich) {
        tecla = e.wich;
    }
    
    if(numerica===false) {
        return (tecla >= '48') && (tecla <= '57')?false:true;
    } else {
        return (tecla<'48') || (tecla>'57')?false:true;
    }
}

function tamanhoCampo(formulario, campo, tamanhoMin, tamanhoMax) {
    var tamanho = document.forms[formulario][campo].value.length+1;
    if(tamanhoMin==='' || tamanhoMin===null || tamanhoMin===undefined || tamanhoMin<1) {
        tamanhoMin=1;
    } 
    
    if(tamanhoMax==='' || tamanhoMax===null || tamanhoMax===undefined || tamanhoMax<1) {
        tamanhoMax=2;
    }
    
    return (tamanho>=tamanhoMin && tamanho<=tamanhoMax)?true:false; 
}

/* AJAX[INICIO] */
function enviarNovaAvaliacao(json) {
    var xmlhttp;
    
    if(window.XMLHttpRequest) {
        /* CODIGO PARA IE7+, Firefox, Chrome, Opera, Safari */
        xmlhttp = new XMLHttpRequest();
    } else {
        /* CODIGO PARA IE6, IE5 */
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
        
    xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState===4 && xmlhttp.status===200) {
            document.getElementById("conteudo").innerHTML = xmlhttp.responseText;
            rodape();
        }
    };
        
    xmlhttp.open('POST', 'registra-avaliacao.php', true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send('jsonString='+json);
}
/* AJAX[FIM] */