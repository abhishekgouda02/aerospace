<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$id = $_POST['id'] ?? $_GET['id'] ?? null;
$flight = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM flights WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['flash_success'] = "Flight deleted successfully.";
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Cannot delete flight: It may have bookings attached.";
        }
        redirect('admin_flights.php');
    }

    if ($action === 'save') {
        $flight_number = $_POST['flight_number'];
        $origin = $_POST['origin'];
        $destination = $_POST['destination'];
        $departure_time = $_POST['departure_time'];
        $arrival_time = $_POST['arrival_time'];
        $price = $_POST['price'];
        $total_seats = $_POST['total_seats'];

        try {
            if ($id) {
                // Update
                $stmt = $pdo->prepare("UPDATE flights SET flight_number=?, origin=?, destination=?, departure_time=?, arrival_time=?, price=?, total_seats=? WHERE id=?");
                $stmt->execute([$flight_number, $origin, $destination, $departure_time, $arrival_time, $price, $total_seats, $id]);
                
                // We should also adjust available seats ideally, but keeping it simple for now.
                $_SESSION['flash_success'] = "Flight updated successfully.";
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO flights (flight_number, origin, destination, departure_time, arrival_time, price, total_seats, available_seats) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$flight_number, $origin, $destination, $departure_time, $arrival_time, $price, $total_seats, $total_seats]);
                $_SESSION['flash_success'] = "Flight added successfully.";
            }
            redirect('admin_flights.php');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Error saving flight: " . $e->getMessage();
        }
    }
} else if ($id) {
    // Edit mode: fetch flight
    $stmt = $pdo->prepare("SELECT * FROM flights WHERE id = ?");
    $stmt->execute([$id]);
    $flight = $stmt->fetch();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $flight ? 'Edit' : 'Add' ?> Flight - AeroBook Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
    </style>
</head>
<body>
    <main>
        <div class="glass-card large">
            <h2><?= $flight ? 'Edit' : 'Add New' ?> Flight</h2>
            
            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="alert alert-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <form method="POST" action="admin_flight_action.php">
                <input type="hidden" name="action" value="save">
                <?php if ($flight): ?>
                    <input type="hidden" name="id" value="<?= $flight['id'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Flight Number</label>
                    <input type="text" name="flight_number" class="form-control" required value="<?= htmlspecialchars($flight['flight_number'] ?? '') ?>">
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Origin</label>
                        <input type="text" name="origin" class="form-control" required value="<?= htmlspecialchars($flight['origin'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Destination</label>
                        <input type="text" name="destination" class="form-control" required value="<?= htmlspecialchars($flight['destination'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Departure Time</label>
                        <input type="datetime-local" name="departure_time" class="form-control" required value="<?= $flight ? date('Y-m-d\TH:i', strtotime($flight['departure_time'])) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Arrival Time</label>
                        <input type="datetime-local" name="arrival_time" class="form-control" required value="<?= $flight ? date('Y-m-d\TH:i', strtotime($flight['arrival_time'])) : '' ?>">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Price ($)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required value="<?= $flight['price'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Total Seats</label>
                        <input type="number" name="total_seats" class="form-control" required value="<?= $flight['total_seats'] ?? '' ?>">
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="submit" class="btn">Save Flight</button>
                    <a href="admin_flights.php" class="btn btn-outline" style="text-align: center;">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
