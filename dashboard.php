<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Check pending flight booking if redirecting from login
if (isset($_SESSION['pending_flight_id'])) {
    $pending = $_SESSION['pending_flight_id'];
    unset($_SESSION['pending_flight_id']);
    // Could auto-submit or prompt user, but let's just clear for now and let them book manually or provide a fast track.
}

// Fetch user's bookings with flight details
$stmt = $pdo->prepare("
    SELECT b.id as booking_id, b.status, b.booking_date, f.* 
    FROM bookings b
    JOIN flights f ON b.flight_id = f.id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AeroBook</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        function confirmCancel() {
            return confirm("Are you sure you want to cancel this ticket?");
        }
    </script>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">✈️ AeroBook</a>
        <div class="nav-links">
            <?php if (isAdmin()): ?>
                <a href="admin_dashboard.php" style="color: var(--primary-color);">Admin Panel</a>
            <?php endif; ?>
            <span style="color: var(--text-color); margin-right: 1rem;">Hello, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="logout.php" class="btn-outline">Logout</a>
        </div>
    </nav>

    <main>
        <div class="glass-card large" style="max-width: 1000px;">
            <h2>My Bookings</h2>
            
            <?php if ($flash_error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($flash_error) ?></div>
            <?php endif; ?>
            
            <?php if ($flash_success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div>
            <?php endif; ?>

            <?php if (empty($bookings)): ?>
                <p class="text-center text-muted">You have no bookings yet. <a href="index.php" style="color: var(--primary-color);">Search for flights</a>.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Flight</th>
                                <th>Route</th>
                                <th>Departure</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($booking['flight_number']) ?></strong></td>
                                    <td><?= htmlspecialchars($booking['origin']) ?> → <?= htmlspecialchars($booking['destination']) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($booking['departure_time'])) ?></td>
                                    <td>
                                        <span class="badge <?= $booking['status'] === 'Booked' ? 'badge-success' : 'badge-danger' ?>">
                                            <?= htmlspecialchars($booking['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($booking['status'] === 'Booked'): ?>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <a href="download.php?booking_id=<?= $booking['booking_id'] ?>" target="_blank" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; width: auto;">Download Ticket</a>
                                                <form action="cancel.php" method="POST" onsubmit="return confirmCancel();" style="display:inline;">
                                                    <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                    <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; width: auto;">Cancel</button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
