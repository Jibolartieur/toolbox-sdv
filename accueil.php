<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.html");
  exit();
}
$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Bienvenue</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .bg {
      background-image: url("cat.jpeg");
      height: 100%;
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      color: white;
      text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.7);
    }

    h1 {
      font-size: 48px;
      margin: 0;
    }

    a.logout {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: rgba(0,0,0,0.5);
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-size: 16px;
    }

    a.logout:hover {
      background-color: rgba(255, 255, 255, 0.3);
    }
  </style>
</head>
<body>

<div class="bg">
  <h1>Bienvenue <?php echo $username; ?></h1>
  <a href="logout.php" class="logout">Déconnexion</a>
</div>

</body>
</html>
