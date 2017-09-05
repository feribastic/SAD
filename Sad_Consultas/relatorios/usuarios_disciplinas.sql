select count(distinct id_usuario),disciplina from respostas
inner join disciplinas on respostas.id_disciplina=disciplinas.id_disciplina
group by disciplina
order by disciplina
