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

    $med_results = $conn->query("SELECT * FROM consultation_medicines WHERE ConsultationID IN ($ids_str)");
    while ($row = $med_results->fetch_assoc()) $all_meds[$row['ConsultationID']][] = $row;

    $inj_results = $conn->query("SELECT * FROM consultation_injections WHERE ConsultationID IN ($ids_str)");
    while ($row = $inj_results->fetch_assoc()) $all_injections[$row['ConsultationID']][] = $row;

    $surg_results = $conn->query("SELECT * FROM consultation_surgeries WHERE ConsultationID IN ($ids_str)");
    while ($row = $surg_results->fetch_assoc()) $all_surgeries[$row['ConsultationID']][] = $row;

    $scan_results = $conn->query("SELECT * FROM consultation_scans WHERE ConsultationID IN ($ids_str)");
    while ($row = $scan_results->fetch_assoc()) $all_scans[$row['ConsultationID']][] = $row;

    $lab_results = $conn->query("SELECT * FROM consultation_lab_tests WHERE ConsultationID IN ($ids_str)");
    while ($row = $lab_results->fetch_assoc()) $all_lab_tests[$row['ConsultationID']][] = $row;
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
        .visit-block { border: 1px solid #000; padding: 15px; margin-bottom: 20px; }
        h1, h2, h3 { margin-top: 0; }
        table { width: 100%; margin-top: 10px; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #eee; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>VetCare Clinic</h1>
        <p>123 Animal Lane, Pet City</p>
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

                <p><strong>Temperature:</strong> <?= htmlspecialchars($consult['Temperature']) ?> | <strong>Weight:</strong> <?= htmlspecialchars($consult['Weight']) ?> kg</p>
                <div><strong>Diagnosis:</strong>
                    <p><?= nl2br(htmlspecialchars($consult['DiagnosisNotes'])) ?></p>
                </div>

                <?php $cid = $consult['ConsultationID']; ?>

                <?php if (!empty($all_meds[$cid])): ?>
                <h4>Medication</h4>
                <table><thead><tr><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Units</th></tr></thead><tbody>
                <?php foreach($all_meds[$cid] as $med): ?><tr><td><?= htmlspecialchars($med['OtherMedicineName'] ?: 'N/A') ?></td><td><?= htmlspecialchars($med['Dosage']) ?></td><td><?= htmlspecialchars($med['Frequency']) ?></td><td><?= htmlspecialchars($med['TotalUnits']) ?></td></tr><?php endforeach; ?>
                </tbody></table>
                <?php endif; ?>

                <?php if (!empty($all_injections[$cid])): ?>
                <h4>Injections</h4>
                <table><thead><tr><th>Name</th><th>Dosage</th></tr></thead><tbody>
                <?php foreach($all_injections[$cid] as $inj): ?><tr><td><?= htmlspecialchars($inj['InjectionName']) ?></td><td><?= htmlspecialchars($inj['Dosage']) ?></td></tr><?php endforeach; ?>
                </tbody></table>
                <?php endif; ?>

                <?php if (!empty($all_surgeries[$cid])): ?>
                <h4>Surgery</h4>
                <?php foreach($all_surgeries[$cid] as $s): ?>
                    <p><strong>Procedure:</strong> <?= htmlspecialchars($s['SurgeryName']) ?><br><strong>Notes:</strong> <?= nl2br(htmlspecialchars($s['SurgeryNotes'])) ?></p>
                <?php endforeach; ?>
                <?php endif; ?>

            </div>
            <?php if ($index < count($consultations) - 1): ?>
                <div class="page-break"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
