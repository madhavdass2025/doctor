-- VetCare Clinic - Database Schema

--
-- Table structure for table `doctors`
--
CREATE TABLE `doctors` (
  `DoctorID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`DoctorID`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `registration`
--
CREATE TABLE `registration` (
  `RegID` int NOT NULL AUTO_INCREMENT,
  `custType` varchar(100) NOT NULL DEFAULT 'regular',
  `RegDt` varchar(100) NOT NULL,
  `RegNo` varchar(100) NOT NULL,
  `Pettyp` varchar(100) NOT NULL,
  `petnam` varchar(100) NOT NULL,
  `petclr` varchar(100) DEFAULT NULL,
  `petsex` varchar(100) DEFAULT NULL,
  `petbred` varchar(100) DEFAULT NULL,
  `year` varchar(10) NOT NULL,
  `month` varchar(10) NOT NULL,
  `gram` varchar(10) NOT NULL,
  `kg` varchar(10) NOT NULL,
  `petsp` varchar(100) NOT NULL,
  `doctor` varchar(100) DEFAULT NULL,
  `ownnam` varchar(100) DEFAULT NULL,
  `ownadd1` varchar(100) DEFAULT NULL,
  `ownadd2` varchar(100) DEFAULT NULL,
  `ownloc` varchar(50) DEFAULT NULL,
  `ownpin` varchar(50) DEFAULT NULL,
  `ownmob` varchar(100) DEFAULT NULL,
  `ownres` varchar(100) DEFAULT NULL,
  `ownemail` varchar(100) DEFAULT NULL,
  `Reports` tinyint(1) DEFAULT '0',
  `cancel` varchar(100) NOT NULL DEFAULT '0',
  `canceldoneby` varchar(100) DEFAULT NULL,
  `dt` date DEFAULT NULL,
   PRIMARY KEY (`RegID`),
   UNIQUE KEY `RegNo` (`RegNo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `consultation_booking`
--
CREATE TABLE `consultation_booking` (
  `BookingID` int NOT NULL AUTO_INCREMENT,
  `RegID` int NOT NULL,
  `DoctorID` int NOT NULL,
  `BookingDate` date NOT NULL,
  `Status` enum('not checked','checked') NOT NULL DEFAULT 'not checked',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`BookingID`),
  FOREIGN KEY (`RegID`) REFERENCES `registration`(`RegID`),
  FOREIGN KEY (`DoctorID`) REFERENCES `doctors`(`DoctorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `consultations`
--
CREATE TABLE `consultations` (
  `ConsultationID` int NOT NULL AUTO_INCREMENT,
  `BookingID` int NOT NULL,
  `RegID` int NOT NULL,
  `Temperature` varchar(50) DEFAULT NULL,
  `Weight` decimal(10,3) DEFAULT NULL,
  `DiagnosisNotes` text,
  `ConsultationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ConsultationID`),
  FOREIGN KEY (`BookingID`) REFERENCES `consultation_booking`(`BookingID`),
  FOREIGN KEY (`RegID`) REFERENCES `registration`(`RegID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `medicines`
--
CREATE TABLE `medicines` (
  `Mid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `UnitPrice` varchar(100) NOT NULL,
  `taxExcluded_price` varchar(100) NOT NULL,
  `taxAmount` varchar(100) NOT NULL,
  `hsn` varchar(100) NOT NULL,
  `taxable` varchar(10) NOT NULL DEFAULT 'yes',
  `Itax` varchar(10) NOT NULL,
  `cess` varchar(10) NOT NULL,
  `submittedby` varchar(100) NOT NULL,
  `submitteddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(100) NOT NULL DEFAULT 'available',
   PRIMARY KEY (`Mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `consultation_medicines`
--
CREATE TABLE `consultation_medicines` (
  `CM_ID` int NOT NULL AUTO_INCREMENT,
  `ConsultationID` int NOT NULL,
  `MedicineID` int DEFAULT NULL,
  `OtherMedicineName` varchar(255) DEFAULT NULL,
  `Dosage` varchar(100) DEFAULT NULL,
  `Frequency` varchar(100) DEFAULT NULL,
  `TotalUnits` varchar(100) DEFAULT NULL,
  `Time` varchar(100) DEFAULT NULL,
  `Type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CM_ID`),
  FOREIGN KEY (`ConsultationID`) REFERENCES `consultations`(`ConsultationID`),
  FOREIGN KEY (`MedicineID`) REFERENCES `medicines`(`Mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `vaccination`
--
CREATE TABLE `vaccination` (
  `VId` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `amount` varchar(10) NOT NULL,
  `type` varchar(10) DEFAULT NULL,
  `duration` varchar(10) DEFAULT NULL,
  `submittedBy` varchar(100) NOT NULL,
  `submitteddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`VId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `consultation_injections`
--
CREATE TABLE `consultation_injections` (
  `CI_ID` int NOT NULL AUTO_INCREMENT,
  `ConsultationID` int NOT NULL,
  `VaccinationID` int DEFAULT NULL,
  `InjectionName` varchar(255) DEFAULT NULL,
  `Dosage` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CI_ID`),
  FOREIGN KEY (`ConsultationID`) REFERENCES `consultations`(`ConsultationID`),
  FOREIGN KEY (`VaccinationID`) REFERENCES `vaccination`(`VId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `surgery`
--
CREATE TABLE `surgery` (
  `surgeryID` int NOT NULL AUTO_INCREMENT,
  `surName` varchar(255) DEFAULT NULL,
  `surAmount` varchar(255) DEFAULT NULL,
  `submitBy` varchar(255) DEFAULT NULL,
  `submitDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`surgeryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `scan`
--
CREATE TABLE `scan` (
  `sID` int NOT NULL AUTO_INCREMENT,
  `scanName` varchar(100) NOT NULL,
  `amount` varchar(100) NOT NULL,
  `submittedby` varchar(100) NOT NULL,
  `submittedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `consultation_surgeries`
--
CREATE TABLE `consultation_surgeries` (
  `CS_ID` int NOT NULL AUTO_INCREMENT,
  `ConsultationID` int NOT NULL,
  `SurgeryID` int DEFAULT NULL,
  `SurgeryName` varchar(255) DEFAULT NULL,
  `SurgeryNotes` text,
  PRIMARY KEY (`CS_ID`),
  FOREIGN KEY (`ConsultationID`) REFERENCES `consultations`(`ConsultationID`),
  FOREIGN KEY (`SurgeryID`) REFERENCES `surgery`(`surgeryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `consultation_scans`
--
CREATE TABLE `consultation_scans` (
  `CSc_ID` int NOT NULL AUTO_INCREMENT,
  `ConsultationID` int NOT NULL,
  `ScanID` int DEFAULT NULL,
  `ScanName` varchar(255) DEFAULT NULL,
  `ScanNotes` text,
  PRIMARY KEY (`CSc_ID`),
  FOREIGN KEY (`ConsultationID`) REFERENCES `consultations`(`ConsultationID`),
  FOREIGN KEY (`ScanID`) REFERENCES `scan`(`sID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `laboratory`
--
CREATE TABLE `laboratory` (
  `Lid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `amount` varchar(10) NOT NULL,
  `submittedBy` varchar(100) NOT NULL,
  `submitteddate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`Lid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table structure for table `consultation_lab_tests`
--
CREATE TABLE `consultation_lab_tests` (
  `CLT_ID` int NOT NULL AUTO_INCREMENT,
  `ConsultationID` int NOT NULL,
  `LabTestID` int DEFAULT NULL,
  `CustomTestName` varchar(255) DEFAULT NULL,
  `Instructions` text,
  PRIMARY KEY (`CLT_ID`),
  FOREIGN KEY (`ConsultationID`) REFERENCES `consultations`(`ConsultationID`),
  FOREIGN KEY (`LabTestID`) REFERENCES `laboratory`(`Lid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
