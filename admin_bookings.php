<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

$stmt = $pdo->query("
    SELECT b.id, b.status, b.booking_date, 
           u.name as user_name, u.email,
           f.flight_number, f.origin, f.destination, f.departure_time
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN flights f ON b.flight_id = f.id
    ORDER BY b.booking_date DESC
");
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings - AeroBook Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-nav {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .admin-nav a {
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 6px;
            color: #fff;
            text-decoration: none;
        }
        .admin-nav a:hover, .admin-nav a.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">✈️ AeroBook Admin</a>
        <div class="nav-links">
            <a href="index.php" class="btn-outline">Main Site</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <main>
        <div class="glass-card large" style="max-width: 1200px;">
            <h2>All Bookings</h2>
            
            <div class="admin-nav">
                <a href="admin_dashboard.php">Overview</a>
                <a href="admin_flights.php">Manage Flights</a>
                <a href="admin_users.php">Manage Users</a>
                <a href="admin_bookings.php" class="active">View Bookings</a>
                <a href="admin_reports.php">Reports</a>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Passenger</th>
                            <th>Flight</th>
                            <th>Route</th>
                            <th>Departure</th>
                            <th>Status</th>
                            <th>Date Booked</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td>#<?= str_pad($b['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($b['user_name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($b['email']) ?></small>
                                </td>
                                <td><strong><?= htmlspecialchars($b['flight_number']) ?></strong></td>
                                <td><?= htmlspecialchars($b['origin']) ?> → <?= htmlspecialchars($b['destination']) ?></td>
                                <td><?= date('M d, H:i', strtotime($b['departure_time'])) ?></td>
                                <td>
                                    <span class="badge <?= $b['status'] === 'Booked' ? 'badge-success' : 'badge-danger' ?>">
                                        <?= htmlspecialchars($b['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($b['booking_date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
