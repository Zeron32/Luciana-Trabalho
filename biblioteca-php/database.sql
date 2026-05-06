-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS biblioteca_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca_db;

-- Tabela de livros
CREATE TABLE IF NOT EXISTS livros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    isbn VARCHAR(20),
    editora VARCHAR(100),
    ano_publicacao INT,
    quantidade INT DEFAULT 1,
    quantidade_disponivel INT DEFAULT 1,
    categoria VARCHAR(100),
    localizacao VARCHAR(50),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    cpf VARCHAR(14) UNIQUE,
    endereco TEXT,
    tipo ENUM('administrador', 'bibliotecario', 'leitor') DEFAULT 'leitor',
    senha VARCHAR(255) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de empréstimos
CREATE TABLE IF NOT EXISTS emprestimos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livro_id INT NOT NULL,
    usuario_id INT NOT NULL,
    data_emprestimo DATE NOT NULL,
    data_prevista_devolucao DATE NOT NULL,
    data_devolucao DATE NULL,
    status ENUM('ativo', 'devolvido', 'atrasado') DEFAULT 'ativo',
    observacoes TEXT,
    FOREIGN KEY (livro_id) REFERENCES livros(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir usuário administrador padrão
INSERT INTO usuarios (nome, email, tipo, senha, cpf) VALUES
('Administrador', 'admin@biblioteca.com', 'administrador', MD5('admin123'), '000.000.000-00');

-- Inserir dados de exemplo - Livros
INSERT INTO livros (titulo, autor, isbn, editora, ano_publicacao, quantidade, quantidade_disponivel, categoria, localizacao) VALUES
('Dom Casmurro', 'Machado de Assis', '978-8535911664', 'Companhia das Letras', 1899, 5, 5, 'Literatura Brasileira', 'A-12'),
('1984', 'George Orwell', '978-8535914849', 'Companhia das Letras', 1949, 3, 2, 'Ficção Distópica', 'B-05'),
('O Senhor dos Anéis', 'J.R.R. Tolkien', '978-8533613379', 'Martins Fontes', 1954, 4, 3, 'Fantasia', 'C-20'),
('Clean Code', 'Robert C. Martin', '978-8576082675', 'Alta Books', 2008, 6, 6, 'Tecnologia', 'D-15'),
('Harry Potter e a Pedra Filosofal', 'J.K. Rowling', '978-8532530787', 'Rocco', 1997, 8, 7, 'Fantasia Juvenil', 'C-18'),
('O Pequeno Príncipe', 'Antoine de Saint-Exupéry', '978-8522008735', 'Agir', 1943, 10, 10, 'Infantil', 'E-01'),
('A Arte da Guerra', 'Sun Tzu', '978-8588208506', 'Jardim dos Livros', -500, 4, 4, 'Filosofia', 'F-08'),
('Sapiens', 'Yuval Noah Harari', '978-8525432629', 'L&PM', 2011, 5, 4, 'História', 'G-22');

-- Inserir dados de exemplo - Usuários leitores
INSERT INTO usuarios (nome, email, telefone, cpf, endereco, tipo, senha) VALUES
('João Silva', 'joao.silva@email.com', '(11) 98765-4321', '123.456.789-00', 'Rua das Flores, 123', 'leitor', MD5('123456')),
('Maria Santos', 'maria.santos@email.com', '(11) 98765-4322', '234.567.890-11', 'Av. Principal, 456', 'leitor', MD5('123456')),
('Pedro Oliveira', 'pedro.oliveira@email.com', '(11) 98765-4323', '345.678.901-22', 'Rua das Acácias, 789', 'leitor', MD5('123456')),
('Ana Costa', 'ana.costa@email.com', '(11) 98765-4324', '456.789.012-33', 'Praça Central, 100', 'bibliotecario', MD5('123456'));

-- Inserir dados de exemplo - Empréstimos
INSERT INTO emprestimos (livro_id, usuario_id, data_emprestimo, data_prevista_devolucao, status) VALUES
(2, 2, '2026-04-15', '2026-04-29', 'ativo'),
(3, 3, '2026-04-18', '2026-05-02', 'ativo'),
(5, 4, '2026-04-10', '2026-04-24', 'ativo'),
(8, 2, '2026-04-05', '2026-04-19', 'atrasado');

-- Atualizar quantidade disponível dos livros emprestados
UPDATE livros SET quantidade_disponivel = quantidade_disponivel - 1 WHERE id IN (2, 3, 5, 8);
