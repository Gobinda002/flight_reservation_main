-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2025 at 07:23 PM
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
-- Database: `flight_reservation_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `email`, `password`) VALUES
(6, 'asd@gmail.com', '123');

-- --------------------------------------------------------

--
-- Table structure for table `airlines`
--

CREATE TABLE `airlines` (
  `airline_id` int(11) NOT NULL,
  `airline_name` varchar(100) NOT NULL,
  `added_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `airlines`
--

INSERT INTO `airlines` (`airline_id`, `airline_name`, `added_by`) VALUES
(12, 'Yeti Air', 6),
(13, 'buddha Air', 6),
(14, 'himalayan air', 6),
(15, 'saurya air', 6);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `flight_id` int(11) NOT NULL,
  `passenger_name` varchar(100) NOT NULL,
  `passenger_email` varchar(100) NOT NULL,
  `passenger_phone` varchar(15) NOT NULL,
  `nofpassenger` int(11) NOT NULL,
  `seatnumber` int(11) NOT NULL,
  `booking_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `flight_id`, `passenger_name`, `passenger_email`, `passenger_phone`, `nofpassenger`, `seatnumber`, `booking_date`) VALUES
(6, 1, 25, 'Apple', 'asd@gmail.com', '1232342342', 12, 0, '2025-08-21 21:32:49'),
(7, 1, 25, 'Apple', 'asd@gmail.com', '1232342342', 12, 1, '2025-08-21 21:33:07'),
(8, 1, 25, 'Apple', 'asd@gmail.com', '1232342342', 12, 2, '2025-08-21 21:33:25'),
(9, 1, 25, 'Apple', 'asd@gmail.com', '3242342342', 1, 3, '2025-08-21 21:34:10'),
(10, 1, 25, 'Apple', 'asd@gmail.com', '3242342342', 1, 4, '2025-08-21 21:34:51'),
(11, 1, 25, 'Apple', 'sdf@gmail.com', '3453453423', 1, 5, '2025-08-21 21:57:12'),
(12, 1, 25, 'Apple', 'sdf@gmail.com', '3453453423', 1, 6, '2025-08-21 21:57:48'),
(13, 1, 25, 'Apple', 'sdf@gmail.com', '3453453423', 1, 7, '2025-08-21 21:57:58');

-- --------------------------------------------------------

--
-- Table structure for table `flights`
--

CREATE TABLE `flights` (
  `flight_id` int(11) NOT NULL,
  `airline_id` int(11) NOT NULL,
  `plane_id` int(11) NOT NULL,
  `origin` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `departure_time` datetime NOT NULL,
  `arrival_time` datetime NOT NULL,
  `total_seats` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `booked_seats` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flights`
--

INSERT INTO `flights` (`flight_id`, `airline_id`, `plane_id`, `origin`, `destination`, `departure_time`, `arrival_time`, `total_seats`, `price`, `booked_seats`) VALUES
(25, 15, 21, 'pokhara', 'kathmandu', '2025-08-23 09:49:00', '2025-08-23 09:51:00', 20, 1000, 4),
(26, 14, 19, 'kathmandu', 'pokhara', '2025-08-26 21:46:00', '2025-08-26 21:49:00', 30, 2000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `planes`
--

CREATE TABLE `planes` (
  `plane_id` int(11) NOT NULL,
  `airline_id` int(11) NOT NULL,
  `plane_number` varchar(20) NOT NULL,
  `capacity` int(11) NOT NULL,
  `added_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `planes`
--

INSERT INTO `planes` (`plane_id`, `airline_id`, `plane_number`, `capacity`, `added_by`) VALUES
(1, 10, 'sdaf', 34, 6),
(2, 2, 'asddrf', 234, 6),
(3, 2, 'f3223', 234, 6),
(4, 11, 'asdasd', 32, 6),
(7, 4, 'asdfaw', 323, 6),
(8, 4, 'asfq32', 32, 6),
(14, 2, 'f4eewf', 234, 6),
(15, 2, '23rasd', 32, 6),
(16, 13, 'ba2233', 20, 6),
(17, 13, 'ba234e', 30, 6),
(18, 14, 'ha2212', 20, 6),
(19, 14, 'ha3d32', 50, 6),
(20, 14, 'ha3sdf', 44, 6),
(21, 15, 'sa2342', 23, 6),
(22, 15, 'sa34f2', 33, 6),
(23, 12, 'ya2342', 20, 6),
(24, 12, 'ya43df', 332, 6);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone`) VALUES
(1, 'apple', 'asd@gmail.com', '$2y$10$ABdFvbdmyJUh7zK0N1qgue/KW/K.scfH2J.88T.S45zsSiscY9/Ty', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `airlines`
--
ALTER TABLE `airlines`
  ADD PRIMARY KEY (`airline_id`),
  ADD UNIQUE KEY `airline_name` (`airline_name`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `flight_id` (`flight_id`);

--
-- Indexes for table `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`flight_id`),
  ADD KEY `plane_id` (`plane_id`),
  ADD KEY `fk_airline_id` (`airline_id`);

--
-- Indexes for table `planes`
--
ALTER TABLE `planes`
  ADD PRIMARY KEY (`plane_id`),
  ADD UNIQUE KEY `model` (`plane_number`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `airlines_fk` (`airline_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `airlines`
--
ALTER TABLE `airlines`
  MODIFY `airline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `flights`
--
ALTER TABLE `flights`
  MODIFY `flight_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `planes`
--
ALTER TABLE `planes`
  MODIFY `plane_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `airlines`
--
ALTER TABLE `airlines`
  ADD CONSTRAINT `airlines_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`flight_id`) ON DELETE CASCADE;

--
-- Constraints for table `flights`
--
ALTER TABLE `flights`
  ADD CONSTRAINT `fk_airline_id` FOREIGN KEY (`airline_id`) REFERENCES `airlines` (`airline_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `flights_ibfk_1` FOREIGN KEY (`plane_id`) REFERENCES `planes` (`plane_id`) ON DELETE CASCADE;

--
-- Constraints for table `planes`
--
ALTER TABLE `planes`
  ADD CONSTRAINT `airlines_fk` FOREIGN KEY (`airline_id`) REFERENCES `airlines` (`airline_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
