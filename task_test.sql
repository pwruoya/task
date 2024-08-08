-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 08, 2024 at 09:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `task_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `intern_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `progress` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `intern_id`, `task_id`, `log_date`, `progress`) VALUES
(1, 1, 1, '2024-08-07', 'Half way done'),
(2, 3, 2, '2024-08-07', 'Finished'),
(3, 3, 2, '2024-08-07', 'Finished'),
(4, 1, 1, '2024-08-07', 'Almost Finished'),
(5, 1, 1, '2024-08-08', 'Finished');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('high','medium','low') NOT NULL,
  `due_date` date NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `intern_id` int(11) NOT NULL,
  `completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `priority`, `due_date`, `supervisor_id`, `intern_id`, `completed`) VALUES
(1, 'Adjust permissions', 'simple', 'high', '2024-08-08', 2, 1, 1),
(2, 'Create a new login system', 'You can do it in teams', 'medium', '2024-08-09', 2, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('intern','supervisor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'rashford09', '$2y$10$2ZavhrBE2f1MjE4C9F/3E.El5SC4lTdq5YwdqZFuuAm12e1p6vREq', 'intern'),
(2, 'Optimus Prime', '$2y$10$L7PHXYpxoCVBmI..Fxi3UuymV96/YOYw5b4C0Ki6DGnat8Ir0s9CG', 'supervisor'),
(3, 'sanjay', '$2y$10$ScFPorfC3HaqHrSpiAvFtOjFqakH.gSZM5Iok.f/Jk8b00eqRr.JC', 'intern');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `intern_id` (`intern_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supervisor_id` (`supervisor_id`),
  ADD KEY `intern_id` (`intern_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`intern_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `logs_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`intern_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
