
select id_usuario, id_disciplina from sad_respostas
inner join sad_disciplinas
where id_avaliacao=6
group by id_disciplina