-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2023 at 07:02 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carpool_test4`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking_tbl`
--

CREATE TABLE `booking_tbl` (
  `booking_id` int(11) NOT NULL,
  `buser_id` int(11) NOT NULL,
  `bseatrate_id` int(11) NOT NULL,
  `bpickup_location` varchar(100) NOT NULL,
  `bdropoff_location` varchar(100) NOT NULL,
  `bbooking_status` varchar(20) NOT NULL DEFAULT 'PENDING',
  `btimestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_tbl`
--

INSERT INTO `booking_tbl` (`booking_id`, `buser_id`, `bseatrate_id`, `bpickup_location`, `bdropoff_location`, `bbooking_status`, `btimestamp`) VALUES
(14, 6, 21, 'Baliwag', 'Pulilan', 'COMPLETED', '2023-06-12 11:52:34');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_tbl`
--

CREATE TABLE `feedback_tbl` (
  `feedback_id` int(11) NOT NULL,
  `fbooking_id` int(11) NOT NULL,
  `frating` int(5) NOT NULL,
  `fcomment` text DEFAULT NULL,
  `ftimestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_tbl`
--

INSERT INTO `feedback_tbl` (`feedback_id`, `fbooking_id`, `frating`, `fcomment`, `ftimestamp`) VALUES
(11, 14, 5, 'Good', '2023-06-12 12:13:29');

-- --------------------------------------------------------

--
-- Table structure for table `payment_tbl`
--

CREATE TABLE `payment_tbl` (
  `payment_id` int(11) NOT NULL,
  `pbooking_id` int(11) NOT NULL,
  `pamount` int(11) NOT NULL,
  `pstatus` varchar(20) NOT NULL DEFAULT 'PENDING',
  `ptimestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_tbl`
--

INSERT INTO `payment_tbl` (`payment_id`, `pbooking_id`, `pamount`, `pstatus`, `ptimestamp`) VALUES
(14, 14, 100, 'PAID', '2023-06-12 11:52:35');

-- --------------------------------------------------------

--
-- Table structure for table `routetrans_tbl`
--

CREATE TABLE `routetrans_tbl` (
  `rtrans_id` int(11) NOT NULL,
  `rtroute_id` int(11) NOT NULL,
  `rttype` varchar(20) NOT NULL,
  `rtamount` int(11) NOT NULL,
  `rttax` int(11) NOT NULL DEFAULT 0,
  `rttimestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routetrans_tbl`
--

INSERT INTO `routetrans_tbl` (`rtrans_id`, `rtroute_id`, `rttype`, `rtamount`, `rttax`, `rttimestamp`) VALUES
(7, 8, 'EARNINGS', 100, 5, '2023-06-12 11:52:49');

-- --------------------------------------------------------

--
-- Table structure for table `route_tbl`
--

CREATE TABLE `route_tbl` (
  `route_id` int(11) NOT NULL,
  `rvehicle_id` int(11) NOT NULL,
  `rstart_point` varchar(100) NOT NULL,
  `rend_point` varchar(100) NOT NULL,
  `rdate_time` datetime NOT NULL,
  `rstatus` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  `rtimestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route_tbl`
--

INSERT INTO `route_tbl` (`route_id`, `rvehicle_id`, `rstart_point`, `rend_point`, `rdate_time`, `rstatus`, `rtimestamp`) VALUES
(8, 3, '1', '2', '2023-06-12 11:11:00', 'COMPLETED', '2023-06-12 04:31:14'),
(9, 3, '1', '2', '2023-06-13 20:30:00', 'ACTIVE', '2023-06-12 12:34:19');

-- --------------------------------------------------------

--
-- Table structure for table `seatrate_tbl`
--

CREATE TABLE `seatrate_tbl` (
  `seatrate_id` int(11) NOT NULL,
  `sroute_id` int(11) NOT NULL,
  `sseat_type_id` int(1) NOT NULL,
  `sprice` int(11) NOT NULL,
  `sstatus` varchar(20) NOT NULL DEFAULT 'AVAILABLE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seatrate_tbl`
--

INSERT INTO `seatrate_tbl` (`seatrate_id`, `sroute_id`, `sseat_type_id`, `sprice`, `sstatus`) VALUES
(21, 8, 1, 100, 'TAKEN'),
(22, 9, 1, 1, 'AVAILABLE'),
(23, 9, 3, 2, 'AVAILABLE');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_tbl`
--

CREATE TABLE `transaction_tbl` (
  `trans_id` int(11) NOT NULL,
  `tuser_id` int(11) NOT NULL,
  `ttrans_type` varchar(10) NOT NULL,
  `tamount` int(11) NOT NULL,
  `tmobile_no` varchar(11) NOT NULL,
  `tref_no` varchar(8) DEFAULT NULL,
  `toutfee` int(11) NOT NULL DEFAULT 0,
  `tinfee` int(11) NOT NULL DEFAULT 0,
  `tstatus` varchar(10) NOT NULL DEFAULT 'PENDING',
  `ttimedate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_tbl`
--

INSERT INTO `transaction_tbl` (`trans_id`, `tuser_id`, `ttrans_type`, `tamount`, `tmobile_no`, `tref_no`, `toutfee`, `tinfee`, `tstatus`, `ttimedate`) VALUES
(1, 19, 'CASH IN', 500, '09876543216', '11111111', 0, 50, 'ACCEPTED', '2023-05-09 06:58:15'),
(4, 19, 'CASH IN', 100, '09638794623', '12389173', 0, 20, 'ACCEPTED', '2023-05-09 07:30:38'),
(5, 19, 'CASH OUT', 100, '09849682384', '01273812', 20, 0, 'ACCEPTED', '2023-05-09 07:42:34'),
(6, 18, 'CASH IN', 500, '09762378462', '12361278', 0, 20, 'ACCEPTED', '2023-05-09 08:16:38'),
(7, 18, 'CASH IN', 250, '09712389712', '12893712', 0, 50, 'ACCEPTED', '2023-05-09 08:16:49'),
(8, 18, 'CASH IN', 100, '09632478264', '34789598', 0, 20, 'ACCEPTED', '2023-05-09 08:16:57'),
(9, 18, 'CASH OUT', 100, '09123127389', '09823490', 20, 0, 'ACCEPTED', '2023-05-09 08:18:35'),
(11, 6, 'CASH IN', 500, '09812309812', '89126738', 0, 50, 'APPROVED', '2023-05-16 09:17:56'),
(12, 6, 'CASH OUT', 5100, '09812379812', '12312312', 120, 0, 'APPROVED', '2023-05-16 11:04:09'),
(15, 6, 'CASH IN', 500, '09871289371', '17238912', 0, 50, 'APPROVED', '2023-05-16 13:39:15'),
(16, 6, 'CASH IN', 500, '09809123801', '71298371', 0, 50, 'APPROVED', '2023-05-16 13:41:37'),
(20, 6, 'CASH OUT', 100, '09819237129', NULL, 20, 0, 'PENDING', '2023-05-16 14:59:02'),
(21, 6, 'CASH IN', 500, '09812309128', '12312313', 0, 50, 'PENDING', '2023-05-16 18:11:34'),
(22, 6, 'CASH IN', 500, '09238402398', '12093819', 0, 50, 'PENDING', '2023-05-16 18:11:40'),
(23, 6, 'CASH IN', 500, '09798123798', '12379128', 0, 50, 'PENDING', '2023-05-16 18:16:34'),
(24, 19, 'CASH OUT', 300, '09012839102', NULL, 20, 0, 'PENDING', '2023-05-20 20:57:52');

-- --------------------------------------------------------

--
-- Table structure for table `user_tbl`
--

CREATE TABLE `user_tbl` (
  `user_id` int(11) NOT NULL,
  `ufname` varchar(100) NOT NULL,
  `umname` varchar(100) NOT NULL,
  `ulname` varchar(100) NOT NULL,
  `uemail` varchar(100) NOT NULL,
  `upassword` varchar(100) NOT NULL,
  `ucnumber` varchar(11) NOT NULL,
  `ubirthdate` date NOT NULL,
  `uaddress` varchar(100) NOT NULL,
  `ucity` varchar(100) NOT NULL,
  `uprovince` varchar(100) NOT NULL,
  `uzip` varchar(10) NOT NULL,
  `uuserlevel` int(1) NOT NULL DEFAULT 1,
  `uvercode` varchar(255) DEFAULT NULL,
  `uidtype` int(1) NOT NULL,
  `uidimg` varchar(256) NOT NULL,
  `upimg` varchar(256) NOT NULL,
  `ubalance` int(11) NOT NULL DEFAULT 0,
  `utimestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_tbl`
--

INSERT INTO `user_tbl` (`user_id`, `ufname`, `umname`, `ulname`, `uemail`, `upassword`, `ucnumber`, `ubirthdate`, `uaddress`, `ucity`, `uprovince`, `uzip`, `uuserlevel`, `uvercode`, `uidtype`, `uidimg`, `upimg`, `ubalance`, `utimestamp`) VALUES
(1, 'Malcolm ', 'C.', 'Graves', 'graves@gmail.com', '123', '09875647281', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 1, NULL, 1, '', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 16:18:48'),
(2, 'Elise', 'K.', 'Zaavan', 'elise@gmail.com', '123', '09857421312', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 6, NULL, 0, 'storimg/dimg/64536b6a14d862.82655935.jpg\n', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 16:18:48'),
(3, 'Garen', 'D.', 'Crownguard', 'garen@gmail.com', '123', '09748372617', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 2, NULL, 1, 'storimg/dimg/64536b6a14d862.82655935.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 16:19:25'),
(4, 'Irelia', 'I.', 'Xan', 'irelia@gmail.com', '123', '09876537371', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 2, NULL, 1, 'storimg/dimg/64536b6a14d862.82655935.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 16:54:34'),
(5, 'Shieda', 'G.', 'Kayn', 'kayn@gmail.com', '123', '09874636162', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 3, NULL, 2, 'storimg/dimg/64536b6a14d862.82655935.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 16:54:34'),
(6, 'Jarvan', 'D.', 'Lightshield', 'jarvan@gmail.com', '123', '09847182910', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 3, NULL, 2, 'storimg/dimg/6486a736722655.90951625.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 1125, '2023-04-19 16:54:34'),
(7, 'Jayce', 'P.', 'Giopara', 'jayce@gmail.com', '123', '09876457182', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 2, NULL, 1, 'storimg/dimg/64536b6a14d862.82655935.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 16:54:34'),
(8, 'Qiyana', 'Q.', 'Yunalai', 'qiyana@gmail.com', '123', '09847172819', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 2, NULL, 1, 'storimg/dimg/64536b6a14d862.82655935.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 16:54:34'),
(9, 'Riven ', 'I.', 'Konte', 'riven@gmail.com', '123', '09876547823', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 2, NULL, 1, 'storimg/dimg/64536b6a14d862.82655935.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 16:54:34'),
(10, 'Talon', 'N.', 'du Couteau', 'talon@gmail.com', '123', '09875678312', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 2, NULL, 1, 'storimg/dimg/64536b6a14d862.82655935.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 16:54:34'),
(11, 'Ivern ', 'B.', 'Bramblefoot', 'ivern@gmail.com', '123', '09874657381', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 2, NULL, 1, 'storimg/dimg/64536b6a14d862.82655935.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 17:01:43'),
(12, 'Shauna ', 'G.', 'Vayne', 'vayne@gmail.com', '123', '09874636271', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 2, NULL, 1, 'storimg/dimg/64536b6a14d862.82655935.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 17:01:43'),
(13, 'Zilean ', 'O.', 'Icath’un', 'zilean@gmail.com', '123', '09874636271', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 2, NULL, 1, 'storimg/dimg/64536b6a14d862.82655935.jpg\n', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 17:01:43'),
(14, 'Tobias ', 'TF.', 'Felix', 'twisted@gmail.com', '123', '09847573491', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 3, NULL, 2, 'storimg/dimg/6457caac0daf82.16236136.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 17:01:43'),
(15, 'Jericho', 'N.', 'Swain', 'swain@gmail.com', '123', '09784839210', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 2, NULL, 1, 'storimg/dimg/64536b6a14d862.82655935.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 17:01:43'),
(16, 'Sona ', 'I.', 'Buvelle ', 'sona@gmail.com', '123', '09748391021', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 2, NULL, 1, 'storimg/dimg/64536b6a14d862.82655935.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 17:01:43'),
(17, 'Sivunas ', 'A.', 'Alahair', 'sivir@gmail.com', '123', '09747382910', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 3, NULL, 2, 'storimg/dimg/6457ca432d2241.79338861.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 0, '2023-04-19 17:01:43'),
(18, 'Orianna ', 'I.', 'Reveck', 'orianna@gmail.com', '123', '09748392651', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 3, NULL, 2, 'storimg/dimg/oriannalicense.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 610, '2023-04-19 17:01:43'),
(19, 'Sarah ', 'MF.', 'Fortune', 'mf@gmail.com', '123', '09784930759', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 4, NULL, 2, 'storimg/dimg/mflicense.png', 'storimg/pimg/6486a65186a550.72994517.png', 390, '2023-04-19 17:01:43'),
(20, 'Cecil ', 'B.', 'Heimerdinger', 'heimer@gmail.com', '123', '09187548919', '2023-05-02', 'Tarcan', 'Baliwag', 'Bulacan', '3000', 4, NULL, 1, 'storimg/dimg/64536b6a14d862.82655935.jpg', 'storimg/pimg/6486a65186a550.72994517.png', 30, '2023-04-19 17:01:43'),
(22, '123', '123', '123', 'lalesa8777@rockdian.com', 'Hello123', '09182390812', '2023-05-28', '123', '123', '123', '123', 3, NULL, 2, 'storimg/dimg/6486ab70b74439.12273392.jpg', 'storimg/pimg/6486ab70b758d3.40672817.jpg', 10, '2023-06-12 05:21:52');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_tbl`
--

CREATE TABLE `vehicle_tbl` (
  `vehicle_id` int(11) NOT NULL,
  `vuser_id` int(11) NOT NULL,
  `vcolor` varchar(50) NOT NULL,
  `vmodel` varchar(50) NOT NULL,
  `vtype` varchar(50) NOT NULL,
  `vplate` varchar(10) NOT NULL,
  `vengnum` varchar(50) NOT NULL,
  `vinsurance` varchar(50) NOT NULL,
  `vcrimg` varchar(256) NOT NULL,
  `vimage` varchar(256) NOT NULL,
  `vstatus` varchar(10) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_tbl`
--

INSERT INTO `vehicle_tbl` (`vehicle_id`, `vuser_id`, `vcolor`, `vmodel`, `vtype`, `vplate`, `vengnum`, `vinsurance`, `vcrimg`, `vimage`, `vstatus`) VALUES
(1, 18, '#ff0000', 'McQueen', 'SportsCar', 'Kachow', '', '', 'storimg/crimg/645295c6600010.82626855.jpg', 'storimg/vimg/645125781101e4.03149014.png', 'PENDING'),
(3, 19, '#000000', 'Jackson', 'SportsCar', 'Storm', '123', '1234', 'storimg/crimg/645295c6600010.82626855.jpg', 'storimg/vimg/645125781101e4.03149014.png', 'ACTIVE'),
(4, 19, '#836d6d', '123', 'SUV', '123', '123', '123', 'storimg/crimg/645295c6600010.82626855.jpg', 'storimg/vimg/6452965fb3d8c4.72560296.webp', 'ACTIVE'),
(5, 19, '#4f4f4f', '123', 'Sedan', '123', '123', '1234', 'storimg/crimg/645295c6600010.82626855.jpg', 'storimg/vimg/645296c8e4a2c6.41062247.webp', 'ACTIVE'),
(7, 20, '#000000', '1', 'SUV', '2', '3', '4', 'storimg/crimg/6484b615b21706.87249114.jpg', 'storimg/vimg/6484b615b1eda9.61486425.jpg', 'ACTIVE');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_tbl`
--
ALTER TABLE `booking_tbl`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `buser_id` (`buser_id`),
  ADD KEY `bseatrate_id` (`bseatrate_id`);

--
-- Indexes for table `feedback_tbl`
--
ALTER TABLE `feedback_tbl`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `fbooking_id` (`fbooking_id`);

--
-- Indexes for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `pbooking_id` (`pbooking_id`);

--
-- Indexes for table `routetrans_tbl`
--
ALTER TABLE `routetrans_tbl`
  ADD PRIMARY KEY (`rtrans_id`),
  ADD KEY `rtroute_id` (`rtroute_id`);

--
-- Indexes for table `route_tbl`
--
ALTER TABLE `route_tbl`
  ADD PRIMARY KEY (`route_id`),
  ADD KEY `VEHICLE_ID - VEHICLE_TBL` (`rvehicle_id`);

--
-- Indexes for table `seatrate_tbl`
--
ALTER TABLE `seatrate_tbl`
  ADD PRIMARY KEY (`seatrate_id`),
  ADD KEY `ROUTE_ID - ROUTE_TBL` (`sroute_id`);

--
-- Indexes for table `transaction_tbl`
--
ALTER TABLE `transaction_tbl`
  ADD PRIMARY KEY (`trans_id`),
  ADD KEY `tuser_id` (`tuser_id`);

--
-- Indexes for table `user_tbl`
--
ALTER TABLE `user_tbl`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `vehicle_tbl`
--
ALTER TABLE `vehicle_tbl`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `vdriver_id` (`vuser_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_tbl`
--
ALTER TABLE `booking_tbl`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `feedback_tbl`
--
ALTER TABLE `feedback_tbl`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `routetrans_tbl`
--
ALTER TABLE `routetrans_tbl`
  MODIFY `rtrans_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `route_tbl`
--
ALTER TABLE `route_tbl`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `seatrate_tbl`
--
ALTER TABLE `seatrate_tbl`
  MODIFY `seatrate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `transaction_tbl`
--
ALTER TABLE `transaction_tbl`
  MODIFY `trans_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_tbl`
--
ALTER TABLE `user_tbl`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `vehicle_tbl`
--
ALTER TABLE `vehicle_tbl`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_tbl`
--
ALTER TABLE `booking_tbl`
  ADD CONSTRAINT `booking_tbl_ibfk_1` FOREIGN KEY (`buser_id`) REFERENCES `user_tbl` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_tbl_ibfk_2` FOREIGN KEY (`bseatrate_id`) REFERENCES `seatrate_tbl` (`seatrate_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedback_tbl`
--
ALTER TABLE `feedback_tbl`
  ADD CONSTRAINT `feedback_tbl_ibfk_1` FOREIGN KEY (`fbooking_id`) REFERENCES `booking_tbl` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_tbl`
--
ALTER TABLE `payment_tbl`
  ADD CONSTRAINT `payment_tbl_ibfk_1` FOREIGN KEY (`pbooking_id`) REFERENCES `booking_tbl` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `routetrans_tbl`
--
ALTER TABLE `routetrans_tbl`
  ADD CONSTRAINT `routetrans_tbl_ibfk_1` FOREIGN KEY (`rtroute_id`) REFERENCES `route_tbl` (`route_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `route_tbl`
--
ALTER TABLE `route_tbl`
  ADD CONSTRAINT `VEHICLE_ID - VEHICLE_TBL` FOREIGN KEY (`rvehicle_id`) REFERENCES `vehicle_tbl` (`vehicle_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seatrate_tbl`
--
ALTER TABLE `seatrate_tbl`
  ADD CONSTRAINT `ROUTE_ID - ROUTE_TBL` FOREIGN KEY (`sroute_id`) REFERENCES `route_tbl` (`route_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_tbl`
--
ALTER TABLE `transaction_tbl`
  ADD CONSTRAINT `transaction_tbl_ibfk_1` FOREIGN KEY (`tuser_id`) REFERENCES `user_tbl` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vehicle_tbl`
--
ALTER TABLE `vehicle_tbl`
  ADD CONSTRAINT `vehicle_tbl_ibfk_1` FOREIGN KEY (`vuser_id`) REFERENCES `user_tbl` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
