<?php
    session_start();

    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }

    if (isset($_POST['submit'])) {
        require_once '../includes/dbh.inc.php';
        require_once '../includes/functions.inc.php';

        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($role)) {
            header("Location: ../admin/adminManageUsers.php?error=emptyFields");
            exit();
        }

        if (emailTaken($conn, $email)) {
            header("Location: ../admin/adminManageUsers.php?error=emailTaken");
            exit();
        }

        createUser($conn, $firstName, $lastName, $email, $password, $role);
    }
?>