-- --------------------------------------------------------
-- VETCARE DATABASE SCHEMA UPDATE
-- --------------------------------------------------------
--
-- This script updates the database schema to support dropdowns for
-- surgeries and scans in the consultation workflow.
--
-- Run these commands on your existing 'vetcare' database to fix the
-- "Unknown column" error without losing data.
-- --------------------------------------------------------

-- 1. Update the `consultation_surgeries` table
ALTER TABLE `consultation_surgeries`
  ADD COLUMN `SurgeryID` INT DEFAULT NULL AFTER `ConsultationID`,
  MODIFY COLUMN `SurgeryName` VARCHAR(255) DEFAULT NULL,
  ADD CONSTRAINT `fk_consultation_surgery` FOREIGN KEY (`SurgeryID`) REFERENCES `surgery`(`surgeryID`);

-- 2. Update the `consultation_scans` table
ALTER TABLE `consultation_scans`
  ADD COLUMN `ScanID` INT DEFAULT NULL AFTER `ConsultationID`,
  MODIFY COLUMN `ScanName` VARCHAR(255) DEFAULT NULL,
  ADD CONSTRAINT `fk_consultation_scan` FOREIGN KEY (`ScanID`) REFERENCES `scan`(`sID`);
