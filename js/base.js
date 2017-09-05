"use strict";function isArray(a){return a.constructor.toString().indexOf("Array")>-1}function removerEspacosEmBranco(a){return a.replace(/^\s+|\s+$/gm,"")}function temClasse(a,b){var c,d=!1;if(void 0!==a&&null!==a&&""!==a&&void 0!==a.className&&null!==a.className&&""!==a.className){c=a.className.split(" ");for(var e=0,f=c.length;e<f;e++)if(c[e]===b){d=!0;break}}return d}function removerClasse(a,b){if(void 0!==a&&null!==a&&""!==a&&""!==b&&temClasse(a,b)&&void 0!==a.className&&null!==a.className&&""!==a.className){for(var c=a.className.replace("  "," "),d=c.split(" "),e="",f=0,g=d.length;f<g;f++)d[f]!==b&&(e=""!==e?e=e+" "+d[f]:d[f]);a.className="",a.className=e}return!temClasse(a,b)}function adicionarClasse(a,b){return void 0!==a&&null!==a&&""!==a&&""!==b&&(temClasse(a,b)||void 0!==a.className&&null!==a.className&&(a.className=a.className=""!==a.className?a.className+" "+b:b)),temClasse(a,b)}function rodape(){var a=document.getElementById("conteudo");if(null!==a){var b=document.getElementById("rodape"),c=0;if(null!==b){c=b.offsetHeight;var d=window.innerHeight||document.documentElement.clientHeight,e=d-c;a.offsetHeight<e?adicionarClasse(b,"rodape-fixo"):removerClasse(b,"rodape-fixo")}}}function entrar(a,b){var c=document.getElementById("entrar"),d=document.getElementById("fundo-primario"),e=document.getElementById("login"),f=document.getElementById("senha"),g=document.getElementById("entrar-botao-fechar");entrarAtivado?(d.style.display="none",g.style.display="none",document.documentElement.style.overflow="auto",entrarHeight>0?(entrarHeight-=b,c.style.height=entrarHeight+"%",setTimeout(function(){entrar(a,b)},50)):0===entrarHeight&&(entrarAtivado=!1,c.style.display="none",e.value="",f.value="")):(d.style.display="block",c.style.display="block",g.style.display="block",document.documentElement.style.overflow="hidden",entrarHeight<a?(entrarHeight+=b,c.style.height=entrarHeight+"%",setTimeout(function(){entrar(a,b)},50)):entrarHeight===a&&(entrarAtivado=!0,e.focus()))}function fechaEntrar(a){var b=new Number;window.event?b=a.keyCode:a.which&&(b=a.which),27===b&&entrar(100,20)}function preAvaliacao(a,b,c,d){var e=a,f=document.getElementById("fundo-primario"),g=a.childNodes;if(preAvaliacaoAtivado){f.style.display="none",document.documentElement.style.overflow="auto";for(var h=0,i=g.length;h<i;h++)if(temClasse(g[h],"disciplinas-conteudo")){g[h].style.display="none";break}preAvaliacaoWidth>0?(preAvaliacaoWidth-=c,e.style.width=preAvaliacaoWidth+"%",setTimeout(function(){preAvaliacao(a,b,c,!0)},40)):0===preAvaliacaoWidth&&(preAvaliacaoAtivado=!1,e.style.display="none")}else if(f.style.display="block",e.style.display="block",document.documentElement.style.overflow="hidden",preAvaliacaoWidth<b)preAvaliacaoWidth+=c,e.style.width=preAvaliacaoWidth+"%",setTimeout(function(){preAvaliacao(a,b,c,!0)},40);else if(preAvaliacaoWidth===b){for(var h=0,i=g.length;h<i;h++)if(temClasse(g[h],"disciplinas-conteudo")){g[h].style.display="block";break}preAvaliacaoAtivado=!0}if(!d){for(var j=0,k=a.childNodes,h=0,i=k.length;h<i;h++)if(temClasse(k[h],"disciplinas-conteudo"))for(var l=k[h].childNodes,m=0,n=l.length;m<n;m++)if(temClasse(l[m],"disciplinas-bloco"))for(var o=l[m].childNodes,p=0,q=o.length;p<q;p++)temClasse(o[p],"checkbox-disciplinas")&&o[p].checked&&j++;if(0===j){for(var r=document.getElementsByClassName("periodos"),h=0,i=r.length;h<i;h++)if(r[h].getAttribute("data-periodo")===a.getAttribute("data-disciplinas")){var e=r[h].previousSibling;for(r[h].checked=!1;e;){if(temClasse(e,"checkbox-especial")&&e.getAttribute("for")===r[h].getAttribute("id")){for(var l=e.childNodes,p=0,q=l.length;p<q;p++)if(temClasse(l[p],"checkbox-quadrado-especial"))for(var o=l[p].childNodes,m=0,n=o.length;m<n;m++)temClasse(o[m],"checkbox-quadrado-marcado-especial")&&(o[m].style.display="none");break}e=e.previousSibling}break}for(var r=document.getElementsByClassName("periodos"),s=a.getAttribute("data-disciplinas"),h=0,i=r.length;h<i;h++)s===r[h].getAttribute("data-periodo")&&podeMostrarVerDisciplinas(r[h].checked?!0:!1,r[h])}}}function verPreAvaliacao(a){for(var b="",c=a.getAttribute("data-disciplinas"),d=document.getElementsByClassName("disciplinas"),e=0,f=d.length;e<f;e++)if(d[e].getAttribute("data-disciplinas")===c){b=d[e];break}preAvaliacao(b,100,20,!0)}function validaCamposPreAvaliacao(){for(var a=document.forms["pre-avaliacao"],b=0,d=0,e=a.length;d<e;d++)"checkbox"===a.elements[d].type&&a.elements[d].checked&&b++;b>0?a.submit():alert("Por favor escolha ao menos 1 Disciplina")}function desmarcaDisciplinas(a){for(var b=a.parentNode.parentNode.childNodes,c=0,d=b.length;c<d;c++)if(temClasse(b[c],"disciplinas-bloco"))for(var e=b[c].childNodes,f=0,g=e.length;f<g;f++)if(temClasse(e[f],"checkbox-disciplinas"))e[f].checked=!1;else if(temClasse(e[f],"checkbox"))for(var h=e[f].childNodes,i=0,j=h.length;i<j;i++)if(temClasse(h[i],"checkbox-quadrado"))for(var k=h[i].childNodes,l=0,m=k.length;l<m;l++)temClasse(k[l],"checkbox-quadrado-marcado")&&(k[l].style.display="none");for(var n=a.parentNode.parentNode.parentNode,o=document.getElementsByClassName("periodos"),c=0,d=o.length;c<d;c++)if(o[c].getAttribute("data-periodo")===n.getAttribute("data-disciplinas")){var p=o[c].previousSibling;for(o[c].checked=!1,podeMostrarVerDisciplinas(!1,o[c]);p;){if(temClasse(p,"checkbox-especial")&&p.getAttribute("for")===o[c].getAttribute("id")){for(var q=p.childNodes,f=0,g=q.length;f<g;f++)if(temClasse(q[f],"checkbox-quadrado-especial"))for(var r=q[f].childNodes,s=0,t=r.length;s<t;s++)temClasse(r[s],"checkbox-quadrado-marcado-especial")&&(r[s].style.display="none");break}p=p.previousSibling}break}preAvaliacao(a.parentNode.parentNode.parentNode,100,20,!0)}function podeMostrarVerDisciplinas(a,b){var c="none";a&&(c="block");for(var d=document.getElementsByClassName("ver-disciplinas"),e=b.getAttribute("data-periodo"),f=0,g=d.length;f<g;f++)d[f].getAttribute("data-disciplinas")===e&&(d[f].style.display=c)}function marcaDesmarcaCheckboxEspecial(a){var c=(a.childNodes,a.getAttribute("for")),d=document.getElementById(c);podeMostrarVerDisciplinas(d.checked?!1:!0,d);for(var e=document.getElementById(c),f=document.getElementById(c).getAttribute("data-periodo"),g=document.getElementsByClassName("disciplinas"),h="",i=!1,j=0,k=g.length;j<k;j++)if(g[j].getAttribute("data-disciplinas")===f){if(h=g[j],e.checked){for(var l=g[j].childNodes,m=0,n=l.length;m<n;m++)if(temClasse(l[m],"disciplinas-conteudo")){for(var o=l[m].childNodes,p=0,q=o.length;p<q;p++)if(temClasse(o[p],"disciplinas-bloco"))for(var r=o[p].childNodes,s=0,t=r.length;s<t;s++)if(temClasse(r[s],"checkbox-disciplinas"))r[s].checked=!1;else if(temClasse(r[s],"checkbox"))for(var u=r[s].childNodes,v=0,w=u.length;v<w;v++)if(temClasse(u[v],"checkbox-quadrado"))for(var x=u[v].childNodes,y=0,z=x.length;y<z;y++)temClasse(x[y],"checkbox-quadrado-marcado")&&(x[y].style.display="none");break}}else i=!0;break}""!==h&&i&&preAvaliacao(h,100,20,!0)}function ordenarElementos(a,b){return a.ordem===b.ordem?0:a.ordem<b.ordem?-1:1}function validaAvaliacao(a){for(var b=[],c=!0,d=[],e=0,f=0,g=[],h=a.getAttribute("data-correspondentes"),i=document.getElementsByClassName("obrigatorio"),j=0,k=i.length;j<k;j++)if(i[j].getAttribute("data-correspondentes")===h)if("radio"===i[j].type||"checkbox"===i[j].type){var l=i[j].getAttribute("name");if(0===b.length)b.push(l);else{for(var m=0,n=b.length;m<n;m++){if(b[m]===l){c=!0;break}c=!1}c||b.push(l)}}else if("text"===i[j].type||"textarea"===i[j].type){var o=document.getElementById(i[j].getAttribute("data-id-aviso")),p=o.parentNode.parentNode;""===i[j].value.replace(/[\\'"]/g,"").trim()?(d.push(!1),adicionarClasse(p,"avaliacao-linha-aviso"),o.style.display="block",g.push({ordem:parseInt(o.getAttribute("data-ordem")),aviso:p,elemento:i[j],e_texto:!0})):(d.push(!0),removerClasse(p,"avaliacao-linha-aviso"),o.style.display="none")}for(var j=0;j<b.length;j++){for(var q=document.getElementsByName(b[j]),m=0;m<q.length;m++)q[m].checked&&f++;d.push(f>0?!0:!1);var o=document.getElementById(q[0].getAttribute("data-id-aviso")),p=o.parentNode.parentNode;if(f>0){var r=o.getAttribute("data-erros");if(null!==r&&void 0!==r)if(r.length>0){for(var s=r.split(","),t="",u=0,v=s.length;u<v;u++)s[u]!==b[j]&&(t+=s[u]+",");t=t.substr(0,t.lastIndexOf(",")),o.setAttribute("data-erros",t)}else removerClasse(p,"avaliacao-linha-aviso"),o.style.display="none";else removerClasse(p,"avaliacao-linha-aviso"),o.style.display="none"}else{var r=o.getAttribute("data-erros");if(null!==r&&void 0!==r)if(r.length>0){for(var s=r.split(","),t="",w=!1,u=0,v=s.length;u<v;u++){if(s[u]===b[j]){w=!0;break}w=!1}w||s.push(b[j]),o.setAttribute("data-erros",s)}else o.setAttribute("data-erros",b[j]);adicionarClasse(p,"avaliacao-linha-aviso"),o.style.display="block",g.push({ordem:parseInt(o.getAttribute("data-ordem")),aviso:p,elemento:null,e_texto:!1})}f=0}if(g.length>0){g.sort(ordenarElementos);var x=g[0].aviso.documentOffsetTop-55;x=x<0?0:x,window.scrollTo(0,x),g[0].e_texto&&null!==g[0].elemento&&g[0].elemento.focus()}for(var y=0;y<d.length;y++)d[y]||e++;return rodape(),0===e?!0:!1}function enviarAvaliacao(a,b,c){validaAvaliacao(a)&&pegaRespostasAvaliacao(b,c)}function perguntasProximas(a){if(validaAvaliacao(a)){a.parentNode.parentNode.parentNode.style.display="none";for(var b=a.parentNode.parentNode.parentNode.nextSibling;b;){if(temClasse(b,"avaliacao-perguntas")){b.style.display="inline";break}b=b.nextSibling}window.scrollTo(0,0)}rodape()}function perguntasAnteriores(a){a.parentNode.parentNode.parentNode.style.display="none";for(var b=a.parentNode.parentNode.parentNode.previousSibling;b;){if(temClasse(b,"avaliacao-perguntas")){b.style.display="inline";break}b=b.previousSibling}window.scrollTo(0,0),rodape()}function registraRespostasAvaliacao(a){var b;b=window.XMLHttpRequest?new XMLHttpRequest:new ActiveXObject("Microsoft.XMLHTTP"),b.onreadystatechange=function(){1===b.readyState&&(document.getElementById("carregamento").style.display="block"),4===b.readyState&&200===b.status&&(document.getElementById("carregamento").style.display="none",document.getElementById("conteudo").innerHTML=b.responseText,rodape())},b.open("POST","registra-avaliacao.php",!0),b.setRequestHeader("Content-type","application/x-www-form-urlencoded"),b.send("jsonString="+a)}function pegaRespostasAvaliacao(a,b){for(var f=[],g=0,h=!0,i=document.forms.avaliacao,j=""===a?'{"disponibilidade":'+b+', avaliacao":[':', "disponibilidade":'+b+', "avaliacao":[',k=0,l=i.length;k<l;k++)if("text"===i.elements[k].type||"textarea"===i.elements[k].type){var m=0===parseInt(i.elements[k].getAttribute("data-disciplina"))?"":', "disciplina":'+i.elements[k].getAttribute("data-disciplina");j=j+'{"pergunta":'+i.elements[k].getAttribute("data-pergunta")+m+', "inteiro":false, "resposta":["'+i.elements[k].value.replace(/[\\'"]/g,"").trim()+'"]},'}else if("checkbox"===i.elements[k].type){if(i.elements[k].checked)if(0===g)f.push(i.elements[k].name),g++;else{for(var n=0,o=f.length;n<o;n++){if(f[n]===i.elements[k].name){h=!0;break}h=!1}h||(f.push(i.elements[k].name),g++)}}else if("radio"===i.elements[k].type&&i.elements[k].checked){var m=0===parseInt(i.elements[k].getAttribute("data-disciplina"))?"":', "disciplina":'+i.elements[k].getAttribute("data-disciplina");j=j+'{"pergunta":'+i.elements[k].getAttribute("data-pergunta")+', "marcador": '+i.elements[k].getAttribute("data-marcador")+m+', "inteiro":true, "resposta":['+i.elements[k].value+"]},"}if(f.length>0)for(var k=0,l=f.length;k<l;k++){var m=0===parseInt(i[f[k]][0].getAttribute("data-disciplina"))?"":', "disciplina":'+i[f[k]][0].getAttribute("data-disciplina");j=j+'{"pergunta":'+i[f[k]][0].getAttribute("data-pergunta")+m+', "inteiro":true, "resposta":[';for(var p=0,q=i[f[k]].length;p<q;p++)i[f[k]][p].checked&&(j=j+i[f[k]][p].value+", ");j=j.substr(0,j.lastIndexOf(","))+"]},"}j=a+j.substr(0,j.lastIndexOf(","))+"]}",registraRespostasAvaliacao(j)}function confirmarConclusaoAvaliacao(){var a=window.self.location.toString();window.location=a.slice(0,a.search("avaliacao"))+"index.php"}function atribuirPorcentagemBlocosAvaliacao(){for(var a=document.getElementsByClassName("porcentagem-valor-destaque"),b=document.getElementsByClassName("porcentagem-preenchida"),c=0,d=porcentagemBlocosAvaliacao.length;c<d;c++)a[c].innerHTML=porcentagemBlocosAvaliacao[c]+"%",b[c].setAttribute("style","width:"+porcentagemBlocosAvaliacao[c]+"%")}window.Object.defineProperty(HTMLElement.prototype,"documentOffsetTop",{get:function(){return this.offsetTop+(this.offsetParent?this.offsetParent.documentOffsetTop:0)}});var entrarHeight=0,entrarAtivado=!1,preAvaliacaoWidth=0,preAvaliacaoAtivado=!1;window.onscroll=function(){var b=document.getElementById("cabecalho");if(b){var c=document.documentElement,d=(window.pageYOffset||c.scrollTop)-(c.clientTop||0);d>0?adicionarClasse(b,"cabecalho-destaque"):removerClasse(b,"cabecalho-destaque")}},window.onresize=function(){rodape()},window.onload=function(){var a=document.getElementById("navegacao-toggle");if(a){var b=document.getElementById("navegacao-container");a.onclick=function(){b.style.display="block",document.documentElement.style.overflow="hidden"};var c=document.getElementById("menu-toggle-fechar");c&&(c.onclick=function(){b.style.display="none",document.documentElement.style.overflow="auto"})}rodape();for(var d=document.getElementsByClassName("botao-avaliacao-continuar"),e=0,f=d.length;e<f;e++)d[e].onclick=function(){perguntasProximas(this)};for(var g=document.getElementsByClassName("botao-avaliacao-voltar"),e=0,f=g.length;e<f;e++)g[e].onclick=function(){perguntasAnteriores(this)};var h=document.getElementById("botao-pre-avaliacao");h&&(h.onclick=function(){validaCamposPreAvaliacao()});for(var i=document.getElementsByClassName("checkbox-especial"),e=0,f=i.length;e<f;e++)i[e].onclick=function(){marcaDesmarcaCheckboxEspecial(this)};for(var j=document.getElementsByClassName("ver-disciplinas"),e=0,f=j.length;e<f;e++)j[e].onclick=function(){verPreAvaliacao(this)};for(var k=document.getElementsByClassName("botao-disciplinas-ok"),e=0,f=k.length;e<f;e++)k[e].onclick=function(){preAvaliacao(this.parentNode.parentNode.parentNode,100,20,!1)};for(var l=document.getElementsByClassName("botao-disciplinas-cancelar"),e=0,f=l.length;e<f;e++)l[e].onclick=function(){desmarcaDisciplinas(this)}};