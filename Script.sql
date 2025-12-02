-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2025 at 03:24 AM
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
-- Database: `mini_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `CategoryID` int(10) NOT NULL,
  `CategoryName` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `challenge`
--

CREATE TABLE `challenge` (
  `Challenge_ID` int(10) NOT NULL,
  `Category_ID` int(10) NOT NULL,
  `City_ID` int(10) NOT NULL,
  `User_ID` int(10) NOT NULL,
  `Title` text NOT NULL,
  `Description` text NOT NULL,
  `Difficulty` text NOT NULL,
  `Points` int(4) NOT NULL,
  `Start_date` date NOT NULL,
  `End_date` date NOT NULL,
  `Rules` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `CityID` int(10) NOT NULL,
  `CityName` text NOT NULL,
  `State` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `moderation`
--

CREATE TABLE `moderation` (
  `Moderation_ID` int(10) NOT NULL,
  `Submission_ID` int(10) NOT NULL,
  `User_ID` int(10) NOT NULL,
  `Action` text NOT NULL,
  `Action_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pointsledger`
--

CREATE TABLE `pointsledger` (
  `Points_earned` int(4) NOT NULL,
  `Earned_date` date NOT NULL,
  `Submission_ID` int(10) NOT NULL,
  `User_ID` int(10) NOT NULL,
  `Type` text NOT NULL,
  `Team_ID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `redeemrecord`
--

CREATE TABLE `redeemrecord` (
  `Reward_ID` int(10) NOT NULL,
  `RedeemRecord_ID` int(10) NOT NULL,
  `Reward_name` text NOT NULL,
  `Redeem_quantity` int(4) NOT NULL,
  `Redeem_By` int(10) NOT NULL,
  `Redeem_Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reward`
--

CREATE TABLE `reward` (
  `Reward_ID` int(10) NOT NULL,
  `Reward_name` text NOT NULL,
  `Points_Required` int(4) NOT NULL,
  `Stock` int(4) NOT NULL,
  `Description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submission`
--

CREATE TABLE `submission` (
  `Submission_ID` int(10) NOT NULL,
  `Challenge_ID` int(10) NOT NULL,
  `User_ID` int(10) NOT NULL,
  `Team_ID` int(10) DEFAULT NULL,
  `Photo` varchar(100) NOT NULL,
  `Caption` text NOT NULL,
  `Submission_date` date NOT NULL,
  `Status` text NOT NULL,
  `Verification_note` varchar(100) DEFAULT NULL,
  `QR_Code` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `Team_ID` int(10) NOT NULL,
  `Owner_ID` int(10) NOT NULL,
  `Team_name` varchar(20) NOT NULL,
  `Team_points` int(4) NOT NULL,
  `Total_members` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `User_ID` int(10) NOT NULL,
  `First_Name` text NOT NULL,
  `Last_Name` text NOT NULL,
  `User_DOB` text NOT NULL,
  `Email` varchar(20) NOT NULL,
  `Phone_num` int(20) NOT NULL,
  `Team_ID` int(10) DEFAULT NULL,
  `Point` int(4) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `Register_Date` varchar(20) NOT NULL,
  `Role` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verification_token`
--

CREATE TABLE `verification_token` (
  `Token_ID` int(10) NOT NULL,
  `User_ID` int(10) NOT NULL,
  `Token` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `challenge`
--
ALTER TABLE `challenge`
  ADD PRIMARY KEY (`Challenge_ID`),
  ADD KEY `challenge_ibfk_1` (`Category_ID`),
  ADD KEY `challenge_ibfk_3` (`User_ID`),
  ADD KEY `City_ID` (`City_ID`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`CityID`);

--
-- Indexes for table `moderation`
--
ALTER TABLE `moderation`
  ADD PRIMARY KEY (`Moderation_ID`),
  ADD KEY `moderation_ibfk_1` (`Submission_ID`),
  ADD KEY `moderation_ibfk_2` (`User_ID`);

--
-- Indexes for table `pointsledger`
--
ALTER TABLE `pointsledger`
  ADD KEY `Submission_ID` (`Submission_ID`),
  ADD KEY `Team_ID` (`Team_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `redeemrecord`
--
ALTER TABLE `redeemrecord`
  ADD PRIMARY KEY (`RedeemRecord_ID`),
  ADD KEY `Reward_ID` (`Reward_ID`),
  ADD KEY `Redeem_By` (`Redeem_By`);

--
-- Indexes for table `reward`
--
ALTER TABLE `reward`
  ADD PRIMARY KEY (`Reward_ID`);

--
-- Indexes for table `submission`
--
ALTER TABLE `submission`
  ADD PRIMARY KEY (`Submission_ID`),
  ADD KEY `Challenge_ID` (`Challenge_ID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Team_ID` (`Team_ID`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`Team_ID`),
  ADD UNIQUE KEY `owner_id_unique` (`Owner_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `email_unique` (`Email`),
  ADD KEY `user_ibfk_1` (`Team_ID`);

--
-- Indexes for table `verification_token`
--
ALTER TABLE `verification_token`
  ADD PRIMARY KEY (`Token_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `CategoryID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `challenge`
--
ALTER TABLE `challenge`
  MODIFY `Challenge_ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `CityID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `moderation`
--
ALTER TABLE `moderation`
  MODIFY `Moderation_ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `redeemrecord`
--
ALTER TABLE `redeemrecord`
  MODIFY `RedeemRecord_ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reward`
--
ALTER TABLE `reward`
  MODIFY `Reward_ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `submission`
--
ALTER TABLE `submission`
  MODIFY `Submission_ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `Team_ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `User_ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verification_token`
--
ALTER TABLE `verification_token`
  MODIFY `Token_ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `challenge`
--
ALTER TABLE `challenge`
  ADD CONSTRAINT `challenge_ibfk_4` FOREIGN KEY (`Category_ID`) REFERENCES `category` (`CategoryID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `challenge_ibfk_5` FOREIGN KEY (`City_ID`) REFERENCES `city` (`CityID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `challenge_ibfk_6` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `moderation`
--
ALTER TABLE `moderation`
  ADD CONSTRAINT `moderation_ibfk_1` FOREIGN KEY (`Submission_ID`) REFERENCES `submission` (`Submission_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `moderation_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pointsledger`
--
ALTER TABLE `pointsledger`
  ADD CONSTRAINT `pointsledger_ibfk_4` FOREIGN KEY (`Submission_ID`) REFERENCES `submission` (`Submission_ID`),
  ADD CONSTRAINT `pointsledger_ibfk_5` FOREIGN KEY (`Team_ID`) REFERENCES `team` (`Team_ID`),
  ADD CONSTRAINT `pointsledger_ibfk_6` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `redeemrecord`
--
ALTER TABLE `redeemrecord`
  ADD CONSTRAINT `redeemrecord_ibfk_1` FOREIGN KEY (`Reward_ID`) REFERENCES `reward` (`Reward_ID`),
  ADD CONSTRAINT `redeemrecord_ibfk_2` FOREIGN KEY (`Redeem_By`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `submission`
--
ALTER TABLE `submission`
  ADD CONSTRAINT `submission_ibfk_4` FOREIGN KEY (`Challenge_ID`) REFERENCES `challenge` (`Challenge_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `submission_ibfk_5` FOREIGN KEY (`Team_ID`) REFERENCES `team` (`Team_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `submission_ibfk_6` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `team`
--
ALTER TABLE `team`
  ADD CONSTRAINT `team_ibfk_1` FOREIGN KEY (`Owner_ID`) REFERENCES `user` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`Team_ID`) REFERENCES `team` (`Team_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `verification_token`
--
ALTER TABLE `verification_token`
  ADD CONSTRAINT `verification_token_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
