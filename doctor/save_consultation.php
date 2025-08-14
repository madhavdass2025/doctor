<?php
session_start();
require_once '../includes/db_connect.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_finalize'])) {

    // --- Begin Transaction ---
    $conn->begin_transaction();

    try {
        // --- 1. Get Core IDs ---
        $booking_id = (int)$_POST['booking_id'];
        $reg_id = (int)$_POST['reg_id'];

        // --- 2. Process and Insert into `consultations` table ---
        $temperature = $_POST['temperature'];
        $diagnosis_notes = $_POST['diagnosis_notes'];

        // Combine weight from kg and g into a single decimal value in kg
        $weight_kg = !empty($_POST['weight_kg']) ? (float)$_POST['weight_kg'] : 0;
        $weight_g = !empty($_POST['weight_g']) ? (float)$_POST['weight_g'] : 0;
        $total_weight = $weight_kg + ($weight_g / 1000);

        $sql_consultation = "INSERT INTO consultations (BookingID, RegID, Temperature, Weight, DiagnosisNotes, ConsultationDate) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt_consultation = $conn->prepare($sql_consultation);
        $stmt_consultation->bind_param("iisds", $booking_id, $reg_id, $temperature, $total_weight, $diagnosis_notes);
        $stmt_consultation->execute();
        $consultation_id = $conn->insert_id; // Get the ID of this consultation
        $stmt_consultation->close();

        // --- 3. Insert into `consultation_medicines` ---
        if (!empty($_POST['medicine_id'])) {
            $sql_med = "INSERT INTO consultation_medicines (ConsultationID, MedicineID, OtherMedicineName, Dosage, Frequency, TotalUnits, `Time`, `Type`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_med = $conn->prepare($sql_med);
            foreach ($_POST['medicine_id'] as $key => $med_id) {
                // Only insert if a medicine was selected or a name was typed
                $other_med_name = $_POST['medicine_other_name'][$key];
                if (!empty($med_id) || !empty($other_med_name)) {
                    $med_id_val = !empty($med_id) ? (int)$med_id : null;
                    $stmt_med->bind_param("iissssss", $consultation_id, $med_id_val, $other_med_name, $_POST['dosage'][$key], $_POST['frequency'][$key], $_POST['total_units'][$key], $_POST['time'][$key], $_POST['type'][$key]);
                    $stmt_med->execute();
                }
            }
            $stmt_med->close();
        }

        // --- 4. Insert into `consultation_injections` ---
        if (!empty($_POST['injection_id'])) {
            $sql_inj = "INSERT INTO consultation_injections (ConsultationID, VaccinationID, InjectionName, Dosage) VALUES (?, ?, ?, ?)";
            $stmt_inj = $conn->prepare($sql_inj);
            foreach ($_POST['injection_id'] as $key => $inj_id) {
                $other_inj_name = $_POST['injection_other_name'][$key];
                if (!empty($inj_id) || !empty($other_inj_name)) {
                    $inj_id_val = !empty($inj_id) ? (int)$inj_id : null;
                    $stmt_inj->bind_param("iiss", $consultation_id, $inj_id_val, $other_inj_name, $_POST['injection_dosage'][$key]);
                    $stmt_inj->execute();
                }
            }
            $stmt_inj->close();
        }

        // --- 5. Insert into `consultation_surgeries` ---
        if (!empty($_POST['surgery_name'])) {
            $sql_surg = "INSERT INTO consultation_surgeries (ConsultationID, SurgeryName, SurgeryNotes) VALUES (?, ?, ?)";
            $stmt_surg = $conn->prepare($sql_surg);
            $stmt_surg->bind_param("iss", $consultation_id, $_POST['surgery_name'], $_POST['surgery_notes']);
            $stmt_surg->execute();
            $stmt_surg->close();
        }

        // --- 6. Insert into `consultation_scans` ---
        if (!empty($_POST['scan_name'])) {
            $sql_scan = "INSERT INTO consultation_scans (ConsultationID, ScanName, ScanNotes) VALUES (?, ?, ?)";
            $stmt_scan = $conn->prepare($sql_scan);
            $stmt_scan->bind_param("iss", $consultation_id, $_POST['scan_name'], $_POST['scan_notes']);
            $stmt_scan->execute();
            $stmt_scan->close();
        }

        // --- 7. Insert into `consultation_lab_tests` ---
        if (!empty($_POST['lab_test_id'])) {
            $sql_lab = "INSERT INTO consultation_lab_tests (ConsultationID, LabTestID, CustomTestName, Instructions) VALUES (?, ?, ?, ?)";
            $stmt_lab = $conn->prepare($sql_lab);
            foreach ($_POST['lab_test_id'] as $key => $lab_id) {
                $other_lab_name = $_POST['lab_test_other_name'][$key];
                if (!empty($lab_id) || !empty($other_lab_name)) {
                     $lab_id_val = !empty($lab_id) ? (int)$lab_id : null;
                     $stmt_lab->bind_param("iiss", $consultation_id, $lab_id_val, $other_lab_name, $_POST['lab_test_instructions'][$key]);
                     $stmt_lab->execute();
                }
            }
            $stmt_lab->close();
        }

        // --- 8. Update Booking Status ---
        $sql_update_status = "UPDATE consultation_booking SET Status = 'checked' WHERE BookingID = ?";
        $stmt_update = $conn->prepare($sql_update_status);
        $stmt_update->bind_param("i", $booking_id);
        $stmt_update->execute();
        $stmt_update->close();

        // --- Commit Transaction ---
        $conn->commit();
        $_SESSION['message'] = "Consultation saved successfully!";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        // --- Rollback Transaction on Error ---
        $conn->rollback();
        $_SESSION['message'] = "Failed to save consultation. Error: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

    // --- Close connection and redirect ---
    $conn->close();
    header("Location: index.php");
    exit();

} else {
    // Redirect if not a POST request
    header("Location: index.php");
    exit();
}
?>
