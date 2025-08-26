-- Create database (change name if you like)
CREATE DATABASE IF NOT EXISTS ra_ogbc
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_0900_ai_ci;

USE ra_ogbc;

SET sql_notes = 0;
SET time_zone = '+00:00';

-- 1) Associations (25 fixed)
CREATE TABLE associations (
  id            BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name          VARCHAR(150) NOT NULL UNIQUE,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2) Ranks (11 fixed, ordered by level)
CREATE TABLE ranks (
  id            BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name          VARCHAR(100) NOT NULL UNIQUE,
  level         INT NOT NULL UNIQUE,  -- 1 (lowest) ... 11 (highest)
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3) Users (Ambassador, President, Super Admin)
CREATE TABLE users (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  unique_id       VARCHAR(50) NOT NULL UNIQUE,      -- e.g., OGBC/RA/6254
  full_name       VARCHAR(150) NOT NULL,
  email           VARCHAR(150) NOT NULL UNIQUE,
  phone           VARCHAR(20)  NOT NULL UNIQUE,
  password_hash   VARCHAR(255) NOT NULL,
  role            ENUM('ambassador','president','super_admin') NOT NULL DEFAULT 'ambassador',
  association_id  BIGINT UNSIGNED NULL,
  rank_id         BIGINT UNSIGNED NULL,
  church          VARCHAR(150),
  age             INT,
  avatar_path     VARCHAR(255),
  email_verified_at DATETIME NULL,
  remember_token  VARCHAR(100),
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_association
    FOREIGN KEY (association_id) REFERENCES associations(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_users_rank
    FOREIGN KEY (rank_id) REFERENCES ranks(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX idx_users_role          ON users(role);
CREATE INDEX idx_users_association   ON users(association_id);
CREATE INDEX idx_users_rank          ON users(rank_id);

-- Other tables continue...
-- (Truncated for brevity - includes all tables from the PLD)

-- Unique ID Trigger
DELIMITER $$

CREATE TRIGGER trg_users_after_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
  IF NEW.unique_id = '' OR NEW.unique_id IS NULL THEN
    UPDATE users
      SET unique_id = CONCAT('OGBC/RA/', LPAD(NEW.id, 4, '0'))
      WHERE id = NEW.id;
  END IF;
END$$

DELIMITER ;