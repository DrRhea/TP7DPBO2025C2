-- db_aquarium.sql

CREATE DATABASE db_aquarium;
USE db_aquarium;

CREATE TABLE aquariums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    size VARCHAR(100),
    location VARCHAR(50)
);

CREATE TABLE species (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    origin VARCHAR(100),
    care_level VARCHAR(50)
);

CREATE TABLE fish (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    species_id INT,
    aquarium_id INT,
    age INT,
    gender ENUM('Male', 'Female'),
    FOREIGN KEY (species_id) REFERENCES species(id),
    FOREIGN KEY (aquarium_id) REFERENCES aquariums(id)
);

CREATE TABLE feeding_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fish_id INT,
    feed_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    food_type VARCHAR(100),
    FOREIGN KEY (fish_id) REFERENCES fish(id)
);

CREATE TABLE water_quality_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aquarium_id INT,
    check_date DATE,
    temperature DECIMAL(5,2),
    ph DECIMAL(3,2),
    ammonia_level DECIMAL(5,2),
    FOREIGN KEY (aquarium_id) REFERENCES aquariums(id)
);


-- Dummy data

-- Table: aquariums
INSERT INTO aquariums (name, size, location) VALUES
('Tropical Tank', '120', 'Living Room'),
('Freshwater Tank', '200', 'Back Office');

-- Table: species
INSERT INTO species (name, origin, care_level) VALUES
('Guppy', 'South America', 'Easy'),
('Betta', 'Southeast Asia', 'Medium'),
('Goldfish', 'China', 'Hard');

-- Table: fish
INSERT INTO fish (name, species_id, aquarium_id, age, gender) VALUES
('Red Guppy', 1, 1, 6, 'Male'),
('Blue Betta', 2, 2, 4, 'Female'),
('Golden Goldfish', 3, 1, 12, 'Male');

-- Table: feeding_logs
INSERT INTO feeding_logs (fish_id, feed_time, food_type) VALUES
(1, '2025-04-18 08:00:00', 'Bloodworms'),
(2, '2025-04-18 09:00:00', 'Mini Pellets'),
(3, '2025-04-18 10:00:00', 'Goldfish Pellets');

-- Table: water_quality_logs
INSERT INTO water_quality_logs (aquarium_id, check_date, temperature, ph, ammonia_level) VALUES
(1, '2025-04-18', 26.5, 7.2, 0.02),
(2, '2025-04-18', 25.0, 6.8, 0.05);
