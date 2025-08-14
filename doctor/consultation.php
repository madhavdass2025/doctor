<?php
// Assume a logged-in doctor
$doctor_id = 1;

// Include DB connection
require_once '../includes/db_connect.php';

// --- Data Fetching ---

// 1. Get and validate Booking ID
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if ($booking_id <= 0) {
    die("Invalid booking ID.");
}

// 2. Fetch Booking and Patient Details
$sql_patient = "SELECT r.*, cb.Status FROM registration r
                JOIN consultation_booking cb ON r.RegID = cb.RegID
                WHERE cb.BookingID = ? AND cb.DoctorID = ?";
$stmt_patient = $conn->prepare($sql_patient);
$stmt_patient->bind_param("ii", $booking_id, $doctor_id);
$stmt_patient->execute();
$patient_result = $stmt_patient->get_result();
$patient = $patient_result->fetch_assoc();
$stmt_patient->close();

if (!$patient) {
    die("Appointment not found or you do not have permission to view it.");
}

// 3. Fetch Master Data for Dropdowns
$medicines = $conn->query("SELECT Mid, name FROM medicines WHERE status = 'available' ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$vaccines = $conn->query("SELECT VId, name FROM vaccination ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$lab_tests = $conn->query("SELECT Lid, name FROM laboratory ORDER BY name")->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation for <?= htmlspecialchars($patient['petnam']) ?> - VetCare</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <h1>Consultation Workflow</h1>

    <!-- Patient Details Header -->
    <div class="patient-details">
        <h2>Pet: <?= htmlspecialchars($patient['petnam']) ?> (<?= htmlspecialchars($patient['Pettyp']) ?>)</h2>
        <p><strong>Reg No:</strong> <?= htmlspecialchars($patient['RegNo']) ?></p>
        <p><strong>Owner:</strong> <?= htmlspecialchars($patient['ownnam']) ?></p>
        <p><strong>Sex:</strong> <?= htmlspecialchars($patient['petsex']) ?> | <strong>Breed:</strong> <?= htmlspecialchars($patient['petbred']) ?></p>
    </div>

    <!-- Tab Navigation -->
    <div class="tabs">
        <button class="tab-link active" data-tab="#diagnosis">Diagnosis & Vitals</button>
        <button class="tab-link" data-tab="#medicine">Medicine</button>
        <button class="tab-link" data-tab="#procedures">Injections/Procedures</button>
        <button class="tab-link" data-tab="#lab-tests">Lab Tests</button>
        <button class="tab-link" data-tab="#preview">Preview & Finalize</button>
    </div>

    <form action="save_consultation.php" method="POST">
        <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
        <input type="hidden" name="reg_id" value="<?= $patient['RegID'] ?>">

        <!-- Tab Content -->
        <div id="diagnosis" class="tab-content active">
            <h3>Diagnosis & Vitals</h3>
            <button type="button" class="btn">View History</button>
            <div class="form-group">
                <label for="temperature">Temperature</label>
                <input type="text" id="temperature" name="temperature" class="form-control">
            </div>
            <div class="form-group">
                <label>Weight</label>
                <input type="number" name="weight_kg" placeholder="kg" class="form-control" style="width: 48%; display: inline-block;">
                <input type="number" name="weight_g" placeholder="g" class="form-control" style="width: 48%; display: inline-block;">
            </div>
            <div class="form-group">
                <label for="diagnosis_notes">Diagnosis Notes / Clinical Findings</label>
                <textarea id="diagnosis_notes" name="diagnosis_notes" class="form-control"></textarea>
            </div>
        </div>

        <div id="medicine" class="tab-content">
            <h3>Prescribe Medication</h3>
            <table class="dynamic-table" id="medicine-table">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Total Units</th>
                        <th>Time</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- JS will add rows here -->
                </tbody>
            </table>
            <button type="button" id="add-medicine-row" class="btn btn-primary" style="margin-top: 10px;">Add Medicine</button>
        </div>

        <div id="procedures" class="tab-content">
            <h3>Injections & Procedures</h3>
            <h4>Injections</h4>
            <table class="dynamic-table" id="injections-table">
                 <thead><tr><th>Injection Name</th><th>Dosage</th><th>Action</th></tr></thead>
                 <tbody></tbody>
            </table>
            <button type="button" id="add-injection-row" class="btn btn-primary" style="margin-top: 10px;">Add Injection</button>

            <h4 style="margin-top: 20px;">Surgery</h4>
             <div class="form-group"><label for="surgery_name">Procedure</label><input type="text" name="surgery_name" class="form-control"></div>
             <div class="form-group"><label for="surgery_notes">Notes</label><textarea name="surgery_notes" class="form-control"></textarea></div>

            <h4 style="margin-top: 20px;">Scanning</h4>
             <div class="form-group"><label for="scan_name">Scan</label><input type="text" name="scan_name" class="form-control"></div>
             <div class="form-group"><label for="scan_notes">Notes</label><textarea name="scan_notes" class="form-control"></textarea></div>
        </div>

        <div id="lab-tests" class="tab-content">
            <h3>Prescribe Lab Tests</h3>
             <table class="dynamic-table" id="lab-tests-table">
                 <thead><tr><th>Test Name</th><th>Instructions</th><th>Action</th></tr></thead>
                 <tbody></tbody>
            </table>
            <button type="button" id="add-lab-test-row" class="btn btn-primary" style="margin-top: 10px;">Add Lab Test</button>
        </div>

        <div id="preview" class="tab-content">
            <h3>Preview & Finalize</h3>
            <p>A summary of the consultation will be displayed here before saving.</p>
            <!-- Preview content will be populated by JS -->
            <div id="preview-content"></div>
            <hr>
            <button type="submit" name="save_finalize" class="btn btn-primary" style="font-size: 18px;">Save & Finalize</button>
        </div>
    </form>
</div>

<script src="../js/main.js"></script>
<script>
// Additional JS for this page to handle dynamic rows for other sections
document.addEventListener('DOMContentLoaded', function() {
    // --- Add Injection Row ---
    document.getElementById('add-injection-row').addEventListener('click', function() {
        const tbody = document.querySelector('#injections-table tbody');
        const newRow = tbody.insertRow();
        newRow.innerHTML = `
            <td>
                <select name="injection_id[]" class="form-control">
                    <option value="">Select Vaccine</option>
                    <?php foreach ($vaccines as $vac): ?>
                        <option value="<?= $vac['VId'] ?>"><?= htmlspecialchars($vac['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="injection_other_name[]" class="form-control" placeholder="Or type other injection">
            </td>
            <td><input type="text" name="injection_dosage[]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
        `;
    });

    // --- Add Lab Test Row ---
    document.getElementById('add-lab-test-row').addEventListener('click', function() {
        const tbody = document.querySelector('#lab-tests-table tbody');
        const newRow = tbody.insertRow();
        newRow.innerHTML = `
            <td>
                <select name="lab_test_id[]" class="form-control">
                    <option value="">Select Test</option>
                     <?php foreach ($lab_tests as $test): ?>
                        <option value="<?= $test['Lid'] ?>"><?= htmlspecialchars($test['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="lab_test_other_name[]" class="form-control" placeholder="Or type other test">
            </td>
            <td><textarea name="lab_test_instructions[]" class="form-control"></textarea></td>
            <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
        `;
    });

    // --- Override default add medicine to include dropdown ---
    const addMedicineBtn = document.getElementById('add-medicine-row');
    const medicineTableBody = document.querySelector('#medicine-table tbody');
    addMedicineBtn.addEventListener('click', function() {
        const newRow = medicineTableBody.insertRow();
        newRow.innerHTML = \`
            <td>
                <select name="medicine_id[]" class="form-control">
                    <option value="">Select Medicine</option>
                    <?php foreach ($medicines as $med): ?>
                        <option value="<?= $med['Mid'] ?>"><?= htmlspecialchars($med['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                 <input type="text" name="medicine_other_name[]" class="form-control" placeholder="Or type other medicine">
            </td>
            <td><input type="text" name="dosage[]" class="form-control"></td>
            <td>
                <select name="frequency[]" class="form-control">
                    <option>Once a day</option>
                    <option>Twice a day</option>
                    <option>Thrice a day</option>
                    <option>Every 6 hours</option>
                    <option>Every 8 hours</option>
                    <option>Every 12 hours</option>
                    <option>As needed</option>
                </select>
            </td>
            <td><input type="text" name="total_units[]" class="form-control"></td>
            <td>
                <select name="time[]" class="form-control">
                    <option>After Food</option>
                    <option>Before Food</option>
                    <option>With Food</option>
                </select>
            </td>
            <td>
                <select name="type[]" class="form-control">
                    <option>Tablet</option>
                    <option>Capsule</option>
                    <option>Syrup</option>
                    <option>Cream</option>
                    <option>Injection</option>
                    <option>Drops</option>
                </select>
            </td>
            <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
        \`;
    });
});
</script>

</body>
</html>
