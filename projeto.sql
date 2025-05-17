-- database: projetoPBD

-- criando tabelas
CREATE TABLE Clube (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    endereço VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL
);

CREATE TABLE Quadras (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL,
    disponibilidade ENUM('disponível', 'reservado') NOT NULL,
    ID_Clube INT,
    FOREIGN KEY (ID_Clube) REFERENCES Clube(ID)
);

CREATE TABLE Membros (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(100) NOT NULL,
    data_nascimento DATE NOT NULL,
    Endereço VARCHAR(255),
    Telefone VARCHAR(20),
    Email VARCHAR(100),
    acesso_ativo BOOLEAN NOT NULL,
    ID_Clube INT,
    FOREIGN KEY (ID_Clube) REFERENCES Clube(ID)
);

CREATE TABLE Mensalidade (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    ID_Membro INT,
    status ENUM('pago', 'pendente') NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    FOREIGN KEY (ID_Membro) REFERENCES Membros(ID)
);

CREATE TABLE Eventos (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    data DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    local VARCHAR(255) NOT NULL,
    ID_Clube INT,
    FOREIGN KEY (ID_Clube) REFERENCES Clube(ID)
);

CREATE TABLE Participantes (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    ID_Eventos INT,
    ID_Membros INT,
    FOREIGN KEY (ID_Eventos) REFERENCES Eventos(ID),
    FOREIGN KEY (ID_Membros) REFERENCES Membros(ID)
);

CREATE TABLE Reservas (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    ID_Quadra INT,
    ID_Membro INT,
    data_reserva DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    data_criacao DATE NOT NULL,
    ID_Clube INT,
    FOREIGN KEY (ID_Quadra) REFERENCES Quadras(ID),
    FOREIGN KEY (ID_Membro) REFERENCES Membros(ID),
    FOREIGN KEY (ID_Clube) REFERENCES Clube(ID)
);

CREATE TABLE ADM (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);

CREATE TABLE Sistema (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    versao VARCHAR(20) NOT NULL,
    ID_ADM INT,
    FOREIGN KEY (ID_ADM) REFERENCES ADM(ID)
);

CREATE TABLE Gestão (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    nome_responsavel VARCHAR(100) NOT NULL,
    data_criacao DATE NOT NULL,
    ID_Clube INT,
    FOREIGN KEY (ID_Clube) REFERENCES Clube(ID)
);

CREATE TABLE Suporte (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    data_solicitacao DATE NOT NULL,
    descricao TEXT NOT NULL,
    status ENUM('pendente', 'resolvido') NOT NULL,
    ID_Quadra INT,
    ID_Clube INT,
    FOREIGN KEY (ID_Quadra) REFERENCES Quadras(ID),
    FOREIGN KEY (ID_Clube) REFERENCES Clube(ID)
);

CREATE TABLE Feedback (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    comentario TEXT NOT NULL,
    data DATE NOT NULL,
    ID_Suporte INT,
    ID_Membro INT,
    ID_Quadra INT,
    FOREIGN KEY (ID_Suporte) REFERENCES Suporte(ID),
    FOREIGN KEY (ID_Membro) REFERENCES Membros(ID),
    FOREIGN KEY (ID_Quadra) REFERENCES Quadras(ID)
);

-- Inserindo dados

INSERT INTO Clube (nome, endereço, telefone)
VALUES ('ANGLO', 'Rua Gomes Carneiro, 1 - Pelotas, RS', '(53) 99965-8330');

INSERT INTO Gestão (nome_responsavel, data_criacao, ID_Clube)
VALUES ('Art Donaldson', '30/08/2000', 1);

INSERT INTO Quadras (tipo, disponibilidade, ID_Clube)
VALUES
    ('Tênis', 'disponível', 1),
    ('Basquete', 'reservado', 1),
    ('Futsal', 'disponível', 1),
    ('Volei', 'disponível', 1);

INSERT INTO Membros (Nome, data_nascimento, Endereço, Telefone, Email, acesso_ativo, ID_Clube)
VALUES 
    ('Art Donaldson', '30-08-2000', 'Dr Cassiano, 51 - Pelotas', '(11) 91234-5678', 'artdonaldson@gmail.com', TRUE, 1),
    ('Tashi Donaldson', '27-05-2000', 'Dr Cassiano, 51 - Pelotas', '(21) 92345-6739', 'tashi@gmail.com', TRUE, 1),
    ('Patrick Zweig', '17-03-2000', 'Dr Cassiano, 51 (porao) - Pelotas', '(21) 43235-6789', 'zweig@gmail.com', TRUE, 1);

INSERT INTO Mensalidade (ID_Membro, status, data_inicio, data_fim)
VALUES 
    (1, 'pago', '2024-01-01', '2024-12-31'),
    (2, 'pago', '2024-01-01', '2024-12-31'),
    (3, 'pendente', '2024-01-01', '2024-12-31');

INSERT INTO Eventos (nome, descricao, data, hora_inicio, hora_fim, local, ID_Clube)
VALUES
    ('Inauguração', 'Inauguração do clube', '2024-09-15', '09:00:00', '18:00:00', 'Recepção', 1);

INSERT INTO Participantes (ID_Eventos, ID_Membros)
VALUES
    (1, 1),
    (1, 2);

INSERT INTO Reservas (ID_Quadra, ID_Membro, data_reserva, hora_inicio, hora_fim, data_criacao, ID_Clube)
VALUES
    (1, 2, '2024-09-15', '10:00:00', '11:00:00', CURRENT_DATE, 1),
    (3, 1, '2024-09-16', '14:00:00', '15:00:00', CURRENT_DATE, 1);

INSERT INTO ADM (nome, email)
VALUES
    ('Ivan Meireles', 'ivmeireles@TCA.com');

INSERT INTO Sistema (versao, ID_ADM)
VALUES
    ('1.0.0', 1);

INSERT INTO Suporte (data_solicitacao, descricao, status, ID_Quadra, ID_Clube)
VALUES
    ('2024-08-25', 'quadra destruída por furacão!', 'pendente', 1, 1),
    ('2024-08-26', 'quadra destruída por terremoto!', 'resolvido', 2, 1);

INSERT INTO Feedback (comentario, data, ID_Suporte, ID_Membro, ID_Quadra)
VALUES
    ('A iluminação da quadra de tênis está melhor agora, obrigado!', '2024-08-26', 1, 1, 1),
    ('A quadra foi resolvida mas os terremotos continuam acontecendo!', '2024-08-27', 2, 2, 2);

-- Criando triggers

DELIMITER //

CREATE TRIGGER trg_verificar_mensalidade_reserva
BEFORE INSERT ON Reservas
FOR EACH ROW
BEGIN
    DECLARE membro_ativo INT;
    
    SELECT COUNT(*) INTO membro_ativo
    FROM Mensalidade
    WHERE ID_Membro = NEW.ID_Membro
      AND status = 'pago'
      AND data_fim >= CURRENT_DATE;
    
    IF membro_ativo = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Acesso negado';
    END IF;
END;
//

DELIMITER ;

DELIMITER //

CREATE TRIGGER trg_verificar_mensalidade_evento
BEFORE INSERT ON Participantes
FOR EACH ROW
BEGIN
    DECLARE membro_ativo INT;
    
    SELECT COUNT(*) INTO membro_ativo
    FROM Mensalidade
    WHERE ID_Membro = NEW.ID_Membros
      AND status = 'pago'
      AND data_fim >= CURRENT_DATE;
    
    IF membro_ativo = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Acesso negado';
    END IF;
END;
//

DELIMITER ;

DELIMITER //

CREATE TRIGGER trg_verificar_mensalidade_feedback
BEFORE INSERT ON Feedback
FOR EACH ROW
BEGIN
    DECLARE membro_ativo INT;
    
    SELECT COUNT(*) INTO membro_ativo
    FROM Mensalidade
    WHERE ID_Membro = NEW.ID_Membro
      AND status = 'pago'
      AND data_fim >= CURRENT_DATE;
    
    IF membro_ativo = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Acesso negado';
    END IF;
END;
//

DELIMITER ;

DELIMITER //

CREATE TRIGGER trg_verificar_conflito_reserva
BEFORE INSERT ON Reservas
FOR EACH ROW
BEGIN
    DECLARE conflito INT;
    
    SELECT COUNT(*) INTO conflito
    FROM Reservas
    WHERE ID_Quadra = NEW.ID_Quadra
      AND data_reserva = NEW.data_reserva
      AND (
          (hora_inicio < NEW.hora_fim AND hora_fim > NEW.hora_inicio)
      );
    
    IF conflito > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Conflito de agendamento';
    END IF;
END;
//

DELIMITER ;

-- realizando consultas

-- consultando reservas realizadas por membros
SELECT Membros.nome, Reservas.data_reserva
FROM Membros
INNER JOIN Reservas ON Membros.ID = Reservas.ID_Membro;

-- consultando status da mensalidade de membros
SELECT Membros.nome, mensalidade.STATUS
FROM Membros
INNER JOIN mensalidade ON Membros.ID = mensalidade.ID_Membro;

-- consultando quais membros estão com a mensalidade pendente
SELECT 
    m.ID AS ID_Membro,
    m.Nome AS Nome_Membro,
    m.Email AS Email_Membro,
    ms.status AS Status_Mensalidade,
    ms.data_inicio AS Data_Inicio,
    ms.data_fim AS Data_Fim
FROM 
    Mensalidade ms
JOIN 
    Membros m ON ms.ID_Membro = m.ID
WHERE 
    ms.status = 'pendente';

-- consultando feedbacks relizados para uma quadra específica
SELECT 
    f.ID AS ID_Feedback,
    f.comentario,
    f.data,
    q.tipo AS Tipo_Quadra,
    m.Nome AS Nome_Membro
FROM 
    Feedback f
JOIN 
    Quadras q ON f.ID_Quadra = q.ID
JOIN 
    Membros m ON f.ID_Membro = m.ID
WHERE 
    q.tipo = 'Tênis';

-- consultando quais membros ja deram feedback
SELECT DISTINCT 
    m.ID AS ID_Membro,
    m.Nome AS Nome_Membro
FROM 
    Feedback f
JOIN 
    Membros m ON f.ID_Membro = m.ID;

-- consultando pedidos de suportes que estão com status pendente
SELECT 
    s.ID AS ID_Suporte,
    s.data_solicitacao AS Data_Solicitacao,
    s.descricao AS Descricao,
    s.status AS Status_Suporte,
    q.tipo AS Tipo_Quadra,
    c.nome AS Nome_Clube
FROM 
    Suporte s
JOIN 
    Quadras q ON s.ID_Quadra = q.ID
JOIN 
    Clube c ON s.ID_Clube = c.ID
WHERE 
    s.status = 'pendente';
