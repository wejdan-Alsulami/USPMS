
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








CREATE TABLE Expense (
    expense_id INT NOT NULL AUTO_INCREMENT,          -- Unique identifier for each expense
    student_id VARCHAR(10) NOT NULL,                 -- Student ID associated with the expense
    amount DECIMAL(15, 2) NOT NULL,                  -- Amount of the expense
    category VARCHAR(50) NOT NULL,                   -- Category of the expense (e.g., Food, Transport)
    expense_date DATE NOT NULL,                       -- Date of the expense
    description TEXT,                                 -- Optional description of the expense


    PRIMARY KEY (expense_id),                         -- Primary key constraint
    FOREIGN KEY (student_id) REFERENCES students(student_id) -- Foreign key reference to students table
);





