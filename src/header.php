<?php
    session_start();

    if (isset($_SESSION['user_role'])) { // If user is already logged in, no need to go to the landing page.
        if ($_SESSION['user_role'] == 'admin') {
            header("Location: admin/admin-dashboard.php");
            exit();
        } else {
            header("Location: cashier/cashier-dashboard.php");
        }
    } else  {
        $userRole = $_SESSION['user_role'] ?? null;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edd's Ngohiong | Landing Page</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Outfit:wght@100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <h1 class="nav-title">Edd's Ngohiong</h1>
            <?php if (isset($_SESSION['user_id'])): // Actually don't really need this since the user doesn't go to the landing page if already logged in. Not gonna remove it tho. :) ?> 
                <nav class="nav-links">
                    <?php if ($userRole == 'admin'): ?>
                        <a href="admin/admin-dashboard.php">Dashboard</a>
                    <?php elseif ($userRole == 'cashier'): ?>
                        <a href="cashier/cashier-dashboard.php">Dashboard</a>
                    <?php endif; ?>
                    <a href="logout.php">Log Out</a>
                </nav>
            <?php endif; ?>
        </div>
    </header>