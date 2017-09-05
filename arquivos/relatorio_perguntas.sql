SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 4 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 3
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 4 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 6
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 5 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 3
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 5 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 6
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 6 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 3
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 6 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 6
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 7 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 3
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 7 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 6
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 8 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 3
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 8 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 6
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 9 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 3
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 9 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 6
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 10 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 3
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;

SELECT disciplina, pergunta, alternativa, COUNT(*) FROM sad_respostas    
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
inner join sad_perguntas on sad_respostas.id_pergunta=sad_perguntas.id_pergunta
inner join sad_alternativas on sad_respostas.id_alternativa=sad_alternativas.id_alternativa
where id_departamento = 10 and id_grupo = 1 and pergunta not like '%deseja%' and id_avaliacao = 6
group by disciplina,pergunta, alternativa
order by disciplina, pergunta;




