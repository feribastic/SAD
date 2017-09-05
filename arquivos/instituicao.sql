SELECT pergunta, alternativa, count(distinct id_usuario)FROM respostas    
inner join perguntas on respostas.id_pergunta=perguntas.id_pergunta
inner join alternativas on respostas.id_alternativa=alternativas.id_alternativa
where id_grupo = 4 and tipo_entrada like "radio"
group by alternativa,pergunta
order by pergunta ASC