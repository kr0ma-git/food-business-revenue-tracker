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
    $transactions = getAllTransactions($conn);
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
        <div class="dashboard-top">
            <div class="dashboard-welcome">
                <h2>Welcome, <?= htmlspecialchars($cashierName); ?></h2>
                <p class="subtitle">Monitor today's performance and manage transactions</p>
            </div>
            <a href="../includes/logout.inc.php" class="logout-btn">Logout</a>
        </div>

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
            <?php if (isset($_GET['success'])) {
                    if ($_GET['success'] == "transactionAdded") {
                        echo "<p style='color: greed;'>Transaction Succesfully Added!</p><br>";
                    }
                }
             ?>
            <form action="../includes/cashierAddTransaction.inc.php" method="POST">
                <div class="form-row">
                    <div class="user-info">
                        <select name="user_id" required>
                            <option value="" disabled selected>Select User</option>
                            <?php 
                                $users = getAllUsers($conn);
                                foreach ($users as $user):
                                    if (!$user['is_disabled']):
                            ?>
                                <option value="<?= $user['user_id']; ?>">
                                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                </option>
                            <?php
                                endif;
                                endforeach;
                            ?>
                        </select>
                        <input type="number" step="0.01" name="amount" placeholder="Amount (Php)" required>
                    </div>
                    <button type="submit" name="submit" class="btn">Add Transactions</button>
                </div>
            </form>
        </div>


        <h4 style="margin-top: 20px; margin-bottom: 10px;">Search:</h4>
        <input type="text" id="search" placeholder="Search by Transaction ID..." autocomplete="off">
        <p id="no-transaction-results" style="display: none;">No transactions found matching your search.</p>
        <br><br>

        <h3 class="transaction-text">Transaction History</h3>
        <div class="transaction-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Name</th>
                        <th>Amount</th>
                        <th>Created</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <tr class="transaction-row" data-user-naming="<?= htmlspecialchars($tx['transaction_id']); ?>">
                            <td>#<?= $tx['transaction_id']; ?></td>
                            <td><?= htmlspecialchars($tx['first_name'] . ' ' . $tx['last_name']); ?></td>
                            <td><?= number_format($tx['amount'], 2); ?></td>
                            <td><?= date("M d, Y h:i A", strtotime($tx['created_at'])); ?></td>
                            <td><?= $tx['updated_at'] ? date("M d, Y h:i A", strtotime($tx['updated_at'])) : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search');
            const userRows = document.querySelectorAll('.transaction-row');
            const noResultsMessage = document.getElementById('no-transaction-results');

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                let visibleCount = 0;

                userRows.forEach(row => {
                    const name = row.getAttribute('data-user-naming');
                    
                    if (name.includes(searchTerm)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>