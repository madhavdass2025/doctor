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

// 3. Fetch data from related tables
$medicines = $conn->query("SELECT * FROM consultation_medicines WHERE ConsultationID = $consultation_id")->fetch_all(MYSQLI_ASSOC);
$injections = $conn->query("SELECT * FROM consultation_injections WHERE ConsultationID = $consultation_id")->fetch_all(MYSQLI_ASSOC);
$surgeries = $conn->query("SELECT * FROM consultation_surgeries WHERE ConsultationID = $consultation_id")->fetch_all(MYSQLI_ASSOC);
$scans = $conn->query("SELECT * FROM consultation_scans WHERE ConsultationID = $consultation_id")->fetch_all(MYSQLI_ASSOC);
$lab_tests = $conn->query("SELECT * FROM consultation_lab_tests WHERE ConsultationID = $consultation_id")->fetch_all(MYSQLI_ASSOC);

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
                    <td><?= htmlspecialchars($med['OtherMedicineName'] ?: 'N/A') ?></td>
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
                    <td><?= htmlspecialchars($inj['InjectionName']) ?></td>
                    <td><?= htmlspecialchars($inj['Dosage']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if (!empty($surgeries)): ?>
        <h3>Surgery/Procedures</h3>
        <?php foreach($surgeries as $surg): ?>
            <p><strong>Procedure:</strong> <?= htmlspecialchars($surg['SurgeryName']) ?></p>
            <div><strong>Notes:</strong> <p><?= nl2br(htmlspecialchars($surg['SurgeryNotes'])) ?></p></div>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($scans)): ?>
        <h3>Scans</h3>
        <?php foreach($scans as $scan): ?>
            <p><strong>Scan:</strong> <?= htmlspecialchars($scan['ScanName']) ?></p>
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
                    <td><?= htmlspecialchars($test['CustomTestName'] ?: 'N/A') ?></td>
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
