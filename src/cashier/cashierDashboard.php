<?php
    session_start();

    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'cashier') {
        header("Location: ../index.php");
        exit();
    }

    require_once '../includes/dbh.inc.php';
    require_once '../includes/functions.inc.php';

    $cashierName = $_SESSION['userName'] ?? 'Cashier';
    $userID = $_SESSION['userID'];
    $summary = getDailyRevenueSummary($conn);
    $transactions = getTransactionsByUser($conn, $userID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edd's Ngohiong | Cashier Dashboard</title>
    <link rel="stylesheet" href="cashier.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Outfit:wght@100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <section class="dashboard-wrapper">
        <h2>Welcome, <?= htmlspecialchars($cashierName); ?></h2>
        <p class="subtitle">Monitor today's performance and manage transactions</p>

        <div class="summary-grid">
            <div class="summary-card">
                <h3>Today's Revenue</h3>
                <p>Php <?= number_format($summary['total'], 2); ?></p>
            </div>
            <div class="summary-card">
                <h3>Today's Transactions</h3>
                <p><?= $summary['count']; ?></p>
            </div>
        </div>

        <div class="add-transaction-form">
            <h3>Add Transactions</h3>
            <form action="../includes/cashierAddTransaction.inc.php" method="POST">
                <input type="hidden" name="user_id" value="<?= $userID; ?>">
                <input type="number" step="0.01" name="amount" placeholder="Amount (Php)" required>
                <button type="submit" name="submit">Add Transaction</button>
            </form>
        </div>

        <div class="transaction-table">
            <h3>Transaction History</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Amount</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td>#<?= $tx['transaction_id']; ?></td>
                            <td><?= number_format($tx['amount'], 2); ?></td>
                            <td><?= date("M d, Y h:i A", strtotime($tx['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>