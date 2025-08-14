<?php
require_once '../includes/db_connect.php';

// 1. Get and validate Reg ID
$reg_id = isset($_GET['reg_id']) ? (int)$_GET['reg_id'] : 0;
if ($reg_id <= 0) {
    die("Invalid patient ID.");
}

// 2. Fetch Patient Details
$stmt_patient = $conn->prepare("SELECT * FROM registration WHERE RegID = ?");
$stmt_patient->bind_param("i", $reg_id);
$stmt_patient->execute();
$patient_result = $stmt_patient->get_result();
$patient = $patient_result->fetch_assoc();
$stmt_patient->close();

if (!$patient) {
    die("Patient not found.");
}

// 3. Fetch Consultation History
$sql_history = "SELECT c.ConsultationID, c.ConsultationDate, c.DiagnosisNotes
                FROM consultations c
                WHERE c.RegID = ?
                ORDER BY c.ConsultationDate DESC";
$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param("i", $reg_id);
$stmt_history->execute();
$history_result = $stmt_history->get_result();
$history = $history_result->fetch_all(MYSQLI_ASSOC);
$stmt_history->close();

$latest_consultation_id = !empty($history) ? $history[0]['ConsultationID'] : null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile for <?= htmlspecialchars($patient['petnam']) ?> - VetCare</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <a href="index.php" class="btn" style="margin-bottom: 20px;">&larr; Back to Dashboard</a>

    <!-- Patient Details Header -->
    <div class="patient-details">
        <h2>Pet: <?= htmlspecialchars($patient['petnam']) ?> (<?= htmlspecialchars($patient['Pettyp']) ?>)</h2>
        <p><strong>Reg No:</strong> <?= htmlspecialchars($patient['RegNo']) ?></p>
        <p><strong>Owner:</strong> <?= htmlspecialchars($patient['ownnam']) ?> | <strong>Mobile:</strong> <?= htmlspecialchars($patient['ownmob']) ?></p>
        <p><strong>Sex:</strong> <?= htmlspecialchars($patient['petsex']) ?> | <strong>Breed:</strong> <?= htmlspecialchars($patient['petbred']) ?></p>
        <p><strong>Age:</strong> <?= htmlspecialchars($patient['year']) ?> years, <?= htmlspecialchars($patient['month']) ?> months</p>
    </div>

    <!-- Print Action Buttons -->
    <div class="print-actions" style="margin-bottom: 30px;">
        <?php if ($latest_consultation_id): ?>
            <a href="print_case_sheet.php?consultation_id=<?= $latest_consultation_id ?>" target="_blank" class="btn btn-primary">Print Current Case Sheet</a>
        <?php endif; ?>
        <a href="print_full_history.php?reg_id=<?= $reg_id ?>" target="_blank" class="btn btn-primary">Print Full History</a>
    </div>

    <!-- Consultation History -->
    <h2>Consultation History</h2>
    <table class="appointments-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Diagnosis Notes (Summary)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($history) > 0): ?>
                <?php foreach ($history as $visit): ?>
                    <tr>
                        <td><?= date('d-M-Y H:i', strtotime($visit['ConsultationDate'])) ?></td>
                        <td><?= nl2br(htmlspecialchars(substr($visit['DiagnosisNotes'], 0, 150))) ?>...</td>
                        <td>
                            <a href="print_case_sheet.php?consultation_id=<?= $visit['ConsultationID'] ?>" target="_blank" class="btn">View/Print Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align:center;">No consultation history found for this patient.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

</body>
</html>
