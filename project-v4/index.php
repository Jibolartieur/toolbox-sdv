<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Toolbox</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div id="app">
        <?php
        session_start();
        if (isset($_SESSION['user_id'])) {
            include 'views/dashboard.php';
        } else {
            include 'views/login.php';
        }
        ?>
    </div>
    <script src="js/main.js"></script>
</body>
</html>