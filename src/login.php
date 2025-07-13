<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edd's Ngohiong | Login Page</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Outfit:wght@100..900&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <section class="login-wrapper">
        <div class="login-container">
            <h2>Login</h2>

            <?php
                if (isset($_GET['error'])) {
                    if ($_GET['error'] === 'emptyInput') {
                        echo "<p class='error-msg'>Please fill in all the fields!</p>";
                    } else if ($_GET['error'] === 'wrongLogin') {
                        echo "<p class='error-msg'>Invalid email or password!</p>";
                    } else if ($_GET['error'] === 'accDisabled') {
                        echo "<p class='error-msg'>Account has been disabled!</p>";
                    } else if ($_GET['error'] === 'stmtFailed') {
                        echo "<p class='error-msg'>An error has occured!</p>";
                    }
                }
            ?>

            <form action="includes/login.inc.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <button type="submit" name="submit" class="btn">Login</button>
            </form>
        </div>
    </section>
</body>
</html>