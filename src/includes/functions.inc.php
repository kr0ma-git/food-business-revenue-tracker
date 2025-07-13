<?php
    function emailTaken($conn, $email) {
        $sql = "SELECT user_id, CONCAT(first_name,' ', last_name) AS name, email, password, role AS user_type, is_disabled FROM users WHERE email = ?";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../login.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: false;
    }
    function emptyInputLogin($email, $password) {
        if(empty($email) || empty($password)) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }
    function loginUser($conn, $email, $password) {
        $userData = emailTaken($conn, $email);

        if ($userData == false) {
            header("Location: ../login.php?error=wrongLogin");
            exit();
        }

        if ($userData['is_disabled'] == 1) {
            header("Location: ../login.php?error=accDisabled");
            exit();
        }

        $passwordHashed = $userData['password'];
        $checkPassword = password_verify($password, $passwordHashed);

        if ($checkPassword == false) {
            header("Location: ../login.php?error=wrongLogin");
            exit();
        }

        session_start();
        $_SESSION['userID'] = $userData['user_id'];
        $_SESSION['userEmail'] = $userData['email'];
        $_SESSION['userRole'] = $userData['user_type'];
        $_SESSION['userName'] = $userData['name'];

        if ($_SESSION['userRole'] == 'cashier') {
            header("Location: ../cashier/cashierDashboard.php");
            exit();
        } else if ($_SESSION['userRole'] == 'admin') {
            header("Location: ../admin/adminDashboard.php");
            exit();
        }

        exit();
    }
    function getDailyRevenueSummary($conn) {
        $sql = "SELECT SUM(amount) AS total, COUNT(*) AS count FROM transactions WHERE DATE(created_at) = CURDATE() AND is_deleted = 0";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../login.php/error=stmtFailed");
            exit();
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            return [
                'total' => $row['total'] ?? 0,
                'count' => $row['count'] ?? 0
            ];
        }

        return ['total' => 0, 'count' => 0];
    }
    function getAllUsers($conn) {
        $sql = "SELECT user_id, first_name, last_name, email, role, is_disabled, created_at FROM users";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../index.php");
            exit();
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }

        mysqli_stmt_close($stmt);
        return $users;
    }
    function userToggle($conn, $userID) {
        $sql = 'UPDATE users SET is_disabled = NOT is_disabled WHERE user_id = ?';
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../index.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "i", $userID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: ../admin/adminManageUsers.php");
        exit();
    }
    function createUser($conn, $firstName, $lastName, $email, $password, $role) {
        $sql = 'INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)';
        $stmt = mysqli_stmt_init($conn);


        if (!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: ../index.php");
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, 'sssss', $firstName, $lastName, $email, $hashedPassword, $role);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: ../admin/adminManageUsers.php?success=userCreated");
        exit();
    }