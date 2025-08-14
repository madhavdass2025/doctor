--
-- Database: `vetcare`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--
CREATE TABLE `doctors` (
  `DoctorID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL, -- Hashed password
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`DoctorID`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--
CREATE TABLE `registration` (
  `RegID` int NOT NULL AUTO_INCREMENT,
  `custType` varchar(100) NOT NULL DEFAULT 'regular',
  `RegDt` varchar(100) NOT NULL,
  `RegNo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Pettyp` varchar(100) NOT NULL,
  `petnam` varchar(100) NOT NULL,
  `petclr` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `petsex` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `petbred` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `year` varchar(10) NOT NULL,
  `month` varchar(10) NOT NULL,
  `gram` varchar(10) NOT NULL,
  `kg` varchar(10) NOT NULL,
  `petsp` varchar(100) NOT NULL,
  `doctor` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownnam` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownadd1` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownadd2` varchar(100) DEFAULT NULL,
  `ownloc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownpin` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownmob` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownres` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ownemail` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Reports` tinyint(1) DEFAULT '0',
  `cancel` varchar(100) NOT NULL DEFAULT '0',
  `canceldoneby` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `dt` date DEFAULT NULL,
   PRIMARY KEY (`RegID`),
   UNIQUE KEY `RegNo` (`RegNo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------

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

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--
CREATE TABLE `consultations` (
  `ConsultationID` int NOT NULL AUTO_INCREMENT,
  `BookingID` int NOT NULL,
  `RegID` int NOT NULL,
  `Temperature` varchar(50) DEFAULT NULL,
  `Weight` decimal(10,3) DEFAULT NULL, -- Storing weight in a single unit e.g., kg
  `DiagnosisNotes` text,
  `ConsultationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ConsultationID`),
  FOREIGN KEY (`BookingID`) REFERENCES `consultation_booking`(`BookingID`),
  FOREIGN KEY (`RegID`) REFERENCES `registration`(`RegID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

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
  `hsn` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `taxable` varchar(10) NOT NULL DEFAULT 'yes',
  `Itax` varchar(10) NOT NULL,
  `cess` varchar(10) NOT NULL,
  `submittedby` varchar(100) NOT NULL,
  `submitteddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(100) NOT NULL DEFAULT 'available',
   PRIMARY KEY (`Mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consultation_medicines`
--
CREATE TABLE `consultation_medicines` (
  `CM_ID` int NOT NULL AUTO_INCREMENT,
  `ConsultationID` int NOT NULL,
  `MedicineID` int DEFAULT NULL, -- Can be NULL if OtherMedicineName is used
  `OtherMedicineName` varchar(255) DEFAULT NULL,
  `Dosage` varchar(100) DEFAULT NULL,
  `Frequency` varchar(100) DEFAULT NULL,
  `TotalUnits` varchar(100) DEFAULT NULL,
  `Time` varchar(100) DEFAULT NULL, -- e.g., 'Before Food', 'After Food'
  `Type` varchar(100) DEFAULT NULL, -- e.g., 'Tablet', 'Syrup'
  PRIMARY KEY (`CM_ID`),
  FOREIGN KEY (`ConsultationID`) REFERENCES `consultations`(`ConsultationID`),
  FOREIGN KEY (`MedicineID`) REFERENCES `medicines`(`Mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

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

-- --------------------------------------------------------

--
-- Table structure for table `consultation_injections`
--
CREATE TABLE `consultation_injections` (
  `CI_ID` int NOT NULL AUTO_INCREMENT,
  `ConsultationID` int NOT NULL,
  `VaccinationID` int DEFAULT NULL, -- For predefined vaccines
  `InjectionName` varchar(255) NOT NULL, -- For custom/other injections
  `Dosage` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CI_ID`),
  FOREIGN KEY (`ConsultationID`) REFERENCES `consultations`(`ConsultationID`),
  FOREIGN KEY (`VaccinationID`) REFERENCES `vaccination`(`VId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consultation_surgeries`
--
CREATE TABLE `consultation_surgeries` (
  `CS_ID` int NOT NULL AUTO_INCREMENT,
  `ConsultationID` int NOT NULL,
  `SurgeryName` varchar(255) NOT NULL,
  `SurgeryNotes` text,
  PRIMARY KEY (`CS_ID`),
  FOREIGN KEY (`ConsultationID`) REFERENCES `consultations`(`ConsultationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consultation_scans`
--
CREATE TABLE `consultation_scans` (
  `CSc_ID` int NOT NULL AUTO_INCREMENT,
  `ConsultationID` int NOT NULL,
  `ScanName` varchar(255) NOT NULL,
  `ScanNotes` text,
  PRIMARY KEY (`CSc_ID`),
  FOREIGN KEY (`ConsultationID`) REFERENCES `consultations`(`ConsultationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

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

-- --------------------------------------------------------

--
-- Table structure for table `consultation_lab_tests`
--
CREATE TABLE `consultation_lab_tests` (
  `CLT_ID` int NOT NULL AUTO_INCREMENT,
  `ConsultationID` int NOT NULL,
  `LabTestID` int DEFAULT NULL, -- FK to laboratory
  `CustomTestName` varchar(255) DEFAULT NULL,
  `Instructions` text,
  PRIMARY KEY (`CLT_ID`),
  FOREIGN KEY (`ConsultationID`) REFERENCES `consultations`(`ConsultationID`),
  FOREIGN KEY (`LabTestID`) REFERENCES `laboratory`(`Lid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
