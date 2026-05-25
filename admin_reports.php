<?php
require_once 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('index.php');
}

// Generate a simple report: Revenue per flight
$stmt = $pdo->query("
    SELECT f.flight_number, f.origin, f.destination, f.price,
           COUNT(b.id) as tickets_sold,
           SUM(f.price) as total_revenue
    FROM flights f
    LEFT JOIN bookings b ON f.id = b.flight_id AND b.status = 'Booked'
    GROUP BY f.id
    ORDER BY total_revenue DESC
");
$reports = $stmt->fetchAll();

$total_system_revenue = 0;
foreach ($reports as $r) {
    $total_system_revenue += $r['total_revenue'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - AeroBook Admin</title>
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
        .summary-box {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 2rem;
        }
        .summary-box h3 { color: #6ee7b7; margin-bottom: 0.5rem; }
        .summary-box .amt { font-size: 2.5rem; font-weight: 700; color: #fff; }
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
        <div class="glass-card large" style="max-width: 1000px;">
            <h2>Financial Reports</h2>
            
            <div class="admin-nav">
                <a href="admin_dashboard.php">Overview</a>
                <a href="admin_flights.php">Manage Flights</a>
                <a href="admin_users.php">Manage Users</a>
                <a href="admin_bookings.php">View Bookings</a>
                <a href="admin_reports.php" class="active">Reports</a>
            </div>

            <div class="summary-box">
                <h3>Total System Revenue</h3>
                <div class="amt">$<?= number_format($total_system_revenue, 2) ?></div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Flight</th>
                            <th>Route</th>
                            <th>Ticket Price</th>
                            <th>Tickets Sold (Active)</th>
                            <th>Revenue Generated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $r): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($r['flight_number']) ?></strong></td>
                                <td><?= htmlspecialchars($r['origin']) ?> → <?= htmlspecialchars($r['destination']) ?></td>
                                <td>$<?= number_format($r['price'], 2) ?></td>
                                <td><?= $r['tickets_sold'] ?></td>
                                <td style="color: #6ee7b7; font-weight: 600;">$<?= number_format($r['total_revenue'] ?? 0, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-4">
                <button onclick="window.print()" class="btn" style="width: auto;">Print Report</button>
            </div>
        </div>
    </main>
</body>
</html>
