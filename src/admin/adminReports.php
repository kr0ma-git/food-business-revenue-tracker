<?php
    session_start();

    if(!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }

    require_once '../includes/dbh.inc.php';
    require_once '../includes/functions.inc.php';

    $daily = getDailyRevenueSummary($conn);
    $weekly = getWeeklyRevenueSummary($conn);
    $monthly = getMonthlyRevenueSummary($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edd's Ngohiong | Reports</title>
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
            <a href="adminTransactions.php" class="nav-link"><i class="fas fa-file-invoice"></i> Transactions</a>
            <a href="adminReports.php" class="nav-link active"><i class="fas fa-chart-pie"></i> Reports</a>
            <a href="adminManageUsers.php" class="nav-link"><i class="fas fa-users-cog"></i> Manage Users</a>
        </nav>

        <div class="sidebar-footer">
            <a href="../includes/logout.inc.php" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <section class="dashboard-wrapper">
        <div class="dashboard-header">
            <h2>Reports</h2>
            <p class="subtitle">Summary of income and transactions</p>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <i class="fas fa-calendar-day summary-icon"></i>
                <h3>Today</h3>
                <p>Php <?= number_format($daily['total'], 2); ?> <br><span style="font-size: 1rem;">(<?= $weekly['count']; ?> transactions)</span></p>
            </div>
            <div class="summary-card">
                <i class="fas fa-calendar-week summary-icon"></i>
                <h3>This Week</h3>
                <p>Php <?= number_format($weekly['total'], 2); ?> <br><span style="font-size: 1rem;">(<?= $weekly['count']; ?> transactions)</span></p>
            </div>
            <div class="summary-card">
                <i class="fas fa-calendar summary-icon"></i>
                <h3>This Month</h3>
                <p>Php <?= number_format($monthly['total'], 2); ?> <br><span style="font-size: 1rem;">(<?= $monthly['count']; ?> transactions)</span></p>
            </div>
        </div>

        <hr style="margin: 2rem 0;">

        <div style="margin-top: 2rem;">
            <h3 style="margin-bottom: 1rem;">Revenue Trends</h3>
            <canvas id="revenue-chart" height="120"></canvas>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenue-chart').getContext('2d');

        const revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Today', 'This Week', 'This Month'],
                datasets: [{
                    label: 'Revenue (Php)',
                    data: [
                        <?= $daily['total']; ?>,
                        <?= $weekly['total']; ?>,
                        <?= $monthly['total']; ?>
                    ],
                    backgroundColor: [
                        'rgba(79, 70, 229, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(234, 179, 8, 0.8)'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: context => 'Php ' + parseFloat(context.raw).toFixed(2)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Php' + value
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>