<?php
    session_start();

    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'cashier') {
        header("Location: ../index.php");
        exit();
    }

    if (isset($_POST['submit'])) {
        require_once 'dbh.inc.php';
        require_once 'functions.inc.php';

        $userID = $_SESSION['userID'];
        $amount = floatval($_POST['amount']);

        cashierInsertTransaction($conn, $userID, $amount);
    }