SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `destiny_blocks` (
  `ID` int(11) NOT NULL,
  `Subnet` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `destiny_latency` (
  `ID` int(11) NOT NULL,
  `BlockID` int(11) NOT NULL,
  `Location` varchar(4) NOT NULL,
  `Latency` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `destiny_requests` (
  `ID` int(11) NOT NULL,
  `IP` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `destiny_blocks`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Subnet` (`Subnet`);

ALTER TABLE `destiny_latency`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `BlockID` (`BlockID`);

ALTER TABLE `destiny_requests`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `IP` (`IP`);


ALTER TABLE `destiny_blocks`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `destiny_latency`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `destiny_requests`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;COMMIT;
