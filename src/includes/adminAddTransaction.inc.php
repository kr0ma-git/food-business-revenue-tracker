<?php
    session_start();

    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }

    if (isset($_POST['submit'])) {
        require_once 'dbh.inc.php';
        require_once 'functions.inc.php';

        $userID = $_POST['user_id'];
        $amount = $_POST['amount'];

        if (empty($userID) || empty($amount)) {
            header("Location: ../admin/adminTransactions.inc.php?error=invalidInput");
            exit();
        }
    }

    addTransaction($conn, $userID, $amount);
