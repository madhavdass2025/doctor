<?php
// For now, we'll assume a doctor is logged in with ID 1.
// In a real application, this would come from a session.
$doctor_id = 1;

// Include the database connection
require_once '../includes/db_connect.php';

// Handle search query
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Fetch today's appointments
$sql = "SELECT
            cb.BookingID,
            cb.Status,
            r.RegID,
            r.petnam,
            r.Pettyp,
            r.ownnam,
            r.RegNo
        FROM
            consultation_booking cb
        JOIN
            registration r ON cb.RegID = r.RegID
        WHERE
            cb.DoctorID = ?
            AND cb.BookingDate = CURDATE()";

if (!empty($search_query)) {
    // Add search condition to the query
    $sql .= " AND (r.petnam LIKE ? OR r.RegNo LIKE ?)";
}

$sql .= " ORDER BY cb.created_at ASC";

$stmt = $conn->prepare($sql);

if (!empty($search_query)) {
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("isss", $doctor_id, $search_param, $search_param);
} else {
    $stmt->bind_param("i", $doctor_id);
}

$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - VetCare</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <div class="container">
        <h1>Today's Consultations</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] == 'success' ? 'success' : 'danger' ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="search-bar">
            <form action="index.php" method="GET">
                <input type="text" name="search" placeholder="Search by Pet Name or ID..." value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <div class="appointments-list">
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Pet Name</th>
                        <th>Pet Type</th>
                        <th>Owner Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($appointments) > 0): ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?= htmlspecialchars($appointment['RegNo']) ?></td>
                                <td><a href="patient_profile.php?reg_id=<?= $appointment['RegID'] ?>"><?= htmlspecialchars($appointment['petnam']) ?></a></td>
                                <td><?= htmlspecialchars($appointment['Pettyp']) ?></td>
                                <td><?= htmlspecialchars($appointment['ownnam']) ?></td>
                                <td>
                                    <span class="status <?= $appointment['Status'] == 'checked' ? 'status-checked' : 'status-not-checked' ?>">
                                        <?= htmlspecialchars(ucfirst($appointment['Status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($appointment['Status'] == 'not checked'): ?>
                                        <a href="consultation.php?booking_id=<?= $appointment['BookingID'] ?>" class="btn btn-primary">Start Consultation</a>
                                    <?php else: ?>
                                        <span>Completed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="no-appointments">
                                    No appointments scheduled for today.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
