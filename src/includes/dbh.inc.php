<?php
    /* Code below is for local hosting. */

    define('DB_SERVER', 'localhost');
    define('DB_USERNAME_LOCAL', 'root');
    define('DB_PASSWORD_LOCAL', '');
    define('DB_NAME_LOCAL', 'edds_revenue_tracker_project'); // *SUBECT TO CHANGE*

    /* Code below is for the DCISM domain. */

    define('DB_USERNAME_REMOTE', 's22102758_eddsrevenuetracker'); // *SUBJECT TO CHANGE* Can also use other members' DCISM databases.
    define('DB_PASSWORD_REMOTE', 'eddtrack'); // *SUBJECT TO CHANGE* Actually, please change this...
    define('DB_NAME_REMOTE', 's22102758_eddsrevenuetracker'); // *SUBJECT TO CHANGE* Same with DB_USERNAME_REMOTE.

    // Verifying...

    $isOnRemote = ($_SERVER['HTTP_HOST'] === 'eddsrevenuetracker.dcism.org'); // *SUBJECT TO CHANGE* If this is changed, change the condition to match the chosen URL.

    if ($isOnRemote) {
        $dbUsername = DB_USERNAME_REMOTE;
        $dbPassword = DB_PASSWORD_REMOTE;
        $dbName = DB_NAME_REMOTE;
    } else {
        $dbUsername = DB_USERNAME_LOCAL;
        $dbPassword = DB_PASSWORD_LOCAL;
        $dbName = DB_NAME_LOCAL;
    }

    // Creating the connection...

    $conn = mysqli_connect(DB_SERVER, $dbUsername, $dbPassword, $dbName);

    if (!$conn) {
        error_log("DATABASE CONNECTION FAILED: " . mysqli_connect_error());
        die ("Sorry, there was a problem connecting to the database. Please try again later.");
    } 