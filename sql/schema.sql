CREATE DATABASE sewn_store;
USE sewn_store;

CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefone VARCHAR(20),
    endereco TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    categoria_id INT,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    tamanho VARCHAR(10),
    cor VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE vendas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT,
    data_venda DATE NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE itens_venda (
    id INT PRIMARY KEY AUTO_INCREMENT,
    venda_id INT,
    produto_id INT,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venda_id) REFERENCES vendas(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

INSERT INTO categorias (nome) VALUES 
('Camisetas'), ('Calças'), ('Vestidos'), ('Acessórios'), ('Casacos');

INSERT INTO clientes (nome, email, telefone, endereco) VALUES
('Maria Silva', 'maria@email.com', '(19) 9999-1111', 'Rua A, 123'),
('João Santos', 'joao@email.com', '(19) 9999-2222', 'Rua B, 456'),
('Ana Costa', 'ana@email.com', '(19) 9999-3333', 'Rua C, 789'),
('Pedro Lima', 'pedro@email.com', '(19) 9999-4444', 'Rua D, 321'),
('Julia Oliveira', 'julia@email.com', '(19) 9999-5555', 'Rua E, 654');

INSERT INTO produtos (nome, categoria_id, preco, estoque, tamanho, cor) VALUES
('Camiseta Básica', 1, 29.90, 50, 'M', 'Branca'),
('Calça Jeans', 2, 89.90, 30, 'P', 'Azul'),
('Vestido Floral', 3, 79.90, 15, 'G', 'Estampado'),
('Cinto Couro', 4, 39.90, 25, 'Único', 'Marrom'),
('Jaqueta Jeans', 5, 129.90, 8, 'M', 'Azul'),
('Camiseta Estampada', 1, 34.90, 20, 'P', 'Preta'),
('Calça Social', 2, 99.90, 12, 'G', 'Preta'),
('Vestido Longo', 3, 119.90, 5, 'M', 'Vermelho');

INSERT INTO vendas (cliente_id, data_venda, total) VALUES
(1, '2024-12-01', 119.80),
(2, '2024-12-02', 89.90),
(1, '2024-12-03', 79.90),
(3, '2024-12-04', 169.80),
(4, '2024-12-05', 129.90),
(2, '2025-01-10', 69.80),
(5, '2025-01-15', 199.80);

INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES
(1, 1, 2, 29.90),
(1, 4, 1, 39.90),
(1, 6, 1, 34.90),
(2, 2, 1, 89.90),
(3, 3, 1, 79.90),
(4, 1, 1, 29.90),
(4, 5, 1, 129.90),
(5, 5, 1, 129.90),
(6, 1, 1, 29.90),
(6, 4, 1, 39.90),
(7, 3, 1, 79.90),
(7, 7, 1, 99.90);