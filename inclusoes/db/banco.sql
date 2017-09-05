CREATE DATABASE IF NOT EXISTS sad CHARACTER SET='utf8' COLLATE='utf8_general_ci';

USE sad;

CREATE TABLE IF NOT EXISTS sad_usuarios(
    id_usuario INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    apelido VARCHAR(50) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
    token VARCHAR(255) NOT NULL,
    nivel_acesso INT(10) UNSIGNED NOT NULL,
    data_ultimo_acesso DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    data_cadastro DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS sad_usuarios_configuracoes(
    id_usuarios_configuracoes INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    cor_tema VARCHAR(6) NOT NULL DEFAULT '66B24C',
    CONSTRAINT fk_usuarios_configuracoes_usuarios FOREIGN KEY(id_usuario) REFERENCES sad_usuarios(id_usuario)
);

CREATE TABLE IF NOT EXISTS sad_usuarios_historico(
    id_usuario_historico INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    acao TEXT NOT NULL,
    data_registro DATETIME NOT NULL,
    CONSTRAINT fk_usuarios_historico_usuarios FOREIGN KEY(id_usuario) REFERENCES sad_usuarios(id_usuario)
);

CREATE TABLE IF NOT EXISTS sad_avaliacoes(
    id_avaliacao INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    inicio DATETIME NOT NULL,
    termino DATETIME NOT NULL,
    e_manutencao BOOLEAN NOT NULL DEFAULT FALSE,
    estatisticas_antecipadas BOOLEAN NOT NULL DEFAULT FALSE,
    esta_processada BOOLEAN NOT NULL DEFAULT FALSE,
    id_usuario_historico INT UNSIGNED NOT NULL,
    CONSTRAINT fk_avaliacoes_usuarios_historico FOREIGN KEY(id_usuario_historico) REFERENCES sad_usuarios_historico(id_usuario_historico)
);

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

CREATE TABLE IF NOT EXISTS sad_disciplinas(
    id_disciplina INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    disciplina VARCHAR(255) NOT NULL,
    periodo INT(20) NOT NULL,
    esta_disponivel BOOLEAN NOT NULL,
    e_eletiva BOOLEAN NOT NULL DEFAULT FALSE,
    id_departamento INT UNSIGNED NOT NULL,
    id_usuario_historico INT UNSIGNED NOT NULL,
    CONSTRAINT fk_disciplinas_departamentos FOREIGN KEY(id_departamento) REFERENCES sad_departamentos(id_departamento),
    CONSTRAINT fk_disciplinas_usuarios_historico FOREIGN KEY(id_usuario_historico) REFERENCES sad_usuarios_historico(id_usuario_historico)
);

CREATE TABLE IF NOT EXISTS sad_grupos(
    id_grupo INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    grupo varchar(255) NOT NULL UNIQUE,
    ordem INT UNSIGNED NOT NULL,
    id_usuario_historico INT UNSIGNED NOT NULL,
    CONSTRAINT fk_grupos_usuarios_historico FOREIGN KEY(id_usuario_historico) REFERENCES sad_usuarios_historico(id_usuario_historico)
);

CREATE TABLE IF NOT EXISTS sad_perguntas(
    id_pergunta INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    pergunta VARCHAR(255) NOT NULL UNIQUE,
    texto_ajuda VARCHAR(255) NOT NULL,
    id_grupo INT UNSIGNED NOT NULL,
    tipo_entrada VARCHAR(25) NOT NULL,
    e_alternativa BOOLEAN NOT NULL,
    e_obrigatoria BOOLEAN NOT NULL,
    id_usuario_historico INT UNSIGNED NOT NULL,
    CONSTRAINT fk_perguntas_grupos FOREIGN KEY(id_grupo) REFERENCES sad_grupos(id_grupo),
    CONSTRAINT fk_perguntas_usuarios_historico FOREIGN KEY(id_usuario_historico) REFERENCES sad_usuarios_historico(id_usuario_historico)
);

CREATE TABLE IF NOT EXISTS sad_alternativas(
    id_alternativa INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    alternativa VARCHAR(255) NOT NULL UNIQUE,
    id_usuario_historico INT UNSIGNED NOT NULL,
    CONSTRAINT fk_alternativas_usuarios_historico FOREIGN KEY(id_usuario_historico) REFERENCES sad_usuarios_historico(id_usuario_historico)
);

CREATE TABLE IF NOT EXISTS sad_perguntas_alternativas(
    id_pergunta_alternativa INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_pergunta INT UNSIGNED NOT NULL,
    id_alternativa INT UNSIGNED NOT NULL,
    prioridade INT UNSIGNED NOT NULL,
    id_usuario_historico INT UNSIGNED NOT NULL,
    CONSTRAINT fk_perguntas_alternativas_perguntas FOREIGN KEY(id_pergunta) REFERENCES sad_perguntas(id_pergunta),
    CONSTRAINT fk_perguntas_alternativas_alternativas FOREIGN KEY(id_alternativa) REFERENCES sad_alternativas(id_alternativa),
    CONSTRAINT fk_perguntas_alternativas_usuarios_historico FOREIGN KEY(id_usuario_historico) REFERENCES sad_usuarios_historico(id_usuario_historico)
);

CREATE TABLE IF NOT EXISTS sad_disciplinas_perguntas(
    id_disciplina_pergunta INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_disciplina INT UNSIGNED NOT NULL,
    id_pergunta INT UNSIGNED NOT NULL,
    esta_disponivel BOOLEAN NOT NULL,
    ordem INT UNSIGNED NOT NULL,
    id_usuario_historico INT UNSIGNED NOT NULL,
    CONSTRAINT fk_disciplinas_perguntas_disciplinas FOREIGN KEY(id_disciplina) REFERENCES sad_disciplinas(id_disciplina),
    CONSTRAINT fk_disciplinas_perguntas_perguntas FOREIGN KEY(id_pergunta) REFERENCES sad_perguntas(id_pergunta),
    CONSTRAINT fk_disciplinas_perguntas_usuarios_historico FOREIGN KEY(id_usuario_historico) REFERENCES sad_usuarios_historico(id_usuario_historico)
);

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

CREATE TABLE IF NOT EXISTS sad_respostas(
    id_resposta INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_avaliacao INT UNSIGNED NOT NULL,
    id_disciplina INT UNSIGNED NOT NULL,
    id_pergunta INT UNSIGNED NOT NULL,
    id_marcador INT UNSIGNED NOT NULL,
    id_alternativa INT UNSIGNED NOT NULL,
    resposta_textual TEXT NOT NULL,
    id_usuario INT UNSIGNED NOT NULL,
    data DATETIME NOT NULL,
    CONSTRAINT fk_respostas_avaliacoes FOREIGN KEY(id_avaliacao) REFERENCES sad_avaliacoes(id_avaliacao),
    CONSTRAINT fk_respostas_disciplinas FOREIGN KEY(id_disciplina) REFERENCES sad_disciplinas(id_disciplina),
    CONSTRAINT fk_respostas_perguntas FOREIGN KEY(id_pergunta) REFERENCES sad_perguntas(id_pergunta),
    CONSTRAINT fk_respostas_marcadores FOREIGN KEY(id_marcador) REFERENCES sad_marcadores(id_marcador),
    CONSTRAINT fk_respostas_alternativas FOREIGN KEY(id_alternativa) REFERENCES sad_alternativas(id_alternativa),
    CONSTRAINT fk_respostas_usuarios FOREIGN KEY(id_usuario) REFERENCES sad_usuarios(id_usuario)
);

INSERT INTO sad_usuarios(id_usuario, apelido, nome, email, senha, token, nivel_acesso, data_ultimo_acesso, data_cadastro) VALUES
(NULL, 'Yan Gabriel', 'Yan Gabriel da Silva Machado', 'yansilvagabriel@gmail.com', AES_ENCRYPT('root', 'Yan.1995'), '', 10, NOW(), NOW()),
(NULL, 'Felipe Ribas', 'Felipe Ribas Coutinho', 'feribastic@gmail.com', AES_ENCRYPT('root', 'Yan.1995'), '', 10, NOW(), NOW());

INSERT INTO sad_usuarios_configuracoes(id_usuarios_configuracoes, id_usuario, cor_tema) VALUES
(NULL, 1, '005DAB'),
(NULL, 2, '005DAB');

INSERT INTO sad_usuarios_historico(id_usuario_historico, id_usuario, acao, data_registro) VALUES
(NULL, 1, 'INSERÇÃO', NOW());

INSERT INTO sad_marcadores(id_marcador, marcador, id_usuario_historico) VALUES
(NULL, 'sem-marcador', 1),
(NULL, 'Marcador', 1);

/* EXEMPLO DE INSERCAO NA TABELA SAD_AVALIACOES[INICIO] *//*
INSERT INTO sad_avaliacoes(id_avaliacao, nome, inicio, termino, e_manutencao, estatisticas_antecipadas, esta_processada, id_usuario_historico) VALUES
(NULL, 'Avaliação Teste', NOW(), '2015-07-15 11:13:59', FALSE, FALSE, FALSE, 1);
/* EXEMPLO DE INSERCAO NA TABELA SAD_AVALIACOES[FIM] */

INSERT INTO sad_unidades_academicas(id_unidade_academica, unidade_academica, sigla, esta_disponivel, id_usuario_historico) VALUES
(NULL, 'Instituto de Nutrição', 'INU', 1, 1);

INSERT INTO sad_departamentos(id_departamento, departamento, sigla, esta_disponivel, id_unidade_academica, id_usuario_historico) VALUES
(NULL, 'Nutrição Aplicada', 'DNA', 1, 1, 1),
(NULL, 'Nutrição Básica e Experimental', 'DNBE', 1, 1, 1),
(NULL, 'Nutrição Social', 'DNS', 1, 1, 1);

INSERT INTO sad_disciplinas(id_disciplina, disciplina, periodo, esta_disponivel, e_eletiva, id_departamento, id_usuario_historico) VALUES
(NULL, 'Anatomia Humana', 1, TRUE, FALSE, 1, 1),
(NULL, 'Bioestatística', 1, TRUE, FALSE, 1, 1),
(NULL, 'Biologia Celular', 1, TRUE, FALSE, 1, 1),
(NULL, 'Genética', 1, TRUE, FALSE, 1, 1),
(NULL, 'Histologia e Embriologia VIII', 1, TRUE, FALSE, 1, 1),
(NULL, 'Introdução à Alimentação e Nutrição', 1, TRUE, FALSE, 1, 1),
(NULL, 'Princípios Básicos de Química Orgânica para Nutrição', 1, TRUE, FALSE, 1, 1),
(NULL, 'Epidemiologia', 2, TRUE, FALSE, 1, 1),
(NULL, 'Introdução à Economia', 2, TRUE, FALSE, 1, 1),
(NULL, 'Metabolismo Intermediário', 2, TRUE, FALSE, 1, 1),
(NULL, 'Microbiologia e Imunologia VII', 2, TRUE, FALSE, 1, 1),
(NULL, 'Pesquisa em Alimentação e Nutrição', 2, TRUE, FALSE, 1, 1),
(NULL, 'Psicologia do Desenvolvimento', 2, TRUE, FALSE, 1, 1),
(NULL, 'Sociologia, Alimentação e Nutrição', 2, TRUE, FALSE, 1, 1),
(NULL, 'Bioquímica Fisiológica', 3, TRUE, FALSE, 1, 1),
(NULL, 'Composição de Alimentos', 3, TRUE, FALSE, 1, 1),
(NULL, 'Fisiologia II', 3, TRUE, FALSE, 1, 1),
(NULL, 'Higiene e Legislação dos Alimentos', 3, TRUE, FALSE, 1, 1),
(NULL, 'Microbiologia de Alimentos', 3, TRUE, FALSE, 1, 1),
(NULL, 'Nutrição e Dietética I', 3, TRUE, FALSE, 1, 1),
(NULL, 'Parasitologia', 3, TRUE, FALSE, 1, 1),
(NULL, 'Políticas de Saúde', 3, TRUE, FALSE, 1, 1),
(NULL, 'Alimentação, Saúde e Cultura', 4, TRUE, FALSE, 1, 1),
(NULL, 'Avaliação Nutricional', 4, TRUE, FALSE, 1, 1),
(NULL, 'Bromatologia', 4, TRUE, FALSE, 1, 1),
(NULL, 'Farmacologia Aplicada à Nutrição', 4, TRUE, FALSE, 1, 1),
(NULL, 'Patologia Geral', 4, TRUE, FALSE, 1, 1),
(NULL, 'Alimentação Coletiva I', 5, TRUE, FALSE, 1, 1),
(NULL, 'Ética Profissional', 5, TRUE, FALSE, 1, 1),
(NULL, 'Nutrição Clínica I', 5, TRUE, FALSE, 1, 1),
(NULL, 'Nutrição Materno-Infantil', 5, TRUE, FALSE, 1, 1),
(NULL, 'Técnica Dietética II', 5, TRUE, FALSE, 1, 1),
(NULL, 'Tecnologia dos Alimentos', 5, TRUE, FALSE, 1, 1),
(NULL, 'Alimentação Coletiva II', 6, TRUE, FALSE, 1, 1),
(NULL, 'Alimentação e Nutrição em Saúde Coletiva', 6, TRUE, FALSE, 1, 1),
(NULL, 'Educação, Alimentação e Nutrição', 6, TRUE, FALSE, 1, 1),
(NULL, 'Nutrição Clínica em Pediatria', 6, TRUE, FALSE, 1, 1),
(NULL, 'Nutrição Clínica II', 6, TRUE, FALSE, 1, 1),
(NULL, 'Projeto de Trabalho de Conclusão de Curso', 6, TRUE, FALSE, 1, 1),
(NULL, 'Estágio Supervisionado de Nutrição em Saúde Coletiva', 7, TRUE, FALSE, 1, 1),
(NULL, 'Estágio Supervisionado em Alimentação Coletiva', 7, TRUE, FALSE, 1, 1),
(NULL, 'Internato em Alimentação Coletiva', 7, TRUE, FALSE, 1, 1),
(NULL, 'Internato de Nutrição em Saúde Coletiva', 7, TRUE, FALSE, 1, 1),
(NULL, 'Trabalho de Conclusão de Curso (Eletivas Restritas)', 7, TRUE, FALSE, 1, 1),
(NULL, 'Estágio Supervisionado em Segurança e Ciências de Alimentos', 8, TRUE, FALSE, 1, 1),
(NULL, 'Estágio Supervisionado em Nutrição Clínica', 8, TRUE, FALSE, 1, 1),
(NULL, 'Internato em Nutrição Clínica', 8, TRUE, FALSE, 1, 1),
(NULL, 'Internato em Segurança e Ciência de Alimentos', 8, TRUE, FALSE, 1, 1);

INSERT INTO sad_grupos(id_grupo, grupo, ordem, id_usuario_historico) VALUES
(NULL, 'Disciplina', 0, 1),
(NULL, 'Instituição', 1, 1),
(NULL, 'Curso', 2, 1),
(NULL, 'Auto-Avaliação', 3, 1);

INSERT INTO sad_alternativas(id_alternativa, alternativa, id_usuario_historico) VALUES
(NULL, 'sem-alternativa', 1),
(NULL, 'Sim', 1),
(NULL, 'Não', 1);

/* EXEMPLO DE INSERCAO[INICIO] *//*
INSERT INTO sad_perguntas(id_pergunta, pergunta, texto_ajuda, id_grupo, tipo_entrada, e_alternativa, e_obrigatoria, id_usuario_historico) VALUES
(NULL, 'Instituição?', 'Texto de Ajuda "Instituição?"', 2, 'radio', TRUE, TRUE, 1),
(NULL, 'Curso?', '',  3, 'radio', TRUE, TRUE, 1),
(NULL, 'Auto-Avalição?', '', 4, 'radio', TRUE, TRUE, 1),
(NULL, 'Disciplina v1?', 'Texto de Ajuda "Disciplina v1?"', 1, 'radio', TRUE, TRUE, 1),
(NULL, 'Disciplina v2?', '', 1, 'radio', TRUE, TRUE, 1);

INSERT INTO sad_perguntas_marcadores(id_pergunta_marcador, id_pergunta, id_marcador, id_usuario_historico) VALUES
(NULL, 1, 1, 1),
(NULL, 2, 1, 1),
(NULL, 3, 1, 1),
(NULL, 4, 1, 1),
(NULL, 5, 1, 1);

INSERT INTO sad_perguntas_alternativas(id_pergunta_alternativa, id_pergunta, id_alternativa, prioridade, id_usuario_historico) VALUES
(NULL, 1, 2, 1, 1),
(NULL, 1, 3, 2, 1),
(NULL, 2, 2, 1, 1),
(NULL, 2, 3, 2, 1),
(NULL, 3, 2, 1, 1),
(NULL, 3, 3, 2, 1),
(NULL, 4, 2, 1, 1),
(NULL, 4, 3, 2, 1),
(NULL, 5, 2, 1, 1),
(NULL, 5, 3, 2, 1);

INSERT INTO sad_disciplinas_perguntas(id_disciplina_pergunta, id_disciplina, id_pergunta, ordem, esta_disponivel, id_usuario_historico) VALUES
(NULL, 2, 1, 1, TRUE, 1),
(NULL, 2, 2, 2, TRUE, 1),
(NULL, 2, 3, 3, TRUE, 1),
(NULL, 2, 4, 4, TRUE, 1),
(NULL, 2, 5, 5, TRUE, 1),
(NULL, 9, 1, 1, TRUE, 1),
(NULL, 9, 2, 2, TRUE, 1),
(NULL, 9, 3, 3, TRUE, 1),
(NULL, 9, 4, 4, TRUE, 1),
(NULL, 9, 5, 5, TRUE, 1),
(NULL, 16, 1, 1, TRUE, 1),
(NULL, 16, 2, 2, TRUE, 1),
(NULL, 16, 3, 3, TRUE, 1),
(NULL, 16, 4, 4, TRUE, 1),
(NULL, 16, 5, 5, TRUE, 1);
/* EXEMPLO DE INSERCAO[FIM] */