-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Jan 18, 2025 at 03:31 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `universitypayroll`
--

-- --------------------------------------------------------

--
-- Table structure for table `expense`
--

CREATE TABLE `expense` (
  `expense_id` int(11) NOT NULL,
  `student_id` varchar(10) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `category` varchar(50) NOT NULL,
  `expense_date` date NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `expense`
--

INSERT INTO `expense` (`expense_id`, `student_id`, `amount`, `category`, `expense_date`, `description`) VALUES
(1, '444000515', '50.00', 'Food', '2025-01-01', ''),
(2, '444000515', '850.00', 'Subscribe to educational courses', '2025-01-10', ''),
(3, '444000852', '150.75', 'Food', '2025-01-13', 'Lunch with friends'),
(9, '444000515', '10.00', 'Food', '2025-01-15', ''),
(10, '444000515', '70.00', 'Transport', '2024-12-05', '');

-- --------------------------------------------------------

--
-- Table structure for table `goal`
--

CREATE TABLE `goal` (
  `goal_id` int(11) NOT NULL,
  `student_id` varchar(10) NOT NULL,
  `description` text NOT NULL,
  `target_amount` decimal(15,2) NOT NULL,
  `status` enum('Pending','Achieved') NOT NULL DEFAULT 'Pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `goal`
--

INSERT INTO `goal` (`goal_id`, `student_id`, `description`, `target_amount`, `status`, `created_at`) VALUES
(1, '444000515', 'Buying a new laptop', '5000.00', 'Pending', '2025-01-13 17:41:14'),
(2, '444002648', 'Buy a new mobile phone', '2500.00', 'Pending', '2025-01-18 17:01:38');

-- --------------------------------------------------------

--
-- Table structure for table `savingsaccount`
--

CREATE TABLE `savingsaccount` (
  `account_id` int(11) NOT NULL,
  `student_id` varchar(10) NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `monthly_saving_goal` decimal(15,2) DEFAULT NULL,
  `opening_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `savingsaccount`
--

INSERT INTO `savingsaccount` (`account_id`, `student_id`, `balance`, `monthly_saving_goal`, `opening_date`) VALUES
(1, '444000515', '100.00', '100.00', '2025-01-13 17:57:31'),
(2, '444002648', '200.00', '200.00', '2025-01-18 17:01:47');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(10) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `birthday` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `balance` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_name`, `email`, `password`, `phone`, `specialization`, `birthday`, `gender`, `created_at`, `balance`) VALUES
('41255550', 'Abdullah Mahmoud Al-Sharif', 'abdcs@gmail.com', '$2y$10$Gl2VjwPngClQUSFsqyE8LuHrE88/77PD3j./AeWmTaRkZ042U53GW', '056522541', 'Computer Science', '1999-08-11', 'Male', '2025-01-11 16:32:24', 1810),
('412555544', 'Majed Ahmad AI-otaibi', 'majedO@gmail.com', '$2y$10$Q4RiJGiVwwodYz1mim6msOVCVw.S7j0GtTKHBoQtH/BPWY8Cg3SOe', '053445684', 'Pharmacy', '1998-04-11', 'Male', '2025-01-11 16:42:47', 1617),
('4147483647', 'Amal Khaled Al-Shareef', 'amalshareef2000@gmail.com', '$2y$10$gTC1lO/znyUd5EaY6o0s5.aokPlZWVgUv64lyiSiAyd8HD2tDPtQ2', '052055535', 'Pharmacy', '2000-02-12', 'Female', '2025-01-11 22:34:49', 2252),
('444000515', 'Ghala Mansour AI-otaibi', 'glaotb@hotmail.com', '$2y$10$/zhjtltf.GM4pXiD63tZIO8gMurXoOMA/8BmBXevEmG0ol/S/zI8K', '053445653', 'Computer Science', '2004-10-10', 'Female', '2025-01-11 16:02:20', 1200),
('444000852', 'Wejdan Muflih Al-sulami', 'wejdanalsolamy@gmail.com', '$2y$10$jF5srTdPt1nqnugRcLEH4ue6UhC5GNvkR.4VCwjtZQmkDcq0.xSIW', '056521412', 'Computer Science', '2003-10-08', 'Female', '2025-01-11 16:09:44', 1230),
('444002648', 'Maya Atif Felemban', 'mayafelemban55@gmail.com', '$2y$10$fobZuzdvHZa8RlUGg44yROptu4KAozNOo0ASCjElP3Kl5qKjv1ZXy', '05344568', 'Computer Science', '2003-10-05', 'Female', '2025-01-11 16:16:06', 1178),
('444003140', 'Anas Hussain Alkhuzaie', 'anasKH@gmail.com', '$2y$10$Sombhn0l6rT02SP3nl7Z2OdVuWsmw2JTJ.ySF981oFHvz.FYyQb12', '0584111000', 'Accounting and Economics', '2000-11-20', 'Male', '2025-01-11 16:30:30', 1700),
('444003445', 'Shooq Hussain Alkhuzaie', 'shooq2782@gmail.com', '$2y$10$UpaNutrqtNk8v17mfFw1gOtuC/gNwfuh57rBXoHFgkqOquosapBCa', '054415153', 'Computer Science', '2003-07-09', 'Female', '2025-01-11 16:25:40', 1340);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `expense`
--
ALTER TABLE `expense`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `goal`
--
ALTER TABLE `goal`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `savingsaccount`
--
ALTER TABLE `savingsaccount`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `expense`
--
ALTER TABLE `expense`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `goal`
--
ALTER TABLE `goal`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `savingsaccount`
--
ALTER TABLE `savingsaccount`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expense`
--
ALTER TABLE `expense`
  ADD CONSTRAINT `expense_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `goal`
--
ALTER TABLE `goal`
  ADD CONSTRAINT `goal_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `savingsaccount`
--
ALTER TABLE `savingsaccount`
  ADD CONSTRAINT `savingsaccount_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `add_monthly_reward` ON SCHEDULE EVERY 1 MONTH STARTS '2025-01-27 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    UPDATE students
    SET balance = balance + 990; -- تحديث رصيد جميع الطلاب بإضافة 990
END$$

CREATE DEFINER=`root`@`localhost` EVENT `DeductMonthlySavings` ON SCHEDULE EVERY 1 MONTH STARTS '2025-01-27 06:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
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
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
