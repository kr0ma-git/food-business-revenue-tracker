<?php
    session_start();

    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
        require_once '../includes/dbh.inc.php';
        require_once '../includes/functions.inc.php';

        $userID = $_POST['user_id'];
        
        userToggle($conn, $userID);
    }