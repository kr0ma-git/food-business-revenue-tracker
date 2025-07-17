<?php
    session_start();

    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }

    if (isset($_POST['add_customer'])) {
        require_once 'dbh.inc.php';
        require_once 'functions.inc.php';

        $fullName = trim($_POST['full_name']);
        $contact = trim($_POST['contact_number']);
        $address = trim($_POST['address']);

        if (empty($fullName) || empty($contact) || empty($address)) {
            header("Location: ../admin/adminManageUsers.php?error=emptyFields");
            exit();
        }

        if (customerExists($conn, $fullName, $contact)) {
            header("Location: ../admin/adminManageUsers.php?error=customerExists");
            exit();
        }

        addCustomer($conn, $fullName, $contact, $address);
        header("Location: ../admin/adminManageUsers.php?success=customerAdded");
        exit();
    } else {
        header("Location: ../index.php");
        exit();
    }