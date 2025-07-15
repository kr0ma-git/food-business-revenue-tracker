<?php
    session_start();

    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }

    if (isset($_POST['submit'])) {
        require_once 'dbh.inc.php';
        require_once 'functions.inc.php';

        $transactionID = $_POST['transaction_id'];

        if (empty($transactionID)) {
            header("Location: ../admin/adminTransactions.php?error=missingID");
            exit();
        }

        softDeleteTransaction($conn, $transactionID);
    }
