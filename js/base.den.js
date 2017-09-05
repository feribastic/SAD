"use strict";

function isArray(myArray) {
    return myArray.constructor.toString().indexOf("Array") > -1;
}

window.Object.defineProperty(HTMLElement.prototype, 'documentOffsetTop', {
    get: function () { 
        return this.offsetTop + (this.offsetParent ? this.offsetParent.documentOffsetTop : 0);
    }
});

/* REMOVE ESPACOS EM BRANCO[INICIO] */
function removerEspacosEmBranco(x) {
    return x.replace(/^\s+|\s+$/gm,'');
}
/* REMOVE ESPACOS EM BRANCO[FIM] */

/* VERIFICA EXISTENCIA DE CLASSE CSS[INICIO] */
function temClasse(elemento, classe) {
    var classes, encontrou=false;
    
    if(elemento!==undefined && elemento!==null && elemento!==''){ 
        if(elemento.className!==undefined && elemento.className!==null && elemento.className!=='') {
            classes=elemento.className.split(' ');

            for(var i=0, l=classes.length; i<l; i++) {
                if(classes[i]===classe) {
                   encontrou=true; 
                   break;
                }
            }
        }
    } return encontrou;
}
/* VERIFICA EXISTENCIA DE CLASSE CSS[FIM] */

/* REMOVE CLASSE CSS[INICIO] */
function removerClasse(elemento, classe) {
    if(elemento!==undefined && elemento!==null && elemento!=='' && classe!=='') {
        if(temClasse(elemento, classe)) { 
            if(elemento.className!==undefined && elemento.className!==null && elemento.className!=='') {
                var c=elemento.className.replace('  ', ' '), classes=c.split(' '), novasClasses='';
                
                for(var i=0, l=classes.length; i<l; i++) {
                    if(classes[i]!==classe) {
                        novasClasses=novasClasses!==''?novasClasses=novasClasses + ' ' + classes[i]:classes[i];
                    }
                } elemento.className=''; elemento.className=novasClasses;
            }
        }
    } return !temClasse(elemento, classe);
}
/* REMOVE CLASSE CSS[FIM] */

/* ADICIONA CLASSE CSS[INICIO] */
function adicionarClasse(elemento, classe) {
    if(elemento!==undefined && elemento!==null && elemento!=='' && classe!=='') {
        if(!temClasse(elemento, classe)) { 
            if(elemento.className!==undefined && elemento.className!==null) {
                elemento.className=elemento.className!==''?elemento.className=elemento.className + ' ' + classe:elemento.className=classe;
            } 
        }
    } return temClasse(elemento, classe);
}
/* ADICIONA CLASSE CSS[FIM] */

/* COLOCA RODAPE NO FIM DA PAGINA[INICIO] */
function rodape() {
    var conteudo = document.getElementById('conteudo');
    if(conteudo!==null) {
        var rodape = document.getElementById('rodape');
        var rodapeHeight = 0;
        if(rodape!==null) {
            rodapeHeight=rodape.offsetHeight;
            var janela = window.innerHeight || document.documentElement.clientHeight;
            var janelaMenosRodape = janela-rodapeHeight;
            //rodape.style.position=conteudo.offsetHeight<janelaMenosRodape?'fixed':'static';
            if(conteudo.offsetHeight<janelaMenosRodape) {
                //console.log('classe adicionada');
                adicionarClasse(rodape, 'rodape-fixo');
            } else {
                //console.log('classe removida');
                removerClasse(rodape, 'rodape-fixo');
            }
        }
    }
}
/* COLOCA RODAPE NO FIM DA PAGINA[FIM] */

/* ENTRAR[INICIO] */
var entrarHeight=0, entrarAtivado=false;

function entrar(tamanho, porcentagem) {
    var x = document.getElementById('entrar'), y = document.getElementById('fundo-primario');
    var campoLogin = document.getElementById('login'), campoSenha=document.getElementById('senha');
    var entrarBotaoFechar = document.getElementById('entrar-botao-fechar');
    
    if(entrarAtivado) {
        y.style.display='none'; entrarBotaoFechar.style.display='none';
        document.documentElement.style.overflow='auto';
        if(entrarHeight>0) {
            entrarHeight=entrarHeight-porcentagem; x.style.height=entrarHeight+'%';
            setTimeout(function(){entrar(tamanho, porcentagem);}, 50);
        } else if(entrarHeight===0) {
            entrarAtivado=false; x.style.display='none'; campoLogin.value='', campoSenha.value='';
        }
    } else {
        y.style.display='block'; x.style.display='block';
        entrarBotaoFechar.style.display='block'; document.documentElement.style.overflow='hidden';
        if(entrarHeight<tamanho) {
            entrarHeight=entrarHeight+porcentagem; x.style.height=entrarHeight+'%';
            setTimeout(function(){entrar(tamanho, porcentagem);}, 50);
        } else if(entrarHeight===tamanho) {
            entrarAtivado=true; campoLogin.focus();
        }
    }
}
/* ENTRAR[FIM] */

/* FECHA ENTRAR[INICIO] */
function fechaEntrar(e) {
    var tecla = new Number();
    if(window.event) {
        tecla=e.keyCode;
    } else if(e.which) {
        tecla=e.which;
    }
    
    if(tecla===27) {
        entrar(100, 20);
    }
}
/* FECHA ENTRAR[FIM] */

/* PRE AVALIACAO[INICIO] */
var preAvaliacaoWidth=0, preAvaliacaoAtivado=false;

function preAvaliacao(elemento, tamanho, porcentagem, padrao) {
    var x=elemento, y=document.getElementById('fundo-primario'), z=elemento.childNodes;
    
    if(preAvaliacaoAtivado) {
        y.style.display = 'none';
        document.documentElement.style.overflow = 'auto';
        
        for(var i=0, l=z.length; i<l; i++) {
            if(temClasse(z[i], 'disciplinas-conteudo')) {
                z[i].style.display = 'none'; 
                break;
            }
        }
        
        if(preAvaliacaoWidth>0) {
            preAvaliacaoWidth = preAvaliacaoWidth - porcentagem; 
            x.style.width = preAvaliacaoWidth + '%';
            setTimeout(function(){
                preAvaliacao(elemento, tamanho, porcentagem, true);
            }, 40);
        } else if(preAvaliacaoWidth===0) {
            preAvaliacaoAtivado = false; 
            x.style.display = 'none';
        }
    } else {
        y.style.display = 'block'; 
        x.style.display = 'block';
        document.documentElement.style.overflow = 'hidden';
        
        if(preAvaliacaoWidth<tamanho) {
            preAvaliacaoWidth = preAvaliacaoWidth + porcentagem; 
            x.style.width = preAvaliacaoWidth + '%';
            setTimeout(function(){
                preAvaliacao(elemento, tamanho, porcentagem, true);
            }, 40);
            
        } else if(preAvaliacaoWidth===tamanho) {
            for(var i=0, l=z.length; i<l; i++) {
                if(temClasse(z[i], 'disciplinas-conteudo')) {
                    z[i].style.display = 'block'; 
                    break;
                }
            } 
            
            preAvaliacaoAtivado = true; 
        }
    }
    
    if(!padrao) {
        var marcados=0, p=elemento.childNodes;
        for(var i=0, l=p.length; i<l; i++) {
            if(temClasse(p[i], 'disciplinas-conteudo')) {
                var filhos = p[i].childNodes;
                for(var f=0, lf=filhos.length; f<lf; f++) {
                    if(temClasse(filhos[f], 'disciplinas-bloco')) {
                        var filhosDeFilhos=filhos[f].childNodes;
                        for(var j=0, lj=filhosDeFilhos.length; j<lj; j++) {
                            if(temClasse(filhosDeFilhos[j], 'checkbox-disciplinas')) {
                                if(filhosDeFilhos[j].checked) {
                                    marcados++;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        if(marcados===0) {
            var periodos = document.getElementsByClassName('periodos');
            for(var i=0, l=periodos.length; i<l; i++) {
                if(periodos[i].getAttribute('data-periodo')===elemento.getAttribute('data-disciplinas')) {
                    var x = periodos[i].previousSibling; 
                    periodos[i].checked = false;
                    while(x) {
                        if(temClasse(x, 'checkbox-especial')) {
                            if(x.getAttribute('for')===periodos[i].getAttribute('id')) {
                                var filhos = x.childNodes;
                                for(var j=0, lj=filhos.length; j<lj; j++) {
                                    if(temClasse(filhos[j], 'checkbox-quadrado-especial')) {
                                        var filhosDeFilhos = filhos[j].childNodes;
                                        for(var f=0, lf=filhosDeFilhos.length; f<lf; f++) {
                                            if(temClasse(filhosDeFilhos[f], 'checkbox-quadrado-marcado-especial')) {
                                                filhosDeFilhos[f].style.display='none';
                                            }
                                        }
                                    }
                                } 
                                
                                break;
                            }
                        } 
                        
                        x = x.previousSibling;
                    } 
                    
                    break;
                }
            }
            
            var periodos=document.getElementsByClassName('periodos'), disciplinas=elemento.getAttribute('data-disciplinas');
            for(var i=0, l=periodos.length; i<l; i++) {
                if(disciplinas===periodos[i].getAttribute('data-periodo')) {
                    podeMostrarVerDisciplinas(periodos[i].checked?true:false, periodos[i]);
                }
            }
        }
    }
}

function verPreAvaliacao(elemento) {
    var alvo='', ver=elemento.getAttribute('data-disciplinas'), disciplinas=document.getElementsByClassName('disciplinas');
    
    for(var j=0, lj=disciplinas.length; j<lj; j++) {
        if(disciplinas[j].getAttribute('data-disciplinas')===ver) {
            alvo = disciplinas[j]; 
            break;
        }
    } preAvaliacao(alvo, 100, 20, true);;
}
/* PRE AVALIACAO[FIM] */

/* VALIDA CAMPOS PRE AVALIACAO[INICIO] */
function validaCamposPreAvaliacao() {
    var campos=document.forms['pre-avaliacao'], marcados=0, prosseguir=false;
    for(var i=0, l=campos.length; i<l; i++) {
        if(campos.elements[i].type==='checkbox') {
            if(campos.elements[i].checked) {
                marcados++;
            }
        }   
    } marcados>0?campos.submit():alert('Por favor escolha ao menos 1 Disciplina');
}
/* VALIDA CAMPOS PRE AVALIACAO[FIM] */

/* CHECKBOX[INICIO] *//*
function marcaDesmarcaCheckbox(checkbox) {
    var elementos = checkbox.childNodes;
    
    for(var i=0, l=elementos.length; i<l; i++) {
        if(temClasse(elementos[i], 'checkbox-quadrado')) {
            var marcados = elementos[i].childNodes, m;
            
            for(var j=0, lj=marcados.length; j<lj; j++) {
               if(temClasse(marcados[j], 'checkbox-quadrado-marcado')) {
                    m = window.getComputedStyle(marcados[j]).getPropertyValue('display') || marcados[j].currentStyle.display; 
                    marcados[j].style.display=m==='none'?'block':'none';
               }
            }
        }
    }
}
/* CHECKBOX[FIM] */

/* DESMARCA DISCIPLINAS[INICIO] */
function desmarcaDisciplinas(checkbox) {
    var pai = checkbox.parentNode.parentNode.childNodes;
    
    for(var i=0, l=pai.length; i<l; i++) {
        if(temClasse(pai[i], 'disciplinas-bloco')) {
            var disciplinas = pai[i].childNodes;
            for(var j=0, lj=disciplinas.length; j<lj; j++) {
                if(temClasse(disciplinas[j], 'checkbox-disciplinas')) {
                    disciplinas[j].checked=false;
                } else if(temClasse(disciplinas[j], 'checkbox')) {
                    var label=disciplinas[j].childNodes;
                    for(var k=0, lk=label.length; k<lk; k++) {
                        if(temClasse(label[k], 'checkbox-quadrado')) {
                            var marcado=label[k].childNodes;
                            for(var m=0, lm=marcado.length; m<lm; m++) {
                                if(temClasse(marcado[m], 'checkbox-quadrado-marcado')) {
                                    marcado[m].style.display='none';
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    var p=checkbox.parentNode.parentNode.parentNode, d=document.getElementsByClassName('periodos');
    for(var i=0, l=d.length; i<l; i++) {
        if(d[i].getAttribute('data-periodo')===p.getAttribute('data-disciplinas')) {
            var x=d[i].previousSibling; d[i].checked=false; podeMostrarVerDisciplinas(false, d[i]);
            while(x) {
                if(temClasse(x, 'checkbox-especial')) {
                    if(x.getAttribute('for')===d[i].getAttribute('id')) {
                        var filhos = x.childNodes;
                        for(var j=0, lj=filhos.length; j<lj; j++) {
                            if(temClasse(filhos[j], 'checkbox-quadrado-especial')) {
                                var filhosDeFilhos = filhos[j].childNodes;
                                for(var f=0, lf=filhosDeFilhos.length; f<lf; f++) {
                                    if(temClasse(filhosDeFilhos[f], 'checkbox-quadrado-marcado-especial')) {
                                        filhosDeFilhos[f].style.display='none';
                                    }
                                }
                            }
                        } break;
                    }
                } x=x.previousSibling;
            } break;
        }
    } preAvaliacao(checkbox.parentNode.parentNode.parentNode, 100, 20, true);
}
/* DESMARCA DISCIPLINAS[FIM] */

/* CHECKBOX ESPECIAL[INICIO] */
function podeMostrarVerDisciplinas(pode, checkboxPeriodo) {
    var display='none';
    if(pode) {
        display='block';
    }
    
    var ver=document.getElementsByClassName('ver-disciplinas'), c=checkboxPeriodo.getAttribute('data-periodo');
    for(var i=0, l=ver.length; i<l; i++) {
        if(ver[i].getAttribute('data-disciplinas')===c) {
            ver[i].style.display=display;
        }
    }
}

function marcaDesmarcaCheckboxEspecial(checkbox) {
    var elementos=checkbox.childNodes, idCheckbox=checkbox.getAttribute('for'), periodo = document.getElementById(idCheckbox);
    podeMostrarVerDisciplinas(periodo.checked?false:true, periodo);
    
    /*
    for(var i=0, l=elementos.length; i<l; i++) {
        if(temClasse(elementos[i], 'checkbox-quadrado-especial')) {
            var marcados = elementos[i].childNodes, m;
            
            for(var j=0, lj=marcados.length; j<lj; j++) {
               if(temClasse(marcados[j], 'checkbox-quadrado-marcado-especial')) {
                    m = window.getComputedStyle(marcados[j]).getPropertyValue('display') || marcados[j].currentStyle.display; 
                    marcados[j].style.display=m==='none'?'block':'none';
               }
            }
        }
    }*/
    
    var checkboxAlvo=document.getElementById(idCheckbox), checkboxes=document.getElementById(idCheckbox).getAttribute('data-periodo'), 
    disciplinas=document.getElementsByClassName('disciplinas'), alvo='', preAvaliacaoAtivar=false;
    
    for(var i=0, l=disciplinas.length; i<l; i++) {
        if(disciplinas[i].getAttribute('data-disciplinas')===checkboxes) {
            alvo = disciplinas[i]; 
            if(!checkboxAlvo.checked) {
                preAvaliacaoAtivar=true;
            } else {
                var filhos=disciplinas[i].childNodes;
                for(var j=0, lj=filhos.length; j<lj; j++) {
                    if(temClasse(filhos[j], 'disciplinas-conteudo')) {
                        var filhosDeFilhos=filhos[j].childNodes;
                        for(var k=0, lk=filhosDeFilhos.length; k<lk; k++) {
                            if(temClasse(filhosDeFilhos[k], 'disciplinas-bloco')) {
                                var dis = filhosDeFilhos[k].childNodes;
                                for(var z=0, lz=dis.length; z<lz; z++) {
                                    if(temClasse(dis[z], 'checkbox-disciplinas')) {
                                        dis[z].checked=false;
                                    } else if(temClasse(dis[z], 'checkbox')) {
                                        var label=dis[z].childNodes;
                                        for(var w=0, lw=label.length; w<lw; w++) {
                                            if(temClasse(label[w], 'checkbox-quadrado')) {
                                                var marcado=label[w].childNodes;
                                                for(var m=0, lm=marcado.length; m<lm; m++) {
                                                    if(temClasse(marcado[m], 'checkbox-quadrado-marcado')) {
                                                        marcado[m].style.display='none';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } break;
                    }
                } 
            } break;
        }
    }
    
    if(alvo!=='' && preAvaliacaoAtivar) {
        preAvaliacao(alvo, 100, 20, true);
    }
}
/* CHECKBOX ESPECIAL[FIM] */

/* VERIFICA CHECKBOXES[INICIO] *//*
function verificaCheckboxes() {
    var x=document.getElementsByClassName('checkbox-verifica'), y=document.getElementsByTagName('label');
    
    for(var i=0, l=x.length; i<l; i++) {
        if(x[i].checked) {
            for(var j=0, lj=y.length; j<lj; j++) {
                if(y[j].getAttribute('for')===x[i].getAttribute('id')) {
                    var elementos = y[j].childNodes;
                    for(var e=0, le=elementos.length; e<le; e++) {
                        if(temClasse(elementos[e], 'checkbox-quadrado') || temClasse(elementos[e], 'checkbox-quadrado-especial')) {
                            var marcado = elementos[e].childNodes;
                            for(var z=0, lz=marcado.length; z<lz; z++) {
                                if(temClasse(marcado[z], 'checkbox-quadrado-marcado') || temClasse(marcado[z], 'checkbox-quadrado-marcado-especial')) {
                                    marcado[z].style.display='block'; break;
                                }
                            } break;
                        }
                    } break;
                }
            }
        } else {
            for(var j=0, lj=y.length; j<lj; j++) {
                if(y[j].getAttribute('for')===x[i].getAttribute('id')) {
                    var elementos = y[j].childNodes;
                    for(var e=0, le=elementos.length; e<le; e++) {
                        if(temClasse(elementos[e], 'checkbox-quadrado') || temClasse(elementos[e], 'checkbox-quadrado-especial')) {
                            var marcado = elementos[e].childNodes;
                            for(var z=0, lz=marcado.length; z<lz; z++) {
                                if(temClasse(marcado[z], 'checkbox-quadrado-marcado') || temClasse(marcado[z], 'checkbox-quadrado-marcado-especial')) {
                                    marcado[z].style.display='none'; break;
                                }
                            } break;
                        }
                    } break;
                }
            }
        }
    }
}*/
/* VERIFICA CHECKBOXES[FIM] */


/* VALIDA AVALIACAO[INICIO] */
function ordenarElementos(a, b) {
    if(a.ordem===b.ordem) return 0;
    return (a.ordem<b.ordem)?-1:1;
}

/*
function validaAvaliacao(botao) {
    var x = botao.parentNode.parentNode.parentNode.childNodes, nomeDosCampos=[], existeNomeDoCampo=true, correrNomeDosCampos=0, camposPreenchidos=[], quantidadeDeFalsos=0, checados=0;
    var elementos=[];
    
    
    for(var i=0, l=x.length; i<l; i++) {
        if(temClasse(x[i], 'avaliacao-linha')) {
            for(var j=0, lj=x[i].childNodes.length; j<lj; j++) {
                if(temClasse(x[i].childNodes[j], 'avaliacao-coluna')) {
                    for(var k=0, lk=x[i].childNodes[j].childNodes.length; k<lk; k++) {
                        if(temClasse(x[i].childNodes[j].childNodes[k], 'obrigatorio')) {
                            if(x[i].childNodes[j].childNodes[k].type==='radio' || x[i].childNodes[j].childNodes[k].type==='checkbox') {
                                var elemento = x[i].childNodes[j].childNodes[k].getAttribute('name');
                                console.log('entrou!');
                                if(correrNomeDosCampos===0) {
                                    nomeDosCampos[correrNomeDosCampos] = elemento;
                                } else {
                                    for(var e=0, len=nomeDosCampos.length; e<len; e++) {
                                       existeNomeDoCampo=nomeDosCampos[e]!==elemento?false:true;
                                    }

                                    // O sinal ! significa se for igual a FALSE 
                                    if(!existeNomeDoCampo) {
                                        nomeDosCampos[nomeDosCampos.length]=elemento;
                                    }
                                } correrNomeDosCampos++;
                            } else if(x[i].childNodes[j].childNodes[k].type==='text' || x[i].childNodes[j].childNodes[k].type==='textarea') {
                                camposPreenchidos[camposPreenchidos.length]=x[i].childNodes[j].childNodes[k].value.replace(/[\\'"]/g, '').trim()===''?false:true;
                                
                                if(x[i].childNodes[j].childNodes[k].value.replace(/[\\'"]/g, '').trim()==='') {
                                    adicionarClasse(x[i].childNodes[j].childNodes[k].parentNode.parentNode, 'avaliacao-linha-aviso');
                                    var filhos=x[i].childNodes[j].childNodes[k].parentNode.parentNode.childNodes;
                                    for(var w=0, lw=filhos.length; w<lw; w++) {
                                        if(temClasse(filhos[w], 'avaliacao-coluna')) {
                                            var filhosDeFilhos=filhos[w].childNodes;
                                            for(var f=0, lf=filhosDeFilhos.length; f<lf; f++) {
                                                if(temClasse(filhosDeFilhos[f], 'avaliacao-obrigatorio')) {
                                                    filhosDeFilhos[f].style.display='block';
                                                    elementos.push({ordem:parseInt(filhosDeFilhos[f].getAttribute('data-ordem')), aviso:filhosDeFilhos[f], elemento:x[i].childNodes[j].childNodes[k], e_texto:true});
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    removerClasse(x[i].childNodes[j].childNodes[k].parentNode.parentNode, 'avaliacao-linha-aviso');
                                    var filhos=x[i].childNodes[j].childNodes[k].parentNode.parentNode.childNodes;
                                    for(var w=0, lw=filhos.length; w<lw; w++) {
                                        if(temClasse(filhos[w], 'avaliacao-coluna')) {
                                            var filhosDeFilhos=filhos[w].childNodes;
                                            for(var f=0, lf=filhosDeFilhos.length; f<lf; f++) {
                                                if(temClasse(filhosDeFilhos[f], 'avaliacao-obrigatorio')) {
                                                    filhosDeFilhos[f].style.display='none';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    for(var i=0; i<nomeDosCampos.length; i++) {
        var grupo = document.getElementsByName(nomeDosCampos[i]);
        
        for(var j=0; j<grupo.length; j++) {
            if(grupo[j].checked) {
                checados++;
            }
        } camposPreenchidos[camposPreenchidos.length]=checados>0?true:false; 
        
        if(checados>0) {
            removerClasse(grupo[0].parentNode.parentNode, 'avaliacao-linha-aviso');
            var filhos=grupo[0].parentNode.parentNode.childNodes;
            for(var w=0, lw=filhos.length; w<lw; w++) {
                if(temClasse(filhos[w], 'avaliacao-coluna')) {
                    var filhosDeFilhos=filhos[w].childNodes;
                    for(var f=0, lf=filhosDeFilhos.length; f<lf; f++) {
                        if(temClasse(filhosDeFilhos[f], 'avaliacao-obrigatorio')) {
                            filhosDeFilhos[f].style.display='none';
                        }
                    }
                }
            }
        } else {
            adicionarClasse(grupo[0].parentNode.parentNode, 'avaliacao-linha-aviso');
            var filhos=grupo[0].parentNode.parentNode.childNodes;
            for(var w=0, lw=filhos.length; w<lw; w++) {
                if(temClasse(filhos[w], 'avaliacao-coluna')) {
                    var filhosDeFilhos=filhos[w].childNodes;
                    for(var f=0, lf=filhosDeFilhos.length; f<lf; f++) {
                        if(temClasse(filhosDeFilhos[f], 'avaliacao-obrigatorio')) {
                            filhosDeFilhos[f].style.display='block';
                            elementos.push({ordem:parseInt(filhosDeFilhos[f].getAttribute('data-ordem')), aviso:filhosDeFilhos[f], elemento:null, e_texto:false});
                        }
                    }
                }
            }
        } checados=0;
    }
    
    if(elementos.length>0) {
        elementos.sort(ordenarElementos);
        var altura = (elementos[0].aviso.parentNode.documentOffsetTop - elementos[0].aviso.offsetHeight) - 30;
        altura = altura<0?0:altura;
        window.scrollTo(0, altura);
        if(elementos[0].e_texto) {
            if(elementos[0].elemento!==null) elementos[0].elemento.focus();
        }
    }
    
    for(var r=0; r<camposPreenchidos.length; r++) {
        if(!camposPreenchidos[r]) {
           quantidadeDeFalsos++;
        } 
    } 
    
    rodape();
    
    return quantidadeDeFalsos===0?true:false;
}*/

function validaAvaliacao(botao) {
    var nomeDosCampos=[], existeNomeDoCampo=true, camposPreenchidos=[], quantidadeDeFalsos=0, checados=0;
    var elementos=[];
    var correspondentes = botao.getAttribute('data-correspondentes');
    var campos = document.getElementsByClassName('obrigatorio');
    
    for(var i=0, l=campos.length; i<l; i++) {
        if(campos[i].getAttribute('data-correspondentes')===correspondentes) {
            if(campos[i].type==='radio' || campos[i].type==='checkbox') {
                var elemento = campos[i].getAttribute('name');
                if(nomeDosCampos.length===0) {
                    nomeDosCampos.push(elemento);
                } else {
                    for(var j=0, lj=nomeDosCampos.length; j<lj; j++) {
                        if(nomeDosCampos[j]===elemento) {
                            existeNomeDoCampo = true; 
                            break;
                        } else {
                            existeNomeDoCampo = false;
                        }
                    }
                    
                    if(!existeNomeDoCampo) {
                        nomeDosCampos.push(elemento);
                    }
                }
            } else if(campos[i].type==='text' || campos[i].type==='textarea') {
                var alvo1 = document.getElementById(campos[i].getAttribute('data-id-aviso'));
                var alvo2 = alvo1.parentNode.parentNode;
                if(campos[i].value.replace(/[\\'"]/g, '').trim()==='') {
                    camposPreenchidos.push(false);
                    adicionarClasse(alvo2, 'avaliacao-linha-aviso');
                    alvo1.style.display='block';
                    elementos.push({
                        ordem:parseInt(alvo1.getAttribute('data-ordem')), 
                        aviso:alvo2, 
                        elemento:campos[i], 
                        e_texto:true
                    });
                } else {
                    camposPreenchidos.push(true);
                    removerClasse(alvo2, 'avaliacao-linha-aviso');
                    alvo1.style.display = 'none';
                }
            }
        }
    }
    
    for(var i=0; i<nomeDosCampos.length; i++) {
        var grupo = document.getElementsByName(nomeDosCampos[i]);
        
        for(var j=0; j<grupo.length; j++) {
            if(grupo[j].checked) {
                checados++;
            }
        } 
        
        camposPreenchidos.push((checados>0)?true:false); 
        var alvo1 = document.getElementById(grupo[0].getAttribute('data-id-aviso'));
        var alvo2 = alvo1.parentNode.parentNode;
        
        if(checados>0) {
            var dataErros = alvo1.getAttribute('data-erros');
            /* VERIFICA SE E TIPO TABELA */
            if(dataErros!==null && dataErros!==undefined) {
                if(dataErros.length>0) {
                    var arrayErros = dataErros.split(',');
                    var novoDataErros = '';
                    
                    for(var iAE=0, lAE=arrayErros.length; iAE<lAE; iAE++) {
                        if(arrayErros[iAE]!==nomeDosCampos[i]) {
                            novoDataErros += arrayErros[iAE]+',';
                        }
                    }
                    
                    novoDataErros = novoDataErros.substr(0, novoDataErros.lastIndexOf(","));
                    alvo1.setAttribute('data-erros', novoDataErros);
                } else {
                    removerClasse(alvo2, 'avaliacao-linha-aviso');
                    alvo1.style.display = 'none';
                }
            } else {
                removerClasse(alvo2, 'avaliacao-linha-aviso');
                alvo1.style.display = 'none';
            }
        } else {
            var dataErros = alvo1.getAttribute('data-erros');
            /* VERIFICA SE E TIPO TABELA */
            if(dataErros!==null && dataErros!==undefined) {
                if(dataErros.length>0) {
                    var arrayErros = dataErros.split(',');
                    var novoDataErros='';
                    var existeAE = false;
                    
                    for(var iAE=0, lAE=arrayErros.length; iAE<lAE; iAE++) {
                        if(arrayErros[iAE]!==nomeDosCampos[i]) {
                            existeAE = false;
                        } else {
                            existeAE = true; 
                            break;
                        }
                    }
                    
                    if(!existeAE) {
                        arrayErros.push(nomeDosCampos[i]);
                    }
                    
                    alvo1.setAttribute('data-erros', arrayErros);
                } else {
                    alvo1.setAttribute('data-erros', nomeDosCampos[i]);
                }
            }
            
            adicionarClasse(alvo2, 'avaliacao-linha-aviso');
            alvo1.style.display = 'block';
            elementos.push({
                ordem:parseInt(alvo1.getAttribute('data-ordem')), 
                aviso:alvo2, 
                elemento:null, 
                e_texto:false
            });
        }
        
        checados=0;
    }
    
    if(elementos.length>0) {
        elementos.sort(ordenarElementos);
        var altura = elementos[0].aviso.documentOffsetTop - 55;
        altura = altura<0?0:altura;
        window.scrollTo(0, altura);
        if(elementos[0].e_texto) {
            if(elementos[0].elemento!==null) elementos[0].elemento.focus();
        }
    }
    
    for(var r=0; r<camposPreenchidos.length; r++) {
        if(!camposPreenchidos[r]) {
           quantidadeDeFalsos++;
        } 
    } 
    
    rodape();
    
    return quantidadeDeFalsos===0?true:false;
}
/* VALIDA AVALIACAO[FIM] */

/* ENVIAR AVALIACAO[INICIO] */
function enviarAvaliacao(botao, disciplinas, disponibilidade) {
    if(validaAvaliacao(botao)) {
        pegaRespostasAvaliacao(disciplinas, disponibilidade);
    }
}
/* ENVIAR AVALIACAO[FIM] */


/* PERGUNTAS PROXIMAS[INICIO] */
function perguntasProximas(botao) {
    if(validaAvaliacao(botao)) {
        botao.parentNode.parentNode.parentNode.style.display='none';
        var x = botao.parentNode.parentNode.parentNode.nextSibling;

        while(x) {
            if(temClasse(x, 'avaliacao-perguntas')) {
                x.style.display='inline'; 
                break;
            } x=x.nextSibling;
        } 
        
        window.scrollTo(0, 0);
    } rodape(); 
}
/* PERGUNTAS PROXIMAS[FIM] */

/* PERGUNTAS ANTERIORES[INICIO] */
function perguntasAnteriores(botao) {
    botao.parentNode.parentNode.parentNode.style.display='none';
    var x = botao.parentNode.parentNode.parentNode.previousSibling;
        
    while(x) {
        if(temClasse(x, 'avaliacao-perguntas')) {
            x.style.display='inline'; 
            break;
        } x=x.previousSibling;
    } 
    
    window.scrollTo(0, 0);
    rodape();
}
/* PERGUNTAS ANTERIORES[FIM] */

/* RADIOS[INICIO] *//*
function marcaDesmarcaRadio(radio) {
    var nome = document.getElementById(radio.getAttribute('for')).getAttribute('name');
    var desmarcados = document.getElementsByName(nome);
    var alvos = document.getElementsByClassName('radio');
    
    for(var i=0, l=desmarcados.length; i<l; i++) {
        for(var j=0, lj=alvos.length; j<lj; j++) {
            if(desmarcados[i].getAttribute('id')===alvos[j].getAttribute('for')) {
                var alvosDesmarcados = alvos[j].childNodes;
                for(var d=0, ld=alvosDesmarcados.length; d<ld; d++) {
                        if(temClasse(alvosDesmarcados[d], 'radio-circulo')) {
                            var marcados = alvosDesmarcados[d].childNodes, m;

                            for(var m=0, lm=marcados.length; m<lm; m++) {
                               if(temClasse(marcados[m], 'radio-circulo-marcado')) {
                                    marcados[m].style.display='none';
                               }
                            }
                        }
                }
            }
        }
    }
    
    var elementos = radio.childNodes;
    
    for(var i=0, l=elementos.length; i<l; i++) {
        if(temClasse(elementos[i], 'radio-circulo')) {
            var marcados = elementos[i].childNodes, m;
            
            for(var j=0, lj=marcados.length; j<lj; j++) {
               if(temClasse(marcados[j], 'radio-circulo-marcado')) {
                    m = window.getComputedStyle(marcados[j]).getPropertyValue('display') || marcados[j].currentStyle.display; 
                    marcados[j].style.display=m==='none'?'block':'none';
               }
            }
        }
    }
    
}*/
/* RADIOS[FIM] */

/* AJAX[INICIO] */
/* REGISTRA RESPOSTAS AVALIACAO[INICIO] */
function registraRespostasAvaliacao(json) {
    var xmlhttp;
    
    if(window.XMLHttpRequest) {
        /* CODIGO PARA IE7+, Firefox, Chrome, Opera, Safari */
        xmlhttp = new XMLHttpRequest();
    } else {
        /* CODIGO PARA IE6, IE5 */
        xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
    }
        
    xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState===1) {
            document.getElementById('carregamento').style.display='block';
        } 
        
        if(xmlhttp.readyState===4 && xmlhttp.status===200) {
            document.getElementById('carregamento').style.display='none';
            document.getElementById('conteudo').innerHTML = xmlhttp.responseText;
            rodape();
        }
    };
        
    xmlhttp.open('POST', 'registra-avaliacao.php', true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send('jsonString='+json);
}
/* REGISTRA RESPOSTAS AVALIACAO[FIM] */

/* PEGA RESPOSTAS AVALIACAO[INICIO] */
function pegaRespostasAvaliacao(disciplinas, disponibilidade) {
    var ok=false, anterior=0, proximo=0, nomesChecados=[], correrNomesChecados=0, existeNomeDoCampo=true;
    var campos = document.forms['avaliacao'], jsonString=disciplinas===''?'{"disponibilidade":'+disponibilidade+', avaliacao":[':', "disponibilidade":'+disponibilidade+', "avaliacao":[';
    
    for(var i=0, l=campos.length; i<l; i++) {
        if(campos.elements[i].type==='text' || campos.elements[i].type==='textarea') {
            var disciplina = parseInt(campos.elements[i].getAttribute('data-disciplina'))===0?'':', "disciplina":' + campos.elements[i].getAttribute('data-disciplina');
            jsonString = jsonString + '{"pergunta":' + campos.elements[i].getAttribute('data-pergunta') + disciplina 
                    + ', "inteiro":false, "resposta":["' + campos.elements[i].value.replace(/[\\'"]/g, '').trim() + '"]},';
        } else if(campos.elements[i].type==='checkbox') {
            if(campos.elements[i].checked) {
                if(correrNomesChecados===0) {
                    nomesChecados.push(campos.elements[i].name);
                    correrNomesChecados++;
                } else {
                    for(var e=0, len=nomesChecados.length; e<len; e++) {
                        if(nomesChecados[e]===campos.elements[i].name) {
                            existeNomeDoCampo = true; 
                            break;
                        } else {
                            existeNomeDoCampo = false;
                        }
                    }

                    if(!existeNomeDoCampo) {
                        nomesChecados.push(campos.elements[i].name);
                        correrNomesChecados++;
                    }
                }
            }
        } else if(campos.elements[i].type==='radio') {
            if(campos.elements[i].checked) {
                var disciplina = parseInt(campos.elements[i].getAttribute('data-disciplina'))===0?'':', "disciplina":' + campos.elements[i].getAttribute('data-disciplina');
                jsonString = jsonString + '{"pergunta":' + campos.elements[i].getAttribute('data-pergunta') 
                    + ', "marcador": ' + campos.elements[i].getAttribute('data-marcador') + disciplina 
                    + ', "inteiro":true, "resposta":[' + campos.elements[i].value + ']},';
            }
        }
    }
    
    if(nomesChecados.length>0) {
        for(var i=0, l=nomesChecados.length; i<l; i++) {
            var disciplina = parseInt(campos[nomesChecados[i]][0].getAttribute('data-disciplina'))===0?'':', "disciplina":' + campos[nomesChecados[i]][0].getAttribute('data-disciplina');
            jsonString = jsonString + '{"pergunta":' + campos[nomesChecados[i]][0].getAttribute('data-pergunta') + disciplina + ', "inteiro":true, "resposta":[';
            for(var j=0, lj=campos[nomesChecados[i]].length; j<lj; j++) {
                if(campos[nomesChecados[i]][j].checked) {
                    jsonString = jsonString + campos[nomesChecados[i]][j].value + ', ';
                }
            } jsonString = jsonString.substr(0, jsonString.lastIndexOf(",")) + ']},';
        }
    } jsonString=disciplinas+jsonString.substr(0, jsonString.lastIndexOf(","))+']}';
                
    //console.log(jsonString);
    
    registraRespostasAvaliacao(jsonString);
}
/* PEGA RESPOSTAS AVALIACAO[FIM] */
/* AJAX[FIM] */

function confirmarConclusaoAvaliacao() {
    var url = window.self.location.toString();
    window.location = url.slice(0, url.search('avaliacao')) + 'index.php';
}

/* A VARIÁVEL porcentagemBlocosAvaliacao É ESCRITA PELO PHP */
function atribuirPorcentagemBlocosAvaliacao() {
    var valores = document.getElementsByClassName('porcentagem-valor-destaque');
    var preenchimentos = document.getElementsByClassName('porcentagem-preenchida');
    
    for(var i=0, l=porcentagemBlocosAvaliacao.length; i<l; i++) {
        valores[i].innerHTML=porcentagemBlocosAvaliacao[i]+'%';
        preenchimentos[i].setAttribute('style', 'width:'+porcentagemBlocosAvaliacao[i]+'%');
    }
}

window.onscroll = function() {
    var retorno=null, cabecalho = document.getElementById('cabecalho');
    if(cabecalho) {
        var doc = document.documentElement;
        var top = (window.pageYOffset || doc.scrollTop) - (doc.clientTop || 0);
                    
        top>0?adicionarClasse(cabecalho, 'cabecalho-destaque'):removerClasse(cabecalho, 'cabecalho-destaque');
    }
};
            
window.onresize=function() {
    /* ATIVA RODAPE NO FIM DA PAGINA[INICIO] */
    rodape();
    /* ATIVA RODAPE NO FIM DA PAGINA[FIM] */
};

window.onload=function() {
    var navegacaoToggle = document.getElementById('navegacao-toggle');
    if(navegacaoToggle) {
        var navegacao = document.getElementById('navegacao-container');
        navegacaoToggle.onclick=function() {
            navegacao.style.display='block';
            document.documentElement.style.overflow='hidden';
        };
                
        var navegacaoToggleFechar = document.getElementById('menu-toggle-fechar');
        if(navegacaoToggleFechar) {
            navegacaoToggleFechar.onclick=function() {
                navegacao.style.display='none';
                document.documentElement.style.overflow='auto';
            };
        }
    }
    
    /* ATIVA RODAPE NO FIM DA PAGINA[INICIO] */
    rodape();
    /* ATIVA RODAPE NO FIM DA PAGINA[FIM] */
    
    /* ATIVA VERIFICA CHECKBOXES[INICIO] *//*
    verificaCheckboxes();
    /* ATIVA VERIFICA CHECKBOXES[FIM] */
    
    /* ATIVA FECHA ENTRAR[INICIO] *//*
    document.body.onkeyup=function() {fechaEntrar(event);};
    /* ATIVA FECHA ENTRAR[FIM] */
    
    /* ATIVA ENTRAR[INICIO] *//*
    var botaoEntrar=document.getElementById('botao-entrar');
    if(botaoEntrar) {
        botaoEntrar.onclick=function() {entrar(100, 20);};
    }
    
    var entrarBotaoCancelar=document.getElementById('entrar-botao-cacelar');
    if(entrarBotaoCancelar) {
        entrarBotaoCancelar.onclick=function() {entrar(100, 20);};
    }
    
    var entrarBotaoFechar=document.getElementById('entrar-botao-fechar');
    if(entrarBotaoFechar) {
        entrarBotaoFechar.onclick=function() {entrar(100, 20);};
    }
    /* ATIVA ENTRAR[FIM] */
    
    /* ATIVA BOTAO AVALIACAO CONTINUAR[INICIO] */
    var botaoAvaliacaoContinuar = document.getElementsByClassName('botao-avaliacao-continuar');
    for(var i=0, l=botaoAvaliacaoContinuar.length; i<l; i++) {
        botaoAvaliacaoContinuar[i].onclick=function(){perguntasProximas(this);};
    }
    /* ATIVA BOTAO AVALIACAO CONTINUAR[FIM] */
    
    /* ATIVA BOTAO AVALIACAO VOLTAR[INICIO] */
    var botaoAvaliacaoVoltar = document.getElementsByClassName('botao-avaliacao-voltar');
    for(var i=0, l=botaoAvaliacaoVoltar.length; i<l; i++) {
        botaoAvaliacaoVoltar[i].onclick=function(){perguntasAnteriores(this);};
    }
    /* ATIVA BOTAO AVALIACAO VOLTAR[FIM] */
    
    /* VALIDA CAMPOS PRE AVALIACAO[INICIO] */
    var botaoPreAvaliacao=document.getElementById('botao-pre-avaliacao');
    if(botaoPreAvaliacao) {
        botaoPreAvaliacao.onclick=function() {validaCamposPreAvaliacao();};
    }
    /* VALIDA CAMPOS PRE AVALIACAO[FIM] */
    
    /* ATIVA CHECKBOX[INICIO] *//*
    var checkboxes = document.getElementsByClassName('checkbox');
    for(var i=0, l=checkboxes.length; i<l; i++) {
        checkboxes[i].onclick=function() {marcaDesmarcaCheckbox(this);};
    }
    /* ATIVA CHECKBOX[FIM] */
    
    /* ATIVA RADIO[INICIO] *//*
    var radios = document.getElementsByClassName('radio');
    for(var i=0, l=radios.length; i<l; i++) {
        radios[i].onclick=function() {marcaDesmarcaRadio(this);};
    }
    /* ATIVA RADIO[FIM] */
    
    /* ATIVA CHECKBOX ESPECIAL[INICIO] */
    var checkboxesEspeciais = document.getElementsByClassName('checkbox-especial');
    for(var i=0, l=checkboxesEspeciais.length; i<l; i++) {
        checkboxesEspeciais[i].onclick=function() {marcaDesmarcaCheckboxEspecial(this);};
    }
    /* ATIVA CHECKBOX ESPECIAL[FIM] */
    
    /* ATIVA PRE AVALIACAO VER[INICIO] */
    var preAvaliacaoVer = document.getElementsByClassName('ver-disciplinas');
    for(var i=0, l=preAvaliacaoVer.length; i<l; i++) {
        preAvaliacaoVer[i].onclick=function() {verPreAvaliacao(this);};
    }
    /* ATIVA PRE AVALIACAO VER[FIM] */
    
    /* ATIVA BOTOES(OK, CANCELAR) DISCIPLINAS[INICIO] */
    var botaoDisciplinasOk=document.getElementsByClassName('botao-disciplinas-ok');
    for(var i=0, l=botaoDisciplinasOk.length; i<l; i++) {
        botaoDisciplinasOk[i].onclick=function() {preAvaliacao(this.parentNode.parentNode.parentNode, 100, 20, false);};
    }
    
    var botaoDisciplinasCancelar=document.getElementsByClassName('botao-disciplinas-cancelar');
    for(var i=0, l=botaoDisciplinasCancelar.length; i<l; i++) {
        botaoDisciplinasCancelar[i].onclick=function() {desmarcaDisciplinas(this);};
    }
    /* ATIVA BOTOES(OK, CANCELAR) DISCIPLINAS[FIM] */
    
    /* EVITA QUE O PROJETO SEJA EXECUTADO EM UM IFRAME[INICIO] */
    /*if(window.self!==window.top) { 
        window.top.location.replace(window.location.href); 
    }*/
    /* EVITA QUE O PROJETO SEJA EXECUTADO EM UM IFRAME[FIM] */
};