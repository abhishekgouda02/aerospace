<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$booking_id = $_GET['booking_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$booking_id) {
    die("Invalid request.");
}

$stmt = $pdo->prepare("
    SELECT b.id as booking_id, b.status, b.booking_date, f.*, u.name as passenger_name, u.email
    FROM bookings b
    JOIN flights f ON b.flight_id = f.id
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->execute([$booking_id, $user_id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die("Ticket not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket - <?= htmlspecialchars($ticket['flight_number']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 2rem;
            color: #1f2937;
        }
        .ticket-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 2px solid #e5e7eb;
        }
        .ticket-header {
            background: #3b82f6;
            color: #fff;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ticket-header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        .ticket-body {
            padding: 2rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        .section-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 0.5rem;
            letter-spacing: 0.05em;
        }
        .data-value {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .route {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border: 1px dashed #d1d5db;
        }
        .route-point {
            text-align: center;
        }
        .route-point h2 {
            margin: 0;
            font-size: 1.8rem;
            color: #3b82f6;
        }
        .barcode {
            text-align: center;
            padding: 1rem;
            background: #f9fafb;
            border-left: 2px dashed #e5e7eb;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .print-btn {
            display: block;
            width: 200px;
            margin: 2rem auto;
            padding: 0.8rem;
            text-align: center;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }
        @media print {
            body { background: white; padding: 0; }
            .ticket-container { box-shadow: none; border: 1px solid #000; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1>✈️ AeroBook E-Ticket</h1>
            <div style="text-align: right;">
                <div>Booking Reference</div>
                <div style="font-weight: 700; font-size: 1.2rem;">#AB-<?= str_pad($ticket['booking_id'], 6, '0', STR_PAD_LEFT) ?></div>
            </div>
        </div>
        
        <div class="ticket-body">
            <div>
                <div class="route">
                    <div class="route-point">
                        <div class="section-title">Origin</div>
                        <h2><?= htmlspecialchars($ticket['origin']) ?></h2>
                        <div><?= date('M d, Y', strtotime($ticket['departure_time'])) ?></div>
                        <div style="font-weight: 600;"><?= date('h:i A', strtotime($ticket['departure_time'])) ?></div>
                    </div>
                    <div style="font-size: 2rem; color: #9ca3af;">✈️</div>
                    <div class="route-point">
                        <div class="section-title">Destination</div>
                        <h2><?= htmlspecialchars($ticket['destination']) ?></h2>
                        <div><?= date('M d, Y', strtotime($ticket['arrival_time'])) ?></div>
                        <div style="font-weight: 600;"><?= date('h:i A', strtotime($ticket['arrival_time'])) ?></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr;">
                    <div>
                        <div class="section-title">Passenger Name</div>
                        <div class="data-value"><?= htmlspecialchars($ticket['passenger_name']) ?></div>
                    </div>
                    <div>
                        <div class="section-title">Flight Number</div>
                        <div class="data-value"><?= htmlspecialchars($ticket['flight_number']) ?></div>
                    </div>
                    <div>
                        <div class="section-title">Status</div>
                        <div class="data-value" style="color: <?= $ticket['status'] === 'Booked' ? '#10b981' : '#ef4444' ?>">
                            <?= htmlspecialchars($ticket['status']) ?>
                        </div>
                    </div>
                    <div>
                        <div class="section-title">Booking Date</div>
                        <div class="data-value"><?= date('M d, Y', strtotime($ticket['booking_date'])) ?></div>
                    </div>
                </div>
            </div>

            <div class="barcode">
                <div class="section-title">Boarding Pass</div>
                <!-- Simulated Barcode -->
                <div style="font-family: 'Courier New', Courier, monospace; font-size: 1.5rem; letter-spacing: -2px; transform: scaleY(2); margin: 2rem 0; font-weight: bold;">
                    |||| || ||| |||| | | |||
                </div>
                <div><?= htmlspecialchars($ticket['flight_number']) ?>-<?= $ticket['booking_id'] ?></div>
                <div style="margin-top: 2rem; color: #6b7280; font-size: 0.8rem;">Please present this ticket at the check-in counter.</div>
            </div>
        </div>
    </div>

    <button onclick="window.print()" class="print-btn">Print / Save as PDF</button>
</body>
</html>
