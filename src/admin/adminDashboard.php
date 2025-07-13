<?php
    session_start();

    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }

    $adminName = $_SESSION['userName'] ?? "Admin";

    require_once '../includes/dbh.inc.php';
    require_once '../includes/functions.inc.php';

    $summary = getDailyRevenueSummary($conn); // Array of total and count of transactions.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edd's Ngohiong | Admin Dashboard</title>
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
            <a href="adminDashboard.php" class="nav-link active"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="adminTransactions.php" class="nav-link"><i class="fas fa-file-invoice"></i> Transactions</a>
            <a href="adminReports.php" class="nav-link"><i class="fas fa-chart-pie"></i> Reports</a>
            <a href="adminManageUsers.php" class="nav-link"><i class="fas fa-users-cog"></i> Manage Users</a>
        </nav>

        <div class="sidebar-footer">
            <a href="../includes/logout.inc.php" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <section class="dashboard-wrapper">
        <div class="dashboard-header">
            <h2>Welcome back, <?php echo htmlspecialchars($adminName); ?></h2>
            <p class="subtitle">Here’s an overview of today’s performance.</p>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <i class="fas fa-coins summary-icon"></i>
                <h3>Today's Revenue</h3>
                <p>Php <?php echo number_format($summary['total'], 2); ?></p>
            </div>
            <div class="summary-card">
                <i class="fas fa-receipt summary-icon"></i>
                <h3>Transactions Today</h3>
                <p><?php echo $summary['count']; ?></p>
            </div>
        </div>

        <div class="admin-actions">
            <h3 class="section-title">Quick Actions</h3>
            <div class="admin-links">
                <a href="adminTransactions.php" class="btn"><i class="fas fa-file-alt"></i> Transactions</a>
                <a href="adminReports.php" class="btn"><i class="fas fa-chart-line"></i> Reports</a>
                <a href="adminManageUsers.php" class="btn"><i class="fas fa-user-cog"></i> Manage Users</a>
            </div>
        </div>
    </section>
</body>
</html>