<?php
    session_start();

    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'cashier') {
        header("Location: ../index.php");
        exit();
    }

    if (isset($_POST['submit'])) {
        require_once 'dbh.inc.php';
        require_once 'functions.inc.php';

        $userID = $_POST['user_id'];
        $productID = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        if (empty($userID) || empty($productID) || empty($quantity)) {
            header("Location: ../admin/adminTransactions.php?error=invalidInput");
            exit();
        }

        if ($userID == 0) {
            addTransactionsWalkIn($conn, $userID, $productID, $quantity);
        } else {
            addTransaction($conn, $userID, $productID, $quantity);
        }
    }