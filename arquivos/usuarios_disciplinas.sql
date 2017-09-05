select count(distinct id_usuario),disciplina from sad_respostas
inner join sad_disciplinas on sad_respostas.id_disciplina=sad_disciplinas.id_disciplina
where id_avaliacao=3
group by disciplina
order by disciplina
