CREATE DATABASE IF NOT EXISTS dualhome_db;
USE dualhome_db;

-- Família: duas casas e o fim de semana de referência usado pelo calendário de alternância
CREATE TABLE Familias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_familia VARCHAR(100) NOT NULL,
    nome_casa_a VARCHAR(100) NOT NULL,
    nome_casa_b VARCHAR(100) NOT NULL,
    inicio_alternancia DATE NOT NULL, -- Data de um fim de semana em que o jovem fica na casa A
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE Utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Preparado para receber hashes (ex: password_hash do PHP)
    tipo_perfil ENUM('jovem', 'pai', 'mae') NOT NULL,
    id_familia INT NULL, -- NULL até o utilizador configurar a família
    data_registo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_utilizador_familia FOREIGN KEY (id_familia) REFERENCES Familias(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Migração para bases de dados já criadas (executar apenas uma vez, em vez dos blocos acima):
-- CREATE TABLE Familias (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     nome_familia VARCHAR(100) NOT NULL,
--     nome_casa_a VARCHAR(100) NOT NULL,
--     nome_casa_b VARCHAR(100) NOT NULL,
--     inicio_alternancia DATE NOT NULL,
--     data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB;
-- ALTER TABLE Utilizadores
--     ADD COLUMN id_familia INT NULL AFTER tipo_perfil,
--     ADD CONSTRAINT fk_utilizador_familia FOREIGN KEY (id_familia) REFERENCES Familias(id) ON DELETE SET NULL;


CREATE TABLE Itens_Checklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_item VARCHAR(150) NOT NULL,
    estado_recolha ENUM('pendente', 'na_mala') DEFAULT 'pendente',
    id_utilizador INT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Ligação: Um item pertence sempre a um utilizador (jovem). Se o utilizador for apagado, os itens também são.
    CONSTRAINT fk_utilizador_item FOREIGN KEY (id_utilizador) REFERENCES Utilizadores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Acessos_Pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    motivo_visita VARCHAR(255) NOT NULL,
    data_visita DATE NOT NULL,
    estado_aprovacao ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
    id_utilizador_registo INT NOT NULL,
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Ligação: Um pedido de acesso é registado por um utilizador. Se o utilizador for apagado, os pedidos também são.
    CONSTRAINT fk_utilizador_acesso FOREIGN KEY (id_utilizador_registo) REFERENCES Utilizadores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Despesas_Partilhadas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(200) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    estado_pagamento ENUM('pendente', 'pago') DEFAULT 'pendente',
    id_progenitor_pagador INT NOT NULL, -- Quem pagou a despesa
    id_progenitor_devedor INT NOT NULL, -- Quem tem de devolver metade
    data_registo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pagador FOREIGN KEY (id_progenitor_pagador) REFERENCES Utilizadores(id) ON DELETE CASCADE,
    CONSTRAINT fk_devedor FOREIGN KEY (id_progenitor_devedor) REFERENCES Utilizadores(id) ON DELETE CASCADE
) ENGINE=InnoDB;