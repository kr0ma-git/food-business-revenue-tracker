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

        <div class="add-user-form">
            <h3>Add New Transaction</h3>

            <?php if (isset($_GET['success']) && $_GET['success'] === "transactionAdded"): ?>
                <p class="success">Transaction Successfully Added!</p>
            <?php endif; ?>

            <form action="../includes/adminAddTransaction.inc.php" method="POST" class="forms">
                <div class="form-row">
                    <label for="user_id">Customer Name:</label>
                    <select name="user_id" id="user_id" required>
                        <option value="" disabled selected>Select Customer</option>
                        <?php 
                            $customers = getAllCustomers($conn);
                            foreach ($customers as $cust): ?>
                                <option value="<?= $cust['customer_id']; ?>">
                                    <?= htmlspecialchars($cust['full_name']); ?>
                                </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <label for="product_id">Product:</label>
                    <select name="product_id" id="product_id" required>
                        <option value="" disabled selected>Select Product</option>
                        <?php 
                            $products = getAllProducts($conn);
                            foreach ($products as $product): ?>
                                <option value="<?= $product['product_id']; ?>">
                                    <?= htmlspecialchars($product['product_name']); ?> (₱<?= number_format($product['price'], 2); ?>)
                                </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" placeholder="Quantity" min="1" required>
                </div>

                <div class="form-row">
                    <label for="payment_type">Payment Type:</label>
                    <select name="payment_type" id="payment_type" required>
                        <option value="" disabled selected>Select Payment Type</option>
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                    </select>
                </div>

                <div class="form-row">
                    <button type="submit" name="submit" class="small-btn">Add Transaction</button>
                </div>
            </form>
        </div>

        <br><br>

        <div class="add-user-form">
            <h3 style="margin-bottom: 30px;">Add New Customer</h3>
            <form action="../includes/cashierAddCustomer.inc.php" method="POST">
                <div class="form-row">
                    <label for="full_name">Full Name:</label>
                    <input type="text" name="full_name" id="full_name" required>
                </div>

                <div class="form-row">
                    <label for="contact_number">Contact Number:</label>
                    <input type="text" name="contact_number" id="contact_number" required>
                </div>

                <div class="form-row">
                    <label for="address">Address:</label>
                    <input type="text" name="address" id="address" required>
                </div>

                <div class="form-row">
                    <button type="submit" name="add_customer" class="small-btn" style="margin-bottom: 10px;">Add Customer</button>
                </div>
            </form>
        </div>

        <br><br><hr><br>

        <h4 style="margin-top: 20px; margin-bottom: 10px;">Search:</h4>
        <input type="text" id="search" placeholder="Search by Transaction ID..." autocomplete="off">
        <p id="no-transaction-results" style="display: none;">No transactions found matching your search.</p>
        <br><br>

        <h3 class="transaction-text">Transaction History</h3>
        <div class="transaction-table">
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Customer Name</th>
                        <th>Contact No.</th>
                        <th>Address</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Date Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <tr class="transaction-row" data-user-naming="<?= htmlspecialchars($tx['transaction_id']); ?>">
                            <td>#<?= $tx['transaction_id']; ?></td>
                            <td><?= htmlspecialchars($tx['full_name']); ?></td>
                            <td><?= htmlspecialchars($tx['contact_number']); ?></td>
                            <td><?= htmlspecialchars($tx['customer_address']); ?></td>
                            <td><?= htmlspecialchars($tx['product_name']); ?></td>
                            <td><?= $tx['quantity']; ?></td>
                            <td><?= number_format($tx['amount'], 2); ?></td>
                            <td><?= $tx['payment_type']; ?></td>
                            <td><?= date("M d, Y h:i A", strtotime($tx['created_at'])); ?></td>
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