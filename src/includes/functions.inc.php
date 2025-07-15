<?php
    function emailTaken($conn, $email) {
        $sql = "SELECT user_id, CONCAT(first_name,' ', last_name) AS name, email, password, role AS user_type, is_disabled FROM users WHERE email = ?;";
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
        $sql = "SELECT SUM(amount) AS total, COUNT(*) AS count FROM transactions WHERE DATE(created_at) = CURDATE() AND is_deleted = 0;";
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
        $sql = "SELECT user_id, first_name, last_name, email, role, is_disabled, created_at FROM users;";
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
        $sql = 'UPDATE users SET is_disabled = NOT is_disabled WHERE user_id = ?;';
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
        $sql = 'INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?);';
        $stmt = mysqli_stmt_init($conn);


        if (!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: ../index.php");
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "sssss", $firstName, $lastName, $email, $hashedPassword, $role);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: ../admin/adminManageUsers.php?success=userCreated");
        exit();
    }
    function getAllTransactions($conn) {
        $sql = "SELECT t.transaction_id, t.amount, t.created_at, t.updated_at, u.first_name, u.last_name FROM transactions t JOIN users u ON t.user_id = u.user_id WHERE t.is_deleted = 0 ORDER BY t.created_at DESC;";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../index.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $transactions = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $transactions[] = $row;
        }

        mysqli_stmt_close($stmt);
        return $transactions;
    }
    function addTransaction($conn, $userID, $amount) {
        $sql = "INSERT INTO transactions (user_id, amount, created_at, is_deleted) VALUES (?, ?, NOW(), 0);";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../index.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "id", $userID, $amount);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: ../admin/adminTransactions.php?success=transactionAdded");
        exit();
    }
    function softDeleteTransaction($conn, $transactionID) {
        $sql = "UPDATE transactions SET is_deleted = 1, updated_at = NOW() WHERE transaction_id = ?;";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../index.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "i", $transactionID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: ../admin/adminTransactions.php?success=transactionDeleted");
        exit();
    }
    function getSingleSummary($conn, $sql) {
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return ['total' => 0, 'count' => 0];
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return [
            'total' => $row['total'] ?? 0,
            'count' => $row['count'] ?? 0
        ];
    }
    function getWeeklyRevenueSummary($conn) {
        $sql = "SELECT SUM(amount) AS total, COUNT(*) AS count FROM transactions WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) AND is_deleted = 0;";
        return getSingleSummary($conn, $sql);
    }
    function getMonthlyRevenueSummary($conn) {
        $sql = "SELECT SUM(amount) AS total, COUNT(*) AS count FROM transactions WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE()) AND is_deleted = 0";
        return getSingleSummary($conn, $sql);
    }
    function cashierInsertTransaction($conn, $userID, $amount) {
        $sql = "INSERT INTO transactions (user_id, amount, is_deleted) VALUES (?, ?, 0);";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../index.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "id", $userID, $amount);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: ../cashier/cashierDashboard.php?success=transactionAdded");
        exit();
    }
    function getTransactionsByUser($conn, $userID) {
        $sql = "SELECT transaction_id, amount, created_at FROM transactions WHERE user_id = ? AND is_deleted = 0 ORDER BY created_at DESC;";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../index.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "i", $userID);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        return $transactions;
    }