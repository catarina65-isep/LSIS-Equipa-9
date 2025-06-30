-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS lsis_equipa9 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lsis_equipa9;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    id_perfilacesso INT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de perfis de acesso
CREATE TABLE IF NOT EXISTS perfilacesso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de permissões
CREATE TABLE IF NOT EXISTS permissoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_perfilacesso INT NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    acao VARCHAR(50) NOT NULL,
    permitido BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_perfilacesso) REFERENCES perfilacesso(id),
    UNIQUE KEY idx_permissoes (id_perfilacesso, modulo, acao)
) ENGINE=InnoDB;

-- Tabela de documentos
CREATE TABLE IF NOT EXISTS documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_colaborador INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    descricao TEXT,
    arquivo VARCHAR(255) NOT NULL,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_validade DATE,
    status VARCHAR(20) DEFAULT 'pendente',
    FOREIGN KEY (id_colaborador) REFERENCES usuarios(id),
    INDEX idx_id_colaborador (id_colaborador)
) ENGINE=InnoDB;

-- Tabela de benefícios
CREATE TABLE IF NOT EXISTS beneficios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_colaborador INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    descricao TEXT,
    data_inicio DATE NOT NULL,
    data_fim DATE,
    status VARCHAR(20) DEFAULT 'ativo',
    FOREIGN KEY (id_colaborador) REFERENCES usuarios(id),
    INDEX idx_id_colaborador (id_colaborador)
) ENGINE=InnoDB;

-- Tabela de alertas
CREATE TABLE IF NOT EXISTS alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    tipo VARCHAR(20) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_validade DATE,
    status VARCHAR(20) DEFAULT 'pendente',
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Tabela de alertas de colaborador
CREATE TABLE IF NOT EXISTS alertas_colaborador (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_alerta INT NOT NULL,
    id_colaborador INT NOT NULL,
    lido BOOLEAN DEFAULT FALSE,
    data_leitura TIMESTAMP NULL,
    FOREIGN KEY (id_alerta) REFERENCES alertas(id),
    FOREIGN KEY (id_colaborador) REFERENCES usuarios(id),
    UNIQUE KEY idx_alerta_colaborador (id_alerta, id_colaborador)
) ENGINE=InnoDB;

-- Tabela de histórico de integração
CREATE TABLE IF NOT EXISTS historico_integracao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_colaborador INT NOT NULL,
    tipo_operacao VARCHAR(50) NOT NULL,
    dados TEXT,
    status VARCHAR(20) NOT NULL,
    mensagem_erro TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_colaborador) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Tabela de fila de integração
CREATE TABLE IF NOT EXISTS fila_integracao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_colaborador INT NOT NULL,
    tipo_operacao VARCHAR(50) NOT NULL,
    dados JSON NOT NULL,
    status VARCHAR(20) DEFAULT 'pendente',
    tentativas INT DEFAULT 0,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_ultima_tentativa TIMESTAMP NULL,
    FOREIGN KEY (id_colaborador) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Inserir perfis de acesso padrão
INSERT INTO perfilacesso (nome, descricao) VALUES
('Administrador', 'Acesso total ao sistema'),
('RH', 'Acesso ao módulo de RH'),
('Coordenador', 'Acesso ao módulo de coordenação'),
('Colaborador', 'Acesso básico ao sistema');

-- Inserir permissões padrão
INSERT INTO permissoes (id_perfilacesso, modulo, acao, permitido) VALUES
-- Administrador
(1, 'usuarios', 'gerenciar', TRUE),
(1, 'perfis', 'gerenciar', TRUE),
(1, 'documentos', 'gerenciar', TRUE),
(1, 'beneficios', 'gerenciar', TRUE),
(1, 'alertas', 'gerenciar', TRUE),
(1, 'exportar', 'gerenciar', TRUE),
(1, 'mod992', 'gerenciar', TRUE),

-- RH
(2, 'usuarios', 'visualizar', TRUE),
(2, 'documentos', 'gerenciar', TRUE),
(2, 'beneficios', 'gerenciar', TRUE),
(2, 'alertas', 'gerenciar', TRUE),
(2, 'exportar', 'gerenciar', TRUE),
(2, 'mod992', 'gerenciar', TRUE),

-- Coordenador
(3, 'usuarios', 'visualizar', TRUE),
(3, 'documentos', 'gerenciar', TRUE),
(3, 'beneficios', 'gerenciar', TRUE),
(3, 'alertas', 'gerenciar', TRUE),
(3, 'exportar', 'gerenciar', TRUE),
(3, 'mod992', 'gerenciar', TRUE),

-- Colaborador
(4, 'usuarios', 'visualizar', TRUE),
(4, 'documentos', 'gerenciar', TRUE),
(4, 'beneficios', 'gerenciar', TRUE),
(4, 'alertas', 'ler', TRUE),
(4, 'exportar', 'gerar', TRUE),
(4, 'mod992', 'gerar', TRUE);
