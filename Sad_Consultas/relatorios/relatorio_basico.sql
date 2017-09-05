select disciplina, pergunta,resposta_textual from respostas
    inner join disciplinas on respostas.id_disciplina=disciplinas.id_disciplina
    inner join perguntas on respostas.id_pergunta=perguntas.id_pergunta
    where (resposta_textual !="") AND (pergunta like '%institui%')    
    order by disciplina


select disciplina, resposta_textual from respostas
inner join disciplinas on respostas.id_disciplina=disciplinas.id_disciplina
inner join perguntas on respostas.id_pergunta=perguntas.id_pergunta
inner join alternativas on respostas.id_alternativa=alternativas.id_alternativa

select pergunta,resposta_textual from respostas
    inner join perguntas on respostas.id_pergunta=perguntas.id_pergunta
    where (resposta_textual !="") AND (pergunta like '%auto-ava%')
group by resposta_textual

select disciplina, pergunta,resposta_textual from respostas
    inner join disciplinas on respostas.id_disciplina=disciplinas.id_disciplina
    inner join perguntas on respostas.id_pergunta=perguntas.id_pergunta
    where (resposta_textual !="") AND (pergunta not like '%curso%')
        AND (pergunta not like '%institui%')
        AND (pergunta not like '%auto-ava%')
order by disciplina




