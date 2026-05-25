<?php
require_once 'config.php';

$origin = $_GET['origin'] ?? '';
$destination = $_GET['destination'] ?? '';
$date = $_GET['date'] ?? '';

$query = "SELECT * FROM flights WHERE origin = :origin AND destination = :destination";
$params = [':origin' => $origin, ':destination' => $destination];

if (!empty($date)) {
    // Basic date matching on datetime field
    $query .= " AND date(departure_time) = :date";
    $params[':date'] = $date;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$flights = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - AeroBook</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">✈️ AeroBook</a>
        <div class="nav-links">
            <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?>
                    <a href="admin_dashboard.php" style="color: var(--primary-color);">Admin Panel</a>
                <?php endif; ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php" class="btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" class="btn-outline">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <main>
        <div class="glass-card large">
            <h2>Search Results</h2>
            <p class="text-center text-muted mb-4">
                Flights from <strong><?= htmlspecialchars($origin) ?></strong> to <strong><?= htmlspecialchars($destination) ?></strong>
                <?= !empty($date) ? " on " . htmlspecialchars($date) : "" ?>
            </p>

            <?php if (empty($flights)): ?>
                <div class="alert alert-error">No flights found matching your criteria. Try different dates or routes.</div>
                <div class="text-center mt-4"><a href="index.php" class="btn" style="width: auto;">Go Back</a></div>
            <?php else: ?>
                <div class="flight-list">
                    <?php foreach ($flights as $flight): ?>
                        <div class="flight-card">
                            <div class="flight-header">
                                <span class="flight-route"><?= htmlspecialchars($flight['flight_number']) ?></span>
                                <span class="flight-price">$<?= number_format($flight['price'], 2) ?></span>
                            </div>
                            <div class="flight-details">
                                <p><strong>Departure:</strong> <?= date('M d, Y h:i A', strtotime($flight['departure_time'])) ?></p>
                                <p><strong>Arrival:</strong> <?= date('M d, Y h:i A', strtotime($flight['arrival_time'])) ?></p>
                                <p><strong>Seats Available:</strong> <?= $flight['available_seats'] ?> / <?= $flight['total_seats'] ?></p>
                            </div>
                            <div class="mt-4">
                                <?php if ($flight['available_seats'] > 0): ?>
                                    <form action="book.php" method="POST">
                                        <input type="hidden" name="flight_id" value="<?= $flight['id'] ?>">
                                        <button type="submit" class="btn">Book Now</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-danger" disabled>Sold Out</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
