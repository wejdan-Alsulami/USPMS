
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








CREATE TABLE Goal (
    goal_id INT NOT NULL AUTO_INCREMENT,          -- Unique identifier for each goal
    student_id VARCHAR(10) NOT NULL,              -- Student ID associated with the goal
    description TEXT NOT NULL,                     -- Description of the goal
    target_amount DECIMAL(15, 2) NOT NULL,        -- Target amount for the goal
    status ENUM('Pending', 'Achieved') NOT NULL DEFAULT 'Pending', -- Status of the goal
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date the goal was created
    PRIMARY KEY (goal_id),                         -- Primary key constraint
    FOREIGN KEY (student_id) REFERENCES students(student_id) -- Foreign key reference to students table
);



CREATE TABLE SavingsAccount (
    account_id INT NOT NULL AUTO_INCREMENT,  -- Unique identifier for each account
    student_id VARCHAR(10) NOT NULL,         -- Student ID associated with the account
    balance DECIMAL(15, 2) NOT NULL DEFAULT 0.00,  -- Current balance in the account
    monthly_saving_goal DECIMAL(15, 2) DEFAULT NULL, -- Monthly saving goal
    opening_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date the account was opened
    PRIMARY KEY (account_id),                -- Primary key constraint
    UNIQUE (student_id),                     -- Ensure student_id is unique
    FOREIGN KEY (student_id) REFERENCES students (student_id) -- Assuming you have a students table
);









//////////////////////////////
1. تشغيل جدولة الأحداث (Event Scheduler):


SET GLOBAL event_scheduler = ON;



2. إنشاء الحدث (Event):

DELIMITER $$

CREATE EVENT add_monthly_reward2
ON SCHEDULE EVERY 1 MONTH -- يتم تكرار الحدث كل شهر
STARTS (CURRENT_DATE + INTERVAL (27 - DAY(CURRENT_DATE)) DAY) -- يبدأ التنفيذ يوم 27 من الشهر الحالي
DO
BEGIN
    UPDATE students
    SET balance = balance + 990; -- تحديث رصيد جميع الطلاب بإضافة 990
END$$

DELIMITER ;


3. عرض الأحداث للتأكد:

SHOW EVENTS;





-- for Test:

DELIMITER $$

CREATE EVENT add_monthly_reward2
ON SCHEDULE EVERY 1 MONTH
STARTS (CURRENT_DATE + INTERVAL (18 - DAY(CURRENT_DATE)) DAY) + INTERVAL '16:05' HOUR_MINUTE
DO
BEGIN
    UPDATE students
    SET balance = balance + 990;
END$$

DELIMITER ;



******************************************

1. خصم لكل طالب القيمة الموجودة ففي savincg account 
DELIMITER $$

CREATE EVENT DeductMonthlySavings
ON SCHEDULE EVERY 1 MONTH
STARTS '2025-01-27 06:00:00'
DO
BEGIN
    -- تحديث رصيد الطلاب وحسابات الادخار
    UPDATE students s
    JOIN SavingsAccount sa ON s.student_id = sa.student_id
    JOIN Goal g ON s.student_id = g.student_id
    SET 
        s.balance = s.balance - sa.monthly_saving_goal,  -- خصم من رصيد الطالب
        sa.balance = sa.balance + sa.monthly_saving_goal -- إضافة إلى حساب الادخار
    WHERE sa.monthly_saving_goal IS NOT NULL 
      AND sa.monthly_saving_goal > 0
      AND g.status != 'Achieved';
END $$

DELIMITER ;



2. عرض الأحداث للتأكد:

SHOW EVENTS;


3. اختبار الحدث يدويًا:
DELIMITER $$

CREATE EVENT DeductMonthlySavings1
ON SCHEDULE EVERY 1 MONTH
STARTS '2025-01-18 17:20:00'
DO
BEGIN
    -- تحديث رصيد الطلاب وحسابات الادخار
    UPDATE students s
    JOIN SavingsAccount sa ON s.student_id = sa.student_id
    JOIN Goal g ON s.student_id = g.student_id
    SET 
        s.balance = s.balance - sa.monthly_saving_goal,  -- خصم من رصيد الطالب
        sa.balance = sa.balance + sa.monthly_saving_goal -- إضافة إلى حساب الادخار
    WHERE sa.monthly_saving_goal IS NOT NULL 
      AND sa.monthly_saving_goal > 0
      AND g.status != 'Achieved';
END $$

DELIMITER ;



4. حذف 

DROP EVENT DeductMonthlySavings1;
