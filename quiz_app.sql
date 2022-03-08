-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 01, 2021 at 07:21 PM
-- Server version: 5.7.34
-- PHP Version: 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quiz_app`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `display_low_score` (IN `userName` VARCHAR(50), IN `quizID` INT, OUT `if_low` BOOLEAN)  BEGIN
	SET @counter =0;
    SELECT COUNT(*) INTO @counter FROM question WHERE quiz_id = quizID;
	SET @score = 0;
    SELECT MAX(score) INTO @score FROM attempt WHERE quiz_id = quizID AND user_name = userName;
    IF (@score/@counter) < 0.4 THEN
    	SET if_low = "1";
    ELSE
    	SET if_low = "0";
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `required_procedure` (IN `user_Name` VARCHAR(50), IN `quiz_ID` INT)  BEGIN

SET @counter = 0;

SELECT COUNT(*) INTO @counter FROM question WHERE question.quiz_id = quiz_ID;

SET @ratio = @counter * 0.4;

SELECT actual_name, score  FROM user_records, attempt WHERE user_records.user_name = user_Name AND attempt.user_name = user_Name AND attempt.score < @ratio;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `question_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `answer` text,
  `is_true` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`question_id`, `answer_id`, `answer`, `is_true`) VALUES
(1, 1, 'Love it.', 1),
(1, 2, 'It\'s decent.', 0),
(1, 3, 'I did not make a decision yet.', 0),
(1, 4, 'None of your business.', 0),
(2, 1, 'I do not know yet.', 0),
(2, 2, 'It\'s bootstrap what do you expect.', 1),
(2, 3, '-', 0),
(2, 4, '-', 0),
(3, 1, 'A low one', 0),
(3, 2, 'A decent one.', 0),
(3, 3, 'A nice one.', 1),
(3, 4, 'Did not decide yet.', 0),
(4, 1, 'Answer 1', 0),
(4, 2, 'Answer 2', 0),
(4, 3, 'Answer 3', 1),
(4, 4, 'Answer 4', 0),
(5, 1, 'Answer 1', 0),
(5, 2, 'Answer 2', 0),
(5, 3, 'Answer 3', 0),
(5, 4, 'Answer 4', 1);

-- --------------------------------------------------------

--
-- Table structure for table `attempt`
--

CREATE TABLE `attempt` (
  `quiz_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `date_of_attempt` datetime NOT NULL,
  `score` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `attempt`
--

INSERT INTO `attempt` (`quiz_id`, `user_name`, `date_of_attempt`, `score`) VALUES
(1, 'aziz313', '2021-11-26 09:43:01', 3),
(1, 'not_admin', '2021-11-26 10:13:41', 0);

-- --------------------------------------------------------

--
-- Table structure for table `delete_log`
--

CREATE TABLE `delete_log` (
  `quiz_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `date_of_delete` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `delete_log`
--

INSERT INTO `delete_log` (`quiz_id`, `user_name`, `date_of_delete`) VALUES
(1, 'aziz313', '2021-11-25 16:50:54');

-- --------------------------------------------------------

--
-- Table structure for table `priviliges`
--

CREATE TABLE `priviliges` (
  `privilige` varchar(50) NOT NULL,
  `pwd` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `priviliges`
--

INSERT INTO `priviliges` (`privilige`, `pwd`) VALUES
('admin', '$2y$10$sEaji896nbT/oInkyxH3KO4Q2yLi0gRRXc1DAqzi31cdZx21n0mue');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `question` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`quiz_id`, `question_id`, `question`) VALUES
(1, 1, 'How do you like the website so far?'),
(1, 2, 'Is the design of the website good?'),
(1, 3, 'What mark are you going to give me?'),
(2, 4, 'Question Here'),
(2, 5, 'Question Here');

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `quiz_id` int(11) NOT NULL,
  `quiz_name` varchar(100) NOT NULL,
  `author` varchar(50) NOT NULL,
  `quiz_available` tinyint(1) DEFAULT '0',
  `quiz_duration` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`quiz_id`, `quiz_name`, `author`, `quiz_available`, `quiz_duration`) VALUES
(1, 'test quiz', 'aziz313', 1, '00:15:00'),
(2, 'fdsfdsa', 'aziz313', 1, '00:15:00');

--
-- Triggers `quiz`
--
DELIMITER $$
CREATE TRIGGER `log_delete` AFTER DELETE ON `quiz` FOR EACH ROW INSERT INTO 
delete_log(quiz_id, user_name, date_of_delete) 
VALUES(OLD.quiz_id, OLD.author, NOW())
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_records`
--

CREATE TABLE `user_records` (
  `user_name` varchar(50) NOT NULL,
  `actual_name` varchar(100) NOT NULL,
  `password_` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_records`
--

INSERT INTO `user_records` (`user_name`, `actual_name`, `password_`, `is_admin`) VALUES
('aziz313', 'Aziz Serin', '$2y$10$bcfc99rSBXss1ngbP0TjSueILdjYPjz7bFF6RwKuFzIVu7vSVrtfC', 1),
('not_admin', 'Tester', '$2y$10$ry1iI0YDt//B.x2B0gz6weMdRTRqB7vAopBT6FBSSTP2Z4XxI3Dw2', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`question_id`,`answer_id`);

--
-- Indexes for table `attempt`
--
ALTER TABLE `attempt`
  ADD PRIMARY KEY (`quiz_id`,`user_name`,`date_of_attempt`),
  ADD KEY `user_name` (`user_name`);

--
-- Indexes for table `delete_log`
--
ALTER TABLE `delete_log`
  ADD PRIMARY KEY (`quiz_id`,`user_name`,`date_of_delete`),
  ADD KEY `user_name` (`user_name`);

--
-- Indexes for table `priviliges`
--
ALTER TABLE `priviliges`
  ADD PRIMARY KEY (`privilige`),
  ADD UNIQUE KEY `privilige` (`privilige`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`question_id`),
  ADD UNIQUE KEY `question_id` (`question_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`quiz_id`),
  ADD UNIQUE KEY `quiz_id` (`quiz_id`),
  ADD KEY `author` (`author`);

--
-- Indexes for table `user_records`
--
ALTER TABLE `user_records`
  ADD PRIMARY KEY (`user_name`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `password_` (`password_`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`question_id`);

--
-- Constraints for table `attempt`
--
ALTER TABLE `attempt`
  ADD CONSTRAINT `attempt_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`),
  ADD CONSTRAINT `attempt_ibfk_2` FOREIGN KEY (`user_name`) REFERENCES `user_records` (`user_name`);

--
-- Constraints for table `delete_log`
--
ALTER TABLE `delete_log`
  ADD CONSTRAINT `delete_log_ibfk_1` FOREIGN KEY (`user_name`) REFERENCES `user_records` (`user_name`);

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`);

--
-- Constraints for table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`author`) REFERENCES `user_records` (`user_name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
