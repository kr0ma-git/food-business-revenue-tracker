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
        $sql = "SELECT COUNT(*) AS count, quantity * p.price AS total FROM transactions JOIN products p ON transactions.product_id = p.product_id WHERE DATE(created_at) = CURDATE() AND is_deleted = 0;";
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
    function getAllCustomers($conn) {
        $sql = "SELECT customer_id, full_name FROM customers;";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../index.php");
            exit();
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $customers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $customers[] = $row;
        }

        mysqli_stmt_close($stmt);
        return $customers;
    }
    function getAllProducts($conn) {
        $sql = "SELECT product_id, product_name, price FROM products;";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../index.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }

        mysqli_stmt_close($stmt);
        return $products;
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
        $sql = "SELECT t.transaction_id, t.quantity * p.price AS amount, t.created_at, u.user_id, u.first_name, u.last_name, t.quantity, t.payment_type, p.product_name FROM transactions t LEFT JOIN users u ON t.user_id = u.user_id JOIN products p ON t.product_id = p.product_id WHERE t.is_deleted = 0 ORDER BY t.created_at DESC;";
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
    function addTransaction($conn, $userID, $productID, $quantity) {
        $sql = "INSERT INTO transactions (user_id, product_id, quantity, created_at, is_deleted) VALUES (?, ?, ?, NOW(), 0)";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../admin/adminTransactions.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "iii", $userID, $productID, $quantity);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: ../admin/adminTransactions.php?success=transactionAdded");
        exit();
    }
    function addTransactionCashier($conn, $userID, $productID, $quantity) {
        $sql = "INSERT INTO transactions (user_id, product_id, quantity, created_at, is_deleted) VALUES (?, ?, ?, NOW(), 0)";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../admin/adminTransactions.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "iii", $userID, $productID, $quantity);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: ../cashier/cashierDashboard.php?success=transactionAdded");
        exit();
    }
    function addTransactionsWalkIn($conn, $userID, $productID, $quantity) {
        $sql = "INSERT INTO transactions (customer_id, product_id, quantity, created_at, is_deleted) VALUES (99, ?, ?, NOW(), 0)";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../admin/adminTransactions.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "iii", $userID, $productID, $quantity);
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
        $sql = "SELECT SUM(t.quantity * p.price) AS total, COUNT(*) AS count FROM transactions t JOIN products p ON t.product_id = p.product_id WHERE YEARWEEK(t.created_at, 1) = YEARWEEK(CURDATE(), 1) AND t.is_deleted = 0;";
        return getSingleSummary($conn, $sql);
    }
    function getMonthlyRevenueSummary($conn) {
        $sql = "SELECT SUM(t.quantity * p.price) AS total, COUNT(*) AS count FROM transactions t JOIN products p ON t.product_id = p.product_id WHERE YEAR(t.created_at) = YEAR(CURDATE()) AND MONTH(t.created_at) = MONTH(CURDATE()) AND t.is_deleted = 0;";
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
    function getRecentTransactions($conn) {
        $sql = "SELECT t.transaction_id, t.created_at, u.first_name, u.last_name, t.quantity * p.price AS amount FROM transactions t JOIN products p ON t.product_id = p.product_id JOIN users u ON t.user_id = u.user_id WHERE t.is_deleted = 0 ORDER BY t.created_at DESC LIMIT 3;";
        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../index.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        return $transactions;
    }
    function addCustomer($conn, $fullName, $contact, $address) {
        $sql = "INSERT INTO customers (full_name, contact_number, customer_address) VALUES (?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../pages/addTransaction.php?error=stmtFailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $fullName, $contact, $address);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    function customerExists($conn, $fullName, $contact) {
        $sql = "SELECT * FROM customers WHERE full_name = ? AND contact_number = ? LIMIT 1;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "ss", $fullName, $contact);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $exists ? true : false;
    }