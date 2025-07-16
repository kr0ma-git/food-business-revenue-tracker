<?php
    session_start();
    
    if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }

    require_once '../includes/dbh.inc.php';
    require_once '../includes/functions.inc.php';

    $users = getAllUsers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edd's Ngohiong | User Management</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Outfit:wght@100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Admin View</h2>
        </div>

        <nav class="sidebar-nav">
            <a href="adminDashboard.php" class="nav-link"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="adminTransactions.php" class="nav-link"><i class="fas fa-file-invoice"></i> Transactions</a>
            <a href="adminReports.php" class="nav-link"><i class="fas fa-chart-pie"></i> Reports</a>
            <a href="adminManageUsers.php" class="nav-link active"><i class="fas fa-users-cog"></i> Manage Users</a>
        </nav>

        <div class="sidebar-footer">
            <a href="../includes/logout.inc.php" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <section class="dashboard-wrapper">
        <div class="dashboard-header">
            <h2>Manage Users</h2>
            <p>View, add, or update user accounts</p>
        </div>

        <div class="add-user-form">
            <h3>Add New User</h3>
            <?php if (isset($_GET['error'])) {
                if ($_GET['error'] == 'emptyFields') {
                    echo "<p class='error'>Please fill in all fields!</p>";
                } else if ($_GET['error'] == 'emailTaken') {
                    echo "<p class='error'>Email is already registered!</p>";
                }
            }    
            ?>
            <?php if (isset($_GET['success']) && $_GET['success'] == 'userCreated')  {
                echo "<p class='success'>Account successfully added!</p>";
            }
            ?>

            <form action="../includes/adminCreateUser.inc.php" method="POST" class="forms">
                <div class="form-row">
                    <input type="text" name="first_name" placeholder="First Name" required>
                    <input type="text" name="last_name" placeholder="Last Name" required>
                </div>
                <div class="form-row">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-row" id="role-form-row">
                    <select name="role" id="role-select" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="cashier">Cashier</option>
                    </select>
                    <button type="submit" name="submit" class="btn">Create User</button>
                </div>
            </form>
        </div>

        <hr style="margin: 2rem 0;">


        <h4 style="margin-top: 20px; margin-bottom: 10px;">Search:</h4>
        <input type="text" id="search" placeholder="Search by Name..." autocomplete="off">
        <p id="no-user-results" style="display: none;">No users found matching your search.</p>
        <br><br>

        <div class="user-table">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="user-row" data-user-naming="<?= strtolower(htmlspecialchars($user['first_name'] . ' ' . $user['last_name'])); ?>">
                            <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td><?= ucfirst($user['role']); ?></td>
                            <td><?= $user['is_disabled'] ? 'Disabled' : 'Active'; ?></td>
                            <td><?= $user['created_at']; ?></td>
                            <td>
                                <form action="../includes/adminToggleUser.inc.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
                                    <button type="submit" class="btn small-btn">
                                        <?= $user['is_disabled'] ? 'Enable' : 'Disable'; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search');
            const userRows = document.querySelectorAll('.user-row');
            const noResultsMessage = document.getElementById('no-user-results');

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                let visibleCount = 0;

                userRows.forEach(row => {
                    const name = row.getAttribute('data-user-naming');
                    
                    if (name.includes(searchTerm)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>