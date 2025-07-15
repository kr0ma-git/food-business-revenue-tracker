<?php
    session_start();

    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }

    require_once '../includes/dbh.inc.php';
    require_once '../includes/functions.inc.php';

    $transactions = getAllTransactions($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edd's Ngohiong | Transactions</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Outfit:wght@100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Admin View</h2>
        </div>

        <nav class="sidebar-nav">
            <a href="adminDashboard.php" class="nav-link"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="adminTransactions.php" class="nav-link active"><i class="fas fa-file-invoice"></i> Transactions</a>
            <a href="adminReports.php" class="nav-link"><i class="fas fa-chart-pie"></i> Reports</a>
            <a href="adminManageUsers.php" class="nav-link"><i class="fas fa-users-cog"></i> Manage Users</a>
        </nav>

        <div class="sidebar-footer">
            <a href="../includes/logout.inc.php" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <section class="dashboard-wrapper">
        <div class="dashboard-header">
            <h2>Transaction History</h2>
            <p>All customer payments and orders</p>
        </div>

        <div class="user-table">
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Customer Name</th>
                        <th>Payment</th>
                        <th>Date Created</th>
                        <th>Date Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td>#<?= $tx['transaction_id']; ?></td>
                            <td><?= htmlspecialchars($tx['first_name'] . ' ' . $tx['last_name']); ?></td>
                            <td><?= number_format($tx['amount'], 2); ?></td>
                            <td><?= date("M d, Y h:i A", strtotime($tx['created_at'])); ?></td>
                            <td><?= $tx['updated_at'] ? date("M d, Y h:i A", strtotime($tx['udpated_at'])) : '-'; ?></td>
                            <td>
                                <form action="../includes/adminDeleteTransaction.inc.php" method="POST" style="display: inline">
                                    <input type="hidden" name="transaction_id" value="<?= $tx['transaction_id']; ?>">
                                    <button type="submit" name="submit" class="btn small-btn" style="background-color: #ef4444; color: white; border-radius: 10px;">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <br><hr><br><br>

        <div class="add-user-form">
            <h3>Add New Transaction</h3>
            <form action="../includes/adminAddTransaction.inc.php" method="POST" class="forms">
                <div class="form-row">
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
                    <button type="submit" name="submit" class="btn">Add Transactions</button>
                </div>
            </form>
        </div>
    </section>
</body>
</html>