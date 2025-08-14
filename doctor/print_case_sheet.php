<?php
require_once '../includes/db_connect.php';

// 1. Get and validate Consultation ID
$consultation_id = isset($_GET['consultation_id']) ? (int)$_GET['consultation_id'] : 0;
if ($consultation_id <= 0) die("Invalid consultation ID.");

// --- Fetch all data for this consultation ---

// 2. Fetch main consultation and patient info
$sql_main = "SELECT c.*, r.* FROM consultations c
             JOIN registration r ON c.RegID = r.RegID
             WHERE c.ConsultationID = ?";
$stmt_main = $conn->prepare($sql_main);
$stmt_main->bind_param("i", $consultation_id);
$stmt_main->execute();
$main_info = $stmt_main->get_result()->fetch_assoc();
$stmt_main->close();
if (!$main_info) die("Consultation not found.");

// 3. Fetch data from related tables with JOINs
// Medicines
$sql_meds = "SELECT cm.*, m.name AS medicine_name
             FROM consultation_medicines cm
             LEFT JOIN medicines m ON cm.MedicineID = m.Mid
             WHERE cm.ConsultationID = ?";
$stmt_meds = $conn->prepare($sql_meds);
$stmt_meds->bind_param("i", $consultation_id);
$stmt_meds->execute();
$medicines = $stmt_meds->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_meds->close();

// Injections
$sql_injs = "SELECT ci.*, v.name AS vaccine_name
             FROM consultation_injections ci
             LEFT JOIN vaccination v ON ci.VaccinationID = v.VId
             WHERE ci.ConsultationID = ?";
$stmt_injs = $conn->prepare($sql_injs);
$stmt_injs->bind_param("i", $consultation_id);
$stmt_injs->execute();
$injections = $stmt_injs->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_injs->close();

// Surgeries
$sql_surgs = "SELECT cs.*, s.surName AS surgery_name
              FROM consultation_surgeries cs
              LEFT JOIN surgery s ON cs.SurgeryID = s.surgeryID
              WHERE cs.ConsultationID = ?";
$stmt_surgs = $conn->prepare($sql_surgs);
$stmt_surgs->bind_param("i", $consultation_id);
$stmt_surgs->execute();
$surgeries = $stmt_surgs->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_surgs->close();

// Scans
$sql_scans = "SELECT csc.*, s.scanName AS scan_name
              FROM consultation_scans csc
              LEFT JOIN scan s ON csc.ScanID = s.sID
              WHERE csc.ConsultationID = ?";
$stmt_scans = $conn->prepare($sql_scans);
$stmt_scans->bind_param("i", $consultation_id);
$stmt_scans->execute();
$scans = $stmt_scans->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_scans->close();

// Lab Tests
$sql_labs = "SELECT clt.*, l.name AS test_name
             FROM consultation_lab_tests clt
             LEFT JOIN laboratory l ON clt.LabTestID = l.Lid
             WHERE clt.ConsultationID = ?";
$stmt_labs = $conn->prepare($sql_labs);
$stmt_labs->bind_param("i", $consultation_id);
$stmt_labs->execute();
$lab_tests = $stmt_labs->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_labs->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Case Sheet - <?= htmlspecialchars($main_info['petnam']) ?> - <?= date('d-M-Y', strtotime($main_info['ConsultationDate'])) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header, .footer { text-align: center; }
        .case-sheet { width: 100%; border-collapse: collapse; }
        .case-sheet th, .case-sheet td { border: 1px solid #000; padding: 8px; text-align: left; }
        .section-title { background-color: #f2f2f2; font-weight: bold; }
        h1, h2 { margin: 0; }
        table { width: 100%; margin-top: 15px; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background-color: #eee; }
        @media print {
            .no-print { display: none; }
            body { margin: 0.5cm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>VetCare Clinic</h1>
        <p>123 Animal Lane, Pet City</p>
        <h2>CASE SHEET</h2>
    </div>

    <table class="case-sheet" style="margin-top: 20px;">
        <tr>
            <th>Reg No:</th><td><?= htmlspecialchars($main_info['RegNo']) ?></td>
            <th>Date:</th><td><?= date('d-M-Y H:i', strtotime($main_info['ConsultationDate'])) ?></td>
        </tr>
        <tr>
            <th>Pet Name:</th><td><?= htmlspecialchars($main_info['petnam']) ?></td>
            <th>Species:</th><td><?= htmlspecialchars($main_info['petsp']) ?></td>
        </tr>
        <tr>
            <th>Owner:</th><td><?= htmlspecialchars($main_info['ownnam']) ?></td>
            <th>Mobile:</th><td><?= htmlspecialchars($main_info['ownmob']) ?></td>
        </tr>
    </table>

    <div class="content">
        <h3>Vitals & Diagnosis</h3>
        <p><strong>Temperature:</strong> <?= htmlspecialchars($main_info['Temperature']) ?></p>
        <p><strong>Weight:</strong> <?= htmlspecialchars($main_info['Weight']) ?> kg</p>
        <div><strong>Clinical Findings / Diagnosis Notes:</strong>
            <p><?= nl2br(htmlspecialchars($main_info['DiagnosisNotes'])) ?></p>
        </div>

        <?php if (!empty($medicines)): ?>
        <h3>Medication</h3>
        <table>
            <thead><tr><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Units</th><th>Time</th><th>Type</th></tr></thead>
            <tbody>
            <?php foreach($medicines as $med): ?>
                <tr>
                    <td><?= htmlspecialchars($med['OtherMedicineName'] ?: ($med['medicine_name'] ?? 'N/A')) ?></td>
                    <td><?= htmlspecialchars($med['Dosage']) ?></td>
                    <td><?= htmlspecialchars($med['Frequency']) ?></td>
                    <td><?= htmlspecialchars($med['TotalUnits']) ?></td>
                    <td><?= htmlspecialchars($med['Time']) ?></td>
                    <td><?= htmlspecialchars($med['Type']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if (!empty($injections)): ?>
        <h3>Injections</h3>
        <table>
            <thead><tr><th>Name</th><th>Dosage</th></tr></thead>
            <tbody>
            <?php foreach($injections as $inj): ?>
                <tr>
                    <td><?= htmlspecialchars($inj['InjectionName'] ?: ($inj['vaccine_name'] ?? 'N/A')) ?></td>
                    <td><?= htmlspecialchars($inj['Dosage']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if (!empty($surgeries)): ?>
        <h3>Surgery/Procedures</h3>
        <?php foreach($surgeries as $surg): ?>
            <p><strong>Procedure:</strong> <?= htmlspecialchars($surg['SurgeryName'] ?: ($surg['surgery_name'] ?? 'N/A')) ?></p>
            <div><strong>Notes:</strong> <p><?= nl2br(htmlspecialchars($surg['SurgeryNotes'])) ?></p></div>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($scans)): ?>
        <h3>Scans</h3>
        <?php foreach($scans as $scan): ?>
            <p><strong>Scan:</strong> <?= htmlspecialchars($scan['ScanName'] ?: ($scan['scan_name'] ?? 'N/A')) ?></p>
            <div><strong>Notes:</strong> <p><?= nl2br(htmlspecialchars($scan['ScanNotes'])) ?></p></div>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($lab_tests)): ?>
        <h3>Lab Tests Prescribed</h3>
        <table>
            <thead><tr><th>Test Name</th><th>Instructions</th></tr></thead>
            <tbody>
            <?php foreach($lab_tests as $test): ?>
                <tr>
                    <td><?= htmlspecialchars($test['CustomTestName'] ?: ($test['test_name'] ?? 'N/A')) ?></td>
                    <td><?= htmlspecialchars($test['Instructions']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

    </div>

    <div class="footer" style="margin-top: 50px;">
        <p>Thank you for choosing VetCare Clinic.</p>
    </div>

</body>
</html>
