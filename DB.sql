
CREATE DATABASE UniversityPayroll;

-- Use the database
USE UniversityPayroll;






-- Create the students table
CREATE TABLE students (
    student_id VARCHAR(10) PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    specialization VARCHAR(100),
    birthday DATE NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    balance INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

