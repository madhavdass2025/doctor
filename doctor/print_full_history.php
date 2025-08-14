<?php
require_once '../includes/db_connect.php';

// 1. Get and validate Reg ID
$reg_id = isset($_GET['reg_id']) ? (int)$_GET['reg_id'] : 0;
if ($reg_id <= 0) die("Invalid patient ID.");

// --- Fetch all data for this patient ---

// 2. Fetch patient info
$stmt_patient = $conn->prepare("SELECT * FROM registration WHERE RegID = ?");
$stmt_patient->bind_param("i", $reg_id);
$stmt_patient->execute();
$patient = $stmt_patient->get_result()->fetch_assoc();
$stmt_patient->close();
if (!$patient) die("Patient not found.");

// 3. Fetch all consultations
$consultations = $conn->query("SELECT * FROM consultations WHERE RegID = $reg_id ORDER BY ConsultationDate DESC")->fetch_all(MYSQLI_ASSOC);

// 4. Fetch all related data in bulk and organize by ConsultationID
$all_meds = [];
$all_injections = [];
$all_surgeries = [];
$all_scans = [];
$all_lab_tests = [];

if (!empty($consultations)) {
    $consultation_ids = array_column($consultations, 'ConsultationID');
    $ids_str = implode(',', $consultation_ids);

    // Fetch Medicines with names
    $med_results = $conn->query("SELECT cm.*, m.name AS medicine_name FROM consultation_medicines cm LEFT JOIN medicines m ON cm.MedicineID = m.Mid WHERE cm.ConsultationID IN ($ids_str)");
    while ($row = $med_results->fetch_assoc()) { $all_meds[$row['ConsultationID']][] = $row; }

    // Fetch Injections with names
    $inj_results = $conn->query("SELECT ci.*, v.name AS vaccine_name FROM consultation_injections ci LEFT JOIN vaccination v ON ci.VaccinationID = v.VId WHERE ci.ConsultationID IN ($ids_str)");
    while ($row = $inj_results->fetch_assoc()) { $all_injections[$row['ConsultationID']][] = $row; }

    // Fetch Surgeries with names
    $surg_results = $conn->query("SELECT cs.*, s.surName AS surgery_name FROM consultation_surgeries cs LEFT JOIN surgery s ON cs.SurgeryID = s.surgeryID WHERE cs.ConsultationID IN ($ids_str)");
    while ($row = $surg_results->fetch_assoc()) { $all_surgeries[$row['ConsultationID']][] = $row; }

    // Fetch Scans with names
    $scan_results = $conn->query("SELECT csc.*, s.scanName AS scan_name FROM consultation_scans csc LEFT JOIN scan s ON csc.ScanID = s.sID WHERE csc.ConsultationID IN ($ids_str)");
    while ($row = $scan_results->fetch_assoc()) { $all_scans[$row['ConsultationID']][] = $row; }

    // Fetch Lab Tests with names
    $lab_results = $conn->query("SELECT clt.*, l.name AS test_name FROM consultation_lab_tests clt LEFT JOIN laboratory l ON clt.LabTestID = l.Lid WHERE clt.ConsultationID IN ($ids_str)");
    while ($row = $lab_results->fetch_assoc()) { $all_lab_tests[$row['ConsultationID']][] = $row; }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Full Medical History - <?= htmlspecialchars($patient['petnam']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header, .footer { text-align: center; }
        .page-break { page-break-after: always; }
        .visit-block { border: 1px solid #000; padding: 15px; margin-bottom: 20px; border-radius: 8px;}
        h1, h2, h3, h4 { margin-top: 0; color: #333; }
        h2 { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px;}
        h3 { border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 10px; }
        table { width: 100%; margin-top: 10px; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #eee; }
        @media print { .no-print { display: none; } body { margin: 0.5cm; } }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>VetCare Clinic</h1>
        <h2>FULL MEDICAL HISTORY</h2>
    </div>

    <table style="margin-top: 20px; margin-bottom: 30px; border: 1px solid #000;">
        <tr>
            <th>Reg No:</th><td><?= htmlspecialchars($patient['RegNo']) ?></td>
            <th>Pet Name:</th><td><?= htmlspecialchars($patient['petnam']) ?></td>
        </tr>
        <tr>
            <th>Owner:</th><td><?= htmlspecialchars($patient['ownnam']) ?></td>
            <th>Mobile:</th><td><?= htmlspecialchars($patient['ownmob']) ?></td>
        </tr>
    </table>

    <?php if (empty($consultations)): ?>
        <p style="text-align:center;">No history found.</p>
    <?php else: ?>
        <?php foreach ($consultations as $index => $consult): ?>
            <div class="visit-block">
                <h3>Visit on: <?= date('d-M-Y H:i', strtotime($consult['ConsultationDate'])) ?></h3>

                <h4>Vitals & Diagnosis</h4>
                <p><strong>Temperature:</strong> <?= htmlspecialchars($consult['Temperature']) ?> | <strong>Weight:</strong> <?= htmlspecialchars($consult['Weight']) ?> kg</p>
                <div><strong>Diagnosis:</strong>
                    <p><?= nl2br(htmlspecialchars($consult['DiagnosisNotes'])) ?></p>
                </div>

                <?php $cid = $consult['ConsultationID']; ?>

                <?php if (!empty($all_meds[$cid])): ?>
                <h4>Medication</h4>
                <table><thead><tr><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Units</th><th>Time</th><th>Type</th></tr></thead><tbody>
                <?php foreach($all_meds[$cid] as $med): ?><tr><td><?= htmlspecialchars($med['OtherMedicineName'] ?: ($med['medicine_name'] ?? 'N/A')) ?></td><td><?= htmlspecialchars($med['Dosage']) ?></td><td><?= htmlspecialchars($med['Frequency']) ?></td><td><?= htmlspecialchars($med['TotalUnits']) ?></td><td><?= htmlspecialchars($med['Time']) ?></td><td><?= htmlspecialchars($med['Type']) ?></td></tr><?php endforeach; ?>
                </tbody></table>
                <?php endif; ?>

                <?php if (!empty($all_injections[$cid])): ?>
                <h4>Injections</h4>
                <table><thead><tr><th>Name</th><th>Dosage</th></tr></thead><tbody>
                <?php foreach($all_injections[$cid] as $inj): ?><tr><td><?= htmlspecialchars($inj['InjectionName'] ?: ($inj['vaccine_name'] ?? 'N/A')) ?></td><td><?= htmlspecialchars($inj['Dosage']) ?></td></tr><?php endforeach; ?>
                </tbody></table>
                <?php endif; ?>

                <?php if (!empty($all_surgeries[$cid])): ?>
                <h4>Surgery</h4>
                <?php foreach($all_surgeries[$cid] as $s): ?>
                    <p><strong>Procedure:</strong> <?= htmlspecialchars($s['SurgeryName'] ?: ($s['surgery_name'] ?? 'N/A')) ?><br><strong>Notes:</strong> <?= nl2br(htmlspecialchars($s['SurgeryNotes'])) ?></p>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($all_scans[$cid])): ?>
                <h4>Scans</h4>
                <?php foreach($all_scans[$cid] as $s): ?>
                    <p><strong>Scan:</strong> <?= htmlspecialchars($s['ScanName'] ?: ($s['scan_name'] ?? 'N/A')) ?><br><strong>Notes:</strong> <?= nl2br(htmlspecialchars($s['ScanNotes'])) ?></p>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($all_lab_tests[$cid])): ?>
                <h4>Lab Tests Prescribed</h4>
                <table><thead><tr><th>Test Name</th><th>Instructions</th></tr></thead><tbody>
                <?php foreach($all_lab_tests[$cid] as $test): ?><tr><td><?= htmlspecialchars($test['CustomTestName'] ?: ($test['test_name'] ?? 'N/A')) ?></td><td><?= htmlspecialchars($test['Instructions']) ?></td></tr><?php endforeach; ?>
                </tbody></table>
                <?php endif; ?>

            </div>
            <?php if ($index < count($consultations) - 1): ?>
                <div class="page-break"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
