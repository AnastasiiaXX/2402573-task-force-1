CREATE DATABASE IF NOT EXISTS task_force
	DEFAULT CHARACTER SET utf8mb4
	DEFAULT COLLATE utf8mb4_general_ci;
  
USE task_force;

CREATE TABLE specialties (
	id INT PRIMARY KEY AUTO_INCREMENT,
	title VARCHAR(100) NOT NULL UNIQUE,
  code VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE locations (
	id INT PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(100) NOT NULL UNIQUE,
  latitude DECIMAL(10, 8) NOT NULL,
  longitude DECIMAL(10, 8) NOT NULL
);

CREATE TABLE users (
	id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  password CHAR(64) NOT NULL,
  email VARCHAR(55) NOT NULL UNIQUE,
  role VARCHAR(55) NOT NULL,
  birthday DATETIME,
  avatar VARCHAR(128),
  phone_number CHAR(11),
  telegram_name VARCHAR(128),
  about VARCHAR(255),
  location_id INT NOT NULL,
  specialty_id INT,
  FOREIGN KEY (specialty_id) REFERENCES specialties(id),
  FOREIGN KEY (location_id) REFERENCES locations(id)
);


CREATE TABLE categories (
	id INT PRIMARY KEY AUTO_INCREMENT,
	title VARCHAR(100) NOT NULL UNIQUE,
  symbol_code VARCHAR(50) UNIQUE
);

CREATE TABLE tasks (
	id INT PRIMARY KEY AUTO_INCREMENT,
	title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  cost INT,
  date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  date_end DATE,
  status ENUM('new','in_progress','completed','canceled','failed') DEFAULT 'new',
  employer_id INT NOT NULL,
  worker_id INT,
  location_id INT,
  category_id INT,
  FOREIGN KEY (employer_id) REFERENCES users(id),
  FOREIGN KEY (worker_id) REFERENCES users(id),
  FOREIGN KEY (location_id) REFERENCES locations(id),
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE files (
	id INT PRIMARY KEY AUTO_INCREMENT,
  path VARCHAR(255) NOT NULL,
  task_id INT,
  FOREIGN KEY (task_id) REFERENCES tasks(id)
);

CREATE TABLE responses (
	id INT PRIMARY KEY AUTO_INCREMENT,
  date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  cost INT,
  comment TEXT,
  worker_id INT,
  task_id INT,
  FOREIGN KEY (task_id) REFERENCES tasks(id),
  FOREIGN KEY (worker_id) REFERENCES users(id)
);

CREATE TABLE reviews (
	id INT PRIMARY KEY AUTO_INCREMENT,
  date_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  text VARCHAR(128) NOT NULL,
  score INT NOT NULL,
  employer_id INT,
  worker_id INT,
  task_id INT,
  FOREIGN KEY (task_id) REFERENCES tasks(id),
  FOREIGN KEY (worker_id) REFERENCES users(id)
);
