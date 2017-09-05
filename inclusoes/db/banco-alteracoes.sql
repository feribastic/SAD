USE sad;

DELETE FROM disciplinas_perguntas WHERE id_disciplina=1;
DELETE FROM disciplinas WHERE id_disciplina=1;

ALTER TABLE disciplinas CHANGE esta_habilitada esta_disponivel BOOLEAN NOT NULL;
ALTER TABLE disciplinas ADD e_eletiva BOOLEAN NOT NULL DEFAULT FALSE AFTER esta_disponivel;
ALTER TABLE disciplinas_perguntas CHANGE esta_habilitada esta_disponivel BOOLEAN NOT NULL;

ALTER TABLE avaliacoes ADD COLUMN nome VARCHAR(255) NOT NULL AFTER id_avaliacao, 
ADD COLUMN estatisticas_antecipadas BOOLEAN NOT NULL DEFAULT FALSE AFTER e_manutencao,
ADD COLUMN esta_processada BOOLEAN NOT NULL DEFAULT FALSE AFTER estatisticas_antecipadas;

ALTER TABLE usuarios ADD COLUMN data_ultimo_acesso DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER nivel_acesso;

RENAME TABLE alternativas TO sad_alternativas, 
avaliacoes TO sad_avaliacoes, 
disciplinas TO sad_disciplinas, 
disciplinas_perguntas TO sad_disciplinas_perguntas, 
grupos TO sad_grupos, perguntas TO sad_perguntas, 
perguntas_alternativas TO sad_perguntas_alternativas, 
respostas TO sad_respostas, usuarios TO sad_usuarios, 
usuarios_configuracoes TO sad_usuarios_configuracoes, 
usuarios_historico TO sad_usuarios_historico;

CREATE TABLE IF NOT EXISTS sad_marcadores(
    id_marcador INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    marcador VARCHAR(255) NOT NULL,
    id_usuario_historico INT UNSIGNED NOT NULL,
    CONSTRAINT fk_marcadores_usuarios_historico FOREIGN KEY(id_usuario_historico) REFERENCES sad_usuarios_historico(id_usuario_historico)
);

CREATE TABLE IF NOT EXISTS sad_perguntas_marcadores(
    id_pergunta_marcador INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_pergunta INT UNSIGNED NOT NULL,
    id_marcador INT UNSIGNED NOT NULL,
    id_usuario_historico INT UNSIGNED NOT NULL,
    CONSTRAINT fk_perguntas_marcadores_perguntas FOREIGN KEY(id_pergunta) REFERENCES sad_perguntas(id_pergunta),
    CONSTRAINT fk_perguntas_marcadores_marcadores FOREIGN KEY(id_marcador) REFERENCES sad_marcadores(id_marcador),
    CONSTRAINT fk_perguntas_marcadores_usuarios_historico FOREIGN KEY(id_usuario_historico) REFERENCES sad_usuarios_historico(id_usuario_historico)
);

INSERT INTO sad_marcadores(id_marcador, marcador, id_usuario_historico) VALUES
(NULL, 'sem-marcador', 1), 
(NULL, 'Marcador', 1);

ALTER TABLE sad_respostas ADD id_marcador INT UNSIGNED NOT NULL DEFAULT 1 AFTER id_pergunta;
ALTER TABLE sad_respostas ADD CONSTRAINT fk_respostas_marcadores FOREIGN KEY sad_respostas(id_marcador) REFERENCES sad_marcadores(id_marcador);

CREATE TABLE sad_unidades_academicas(
    id_unidade_academica INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    unidade_academica VARCHAR(255) NOT NULL,
    sigla VARCHAR(30) NOT NULL,
    esta_disponivel BOOLEAN NOT NULL DEFAULT TRUE,
    id_usuario_historico INT UNSIGNED NOT NULL,
    CONSTRAINT fk_unidades_academicas_usuarios_historico FOREIGN KEY(id_usuario_historico) REFERENCES sad_usuarios_historico(id_usuario_historico)
);

CREATE TABLE sad_departamentos(
    id_departamento INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    departamento VARCHAR(255) NOT NULL,
    sigla VARCHAR(30) NOT NULL,
    esta_disponivel BOOLEAN NOT NULL DEFAULT TRUE,
    id_unidade_academica INT UNSIGNED NOT NULL,
    id_usuario_historico INT UNSIGNED NOT NULL,
    CONSTRAINT fk_departamentos_unidades_academicas FOREIGN KEY(id_unidade_academica) REFERENCES sad_unidades_academicas(id_unidade_academica),
    CONSTRAINT fk_departamentos_usuarios_historico FOREIGN KEY(id_usuario_historico) REFERENCES sad_usuarios_historico(id_usuario_historico)
);

INSERT INTO sad_unidades_academicas(id_unidade_academica, unidade_academica, sigla, esta_disponivel, id_usuario_historico) VALUES
(NULL, 'Instituto de Nutrição', 'INU', 1, 1);

INSERT INTO sad_departamentos(id_departamento, departamento, sigla, esta_disponivel, id_unidade_academica, id_usuario_historico) VALUES
(NULL, 'Nutrição Aplicada', 'DNA', 1, 1, 1),
(NULL, 'Nutrição Básica e Experimental', 'DNBE', 1, 1, 1),
(NULL, 'Nutrição Social', 'DNS', 1, 1, 1);

ALTER TABLE sad_disciplinas ADD id_departamento INT UNSIGNED NOT NULL DEFAULT 1 AFTER e_eletiva;
ALTER TABLE sad_disciplinas ADD CONSTRAINT fk_disciplinas_departamentos FOREIGN KEY(id_departamento) REFERENCES sad_departamentos(id_departamento);

INSERT INTO sad_usuarios(id_usuario, apelido, nome, email, senha, token, nivel_acesso, data_ultimo_acesso, data_cadastro) VALUES
(NULL, 'Felipe Ribas', 'Felipe Ribas Coutinho', 'feribastic@gmail.com', AES_ENCRYPT('root', 'Yan.1995'), '', 10, NOW(), NOW());

UPDATE sad_usuarios SET token='';
UPDATE sad_usuarios SET senha=AES_ENCRYPT('root', 'Yan.1995') WHERE id_usuario=1;

UPDATE sad_usuarios_configuracoes SET cor_tema='005DAB' WHERE id_usuario=1;

/*
SELECT sad_avaliacoes.nome, DATE_FORMAT(sad_avaliacoes.inicio, '%d/%m/%Y %k:%i:%s') 
AS inicio, DATE_FORMAT(sad_avaliacoes.termino, '%d/%m/%Y %k:%i:%s') AS termino, sad_disciplinas.id_disciplina,
sad_disciplinas.disciplina, sad_perguntas.id_pergunta, sad_perguntas.pergunta, sad_marcadores.id_marcador, sad_marcadores.marcador, 
sad_grupos.id_grupo, sad_grupos.grupo, sad_alternativas.id_alternativa, sad_alternativas.alternativa, 
sad_respostas.resposta_textual, sad_respostas.id_usuario, DATE_FORMAT(sad_respostas.data, '%d/%m/%Y %k:%i:%s') AS data
FROM sad_respostas INNER JOIN sad_avaliacoes ON sad_respostas.id_avaliacao=sad_avaliacoes.id_avaliacao INNER JOIN sad_disciplinas 
ON sad_respostas.id_disciplina=sad_disciplinas.id_disciplina INNER JOIN sad_perguntas
ON sad_respostas.id_pergunta=sad_perguntas.id_pergunta INNER JOIN sad_marcadores 
ON sad_respostas.id_marcador=sad_marcadores.id_marcador INNER JOIN sad_grupos 
ON sad_perguntas.id_grupo=sad_grupos.id_grupo INNER JOIN sad_alternativas 
ON sad_respostas.id_alternativa=sad_alternativas.id_alternativa
ORDER BY disciplina, pergunta, marcador, alternativa, id_usuario;
*/